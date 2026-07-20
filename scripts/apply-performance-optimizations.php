<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/upload/config.php';

$database = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, defined('DB_PORT') ? (int)DB_PORT : 3306);

if ($database->connect_errno) {
    fwrite(STDERR, 'Database connection failed: ' . $database->connect_error . PHP_EOL);
    exit(1);
}

$database->set_charset('utf8mb4');

function getTableIndexes(mysqli $database, $table) {
    $indexes = array();
    $result = $database->query('SHOW INDEX FROM `' . $table . '`');

    while ($row = $result->fetch_assoc()) {
        $name = $row['Key_name'];
        $sequence = (int)$row['Seq_in_index'];
        $indexes[$name][$sequence] = $row['Column_name'];
    }

    foreach ($indexes as $name => $columns) {
        ksort($columns);
        $indexes[$name] = array_values($columns);
    }

    return $indexes;
}

function ensureIndex(mysqli $database, $table, $name, array $columns) {
    $indexes = getTableIndexes($database, $table);

    foreach ($indexes as $existingName => $existingColumns) {
        if ($existingColumns === $columns) {
            echo 'Index already present: ' . $table . '.' . $existingName . PHP_EOL;
            return;
        }
    }

    if (isset($indexes[$name])) {
        throw new RuntimeException('Index name already exists with different columns: ' . $table . '.' . $name);
    }

    $quotedColumns = array();

    foreach ($columns as $column) {
        $quotedColumns[] = '`' . $column . '`';
    }

    $database->query(
        'ALTER TABLE `' . $table . '` ADD INDEX `' . $name . '` (' . implode(', ', $quotedColumns) . ')'
    );

    echo 'Added index: ' . $table . '.' . $name . PHP_EOL;
}

$eventTable = DB_PREFIX . 'event';
$eventResult = $database->query(
    'SELECT `event_id`, `code`, `trigger`, `action`, `status`, `sort_order`'
    . ' FROM `' . $eventTable . '` ORDER BY `event_id` ASC'
);
$seenEvents = array();
$duplicateEvents = array();

while ($event = $eventResult->fetch_assoc()) {
    $signature = hash('sha256', json_encode(array(
        $event['code'],
        $event['trigger'],
        $event['action'],
        (int)$event['status'],
        (int)$event['sort_order'],
    )));

    if (isset($seenEvents[$signature])) {
        $event['kept_event_id'] = $seenEvents[$signature];
        $duplicateEvents[] = $event;
    } else {
        $seenEvents[$signature] = (int)$event['event_id'];
    }
}

if ($duplicateEvents) {
    $backupDirectory = dirname(__DIR__) . '/database/backups';

    if (!is_dir($backupDirectory) && !mkdir($backupDirectory, 0775, true) && !is_dir($backupDirectory)) {
        throw new RuntimeException('Could not create the database backup directory.');
    }

    $backupPath = $backupDirectory . '/performance-events-' . date('Ymd-His') . '.json';
    $backup = array(
        'created_at' => date(DATE_ATOM),
        'removed_exact_duplicates' => $duplicateEvents,
    );

    if (file_put_contents($backupPath, json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) === false) {
        throw new RuntimeException('Could not write the duplicate event backup.');
    }

    $duplicateIds = array();

    foreach ($duplicateEvents as $event) {
        $duplicateIds[] = (int)$event['event_id'];
    }

    $database->begin_transaction();

    try {
        $database->query(
            'DELETE FROM `' . $eventTable . '` WHERE `event_id` IN (' . implode(',', $duplicateIds) . ')'
        );
        $database->commit();
    } catch (Throwable $error) {
        $database->rollback();
        throw $error;
    }

    echo 'Removed ' . count($duplicateIds) . ' exact duplicate events.' . PHP_EOL;
    echo 'Backup: ' . $backupPath . PHP_EOL;
} else {
    echo 'No exact duplicate events found.' . PHP_EOL;
}

$indexDefinitions = array(
    array('table' => 'event', 'name' => 'idx_dryzen_status_sort', 'columns' => array('status', 'sort_order')),
    array('table' => 'extension', 'name' => 'idx_dryzen_type_code', 'columns' => array('type', 'code')),
    array('table' => 'layout_module', 'name' => 'idx_dryzen_layout_position_sort', 'columns' => array('layout_id', 'position', 'sort_order')),
    array('table' => 'product_image', 'name' => 'idx_dryzen_product_image_order', 'columns' => array('product_id', 'sort_order', 'product_image_id')),
    array('table' => 'seo_url', 'name' => 'idx_dryzen_keyword_store', 'columns' => array('keyword', 'store_id')),
    array('table' => 'setting', 'name' => 'idx_dryzen_store_code', 'columns' => array('store_id', 'code')),
);

foreach ($indexDefinitions as $definition) {
    ensureIndex(
        $database,
        DB_PREFIX . $definition['table'],
        $definition['name'],
        $definition['columns']
    );
}

echo 'Database performance optimizations are applied.' . PHP_EOL;
