<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the command line.');
}

$projectRoot = dirname(__DIR__);
$configFile = $projectRoot . '/upload/config.php';
$contentFile = $projectRoot . '/database/content/hr/need-hands-page.php';
$styleFile = $projectRoot . '/database/content/hr/need-hands-page.css';
$seoRoutesFile = $projectRoot . '/database/content/hr/seo-routes.php';

foreach (array($configFile, $contentFile, $styleFile, $seoRoutesFile) as $requiredFile) {
    if (!is_file($requiredFile)) {
        fwrite(STDERR, 'Missing required file: ' . $requiredFile . "\n");
        exit(1);
    }
}

require_once $configFile;
$content = require $contentFile;
$seoRoutes = require $seoRoutesFile;
$pageCss = file_get_contents($styleFile);

if ($pageCss === false) {
    fwrite(STDERR, "Unable to read the page style file.\n");
    exit(1);
}

$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, (int) DB_PORT);

if ($db->connect_errno) {
    fwrite(STDERR, 'Database connection failed: ' . $db->connect_error . "\n");
    exit(1);
}

$db->set_charset('utf8mb4');

function handsLanguageValues($croatian, $english = '')
{
    return array(
        3 => $croatian,
        1 => $english,
    );
}

function handsEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function handsBlockSettings($fullWidth)
{
    return array(
        'title' => 0,
        'title_pl' => handsLanguageValues(''),
        'title_m' => handsLanguageValues(''),
        'title_b' => handsLanguageValues(''),
        'custom_m' => 0,
        'mt' => '',
        'mr' => '',
        'mb' => '',
        'ml' => '',
        'fw' => $fullWidth ? 1 : 0,
        'block_bg' => 0,
        'bg_color' => '',
        'block_bgi' => 0,
        'bg_par' => 0,
        'bg_pos' => 'center center',
        'bg_repeat' => 'no-repeat',
        'block_bgv' => 0,
        'bg_video' => '',
        'block_css' => 0,
        'css' => '',
    );
}

function handsHtmlColumn($html)
{
    return array(
        'w' => 'custom',
        'w_sm' => 'col-xs-12',
        'w_md' => 'col-sm-12',
        'w_lg' => 'col-md-12',
        'type' => 'html',
        'data1' => handsLanguageValues($html),
        'data2' => '',
        'data3' => handsLanguageValues(''),
        'data4' => '',
        'data5' => '',
        'data6' => '',
        'data7' => 'vertical-top text-left',
        'data8' => '',
    );
}

function handsModuleSettings($name, $html, $fullWidth)
{
    return array(
        'save' => 'stay',
        'name' => $name,
        'status' => 1,
        'b_setting' => handsBlockSettings($fullWidth),
        'bg_image' => '',
        'c_setting' => array(
            'fw' => 0,
            'block_css' => 0,
            'css' => '',
            'nm' => 0,
            'eh' => 0,
        ),
        'columns' => array(
            handsHtmlColumn($html),
        ),
    );
}

function handsFetchAll(mysqli $db, $sql)
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

function handsSaveBackup(mysqli $db, $projectRoot, $slug)
{
    $layoutIds = array();
    $layoutRows = handsFetchAll(
        $db,
        "SELECT * FROM " . DB_PREFIX . "layout
         WHERE name = 'DryZen - Dlanovi'
         ORDER BY layout_id"
    );

    foreach ($layoutRows as $row) {
        $layoutIds[] = (int) $row['layout_id'];
    }

    $informationIds = array();
    $slugStatement = $db->prepare(
        'SELECT query FROM ' . DB_PREFIX . 'seo_url
         WHERE store_id = 0 AND language_id = 3 AND keyword = ?
         ORDER BY seo_url_id'
    );
    $slugStatement->bind_param('s', $slug);
    $slugStatement->execute();
    $slugResult = $slugStatement->get_result();

    while ($row = $slugResult->fetch_assoc()) {
        if (preg_match('/^information_id=(\d+)$/', $row['query'], $matches)) {
            $informationIds[] = (int) $matches[1];
        }
    }

    $slugStatement->close();

    $backup = array(
        'created_at' => date(DATE_ATOM),
        'layouts' => $layoutRows,
        'layout_routes' => array(),
        'layout_modules' => array(),
        'modules' => handsFetchAll(
            $db,
            "SELECT * FROM " . DB_PREFIX . "module
             WHERE code = 'basel_content'
               AND name IN ('DryZen Hands Hero', 'DryZen Hands Symptoms', 'DryZen Hands Products')
             ORDER BY module_id"
        ),
        'information' => array(),
        'information_descriptions' => array(),
        'information_stores' => array(),
        'information_layouts' => array(),
        'seo_urls' => handsFetchAll(
            $db,
            "SELECT * FROM " . DB_PREFIX . "seo_url
             WHERE keyword = '" . $db->real_escape_string($slug) . "'
             ORDER BY seo_url_id"
        ),
        'settings' => handsFetchAll(
            $db,
            "SELECT * FROM " . DB_PREFIX . "setting
             WHERE store_id = 0
               AND code = 'basel'
               AND `key` = 'basel_custom_css'
             ORDER BY setting_id"
        ),
    );

    if ($layoutIds) {
        $layoutList = implode(',', array_map('intval', $layoutIds));
        $backup['layout_routes'] = handsFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'layout_route WHERE layout_id IN (' . $layoutList . ')'
        );
        $backup['layout_modules'] = handsFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'layout_module WHERE layout_id IN (' . $layoutList . ')'
        );
    }

    if ($informationIds) {
        $informationList = implode(',', array_map('intval', array_unique($informationIds)));
        $backup['information'] = handsFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'information WHERE information_id IN (' . $informationList . ')'
        );
        $backup['information_descriptions'] = handsFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'information_description WHERE information_id IN (' . $informationList . ')'
        );
        $backup['information_stores'] = handsFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'information_to_store WHERE information_id IN (' . $informationList . ')'
        );
        $backup['information_layouts'] = handsFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'information_to_layout WHERE information_id IN (' . $informationList . ')'
        );
    }

    $backupDirectory = $projectRoot . '/.local-backup';

    if (!is_dir($backupDirectory) && !mkdir($backupDirectory, 0775, true) && !is_dir($backupDirectory)) {
        throw new RuntimeException('Unable to create the backup directory.');
    }

    $backupFile = $backupDirectory . '/need-hands-before-' . date('Ymd-His') . '.json';
    $encoded = json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($encoded === false || file_put_contents($backupFile, $encoded) === false) {
        throw new RuntimeException('Unable to create the page backup.');
    }

    return $backupFile;
}

function handsUpsertLayout(mysqli $db)
{
    $name = 'DryZen - Dlanovi';
    $select = $db->prepare(
        'SELECT layout_id FROM ' . DB_PREFIX . 'layout
         WHERE name = ?
         ORDER BY layout_id
         LIMIT 1
         FOR UPDATE'
    );
    $select->bind_param('s', $name);
    $select->execute();
    $existing = $select->get_result()->fetch_assoc();
    $select->close();

    if ($existing) {
        return (int) $existing['layout_id'];
    }

    $insert = $db->prepare('INSERT INTO ' . DB_PREFIX . 'layout (name) VALUES (?)');
    $insert->bind_param('s', $name);
    $insert->execute();
    $layoutId = (int) $insert->insert_id;
    $insert->close();

    return $layoutId;
}

function handsFindInformationId(mysqli $db, $slug, $title)
{
    $select = $db->prepare(
        'SELECT query FROM ' . DB_PREFIX . 'seo_url
         WHERE store_id = 0 AND language_id = 3 AND keyword = ?
         ORDER BY seo_url_id
         LIMIT 1
         FOR UPDATE'
    );
    $select->bind_param('s', $slug);
    $select->execute();
    $row = $select->get_result()->fetch_assoc();
    $select->close();

    if ($row && preg_match('/^information_id=(\d+)$/', $row['query'], $matches)) {
        return (int) $matches[1];
    }

    $select = $db->prepare(
        'SELECT information_id FROM ' . DB_PREFIX . 'information_description
         WHERE language_id = 3 AND title = ?
         ORDER BY information_id
         LIMIT 1
         FOR UPDATE'
    );
    $select->bind_param('s', $title);
    $select->execute();
    $row = $select->get_result()->fetch_assoc();
    $select->close();

    return $row ? (int) $row['information_id'] : 0;
}

function handsUpsertInformation(mysqli $db, array $page, $layoutId)
{
    $informationId = handsFindInformationId($db, $page['slug'], $page['title']);

    if ($informationId) {
        $update = $db->prepare(
            'UPDATE ' . DB_PREFIX . 'information
             SET bottom = 0, sort_order = 0, status = 1
             WHERE information_id = ?'
        );
        $update->bind_param('i', $informationId);
        $update->execute();
        $update->close();
    } else {
        $db->query(
            'INSERT INTO ' . DB_PREFIX . 'information (bottom, sort_order, status)
             VALUES (0, 0, 1)'
        );
        $informationId = (int) $db->insert_id;
    }

    $delete = $db->prepare(
        'DELETE FROM ' . DB_PREFIX . 'information_description WHERE information_id = ?'
    );
    $delete->bind_param('i', $informationId);
    $delete->execute();
    $delete->close();

    $insertDescription = $db->prepare(
        'INSERT INTO ' . DB_PREFIX . 'information_description
         (information_id, language_id, title, description, meta_title, meta_description, meta_keyword)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $description = '';
    $metaKeyword = '';

    foreach (array(3, 1) as $languageId) {
        $insertDescription->bind_param(
            'iisssss',
            $informationId,
            $languageId,
            $page['title'],
            $description,
            $page['meta_title'],
            $page['meta_description'],
            $metaKeyword
        );
        $insertDescription->execute();
    }

    $insertDescription->close();

    $insertStore = $db->prepare(
        'INSERT IGNORE INTO ' . DB_PREFIX . 'information_to_store (information_id, store_id)
         VALUES (?, 0)'
    );
    $insertStore->bind_param('i', $informationId);
    $insertStore->execute();
    $insertStore->close();

    $deleteLayout = $db->prepare(
        'DELETE FROM ' . DB_PREFIX . 'information_to_layout
         WHERE information_id = ? AND store_id = 0'
    );
    $deleteLayout->bind_param('i', $informationId);
    $deleteLayout->execute();
    $deleteLayout->close();

    $insertLayout = $db->prepare(
        'INSERT INTO ' . DB_PREFIX . 'information_to_layout (information_id, store_id, layout_id)
         VALUES (?, 0, ?)'
    );
    $insertLayout->bind_param('ii', $informationId, $layoutId);
    $insertLayout->execute();
    $insertLayout->close();

    $query = 'information_id=' . $informationId;
    $conflict = $db->prepare(
        'SELECT seo_url_id, query FROM ' . DB_PREFIX . 'seo_url
         WHERE store_id = 0 AND language_id = 3 AND keyword = ? AND query <> ?
         LIMIT 1'
    );
    $conflict->bind_param('ss', $page['slug'], $query);
    $conflict->execute();
    $conflictRow = $conflict->get_result()->fetch_assoc();
    $conflict->close();

    if ($conflictRow) {
        throw new RuntimeException('The requested SEO slug is already used by ' . $conflictRow['query']);
    }

    $deleteSeo = $db->prepare(
        'DELETE FROM ' . DB_PREFIX . 'seo_url
         WHERE store_id = 0 AND language_id = 3 AND query = ?'
    );
    $deleteSeo->bind_param('s', $query);
    $deleteSeo->execute();
    $deleteSeo->close();

    $insertSeo = $db->prepare(
        'INSERT INTO ' . DB_PREFIX . 'seo_url (store_id, language_id, query, keyword)
         VALUES (0, 3, ?, ?)'
    );
    $insertSeo->bind_param('ss', $query, $page['slug']);
    $insertSeo->execute();
    $insertSeo->close();

    return $informationId;
}

function handsLoadProducts(mysqli $db, array $productDefinitions, array $productSeoRoutes)
{
    $products = array();
    $select = $db->prepare(
        'SELECT p.product_id, p.status, pd.name
         FROM ' . DB_PREFIX . 'product p
         INNER JOIN ' . DB_PREFIX . 'product_description pd
           ON pd.product_id = p.product_id AND pd.language_id = 3
         WHERE p.product_id = ?
         LIMIT 1'
    );

    foreach ($productDefinitions as $definition) {
        $productId = (int) $definition['product_id'];
        $select->bind_param('i', $productId);
        $select->execute();
        $product = $select->get_result()->fetch_assoc();

        if (!$product || !(int) $product['status']) {
            throw new RuntimeException('Required active product was not found: ' . $productId);
        }

        if (empty($productSeoRoutes[$productId])) {
            throw new RuntimeException('Required SEO URL was not found for product: ' . $productId);
        }

        $definition['product_name'] = $product['name'];
        $definition['href'] = '/' . ltrim($productSeoRoutes[$productId], '/');
        $products[] = $definition;
    }

    $select->close();

    return $products;
}

function handsRenderHero(array $hero)
{
    return '<section class="dryzen-hands-hero">'
        . '<div class="dryzen-hands-hero-copy">'
        . '<h1>' . handsEscape($hero['title']) . '</h1>'
        . '<p>' . handsEscape($hero['copy']) . '</p>'
        . '</div>'
        . '<figure class="dryzen-hands-hero-media">'
        . '<img src="/image/' . handsEscape($hero['image']) . '" alt="" width="1600" height="1066">'
        . '</figure>'
        . '</section>';
}

function handsRenderSymptoms(array $symptoms)
{
    $html = '<section class="dryzen-hands-symptoms">'
        . '<h2>' . handsEscape($symptoms['title']) . '</h2>'
        . '<ul class="dryzen-hands-symptom-list">';

    foreach ($symptoms['items'] as $item) {
        $html .= '<li>' . handsEscape($item) . '</li>';
    }

    return $html . '</ul></section>';
}

function handsRenderProducts($intro, $buttonText, array $products)
{
    $html = '<section id="odaberi-proizvod" class="dryzen-hands-products">'
        . '<header class="dryzen-hands-products-header">'
        . '<h2>' . handsEscape($intro) . '</h2>'
        . '</header>'
        . '<div class="dryzen-hands-product-grid">';

    foreach ($products as $product) {
        $html .= '<article class="dryzen-hands-product-card">'
            . '<a class="dryzen-hands-product-media" href="' . $product['href'] . '" aria-label="'
            . handsEscape($product['product_name']) . '">'
            . '<img src="/image/' . handsEscape($product['image']) . '" alt="'
            . handsEscape($product['product_name']) . '" width="1100" height="733" loading="lazy">'
            . '</a>'
            . '<div class="dryzen-hands-product-body">'
            . '<h3>' . handsEscape($product['label']) . '</h3>'
            . '<p>' . handsEscape($product['description']) . '</p>'
            . '<a class="dryzen-hands-product-link" href="' . $product['href'] . '">'
            . handsEscape($buttonText)
            . '</a>'
            . '</div>'
            . '</article>';
    }

    return $html . '</div></section>';
}

function handsUpsertModule(mysqli $db, $name, array $settings)
{
    $code = 'basel_content';
    $select = $db->prepare(
        'SELECT module_id FROM ' . DB_PREFIX . 'module
         WHERE code = ? AND name = ?
         ORDER BY module_id
         LIMIT 1
         FOR UPDATE'
    );
    $select->bind_param('ss', $code, $name);
    $select->execute();
    $existing = $select->get_result()->fetch_assoc();
    $select->close();

    $json = json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($json === false) {
        throw new RuntimeException('Unable to encode module settings for ' . $name);
    }

    if ($existing) {
        $moduleId = (int) $existing['module_id'];
        $update = $db->prepare(
            'UPDATE ' . DB_PREFIX . 'module SET setting = ? WHERE module_id = ?'
        );
        $update->bind_param('si', $json, $moduleId);
        $update->execute();
        $update->close();

        return $moduleId;
    }

    $insert = $db->prepare(
        'INSERT INTO ' . DB_PREFIX . 'module (name, code, setting) VALUES (?, ?, ?)'
    );
    $insert->bind_param('sss', $name, $code, $json);
    $insert->execute();
    $moduleId = (int) $insert->insert_id;
    $insert->close();

    return $moduleId;
}

function handsFindModuleId(mysqli $db, $code, $name)
{
    $select = $db->prepare(
        'SELECT module_id, setting FROM ' . DB_PREFIX . 'module
         WHERE code = ? AND name = ?
         ORDER BY module_id
         LIMIT 1'
    );
    $select->bind_param('ss', $code, $name);
    $select->execute();
    $module = $select->get_result()->fetch_assoc();
    $select->close();

    if (!$module) {
        throw new RuntimeException('Required module was not found: ' . $name);
    }

    $settings = json_decode($module['setting'], true);

    if (!is_array($settings) || empty($settings['status'])) {
        throw new RuntimeException('Required module is not active: ' . $name);
    }

    return (int) $module['module_id'];
}

function handsUpsertCss(mysqli $db, $pageCss)
{
    $startMarker = '/* DRYZEN_NEED_HANDS_UX_START */';
    $endMarker = '/* DRYZEN_NEED_HANDS_UX_END */';
    $code = 'basel';
    $key = 'basel_custom_css';
    $select = $db->prepare(
        'SELECT setting_id, value FROM ' . DB_PREFIX . 'setting
         WHERE store_id = 0 AND code = ? AND `key` = ?
         ORDER BY setting_id
         LIMIT 1
         FOR UPDATE'
    );
    $select->bind_param('ss', $code, $key);
    $select->execute();
    $existing = $select->get_result()->fetch_assoc();
    $select->close();

    $currentCss = $existing ? $existing['value'] : '';
    $pattern = '~\R?' . preg_quote($startMarker, '~') . '.*?' . preg_quote($endMarker, '~') . '\R?~s';
    $baseCss = preg_replace($pattern, "\n", $currentCss);

    if ($baseCss === null) {
        throw new RuntimeException('Unable to prepare the page CSS.');
    }

    $newCss = rtrim($baseCss)
        . "\n\n" . $startMarker . "\n"
        . trim($pageCss)
        . "\n" . $endMarker . "\n";

    if ($existing) {
        $settingId = (int) $existing['setting_id'];
        $update = $db->prepare(
            'UPDATE ' . DB_PREFIX . 'setting
             SET value = ?, serialized = 0
             WHERE setting_id = ?'
        );
        $update->bind_param('si', $newCss, $settingId);
        $update->execute();
        $update->close();

        return;
    }

    $insert = $db->prepare(
        'INSERT INTO ' . DB_PREFIX . 'setting (store_id, code, `key`, value, serialized)
         VALUES (0, ?, ?, ?, 0)'
    );
    $insert->bind_param('sss', $code, $key, $newCss);
    $insert->execute();
    $insert->close();
}

function handsAuditText(array $content, $html)
{
    $expected = array(
        $content['hero']['title'],
        $content['hero']['copy'],
        $content['symptoms']['title'],
        $content['products_intro'],
    );

    foreach ($content['symptoms']['items'] as $item) {
        $expected[] = $item;
    }

    foreach ($content['products'] as $product) {
        $expected[] = $product['label'];
        $expected[] = $product['description'];
        $expected[] = $content['button_text'];
    }

    $visibleText = html_entity_decode(strip_tags($html), ENT_QUOTES, 'UTF-8');
    $missing = array();

    foreach (array_count_values($expected) as $text => $requiredCount) {
        if (substr_count($visibleText, $text) < $requiredCount) {
            $missing[] = $text;
        }
    }

    if ($missing) {
        throw new RuntimeException('Text audit failed: ' . implode(' | ', $missing));
    }

    if (stripos($html, 'style=') !== false) {
        throw new RuntimeException('Inline style audit failed.');
    }

    return count($expected);
}

try {
    $backupFile = handsSaveBackup($db, $projectRoot, $content['page']['slug']);
    $db->begin_transaction();

    $layoutId = handsUpsertLayout($db);
    $informationId = handsUpsertInformation($db, $content['page'], $layoutId);
    $products = handsLoadProducts($db, $content['products'], $seoRoutes['products']);

    $heroHtml = handsRenderHero($content['hero']);
    $symptomsHtml = handsRenderSymptoms($content['symptoms']);
    $productsHtml = handsRenderProducts($content['products_intro'], $content['button_text'], $products);
    $textCount = handsAuditText($content, $heroHtml . $symptomsHtml . $productsHtml);

    $moduleIds = array(
        handsUpsertModule(
            $db,
            'DryZen Hands Hero',
            handsModuleSettings('DryZen Hands Hero', $heroHtml, true)
        ),
        handsUpsertModule(
            $db,
            'DryZen Hands Symptoms',
            handsModuleSettings('DryZen Hands Symptoms', $symptomsHtml, false)
        ),
        handsUpsertModule(
            $db,
            'DryZen Hands Products',
            handsModuleSettings('DryZen Hands Products', $productsHtml, true)
        ),
    );
    $newsletterModuleId = handsFindModuleId($db, 'basel_content', 'Newsletter Signup');
    $layoutModuleIds = $moduleIds;
    $layoutModuleIds[] = $newsletterModuleId;

    $deleteModules = $db->prepare(
        'DELETE FROM ' . DB_PREFIX . 'layout_module
         WHERE layout_id = ? AND position = ?'
    );
    $position = 'top';
    $deleteModules->bind_param('is', $layoutId, $position);
    $deleteModules->execute();
    $deleteModules->close();

    $insertModule = $db->prepare(
        'INSERT INTO ' . DB_PREFIX . 'layout_module (layout_id, code, position, sort_order)
         VALUES (?, ?, ?, ?)'
    );

    foreach ($layoutModuleIds as $index => $moduleId) {
        $moduleCode = 'basel_content.' . $moduleId;
        $sortOrder = $index + 1;
        $insertModule->bind_param('issi', $layoutId, $moduleCode, $position, $sortOrder);
        $insertModule->execute();
    }

    $insertModule->close();

    $resolvedCss = str_replace('__INFORMATION_ID__', (string) $informationId, $pageCss);
    handsUpsertCss($db, $resolvedCss);

    $db->commit();

    echo "Hands page applied successfully.\n";
    echo 'URL: /' . $content['page']['slug'] . "\n";
    echo 'Information ID: ' . $informationId . "\n";
    echo 'Layout ID: ' . $layoutId . "\n";
    echo 'Module IDs: ' . implode(', ', $moduleIds) . "\n";
    echo 'Newsletter module ID: ' . $newsletterModuleId . "\n";
    echo 'Exact text entries audited: ' . $textCount . "\n";
    echo 'Backup: ' . $backupFile . "\n";
} catch (Throwable $exception) {
    $db->rollback();
    fwrite(STDERR, $exception->getMessage() . "\n");
    exit(1);
} finally {
    $db->close();
}
