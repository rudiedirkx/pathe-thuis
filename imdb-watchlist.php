<?php

require __DIR__ . '/inc.bootstrap.php';

require 'tpl.header.php';

$counts = $db->select('imdb_watchlist', '1=1 order by date asc')->all();

$perMonthWatchlist = $perMonthSeen = [];
foreach ($counts as $count) {
	if (str_ends_with($count['date'], '-01')) {
		$month = substr($count['date'], 0, 7);
		if ($count['count']) $perMonthWatchlist[$month] ??= $count['count'];
		if ($count['seen']) $perMonthSeen[$month] ??= $count['seen'];
	}
}
foreach ([&$perMonthWatchlist, &$perMonthSeen] as &$arr) {
	$arr = array_map(function(int $start, int $end) {
		return $end - $start;
	}, array_slice($arr, 0, -1), array_slice($arr, 1));
	$arr = abs(round(array_sum($arr) / count($arr)));
}

?>
<div id="chart" style="width: 100%; aspect-ratio: 3/1"></div>

<p>Watchlist: <?= $perMonthWatchlist ?> per month. Rated: <?= $perMonthSeen ?> per month.</p>

<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
<script>
(function() {
	const chart = new CanvasJS.Chart("chart", {
		animationEnabled: false,
		axisX: {
			valueFormatString: "DD-MM-'YY",
		},
		axisY: {
			title: "Watchlist",
		},
		axisY2: {
			title: "Rated",
		},
		toolTip: {
			enabled: true,
		},
		data: [
			{
				name: "Watchlist",
				type: "line",
				color: "green",
				markerSize: 0,
				showInLegend: true,
				dataPoints: [
					<? foreach ($counts as $info): ?>
						{
							x: new Date('<?= $info->date ?>'),
							y: <?= $info->count ?>,
						},
					<? endforeach ?>
				],
			},
			{
				name: "Rated",
				axisYType: "secondary",
				type: "line",
				color: "red",
				markerSize: 0,
				showInLegend: true,
				dataPoints: [
					<? foreach ($counts as $info): if ($info->seen): ?>
						{
							x: new Date('<?= $info->date ?>'),
							y: <?= $info->seen ?>,
						},
					<? endif; endforeach ?>
				],
			},
		],
	});
	chart.render();
})();
</script>
