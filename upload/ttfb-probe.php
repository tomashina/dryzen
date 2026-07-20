<?php

$startedAt = microtime(true);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('X-Robots-Tag: noindex, nofollow, noarchive');

$payload = array(
	'status'         => 'ok',
	'php_elapsed_ms' => round((microtime(true) - $startedAt) * 1000, 3),
);

echo json_encode($payload);
