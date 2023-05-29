<?php

return [
	'version' => 5,
	'tables' => [
		'movies' => [
			'id' => ['pk' => true],
			'name',
			'pathe_id',
			'imdb_id',
			'rating' => ['type' => 'int', 'default' => 0],
			'deleted' => ['type' => 'int', 'default' => 0],
		],
		'price_changes' => [
			'id' => ['pk' => true],
			'movie_id' => ['unsigned' => true, 'references' => ['movies', 'id', 'cascade']],
			'price' => ['type' => 'float'],
			'first_fetch_on' => ['unsigned' => true, 'null' => false, 'default' => 0],
			'last_fetch_on' => ['unsigned' => true, 'null' => false, 'default' => 0],
		],
	],
];
