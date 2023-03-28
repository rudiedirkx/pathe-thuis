<?php

require __DIR__ . '/env.php';
require __DIR__ . '/vendor/autoload.php';

header('Content-type: text/plain; charset=utf-8');

$db = db_sqlite::open(array('database' => PATHE_DB_FILE));
db_generic_model::$_db = $db;
$db->ensureSchema(require 'inc.db-schema.php');

session_start();

$_SESSION['pathe_thuis_csrf'] ??= (string) rand();

const PATHE_URL = 'https://www.pathe-thuis.nl/film/%s/%s';
