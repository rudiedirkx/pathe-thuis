<?php

use rdx\jsdom\Node;
use rdx\pathethuis\Movie;

require __DIR__ . '/inc.bootstrap.php';

$movies = Movie::all('1 ORDER BY name');

if (isset($_POST['source'])) {
	$doc = Node::create($_POST['source']);
	$exist = array_column($movies, 'name', 'pathe_id');
// print_r($exist);

	$items = $doc->queryAll('.vertical-poster-list__item');
// var_dump(count($items));
	foreach ($items as $item) {
		$href = $item->query('a')['href'];
		preg_match('#^/film/(\d+)/#', $href, $match);
		$id = $match[1];
		$name = $item->query('.poster__caption')->textContent;
// var_dump($id, $name);
		if (!isset($exist[$id])) {
			Movie::insert([
				'name' => $name,
				'pathe_id' => $id,
			]);
		}
	}

	return do_redirect('index');
}

require 'tpl.header.php';

Movie::eager('prices', $movies);

?>
<h1>Movies</h1>

<table>
	<thead>
		<tr>
			<th>Name</th>
			<th>ID</th>
			<th data-sort>Price</th>
		</tr>
	</thead>
	<tbody>
		<? foreach ($movies as $movie): ?>
			<tr>
				<td><?= html($movie->name) ?></td>
				<td><a href="<?= $movie->full_url ?>"><?= html($movie->pathe_id) ?></a></td>
				<td class="prices" data-value="<?= (array_values($movie->prices)[0]->price ?? 99) * 100 + 1000 ?>">
					<? foreach (array_values($movie->prices) as $i => $price): ?>
						<? if ($i == 0): ?>
							<span class="current"><?= html_price(round($price->price)) ?></span>
						<? else: ?>
							<?= $i ? ' &lt;' : '' ?>
							<?= html_price(round($price->price)) ?>
						<? endif ?>
					<? endforeach ?>
				</td>
			</tr>
		<? endforeach ?>
	</tbody>
</table>

<br>

<form method="post" action>
	<fieldset>
		<legend>Sync watchlist</legend>
		<p>Source HTML:<br><textarea name="source"></textarea></p>
		<p><button>Sync</button></p>
	</fieldset>
</form>

<script>
document.querySelector('[data-sort]').addEventListener('click', function(e) {
	const ci = this.cellIndex;
	const tbody = this.closest('table').querySelector('tbody');
	const rows = [...tbody.rows].sort((a, b) => a.cells[ci].dataset.value < b.cells[ci].dataset.value ? -1 : 1);
	rows.forEach(tr => tbody.append(tr));
});
</script>
<?php

require 'tpl.footer.php';
