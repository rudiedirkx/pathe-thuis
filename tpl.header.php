<?php

header('Content-type: text/html; charset=utf-8');

?>
<!doctype html>
<html>

<head>
	<meta charset="utf-8" />
	<meta name="theme-color" content="#333" />
	<meta name="referrer" content="no-referrer" />
	<title>Pathe Thuis Tracker</title>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="icon" type="image/png" href="/favicon-128.png" sizes="128x128" />
	<link rel="icon" href="/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	<style>
	html {
		font-family: sans-serif;
	}

	table {
		border-spacing: 0;
	}
	th, td {
		border: solid 1px #aaa;
		padding: 4px 7px;
		vertical-align: top;
	}
	th {
		text-align: left;
		border-bottom: solid 2px #000;
	}
	th[data-sort]:after {
		content: " \21F3";
	}

	tr.deleted {
		color: red;
	}

	.prices .discount {
		font-weight: bold;
		color: green;
	}
	</style>
</head>

<body>
