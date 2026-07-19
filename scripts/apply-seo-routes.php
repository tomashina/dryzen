<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the command line.');
}

$projectRoot = dirname(__DIR__);
require_once $projectRoot . '/upload/config.php';
$config = require $projectRoot . '/database/content/hr/seo-routes.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, (int) DB_PORT);
$db->set_charset('utf8mb4');

$languageId = (int) $config['language_id'];
$storeId = (int) $config['store_id'];

function dryzenSeoUpsert(mysqli $db, $table, $where, $values)
{
    $conditions = array();

    foreach ($where as $column => $value) {
        $conditions[] = '`' . $column . "` = '" . $db->real_escape_string((string) $value) . "'";
    }

    $result = $db->query(
        'SELECT * FROM `' . $table . '` WHERE ' . implode(' AND ', $conditions) . ' LIMIT 1'
    );

    if ($result->num_rows) {
        $assignments = array();

        foreach ($values as $column => $value) {
            $assignments[] = '`' . $column . "` = '" . $db->real_escape_string((string) $value) . "'";
        }

        $db->query(
            'UPDATE `' . $table . '` SET ' . implode(', ', $assignments)
            . ' WHERE ' . implode(' AND ', $conditions)
        );

        return 'updated';
    }

    $insert = array_merge($where, $values);
    $columns = array();
    $escapedValues = array();

    foreach ($insert as $column => $value) {
        $columns[] = '`' . $column . '`';
        $escapedValues[] = "'" . $db->real_escape_string((string) $value) . "'";
    }

    $db->query(
        'INSERT INTO `' . $table . '` (' . implode(', ', $columns) . ') VALUES ('
        . implode(', ', $escapedValues) . ')'
    );

    return 'inserted';
}

try {
    $db->begin_transaction();

    foreach ($config['products'] as $productId => $keyword) {
        $status = dryzenSeoUpsert(
            $db,
            DB_PREFIX . 'seo_url',
            array(
                'store_id' => $storeId,
                'language_id' => $languageId,
                'query' => 'product_id=' . (int) $productId,
            ),
            array('keyword' => $keyword)
        );

        echo strtoupper($status) . ' product ' . (int) $productId . ': /' . $keyword . "\n";
    }

    foreach ($config['routes'] as $route => $keyword) {
        $status = dryzenSeoUpsert(
            $db,
            DB_PREFIX . 'hb_url',
            array(
                'route' => $route,
                'language_id' => $languageId,
                'store_id' => $storeId,
            ),
            array('keyword' => $keyword)
        );

        echo strtoupper($status) . ' route ' . $route . ': /' . $keyword . "\n";
    }

    foreach ($config['route_meta'] as $route => $meta) {
        $status = dryzenSeoUpsert(
            $db,
            DB_PREFIX . 'hb_route_meta',
            array(
                'route' => $route,
                'language_id' => $languageId,
                'store_id' => $storeId,
            ),
            $meta
        );

        echo strtoupper($status) . ' metadata ' . $route . "\n";
    }

    foreach ($config['settings'] as $key => $value) {
        $status = dryzenSeoUpsert(
            $db,
            DB_PREFIX . 'setting',
            array(
                'store_id' => $storeId,
                'code' => 'hb_snippets',
                'key' => $key,
            ),
            array(
                'value' => $value,
                'serialized' => 0,
            )
        );

        echo strtoupper($status) . ' setting ' . $key . "\n";
    }

    $db->query(
        "UPDATE `" . DB_PREFIX . "setting`
         SET `value` = REPLACE(
             `value`,
             \" style='margin-left:5px;border-bottom:1px solid rgba(35,31,32,0.18);'\",
             \" class='dryzen-promo-email'\"
         )
         WHERE `code` = 'basel' AND `key` = 'basel_promo'"
    );
    $db->query(
        "UPDATE `" . DB_PREFIX . "setting`
         SET `value` = REPLACE(
             `value`,
             '&lt;p style=&quot;margin-bottom:10px&quot;&gt;',
             '&lt;p class=&quot;dryzen-footer-support-intro&quot;&gt;'
         )
         WHERE `code` = 'basel' AND `key` = 'footer_block_2'"
    );
    echo "UPDATED public theme copy styles\n";

    $db->commit();
    echo "SEO routes applied successfully.\n";
} catch (Throwable $exception) {
    $db->rollback();
    fwrite(STDERR, 'FAIL: ' . $exception->getMessage() . "\n");
    exit(1);
} finally {
    $db->close();
}
