<?php

use rdx\imdb\Client as Imdb;

function html( ?string $text ) : string {
	return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8') ?: htmlspecialchars((string)$text, ENT_QUOTES, 'ISO-8859-1');
}

function html_price( float $amount ) : string {
	return number_format($amount, 2, '.', ' ');
}

/**
 * @param AssocArray $query
 */
function get_url( ?string $path, array $query = array() ) : string {
	$query = $query ? '?' . http_build_query($query) : '';
	$path = $path ? $path . '.php' : basename($_SERVER['SCRIPT_NAME']);
	return $path . $query;
}

/**
 * @param AssocArray $query
 */
function do_redirect( ?string $path, array $query = array() ) : never {
	$url = get_url($path, $query);
	header('Location: ' . $url);
	exit;
}

function db() : db_generic {
	return $GLOBALS['db'];
}

function imdb() : ?Imdb {
	return $GLOBALS['imdb'];
}
