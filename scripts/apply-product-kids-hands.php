<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the command line.');
}

$projectRoot = dirname(__DIR__);
$configFile = $projectRoot . '/upload/config.php';
$contentFile = $projectRoot . '/database/content/hr/product-kids-hands.php';

foreach (array($configFile, $contentFile) as $requiredFile) {
    if (!is_file($requiredFile)) {
        fwrite(STDERR, 'Missing required file: ' . $requiredFile . "\n");
        exit(1);
    }
}

require_once $configFile;
$content = require $contentFile;

$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, (int) DB_PORT);

if ($db->connect_errno) {
    fwrite(STDERR, 'Database connection failed: ' . $db->connect_error . "\n");
    exit(1);
}

$db->set_charset('utf8mb4');

function kidsHandsEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function kidsHandsFetchAll(mysqli $db, $sql)
{
    $rows = array();
    $result = $db->query($sql);

    if (!$result) {
        throw new RuntimeException($db->error);
    }

    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    return $rows;
}

function kidsHandsSaveBackup(mysqli $db, $projectRoot, $productId)
{
    $linkedTabRows = kidsHandsFetchAll(
        $db,
        'SELECT tab_id FROM ' . DB_PREFIX . 'product_tabs_to_product
         WHERE product_id = ' . (int) $productId
    );
    $tabIds = array();

    foreach ($linkedTabRows as $row) {
        $tabIds[] = (int) $row['tab_id'];
    }

    $backup = array(
        'created_at' => date(DATE_ATOM),
        'product' => kidsHandsFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'product WHERE product_id = ' . (int) $productId
        ),
        'product_description' => kidsHandsFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'product_description
             WHERE product_id = ' . (int) $productId . ' ORDER BY language_id'
        ),
        'product_image' => kidsHandsFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'product_image
             WHERE product_id = ' . (int) $productId . ' ORDER BY sort_order, product_image_id'
        ),
        'product_tabs_to_product' => $linkedTabRows,
        'product_tabs' => array(),
        'product_tabs_description' => array(),
    );

    if ($tabIds) {
        $idList = implode(',', $tabIds);
        $backup['product_tabs'] = kidsHandsFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'product_tabs
             WHERE tab_id IN (' . $idList . ') ORDER BY tab_id'
        );
        $backup['product_tabs_description'] = kidsHandsFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'product_tabs_description
             WHERE tab_id IN (' . $idList . ') ORDER BY tab_id, language_id'
        );
    }

    $backupDirectory = $projectRoot . '/database/backups';

    if (!is_dir($backupDirectory) && !mkdir($backupDirectory, 0775, true) && !is_dir($backupDirectory)) {
        throw new RuntimeException('Unable to create backup directory.');
    }

    $backupFile = $backupDirectory . '/product-kids-hands-' . date('Ymd-His') . '.json';
    $encoded = json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($encoded === false || file_put_contents($backupFile, $encoded) === false) {
        throw new RuntimeException('Unable to write product backup.');
    }

    return $backupFile;
}

function kidsHandsRenderList(array $items, $ordered)
{
    $tag = $ordered ? 'ol' : 'ul';
    $html = '<' . $tag . '>';

    foreach ($items as $item) {
        $html .= '<li>' . kidsHandsEscape($item) . '</li>';
    }

    return $html . '</' . $tag . '>';
}

function kidsHandsRenderParagraph($paragraph)
{
    $escaped = kidsHandsEscape($paragraph);
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

function kidsHandsRenderSection(array $section, $includeTitle)
{
    $html = '';

    if ($includeTitle && !empty($section['title'])) {
        $html .= '<h2>' . kidsHandsEscape($section['title']) . '</h2>';
    }

    if (!empty($section['paragraphs'])) {
        foreach ($section['paragraphs'] as $paragraph) {
            $html .= kidsHandsRenderParagraph($paragraph);
        }
    }

    if (!empty($section['ordered_list'])) {
        $html .= kidsHandsRenderList($section['ordered_list'], true);
    }

    if (!empty($section['list'])) {
        $html .= kidsHandsRenderList($section['list'], false);
    }

    if (!empty($section['subsections'])) {
        foreach ($section['subsections'] as $subsection) {
            if (!empty($subsection['title'])) {
                $html .= '<h3>' . kidsHandsEscape($subsection['title']) . '</h3>';
            }

            if (!empty($subsection['paragraphs'])) {
                foreach ($subsection['paragraphs'] as $paragraph) {
                    $html .= kidsHandsRenderParagraph($paragraph);
                }
            }

            if (!empty($subsection['list'])) {
                $html .= kidsHandsRenderList($subsection['list'], false);
            }
        }
    }

    return $html;
}

function kidsHandsBuildDescription(array $content)
{
    $html = '';

    foreach ($content['intro'] as $paragraph) {
        $html .= kidsHandsRenderParagraph($paragraph);
    }

    $html .= kidsHandsRenderSection($content['description'], true);

    return $html;
}

function kidsHandsExpectedText(array $content)
{
    $expected = array(
        $content['name'],
        $content['subtitle'],
        $content['meta_description'],
        $content['description']['title'],
    );

    foreach ($content['intro'] as $paragraph) {
        $expected[] = $paragraph;
    }

    foreach ($content['description']['paragraphs'] as $paragraph) {
        $expected[] = $paragraph;
    }

    foreach ($content['tabs'] as $tab) {
        $expected[] = $tab['name'];

        foreach (array('paragraphs', 'ordered_list', 'list') as $key) {
            foreach (isset($tab[$key]) ? $tab[$key] : array() as $text) {
                $expected[] = $text;
            }
        }

        foreach (isset($tab['subsections']) ? $tab['subsections'] : array() as $subsection) {
            $expected[] = $subsection['title'];

            foreach (array('paragraphs', 'list') as $key) {
                foreach (isset($subsection[$key]) ? $subsection[$key] : array() as $text) {
                    foreach (preg_split('/\R/u', $text) as $line) {
                        if ($line !== '') {
                            $expected[] = $line;
                        }
                    }
                }
            }
        }
    }

    return $expected;
}

function kidsHandsAuditText(array $content, $description, array $tabs)
{
    $html = $content['name'] . ' ' . $content['subtitle'] . ' ' . $content['meta_description']
        . ' ' . $description;

    foreach ($tabs as $tab) {
        $html .= ' ' . $tab['name'] . ' ' . $tab['description'];
    }

    if (stripos($html, 'style=') !== false) {
        throw new RuntimeException('Inline style audit failed.');
    }

    if (preg_match('~</?div\b~i', $html)) {
        throw new RuntimeException('Editor-safe HTML audit failed: div elements are not allowed.');
    }

    $visibleText = html_entity_decode(strip_tags(str_replace(array('<br>', '<br/>', '<br />'), "\n", $html)), ENT_QUOTES, 'UTF-8');
    $missing = array();

    foreach (kidsHandsExpectedText($content) as $expected) {
        if (strpos($visibleText, $expected) === false) {
            $missing[] = $expected;
        }
    }

    if ($missing) {
        throw new RuntimeException('Exact text audit failed: ' . implode(' | ', $missing));
    }

    return count(kidsHandsExpectedText($content));
}

function kidsHandsDeleteManagedTabs(mysqli $db, $productId, array $tabNames)
{
    if (!$tabNames) {
        return;
    }

    $quotedNames = array();

    foreach ($tabNames as $name) {
        $quotedNames[] = "'" . $db->real_escape_string($name) . "'";
    }

    $rows = kidsHandsFetchAll(
        $db,
        'SELECT DISTINCT ptd.tab_id
         FROM ' . DB_PREFIX . 'product_tabs_description ptd
         INNER JOIN ' . DB_PREFIX . 'product_tabs_to_product ptp ON ptp.tab_id = ptd.tab_id
         WHERE ptp.product_id = ' . (int) $productId . '
           AND ptd.language_id = 3
           AND ptd.name IN (' . implode(',', $quotedNames) . ')'
    );

    if (!$rows) {
        return;
    }

    $tabIds = array();

    foreach ($rows as $row) {
        $tabIds[] = (int) $row['tab_id'];
    }

    $idList = implode(',', $tabIds);
    $db->query('DELETE FROM ' . DB_PREFIX . 'product_tabs_to_product WHERE tab_id IN (' . $idList . ')');
    $db->query('DELETE FROM ' . DB_PREFIX . 'product_tabs_description WHERE tab_id IN (' . $idList . ')');
    $db->query('DELETE FROM ' . DB_PREFIX . 'product_tabs WHERE tab_id IN (' . $idList . ')');
}

try {
    $productId = (int) $content['product_id'];
    $productResult = $db->query(
        'SELECT product_id, model, price, tax_class_id, image
         FROM ' . DB_PREFIX . 'product
         WHERE product_id = ' . $productId . '
         LIMIT 1'
    );
    $product = $productResult ? $productResult->fetch_assoc() : null;

    if (!$product || $product['model'] !== $content['expected_model']) {
        throw new RuntimeException('Expected existing DryZen Kids product was not found at product ID ' . $productId . '.');
    }

    foreach (array_merge(array($content['images']['main']), $content['images']['additional']) as $imagePath) {
        if (!is_file($projectRoot . '/upload/image/' . $imagePath)) {
            throw new RuntimeException('Missing product image: ' . $imagePath);
        }
    }

    $backupFile = kidsHandsSaveBackup($db, $projectRoot, $productId);
    $description = kidsHandsBuildDescription($content);
    $renderedTabs = array();

    foreach ($content['tabs'] as $tab) {
        $renderedTabs[] = array(
            'name' => $tab['name'],
            'description' => kidsHandsRenderSection($tab, false),
        );
    }

    $textCount = kidsHandsAuditText($content, $description, $renderedTabs);

    $updateDescription = $db->prepare(
        'UPDATE ' . DB_PREFIX . 'product_description
         SET name = ?, subtitle = ?, description = ?, meta_title = ?, meta_description = ?
         WHERE product_id = ? AND language_id = 3'
    );
    $updateDescription->bind_param(
        'sssssi',
        $content['name'],
        $content['subtitle'],
        $description,
        $content['meta_title'],
        $content['meta_description'],
        $productId
    );
    $updateDescription->execute();
    $updateDescription->close();

    $updateImage = $db->prepare(
        'UPDATE ' . DB_PREFIX . 'product
         SET image = ?, date_modified = NOW()
         WHERE product_id = ?'
    );
    $updateImage->bind_param('si', $content['images']['main'], $productId);
    $updateImage->execute();
    $updateImage->close();

    $deleteImages = $db->prepare(
        'DELETE FROM ' . DB_PREFIX . 'product_image WHERE product_id = ?'
    );
    $deleteImages->bind_param('i', $productId);
    $deleteImages->execute();
    $deleteImages->close();

    $insertImage = $db->prepare(
        'INSERT INTO ' . DB_PREFIX . 'product_image (product_id, image, sort_order)
         VALUES (?, ?, ?)'
    );

    foreach ($content['images']['additional'] as $sortOrder => $imagePath) {
        $insertImage->bind_param('isi', $productId, $imagePath, $sortOrder);
        $insertImage->execute();
    }

    $insertImage->close();

    $tabNames = array();

    foreach ($renderedTabs as $tab) {
        $tabNames[] = $tab['name'];
    }

    kidsHandsDeleteManagedTabs($db, $productId, $tabNames);

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
    $tabIds = array();

    foreach ($renderedTabs as $sortOrder => $tab) {
        $order = $sortOrder + 1;
        $insertTab->bind_param('i', $order);
        $insertTab->execute();
        $tabId = (int) $insertTab->insert_id;
        $tabIds[] = $tabId;

        foreach (array(1, 3) as $languageId) {
            $insertTabDescription->bind_param(
                'iiss',
                $tabId,
                $languageId,
                $tab['name'],
                $tab['description']
            );
            $insertTabDescription->execute();
        }

        $insertTabProduct->bind_param('ii', $tabId, $productId);
        $insertTabProduct->execute();
    }

    $insertTab->close();
    $insertTabDescription->close();
    $insertTabProduct->close();

    echo "DryZen Kids hand wipes product applied successfully.\n";
    echo 'Product ID: ' . $productId . "\n";
    echo 'URL: /index.php?route=product/product&product_id=' . $productId . "\n";
    echo 'Gallery images: ' . (1 + count($content['images']['additional'])) . "\n";
    echo 'Editable product tabs: ' . count($tabIds) . "\n";
    echo 'Exact text entries audited: ' . $textCount . "\n";
    echo 'Backup: ' . $backupFile . "\n";
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->getMessage() . "\n");
    exit(1);
} finally {
    $db->close();
}
