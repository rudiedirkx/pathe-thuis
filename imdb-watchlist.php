<?php

require __DIR__ . '/inc.bootstrap.php';

require 'tpl.header.php';

$counts = $db->select_fields('imdb_watchlist', 'date, count', '1=1');

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
			title: "Count",
			minimum: <?= 100 * floor(max(0, min($counts) - 10) / 100) ?>,
		},
		toolTip: {
			enabled: false,
		},
		data: [
			{
				name: "Watchlist",
				type: "spline",
				color: "green",
				showInLegend: false,
				dataPoints: [
					<? foreach ($counts as $date => $count): ?>
						{
							x: new Date('<?= $date ?>'),
							y: <?= $count ?>,
						},
					<? endforeach ?>
				],
			},
		],
	});
	chart.render();
})();
</script>
