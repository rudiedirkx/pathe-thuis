<?php

use GuzzleHttp\Client as Guzzle;
use rdx\pathethuis\Movie;
use rdx\pathethuis\PriceChange;

require __DIR__ . '/inc.bootstrap.php';

$movies = Movie::all('1 ORDER BY name');

$guzzle = new Guzzle();

$unfound = 0;
$updates = [];
foreach ($movies as $movie) {
	echo "$movie->name\n";

	$rsp = $guzzle->get($movie->full_url);
	$html = $rsp->getBody();

	if (!preg_match('#"price":(\d+(?:\.\d+)?),#', $html, $match)) {
		$unfound++;
		echo "    COULDN'T FIND PRICE!\n";
		continue;
	}

	$price = round($match[1], 2);

	$change = $movie->last_price;
	if (!$change || $change->price != $price) {
		$id = PriceChange::insert([
			'movie_id' => $movie->id,
			'price' => $price,
			'first_fetch_on' => time(),
			'last_fetch_on' => time(),
		]);
		if ($change && $price < $change->price) {
			$updates[] = "$movie->name: $change->price -> $price";
		}
	}
	else {
		$id = $change->id;
		$change->update([
			'last_fetch_on' => time(),
		]);
	}

	usleep(1000 * rand(200, 700));
}

echo "\nDONE.\n";

if (count($updates)) {
	echo "\n- " . implode("\n- ", $updates) . "\n";
}

exit($unfound > 3 || count($updates) ? 1 : 0);
