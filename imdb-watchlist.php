<?php

require __DIR__ . '/inc.bootstrap.php';

require 'tpl.header.php';

$counts = db()->select('imdb_watchlist', '1=1 order by date asc');

$onlyWeekly = function(array $in) : array {
	if (count($in) < 500) {
		return $in;
	}

	$chunks = array_column(array_chunk($in, 7), 0);
	if (end($chunks) != end($in)) {
		$chunks[] = end($in);
	}

	return $chunks;
};

$perMonthWatchlist = $perMonthSeen = [];
foreach ($counts as $count) {
	$month = substr($count->date, 0, 7);
	if ($count->count) $perMonthWatchlist[$month] ??= $count->count;
	if ($count->seen) $perMonthSeen[$month] ??= $count->seen;
}
// dump($perMonthWatchlist, $perMonthSeen);

foreach ([&$perMonthWatchlist, &$perMonthSeen] as $i => &$in) {
	$out = [];
	foreach ($in as $month => $num) {
		$nextMonth = date('Y-m', strtotime('+1 month', strtotime("$month-01")));
		if (isset($in[$nextMonth])) {
			$out[$month] = ($in[$nextMonth] - $num) * ($i == 0 ? -1 : 1);
		}
	}
	$in = $out;
}
// dump($perMonthWatchlist, $perMonthSeen);

?>
<div id="chart1" style="width: 100%; aspect-ratio: 3/1"></div>
<br><hr><br>
<div id="chart2" style="width: 100%; aspect-ratio: 3/1"></div>

<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
<script>
(function() {
	const chart1 = new CanvasJS.Chart("chart1", {
		animationEnabled: false,
		axisX: {
			valueFormatString: "DD-MM-'YY",
		},
		axisY: {
			title: "Watchlist (absolute)",
		},
		axisY2: {
			title: "Rated (absolute)",
		},
		toolTip: {
			enabled: true,
		},
		data: [
			{
				name: "Watchlist (absolute)",
				type: "line",
				color: "green",
				markerSize: 0,
				showInLegend: true,
				dataPoints: [
					<? foreach ($onlyWeekly($counts) as $info): ?>
						{
							x: new Date('<?= $info->date ?>'),
							y: <?= (int) $info->count ?>,
						},
					<? endforeach ?>
				],
			},
			{
				name: "Rated (absolute)",
				axisYType: "secondary",
				type: "line",
				color: "red",
				markerSize: 0,
				showInLegend: true,
				dataPoints: [
					<? foreach ($onlyWeekly(array_filter($counts, fn($info) => $info->seen)) as $info): ?>
						{
							x: new Date('<?= $info->date ?>'),
							y: <?= (int) $info->seen ?>,
						},
					<? endforeach ?>
				],
			},
		],
	});
	chart1.render();

	const chart2 = new CanvasJS.Chart("chart2", {
		animationEnabled: false,
		axisX: {
			valueFormatString: "DD-MM-'YY",
		},
		axisY: {
			title: "Watchlist (change/month)",
		},
		axisY2: {
			title: "Rated (change/month)",
		},
		toolTip: {
			enabled: true,
		},
		data: [
			{
				name: "Watchlist (change/month)",
				type: "spline",
				color: "green",
				markerSize: 0,
				showInLegend: true,
				dataPoints: [
					<? foreach ($perMonthWatchlist as $month => $num): ?>
						{
							x: new Date('<?= $month ?>-01'),
							y: <?= (int) $num ?>,
						},
					<? endforeach ?>
				],
			},
			{
				name: "Rated (change/month)",
				axisYType: "secondary",
				type: "spline",
				color: "red",
				markerSize: 0,
				showInLegend: true,
				dataPoints: [
					<? foreach ($perMonthSeen as $month => $num): ?>
						{
							x: new Date('<?= $month ?>-01'),
							y: <?= (int) $num ?>,
						},
					<? endforeach ?>
				],
			},
		],
	});
	chart2.render();
})();
</script>
