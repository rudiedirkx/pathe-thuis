<?php

return [
	'version' => 2,
	'tables' => [
		'movies' => [
			'id' => ['pk' => true],
			'name',
			'pathe_id',
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
