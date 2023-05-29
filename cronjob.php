<?php

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\BadResponseException;
use rdx\pathethuis\Movie;
use rdx\pathethuis\PriceChange;

require __DIR__ . '/inc.bootstrap.php';

$movies = Movie::all("deleted = '0' ORDER BY name ASC");

$guzzle = new Guzzle();

$warnings = 0;
$updates = [];
foreach ($movies as $movie) {
	echo "$movie->name\n";

	try {
		$rsp = $guzzle->get($movie->full_url);
		$html = $rsp->getBody();
	}
	catch (BadResponseException $ex) {
		$warnings++;
		echo "    RESPONSE CODE " . $ex->getResponse()->getStatusCode() . "!\n";
		continue;
	}

	if (!preg_match('#"price":(\d+(?:\.\d+)?),#', $html, $match)) {
		$warnings++;
		echo "    COULDN'T FIND PRICE!\n";
		continue;
	}

	$price = round($match[1], 2);
// var_dump($price);

	$rating = 0;
	if (preg_match('#"ratingValue":"([\d\.]+)",#', $html, $match)) {
		$rating = round($match[1] * 10);
	}
// var_dump($rating);

	$imdb = 0;
	if (preg_match('#"sameAs":"https://www.imdb.com/title/(tt\d{6,})/?",#', $html, $match)) {
		$imdb = $match[1];
	}
// var_dump($imdb);

	$movie->update([
		'rating' => $rating,
		'imdb_id' => $imdb,
	]);

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

exit($warnings > 3 || count($updates) ? 1 : 0);
