<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the command line.');
}

$projectRoot = dirname(__DIR__);
require_once $projectRoot . '/upload/config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$database = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, (int) DB_PORT);
$database->set_charset('utf8mb4');

if (!function_exists('curl_init')) {
    throw new RuntimeException('cURL is required to refresh OCMOD.');
}

$userResult = $database->query(
    'SELECT `user_id` FROM `' . DB_PREFIX . 'user`
     WHERE `status` = 1 AND `user_group_id` = 1
     ORDER BY `user_id` LIMIT 1'
);

if (!$userResult->num_rows) {
    throw new RuntimeException('No active super-admin user is available for OCMOD refresh.');
}

$sessionId = bin2hex(random_bytes(16));
$userToken = bin2hex(random_bytes(16));
$userId = (int)$userResult->fetch_assoc()['user_id'];
$sessionData = json_encode(array(
    'user_id' => $userId,
    'user_token' => $userToken,
));
$session = $database->prepare(
    'REPLACE INTO `' . DB_PREFIX . 'session`
     (`session_id`, `data`, `expire`)
     VALUES (?, ?, DATE_ADD(UTC_TIMESTAMP(), INTERVAL 10 MINUTE))'
);
$session->bind_param('ss', $sessionId, $sessionData);
$session->execute();

try {
    $url = rtrim(HTTP_SERVER, '/')
        . '/admin/index.php?route=marketplace/modification/refresh&user_token='
        . rawurlencode($userToken);
    $curl = curl_init($url);
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIE => 'OCSESSID=' . $sessionId,
        CURLOPT_TIMEOUT => 180,
    ));
    $response = curl_exec($curl);
    $status = (int)curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    $error = curl_error($curl);
    curl_close($curl);

    if ($response === false || $error !== '' || $status >= 400) {
        throw new RuntimeException('OCMOD refresh failed (HTTP ' . $status . '): ' . $error);
    }
} finally {
    $delete = $database->prepare(
        'DELETE FROM `' . DB_PREFIX . 'session` WHERE `session_id` = ?'
    );
    $delete->bind_param('s', $sessionId);
    $delete->execute();
}

$activeOrderMail = DIR_MODIFICATION . 'catalog/controller/mail/order.php';
$orderMailSource = $projectRoot . '/upload/catalog/controller/mail/order.php';
$activeOrderMailCode = is_file($activeOrderMail)
    ? file_get_contents($activeOrderMail)
    : file_get_contents($orderMailSource);

if (
    strpos($activeOrderMailCode, 'createOrderConfirmationMail') === false
    || strpos($activeOrderMailCode, 'setBcc($bcc)') === false
) {
    throw new RuntimeException('OCMOD refreshed, but the active order mail controller is stale.');
}

echo "OCMOD cache refreshed and active order mail verified.\n";
