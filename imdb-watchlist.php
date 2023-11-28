<?php

require __DIR__ . '/inc.bootstrap.php';

require 'tpl.header.php';

$counts = $db->select('imdb_watchlist', '1=1 order by date asc')->all();

?>
<div id="chart"></div>

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
			enabled: false,
		},
		data: [
			{
				name: "Watchlist",
				type: "spline",
				color: "green",
				markerSize: 0,
				showInLegend: false,
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
				type: "spline",
				color: "red",
				markerSize: 0,
				showInLegend: false,
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
