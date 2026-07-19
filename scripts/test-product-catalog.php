<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the command line.');
}

$projectRoot = dirname(__DIR__);
require_once $projectRoot . '/upload/config.php';

$catalog = json_decode(
    file_get_contents($projectRoot . '/database/content/hr/product-catalog.json'),
    true,
    512,
    JSON_THROW_ON_ERROR
);
$kidsHands = require $projectRoot . '/database/content/hr/product-kids-hands.php';
$expected = array();

foreach ($catalog as $product) {
    $expected[(int) $product['product_id']] = $product;
}

$expected[(int) $kidsHands['product_id']] = $kidsHands;
ksort($expected);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, (int) DB_PORT);
$db->set_charset('utf8mb4');

function catalogTestRows(mysqli $db, $sql)
{
    $rows = array();
    $result = $db->query($sql);

    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    return $rows;
}

function catalogTestAssert($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

try {
    catalogTestAssert(count($expected) === 13, 'Expected 13 DryZen products in the test inventory.');
    $totalImages = 0;
    $totalTabs = 0;

    foreach ($expected as $productId => $product) {
        $rows = catalogTestRows(
            $db,
            'SELECT p.model, p.image, p.status, pd.name, pd.subtitle, pd.description,
                    pd.meta_title, pd.meta_description, pd.image_alt, pd.image_title
             FROM ' . DB_PREFIX . 'product p
             INNER JOIN ' . DB_PREFIX . 'product_description pd ON pd.product_id = p.product_id
             WHERE p.product_id = ' . (int) $productId . ' AND pd.language_id = 3'
        );
        catalogTestAssert(count($rows) === 1, 'Missing Croatian product row: ' . $productId);
        $row = $rows[0];
        catalogTestAssert($row['model'] === $product['expected_model'], 'Model mismatch: ' . $productId);
        catalogTestAssert((int) $row['status'] === 1, 'Inactive product: ' . $productId);
        catalogTestAssert($row['name'] === $product['name'], 'Name mismatch: ' . $productId);
        catalogTestAssert($row['subtitle'] === $product['subtitle'], 'Subtitle mismatch: ' . $productId);
        catalogTestAssert($row['image'] === $product['images']['main'], 'Main image mismatch: ' . $productId);
        catalogTestAssert($row['meta_title'] !== '', 'Missing meta title: ' . $productId);
        catalogTestAssert($row['meta_description'] !== '', 'Missing meta description: ' . $productId);
        catalogTestAssert(
            mb_strlen($row['meta_description'], 'UTF-8') <= 255,
            'Meta description is too long: ' . $productId
        );
        catalogTestAssert(
            stripos($row['description'], 'style=') === false
                && !preg_match('~</?div\b~i', $row['description']),
            'Editor-unsafe product description: ' . $productId
        );

        $imagePaths = array_merge(array($row['image']), $product['images']['additional']);
        $additionalRows = catalogTestRows(
            $db,
            'SELECT image FROM ' . DB_PREFIX . 'product_image
             WHERE product_id = ' . (int) $productId . '
             ORDER BY sort_order, product_image_id'
        );
        catalogTestAssert(
            array_column($additionalRows, 'image') === $product['images']['additional'],
            'Additional image order mismatch: ' . $productId
        );
        catalogTestAssert(count($imagePaths) >= 4, 'Product has fewer than four gallery images: ' . $productId);

        foreach ($imagePaths as $imagePath) {
            $absolutePath = $projectRoot . '/upload/image/' . $imagePath;
            catalogTestAssert(
                strtolower(pathinfo($imagePath, PATHINFO_EXTENSION)) === 'webp',
                'Product image is not WebP: ' . $imagePath
            );
            catalogTestAssert(is_file($absolutePath), 'Missing image file: ' . $imagePath);
            $size = getimagesize($absolutePath);
            catalogTestAssert(
                $size && $size[2] === IMAGETYPE_WEBP && $size[0] >= 1000 && $size[1] >= 700,
                'Insufficient image resolution: ' . $imagePath
            );
        }

        $tabRows = catalogTestRows(
            $db,
            'SELECT ptd.name, ptd.description
             FROM ' . DB_PREFIX . 'product_tabs_to_product ptp
             INNER JOIN ' . DB_PREFIX . 'product_tabs pt ON pt.tab_id = ptp.tab_id
             INNER JOIN ' . DB_PREFIX . 'product_tabs_description ptd ON ptd.tab_id = pt.tab_id
             WHERE ptp.product_id = ' . (int) $productId . '
               AND ptd.language_id = 3
               AND pt.status = 1
             ORDER BY pt.sort_order, pt.tab_id'
        );
        catalogTestAssert(count($tabRows) === 7, 'Product does not have seven editable tabs: ' . $productId);

        foreach ($tabRows as $tab) {
            catalogTestAssert($tab['name'] !== '', 'Empty tab name: ' . $productId);
            catalogTestAssert(
                stripos($tab['description'], 'style=') === false
                    && !preg_match('~</?div\b~i', $tab['description']),
                'Editor-unsafe tab HTML: ' . $productId . ' / ' . $tab['name']
            );
        }

        $totalImages += count($imagePaths);
        $totalTabs += count($tabRows);
        echo 'PASS product ' . $productId . ': '
            . count($imagePaths) . ' images, ' . count($tabRows) . " editable tabs\n";
    }

    echo "PASS catalog database audit\n";
    echo 'Products: ' . count($expected) . "\n";
    echo 'Gallery images: ' . $totalImages . "\n";
    echo 'Editable tabs: ' . $totalTabs . "\n";
} catch (Throwable $exception) {
    fwrite(STDERR, 'FAIL: ' . $exception->getMessage() . "\n");
    exit(1);
} finally {
    $db->close();
}
