<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the command line.');
}

$projectRoot = dirname(__DIR__);
$configFile = $projectRoot . '/upload/config.php';
$contentFile = $projectRoot . '/database/content/hr/product-catalog.json';

foreach (array($configFile, $contentFile) as $requiredFile) {
    if (!is_file($requiredFile)) {
        fwrite(STDERR, 'Missing required file: ' . $requiredFile . "\n");
        exit(1);
    }
}

require_once $configFile;

try {
    $catalog = json_decode(file_get_contents($contentFile), true, 512, JSON_THROW_ON_ERROR);
} catch (Throwable $exception) {
    fwrite(STDERR, 'Unable to read product catalog: ' . $exception->getMessage() . "\n");
    exit(1);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, (int) DB_PORT);
$db->set_charset('utf8mb4');

function catalogFetchAll(mysqli $db, $sql)
{
    $rows = array();
    $result = $db->query($sql);

    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    return $rows;
}

function catalogEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function catalogRenderParagraph($paragraph)
{
    $escaped = catalogEscape($paragraph);
    $escaped = preg_replace(
        '~(?<![">])(dryzen@dryzen\.eu)~',
        '<a href="mailto:$1">$1</a>',
        $escaped
    );
    $escaped = preg_replace(
        '~(?<![">])(www\.dryzen\.eu)~',
        '<a href="https://$1">$1</a>',
        $escaped
    );

    return '<p>' . nl2br($escaped, false) . '</p>';
}

function catalogRenderList(array $items, $ordered)
{
    if (!$items) {
        return '';
    }

    $tag = $ordered ? 'ol' : 'ul';
    $html = '<' . $tag . '>';

    foreach ($items as $item) {
        $html .= '<li>' . catalogEscape($item) . '</li>';
    }

    return $html . '</' . $tag . '>';
}

function catalogRenderBlock(array $block)
{
    $html = '';

    foreach (isset($block['paragraphs']) ? $block['paragraphs'] : array() as $paragraph) {
        $html .= catalogRenderParagraph($paragraph);
    }

    $html .= catalogRenderList(
        isset($block['ordered_list']) ? $block['ordered_list'] : array(),
        true
    );
    $html .= catalogRenderList(
        isset($block['list']) ? $block['list'] : array(),
        false
    );

    foreach (isset($block['subsections']) ? $block['subsections'] : array() as $subsection) {
        if (!empty($subsection['title'])) {
            $html .= '<h3>' . catalogEscape($subsection['title']) . '</h3>';
        }

        $html .= catalogRenderBlock($subsection);
    }

    return $html;
}

function catalogBuildDescription(array $product)
{
    $html = '';

    foreach ($product['intro'] as $paragraph) {
        $html .= catalogRenderParagraph($paragraph);
    }

    return $html;
}

function catalogCollectBlockText(array $block)
{
    $text = array();

    foreach (array('paragraphs', 'ordered_list', 'list') as $key) {
        foreach (isset($block[$key]) ? $block[$key] : array() as $entry) {
            $text[] = $entry;
        }
    }

    foreach (isset($block['subsections']) ? $block['subsections'] : array() as $subsection) {
        if (!empty($subsection['title'])) {
            $text[] = $subsection['title'];
        }

        $text = array_merge($text, catalogCollectBlockText($subsection));
    }

    return $text;
}

function catalogExpectedText(array $product)
{
    $expected = array($product['name'], $product['subtitle']);

    foreach ($product['intro'] as $paragraph) {
        $expected[] = $paragraph;
    }

    foreach ($product['tabs'] as $tab) {
        $expected[] = $tab['name'];
        $expected = array_merge($expected, catalogCollectBlockText($tab));
    }

    return $expected;
}

function catalogNormalizeText($text)
{
    $decoded = html_entity_decode(
        strip_tags(str_replace(array('<br>', '<br/>', '<br />'), ' ', (string) $text)),
        ENT_QUOTES,
        'UTF-8'
    );

    return trim(preg_replace('/\s+/u', ' ', $decoded));
}

function catalogAuditRenderedText(array $product, $description, array $tabs)
{
    $html = $product['name'] . ' ' . $product['subtitle'] . ' ' . $description;

    foreach ($tabs as $tab) {
        $html .= ' ' . $tab['name'] . ' ' . $tab['description'];
    }

    if (stripos($html, 'style=') !== false) {
        throw new RuntimeException('Inline style audit failed for product ' . $product['product_id'] . '.');
    }

    if (preg_match('~</?div\b~i', $html)) {
        throw new RuntimeException(
            'Editor-safe HTML audit failed for product ' . $product['product_id'] . ': div elements are not allowed.'
        );
    }

    $visibleText = catalogNormalizeText($html);
    $missing = array();

    foreach (catalogExpectedText($product) as $expected) {
        if (strpos($visibleText, catalogNormalizeText($expected)) === false) {
            $missing[] = $expected;
        }
    }

    if ($missing) {
        throw new RuntimeException(
            'Exact Word text audit failed for product ' . $product['product_id'] . ': ' . implode(' | ', $missing)
        );
    }

    return count(catalogExpectedText($product));
}

function catalogSaveBackup(mysqli $db, $projectRoot, array $productIds)
{
    $idList = implode(',', array_map('intval', $productIds));
    $links = catalogFetchAll(
        $db,
        'SELECT * FROM ' . DB_PREFIX . 'product_tabs_to_product
         WHERE product_id IN (' . $idList . ')
         ORDER BY product_id, tab_id'
    );
    $tabIds = array();

    foreach ($links as $link) {
        $tabIds[] = (int) $link['tab_id'];
    }

    $tabIds = array_values(array_unique($tabIds));
    $backup = array(
        'created_at' => date(DATE_ATOM),
        'product_ids' => $productIds,
        'product' => catalogFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'product
             WHERE product_id IN (' . $idList . ')
             ORDER BY product_id'
        ),
        'product_description' => catalogFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'product_description
             WHERE product_id IN (' . $idList . ')
             ORDER BY product_id, language_id'
        ),
        'product_image' => catalogFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'product_image
             WHERE product_id IN (' . $idList . ')
             ORDER BY product_id, sort_order, product_image_id'
        ),
        'product_tabs_to_product' => $links,
        'product_tabs' => array(),
        'product_tabs_description' => array(),
        'product_tabs_to_category' => array(),
    );

    if ($tabIds) {
        $tabIdList = implode(',', $tabIds);
        $backup['product_tabs'] = catalogFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'product_tabs
             WHERE tab_id IN (' . $tabIdList . ')
             ORDER BY tab_id'
        );
        $backup['product_tabs_description'] = catalogFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'product_tabs_description
             WHERE tab_id IN (' . $tabIdList . ')
             ORDER BY tab_id, language_id'
        );
        $backup['product_tabs_to_category'] = catalogFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'product_tabs_to_category
             WHERE tab_id IN (' . $tabIdList . ')
             ORDER BY tab_id, category_id'
        );
    }

    $backupDirectory = $projectRoot . '/database/backups';

    if (!is_dir($backupDirectory) && !mkdir($backupDirectory, 0775, true) && !is_dir($backupDirectory)) {
        throw new RuntimeException('Unable to create backup directory.');
    }

    $backupFile = $backupDirectory . '/product-catalog-' . date('Ymd-His') . '.json';
    $json = json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($json === false || file_put_contents($backupFile, $json . "\n") === false) {
        throw new RuntimeException('Unable to write product catalog backup.');
    }

    return $backupFile;
}

function catalogRemoveExistingProductTabs(mysqli $db, $productId)
{
    $tabs = catalogFetchAll(
        $db,
        'SELECT pt.tab_id, pt.global
         FROM ' . DB_PREFIX . 'product_tabs pt
         INNER JOIN ' . DB_PREFIX . 'product_tabs_to_product ptp ON ptp.tab_id = pt.tab_id
         WHERE ptp.product_id = ' . (int) $productId
    );

    $db->query(
        'DELETE FROM ' . DB_PREFIX . 'product_tabs_to_product
         WHERE product_id = ' . (int) $productId
    );

    foreach ($tabs as $tab) {
        $tabId = (int) $tab['tab_id'];

        if ((int) $tab['global'] !== 0) {
            continue;
        }

        $productLinks = catalogFetchAll(
            $db,
            'SELECT tab_id FROM ' . DB_PREFIX . 'product_tabs_to_product
             WHERE tab_id = ' . $tabId . ' LIMIT 1'
        );
        $categoryLinks = catalogFetchAll(
            $db,
            'SELECT tab_id FROM ' . DB_PREFIX . 'product_tabs_to_category
             WHERE tab_id = ' . $tabId . ' LIMIT 1'
        );

        if (!$productLinks && !$categoryLinks) {
            $db->query(
                'DELETE FROM ' . DB_PREFIX . 'product_tabs_description
                 WHERE tab_id = ' . $tabId
            );
            $db->query(
                'DELETE FROM ' . DB_PREFIX . 'product_tabs
                 WHERE tab_id = ' . $tabId
            );
        }
    }
}

function catalogValidateDatabaseState(mysqli $db, array $prepared)
{
    foreach ($prepared as $productId => $entry) {
        $product = $entry['product'];
        $descriptionRows = catalogFetchAll(
            $db,
            'SELECT name, subtitle, description, meta_title, meta_description, image_alt, image_title
             FROM ' . DB_PREFIX . 'product_description
             WHERE product_id = ' . (int) $productId . ' AND language_id = 3'
        );

        if (count($descriptionRows) !== 1) {
            throw new RuntimeException('Croatian description row audit failed for product ' . $productId . '.');
        }

        $descriptionRow = $descriptionRows[0];
        $expectedDescription = $entry['description'];

        foreach (array(
            'name' => $product['name'],
            'subtitle' => $product['subtitle'],
            'description' => $expectedDescription,
            'meta_title' => $product['meta_title'],
            'meta_description' => $product['meta_description'],
            'image_alt' => $product['name'],
            'image_title' => $product['name'],
        ) as $key => $expectedValue) {
            if ($descriptionRow[$key] !== $expectedValue) {
                throw new RuntimeException('Database text audit failed for product ' . $productId . ': ' . $key);
            }
        }

        $imageRows = catalogFetchAll(
            $db,
            'SELECT image FROM ' . DB_PREFIX . 'product_image
             WHERE product_id = ' . (int) $productId . '
             ORDER BY sort_order, product_image_id'
        );
        $savedAdditionalImages = array_column($imageRows, 'image');

        if ($savedAdditionalImages !== $product['images']['additional']) {
            throw new RuntimeException('Additional image audit failed for product ' . $productId . '.');
        }

        $tabRows = catalogFetchAll(
            $db,
            'SELECT ptd.name, ptd.description
             FROM ' . DB_PREFIX . 'product_tabs_to_product ptp
             INNER JOIN ' . DB_PREFIX . 'product_tabs pt ON pt.tab_id = ptp.tab_id
             INNER JOIN ' . DB_PREFIX . 'product_tabs_description ptd ON ptd.tab_id = pt.tab_id
             WHERE ptp.product_id = ' . (int) $productId . '
               AND ptd.language_id = 3
             ORDER BY pt.sort_order, pt.tab_id'
        );

        if ($tabRows !== $entry['tabs']) {
            throw new RuntimeException('Editable tab audit failed for product ' . $productId . '.');
        }

        catalogAuditRenderedText($product, $descriptionRow['description'], $tabRows);
    }
}

$transactionStarted = false;

try {
    if (!is_array($catalog) || count($catalog) !== 12) {
        throw new RuntimeException('Expected exactly 12 remaining products in the generated catalog.');
    }

    $productIds = array();
    $prepared = array();
    $textCount = 0;
    $imageCount = 0;

    foreach ($catalog as $product) {
        $productId = (int) $product['product_id'];

        if (isset($prepared[$productId])) {
            throw new RuntimeException('Duplicate product ID in catalog: ' . $productId);
        }

        if (count($product['tabs']) !== 7) {
            throw new RuntimeException('Expected seven editable tabs for product ' . $productId . '.');
        }

        if (mb_strlen($product['meta_description'], 'UTF-8') > 255) {
            throw new RuntimeException('Meta description is too long for product ' . $productId . '.');
        }

        $databaseProducts = catalogFetchAll(
            $db,
            'SELECT product_id, model
             FROM ' . DB_PREFIX . 'product
             WHERE product_id = ' . $productId . '
             LIMIT 1'
        );

        if (
            count($databaseProducts) !== 1
            || $databaseProducts[0]['model'] !== $product['expected_model']
        ) {
            throw new RuntimeException('Expected existing product/model was not found at product ID ' . $productId . '.');
        }

        foreach (array_merge(array($product['images']['main']), $product['images']['additional']) as $imagePath) {
            $absoluteImagePath = $projectRoot . '/upload/image/' . $imagePath;

            if (!is_file($absoluteImagePath)) {
                throw new RuntimeException('Missing generated product image: ' . $imagePath);
            }

            $dimensions = getimagesize($absoluteImagePath);

            if (!$dimensions || $dimensions[0] < 1000 || $dimensions[1] < 700) {
                throw new RuntimeException('Product image quality audit failed: ' . $imagePath);
            }

            $imageCount++;
        }

        $description = catalogBuildDescription($product);
        $tabs = array();

        foreach ($product['tabs'] as $tab) {
            $tabs[] = array(
                'name' => $tab['name'],
                'description' => catalogRenderBlock($tab),
            );
        }

        $textCount += catalogAuditRenderedText($product, $description, $tabs);
        $prepared[$productId] = array(
            'product' => $product,
            'description' => $description,
            'tabs' => $tabs,
        );
        $productIds[] = $productId;
    }

    $backupFile = catalogSaveBackup($db, $projectRoot, $productIds);
    $db->begin_transaction();
    $transactionStarted = true;

    $updateDescription = $db->prepare(
        'UPDATE ' . DB_PREFIX . 'product_description
         SET name = ?, subtitle = ?, description = ?, meta_title = ?, meta_description = ?,
             image_alt = ?, image_title = ?
         WHERE product_id = ? AND language_id = 3'
    );
    $updateProductImage = $db->prepare(
        'UPDATE ' . DB_PREFIX . 'product
         SET image = ?, date_modified = NOW()
         WHERE product_id = ?'
    );
    $deleteProductImages = $db->prepare(
        'DELETE FROM ' . DB_PREFIX . 'product_image WHERE product_id = ?'
    );
    $insertProductImage = $db->prepare(
        'INSERT INTO ' . DB_PREFIX . 'product_image (product_id, image, sort_order)
         VALUES (?, ?, ?)'
    );
    $insertTab = $db->prepare(
        'INSERT INTO ' . DB_PREFIX . 'product_tabs (sort_order, status, global)
         VALUES (?, 1, 0)'
    );
    $insertTabDescription = $db->prepare(
        'INSERT INTO ' . DB_PREFIX . 'product_tabs_description (tab_id, language_id, name, description)
         VALUES (?, ?, ?, ?)'
    );
    $insertTabProduct = $db->prepare(
        'INSERT INTO ' . DB_PREFIX . 'product_tabs_to_product (tab_id, product_id)
         VALUES (?, ?)'
    );

    foreach ($prepared as $productId => $entry) {
        $product = $entry['product'];
        $name = $product['name'];
        $subtitle = $product['subtitle'];
        $description = $entry['description'];
        $metaTitle = $product['meta_title'];
        $metaDescription = $product['meta_description'];
        $imageAlt = $product['name'];
        $imageTitle = $product['name'];
        $updateDescription->bind_param(
            'sssssssi',
            $name,
            $subtitle,
            $description,
            $metaTitle,
            $metaDescription,
            $imageAlt,
            $imageTitle,
            $productId
        );
        $updateDescription->execute();

        if ($updateDescription->affected_rows === 0) {
            $exists = catalogFetchAll(
                $db,
                'SELECT product_id FROM ' . DB_PREFIX . 'product_description
                 WHERE product_id = ' . (int) $productId . ' AND language_id = 3'
            );

            if (!$exists) {
                throw new RuntimeException('Missing Croatian description row for product ' . $productId . '.');
            }
        }

        $mainImage = $product['images']['main'];
        $updateProductImage->bind_param('si', $mainImage, $productId);
        $updateProductImage->execute();

        $deleteProductImages->bind_param('i', $productId);
        $deleteProductImages->execute();

        foreach ($product['images']['additional'] as $index => $additionalImage) {
            $sortOrder = $index + 1;
            $insertProductImage->bind_param('isi', $productId, $additionalImage, $sortOrder);
            $insertProductImage->execute();
        }

        catalogRemoveExistingProductTabs($db, $productId);

        foreach ($entry['tabs'] as $index => $tab) {
            $sortOrder = $index + 1;
            $insertTab->bind_param('i', $sortOrder);
            $insertTab->execute();
            $tabId = (int) $insertTab->insert_id;
            $tabName = $tab['name'];
            $tabDescription = $tab['description'];

            foreach (array(1, 3) as $languageId) {
                $insertTabDescription->bind_param(
                    'iiss',
                    $tabId,
                    $languageId,
                    $tabName,
                    $tabDescription
                );
                $insertTabDescription->execute();
            }

            $insertTabProduct->bind_param('ii', $tabId, $productId);
            $insertTabProduct->execute();
        }
    }

    $updateDescription->close();
    $updateProductImage->close();
    $deleteProductImages->close();
    $insertProductImage->close();
    $insertTab->close();
    $insertTabDescription->close();
    $insertTabProduct->close();

    catalogValidateDatabaseState($db, $prepared);
    $db->commit();
    $transactionStarted = false;

    echo "DryZen product catalog applied successfully.\n";
    echo 'Existing products updated: ' . count($prepared) . "\n";
    echo 'Editable tabs created: ' . (count($prepared) * 7) . "\n";
    echo 'High-resolution gallery images assigned: ' . $imageCount . "\n";
    echo 'Exact Word text entries audited: ' . $textCount . "\n";
    echo 'Backup: ' . $backupFile . "\n";
} catch (Throwable $exception) {
    if ($transactionStarted) {
        $db->rollback();
    }

    fwrite(STDERR, $exception->getMessage() . "\n");
    exit(1);
} finally {
    $db->close();
}
