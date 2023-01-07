<?php

return [
	'version' => 3,
	'tables' => [
		'movies' => [
			'id' => ['pk' => true],
			'name',
			'pathe_id',
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
