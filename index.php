<?php

use rdx\pathethuis\Movie;

require __DIR__ . '/inc.bootstrap.php';

if (isset($_POST['html']) || isset($_POST['json'])) {
	header('Access-Control-Allow-Private-Network: true');
	header('Access-Control-Allow-Origin: https://www.pathe-thuis.nl');

	$items = isset($_POST['html']) ? Movie::htmlToMovies($_POST['html']) : json_decode($_POST['json'], true);
	[$created, $deleted] = Movie::syncMovies($items);

	if (isset($_POST['ajax'])) {
		exit("Created $created movies, and deleted $deleted.");
	}

	return do_redirect('index');
}

require 'tpl.header.php';

$deleted = isset($_GET['deleted']);
$movies = Movie::all(($deleted ? '1=1' : "deleted = '0'") . " ORDER BY name");
Movie::eager('prices', $movies);

$numDeleted = $deleted ? 0 : Movie::count("deleted = '1'");

?>
<h1>Movies (<?= count($movies) ?>)</h1>
<? if ($numDeleted): ?>
	<p><a href="?deleted=1">+ <?= $numDeleted ?> deleted</a></p>
<? endif ?>

<table>
	<thead>
		<tr>
			<th>Name</th>
			<th>ID</th>
			<th data-sort>Price</th>
			<th data-sort>Last checked</th>
		</tr>
	</thead>
	<tbody>
		<? foreach ($movies as $movie):
			$prices = array_values($movie->prices);
			?>
			<tr class="<?= $movie->deleted ? 'deleted' : '' ?>">
				<td><?= html($movie->name) ?></td>
				<td><a href="<?= $movie->full_url ?>"><?= html($movie->pathe_id) ?></a></td>
				<td class="prices" data-value="<?= ($prices[0]->price ?? 99) * 100 + 1000 ?>">
					<? foreach ($prices as $i => $price): ?>
						<? if ($i == 0): ?>
							<span
								class="<?= $price->price < ($prices[1]->price ?? 0) ? 'discount' : '' ?>"
								title="Found: <?= date('d-m-Y', $price->first_fetch_on) ?>"
							>
								<?= html_price(round($price->price)) ?>
							</span>
						<? else: ?>
							<?= $i ? ' &lt;' : '' ?>
							<span title="Found: <?= date('d-m-Y', $price->first_fetch_on) ?>">
								<?= html_price(round($price->price)) ?>
							</span>
						<? endif ?>
					<? endforeach ?>
				</td>
				<td data-value="<?= $prices[0]->last_fetch_on ?? 0 ?>">
					<?= count($prices) ? date('Y-m-d', $prices[0]->last_fetch_on) : '' ?>
				</td>
			</tr>
		<? endforeach ?>
	</tbody>
</table>

<br>

<fieldset>
	<legend>Sync watchlist</legend>
	<form method="post" action>
		<p>Source HTML:<br><textarea name="html"></textarea></p>
		<p><button>Sync</button></p>
	</form>
	<form method="post" action>
		<p>JSON export:<br><textarea name="json"></textarea></p>
		<p><button>Sync</button></p>
	</form>
</fieldset>

<script>
document.querySelectorAll('[data-sort]').forEach(el => el.addEventListener('click', function(e) {
	const ci = this.cellIndex;
	const tbody = this.closest('table').querySelector('tbody');
	const rows = [...tbody.rows].sort((a, b) => a.cells[ci].dataset.value < b.cells[ci].dataset.value ? -1 : 1);
	rows.forEach(tr => tbody.append(tr));
}));
</script>
<?php

require 'tpl.footer.php';
