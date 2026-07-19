<?php

require_once dirname(__DIR__) . '/upload/config.php';

$database = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, defined('DB_PORT') ? DB_PORT : 3306);

if ($database->connect_errno) {
    fwrite(STDERR, 'Database connection failed: ' . $database->connect_error . PHP_EOL);
    exit(1);
}

$database->set_charset('utf8mb4');

$settingKeys = array(
    'theme_default_image_product_width',
    'theme_default_image_product_height',
);

$placeholders = implode(',', array_fill(0, count($settingKeys), '?'));
$select = $database->prepare(
    'SELECT setting_id, store_id, code, `key`, `value`, serialized
     FROM `' . DB_PREFIX . 'setting`
     WHERE `key` IN (' . $placeholders . ')
     ORDER BY setting_id'
);

$select->bind_param('ss', $settingKeys[0], $settingKeys[1]);
$select->execute();
$result = $select->get_result();
$before = array();

while ($row = $result->fetch_assoc()) {
    $before[] = $row;
}

if (count($before) !== 2) {
    fwrite(STDERR, 'Expected both existing product image dimension settings.' . PHP_EOL);
    exit(1);
}

$backupDirectory = dirname(__DIR__) . '/database/backups';

if (!is_dir($backupDirectory) && !mkdir($backupDirectory, 0775, true) && !is_dir($backupDirectory)) {
    fwrite(STDERR, 'Could not create backup directory.' . PHP_EOL);
    exit(1);
}

$backupPath = $backupDirectory . '/shop-card-image-settings-' . date('Ymd-His') . '.json';
$backup = array(
    'created_at' => date(DATE_ATOM),
    'settings' => $before,
);

if (file_put_contents($backupPath, json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) === false) {
    fwrite(STDERR, 'Could not write settings backup.' . PHP_EOL);
    exit(1);
}

$newSize = '900';
$update = $database->prepare(
    'UPDATE `' . DB_PREFIX . 'setting`
     SET `value` = ?
     WHERE `key` = ?'
);

$database->begin_transaction();

try {
    foreach ($settingKeys as $settingKey) {
        $update->bind_param('ss', $newSize, $settingKey);
        $update->execute();

        if ($update->affected_rows > 1) {
            throw new RuntimeException('Unexpected number of settings updated for ' . $settingKey);
        }
    }

    $database->commit();
} catch (Throwable $error) {
    $database->rollback();
    fwrite(STDERR, $error->getMessage() . PHP_EOL);
    exit(1);
}

echo 'Shop product images set to 900 x 900 px.' . PHP_EOL;
echo 'Backup: ' . $backupPath . PHP_EOL;
