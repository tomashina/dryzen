<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the command line.');
}

$projectRoot = dirname(__DIR__);
$configFile = $projectRoot . '/upload/config.php';
$contentFile = $projectRoot . '/database/content/hr/need-pages-3-5.php';
$styleFile = $projectRoot . '/database/content/hr/need-pages-3-5.css';

foreach (array($configFile, $contentFile, $styleFile) as $requiredFile) {
    if (!is_file($requiredFile)) {
        fwrite(STDERR, 'Missing required file: ' . $requiredFile . "\n");
        exit(1);
    }
}

require_once $configFile;
$pages = require $contentFile;
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

function needPagesLanguageValues($croatian, $english = '')
{
    return array(
        3 => $croatian,
        1 => $english,
    );
}

function needPagesEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function needPagesBlockSettings($fullWidth)
{
    return array(
        'title' => 0,
        'title_pl' => needPagesLanguageValues(''),
        'title_m' => needPagesLanguageValues(''),
        'title_b' => needPagesLanguageValues(''),
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

function needPagesHtmlColumn($html)
{
    return array(
        'w' => 'custom',
        'w_sm' => 'col-xs-12',
        'w_md' => 'col-sm-12',
        'w_lg' => 'col-md-12',
        'type' => 'html',
        'data1' => needPagesLanguageValues($html),
        'data2' => '',
        'data3' => needPagesLanguageValues(''),
        'data4' => '',
        'data5' => '',
        'data6' => '',
        'data7' => 'vertical-top text-left',
        'data8' => '',
    );
}

function needPagesModuleSettings($name, $html, $fullWidth)
{
    return array(
        'save' => 'stay',
        'name' => $name,
        'status' => 1,
        'b_setting' => needPagesBlockSettings($fullWidth),
        'bg_image' => '',
        'c_setting' => array(
            'fw' => 0,
            'block_css' => 0,
            'css' => '',
            'nm' => 0,
            'eh' => 0,
        ),
        'columns' => array(
            needPagesHtmlColumn($html),
        ),
    );
}

function needPagesFetchAll(mysqli $db, $sql)
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

function needPagesQuotedList(mysqli $db, array $values)
{
    $quoted = array();

    foreach ($values as $value) {
        $quoted[] = "'" . $db->real_escape_string($value) . "'";
    }

    return implode(',', $quoted);
}

function needPagesSaveBackup(mysqli $db, $projectRoot, array $pages)
{
    $layoutNames = array();
    $moduleNames = array();
    $slugs = array();
    $informationIds = array();

    foreach ($pages as $page) {
        $layoutNames[] = $page['page']['layout_name'];
        $slugs[] = $page['page']['slug'];

        foreach (array(' Hero', ' Symptoms', ' Products') as $suffix) {
            $moduleNames[] = $page['page']['module_prefix'] . $suffix;
        }
    }

    $layoutRows = needPagesFetchAll(
        $db,
        'SELECT * FROM ' . DB_PREFIX . 'layout
         WHERE name IN (' . needPagesQuotedList($db, $layoutNames) . ')
         ORDER BY layout_id'
    );
    $layoutIds = array();

    foreach ($layoutRows as $row) {
        $layoutIds[] = (int) $row['layout_id'];
    }

    $seoRows = needPagesFetchAll(
        $db,
        'SELECT * FROM ' . DB_PREFIX . 'seo_url
         WHERE store_id = 0
           AND language_id = 3
           AND keyword IN (' . needPagesQuotedList($db, $slugs) . ')
         ORDER BY seo_url_id'
    );

    foreach ($seoRows as $row) {
        if (preg_match('/^information_id=(\d+)$/', $row['query'], $matches)) {
            $informationIds[] = (int) $matches[1];
        }
    }

    $backup = array(
        'created_at' => date(DATE_ATOM),
        'layouts' => $layoutRows,
        'layout_modules' => array(),
        'modules' => needPagesFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'module
             WHERE code = \'basel_content\'
               AND name IN (' . needPagesQuotedList($db, $moduleNames) . ')
             ORDER BY module_id'
        ),
        'information' => array(),
        'information_descriptions' => array(),
        'information_stores' => array(),
        'information_layouts' => array(),
        'seo_urls' => $seoRows,
        'settings' => needPagesFetchAll(
            $db,
            "SELECT * FROM " . DB_PREFIX . "setting
             WHERE store_id = 0
               AND code = 'basel'
               AND `key` = 'basel_custom_css'
             ORDER BY setting_id"
        ),
    );

    if ($layoutIds) {
        $backup['layout_modules'] = needPagesFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'layout_module
             WHERE layout_id IN (' . implode(',', array_map('intval', $layoutIds)) . ')
             ORDER BY layout_id, position, sort_order'
        );
    }

    if ($informationIds) {
        $informationList = implode(',', array_map('intval', array_unique($informationIds)));
        $backup['information'] = needPagesFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'information WHERE information_id IN (' . $informationList . ')'
        );
        $backup['information_descriptions'] = needPagesFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'information_description WHERE information_id IN (' . $informationList . ')'
        );
        $backup['information_stores'] = needPagesFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'information_to_store WHERE information_id IN (' . $informationList . ')'
        );
        $backup['information_layouts'] = needPagesFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'information_to_layout WHERE information_id IN (' . $informationList . ')'
        );
    }

    $backupDirectory = $projectRoot . '/.local-backup';

    if (!is_dir($backupDirectory) && !mkdir($backupDirectory, 0775, true) && !is_dir($backupDirectory)) {
        throw new RuntimeException('Unable to create the backup directory.');
    }

    $backupFile = $backupDirectory . '/need-pages-3-5-before-' . date('Ymd-His') . '.json';
    $encoded = json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($encoded === false || file_put_contents($backupFile, $encoded) === false) {
        throw new RuntimeException('Unable to create the page backup.');
    }

    return $backupFile;
}

function needPagesUpsertLayout(mysqli $db, $name)
{
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

function needPagesFindInformationId(mysqli $db, $slug, $title)
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

function needPagesUpsertInformation(mysqli $db, array $page, $layoutId)
{
    $informationId = needPagesFindInformationId($db, $page['slug'], $page['title']);

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

    $deleteDescription = $db->prepare(
        'DELETE FROM ' . DB_PREFIX . 'information_description WHERE information_id = ?'
    );
    $deleteDescription->bind_param('i', $informationId);
    $deleteDescription->execute();
    $deleteDescription->close();

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

function needPagesLoadProduct(mysqli $db, array $definition)
{
    $productId = (int) $definition['product_id'];
    $select = $db->prepare(
        'SELECT p.product_id, p.status, pd.name
         FROM ' . DB_PREFIX . 'product p
         INNER JOIN ' . DB_PREFIX . 'product_description pd
           ON pd.product_id = p.product_id AND pd.language_id = 3
         WHERE p.product_id = ?
         LIMIT 1'
    );
    $select->bind_param('i', $productId);
    $select->execute();
    $product = $select->get_result()->fetch_assoc();
    $select->close();

    if (!$product || !(int) $product['status']) {
        throw new RuntimeException('Required active product was not found: ' . $productId);
    }

    $definition['product_name'] = $product['name'];
    $definition['href'] = 'index.php?route=product/product&amp;product_id=' . $productId;

    return $definition;
}

function needPagesLoadProducts(mysqli $db, array $definitions)
{
    $products = array();

    foreach ($definitions as $definition) {
        $products[] = needPagesLoadProduct($db, $definition);
    }

    return $products;
}

function needPagesRenderHero(array $hero)
{
    return '<section class="dryzen-need-hero dryzen-need-hero--' . needPagesEscape($hero['modifier']) . '">'
        . '<div class="dryzen-need-hero-copy">'
        . '<h1>' . needPagesEscape($hero['title']) . '</h1>'
        . '<p>' . needPagesEscape($hero['copy']) . '</p>'
        . '</div>'
        . '<figure class="dryzen-need-hero-media">'
        . '<img src="/image/' . needPagesEscape($hero['image']) . '" alt="" width="1600" height="1066">'
        . '</figure>'
        . '</section>';
}

function needPagesRenderSymptoms(array $symptoms)
{
    $html = '<section class="dryzen-need-symptoms">'
        . '<h2>' . needPagesEscape($symptoms['title']) . '</h2>'
        . '<ul class="dryzen-need-symptom-list">';

    foreach ($symptoms['items'] as $item) {
        $html .= '<li>' . needPagesEscape($item) . '</li>';
    }

    return $html . '</ul></section>';
}

function needPagesRenderProductGrid($intro, $buttonText, array $products)
{
    $html = '<section class="dryzen-need-products">'
        . '<header class="dryzen-need-products-header">'
        . '<h2>' . needPagesEscape($intro) . '</h2>'
        . '</header>'
        . '<div class="dryzen-need-product-grid">';

    foreach ($products as $product) {
        $html .= '<article class="dryzen-need-product-card">'
            . '<a class="dryzen-need-product-media" href="' . $product['href'] . '" aria-label="'
            . needPagesEscape($product['product_name']) . '">'
            . '<img src="/image/' . needPagesEscape($product['image']) . '" alt="'
            . needPagesEscape($product['product_name']) . '" width="1100" height="733" loading="lazy">'
            . '</a>'
            . '<div class="dryzen-need-product-body">'
            . '<h3>' . needPagesEscape($product['label']) . '</h3>'
            . '<p>' . needPagesEscape($product['description']) . '</p>'
            . '<a class="dryzen-need-product-link" href="' . $product['href'] . '">'
            . needPagesEscape($buttonText)
            . '</a>'
            . '</div>'
            . '</article>';
    }

    return $html . '</div></section>';
}

function needPagesRenderSingleProduct($intro, $buttonText, array $product)
{
    $html = '<section class="dryzen-need-products dryzen-need-products--single">'
        . '<header class="dryzen-need-products-header">'
        . '<h2>' . needPagesEscape($intro) . '</h2>'
        . '</header>'
        . '<article class="dryzen-need-single-product">'
        . '<a class="dryzen-need-product-media" href="' . $product['href'] . '" aria-label="'
        . needPagesEscape($product['product_name']) . '">'
        . '<img src="/image/' . needPagesEscape($product['image']) . '" alt="'
        . needPagesEscape($product['product_name']) . '" width="1100" height="733" loading="lazy">'
        . '</a>'
        . '<div class="dryzen-need-single-copy">'
        . '<h3>' . needPagesEscape($product['label']) . '</h3>'
        . '<p>' . needPagesEscape($product['description']) . '</p>'
        . '<p class="dryzen-need-feature-title">' . needPagesEscape($product['features_title']) . '</p>'
        . '<ul class="dryzen-need-feature-list">';

    foreach ($product['features'] as $feature) {
        $html .= '<li>' . needPagesEscape($feature) . '</li>';
    }

    return $html
        . '</ul>'
        . '<a class="dryzen-need-product-link" href="' . $product['href'] . '">'
        . needPagesEscape($buttonText)
        . '</a>'
        . '</div>'
        . '</article>'
        . '</section>';
}

function needPagesUpsertModule(mysqli $db, $name, array $settings)
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

function needPagesFindModuleId(mysqli $db, $code, $name)
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

function needPagesAssignModules(mysqli $db, $layoutId, array $moduleIds)
{
    $position = 'top';
    $delete = $db->prepare(
        'DELETE FROM ' . DB_PREFIX . 'layout_module
         WHERE layout_id = ? AND position = ?'
    );
    $delete->bind_param('is', $layoutId, $position);
    $delete->execute();
    $delete->close();

    $insert = $db->prepare(
        'INSERT INTO ' . DB_PREFIX . 'layout_module (layout_id, code, position, sort_order)
         VALUES (?, ?, ?, ?)'
    );

    foreach ($moduleIds as $index => $moduleId) {
        $moduleCode = 'basel_content.' . $moduleId;
        $sortOrder = $index + 1;
        $insert->bind_param('issi', $layoutId, $moduleCode, $position, $sortOrder);
        $insert->execute();
    }

    $insert->close();
}

function needPagesUpsertCss(mysqli $db, $pageCss)
{
    $startMarker = '/* DRYZEN_NEED_PAGES_3_5_UX_START */';
    $endMarker = '/* DRYZEN_NEED_PAGES_3_5_UX_END */';
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

function needPagesAuditText(array $page, $html)
{
    $expected = array(
        $page['hero']['title'],
        $page['hero']['copy'],
        $page['symptoms']['title'],
        $page['products_intro'],
    );

    foreach ($page['symptoms']['items'] as $item) {
        $expected[] = $item;
    }

    if (isset($page['products'])) {
        foreach ($page['products'] as $product) {
            $expected[] = $product['label'];
            $expected[] = $product['description'];
            $expected[] = $page['button_text'];
        }
    } else {
        $expected[] = $page['single_product']['label'];
        $expected[] = $page['single_product']['description'];
        $expected[] = $page['single_product']['features_title'];

        foreach ($page['single_product']['features'] as $feature) {
            $expected[] = $feature;
        }

        $expected[] = $page['button_text'];
    }

    $visibleText = html_entity_decode(strip_tags($html), ENT_QUOTES, 'UTF-8');
    $missing = array();

    foreach (array_count_values($expected) as $text => $requiredCount) {
        if (substr_count($visibleText, $text) < $requiredCount) {
            $missing[] = $text;
        }
    }

    if ($missing) {
        throw new RuntimeException(
            'Text audit failed for ' . $page['page']['slug'] . ': ' . implode(' | ', $missing)
        );
    }

    if (stripos($html, 'style=') !== false) {
        throw new RuntimeException('Inline style audit failed for ' . $page['page']['slug']);
    }

    return count($expected);
}

try {
    $backupFile = needPagesSaveBackup($db, $projectRoot, $pages);
    $db->begin_transaction();
    $newsletterModuleId = needPagesFindModuleId($db, 'basel_content', 'Newsletter Signup');
    $results = array();
    $informationIds = array();

    foreach ($pages as $pageKey => $page) {
        $layoutId = needPagesUpsertLayout($db, $page['page']['layout_name']);
        $informationId = needPagesUpsertInformation($db, $page['page'], $layoutId);
        $informationIds[] = $informationId;

        $heroHtml = needPagesRenderHero($page['hero']);
        $symptomsHtml = needPagesRenderSymptoms($page['symptoms']);

        if (isset($page['products'])) {
            $loadedProducts = needPagesLoadProducts($db, $page['products']);
            $productsHtml = needPagesRenderProductGrid(
                $page['products_intro'],
                $page['button_text'],
                $loadedProducts
            );
        } else {
            $loadedProduct = needPagesLoadProduct($db, $page['single_product']);
            $productsHtml = needPagesRenderSingleProduct(
                $page['products_intro'],
                $page['button_text'],
                $loadedProduct
            );
        }

        $textCount = needPagesAuditText($page, $heroHtml . $symptomsHtml . $productsHtml);
        $modulePrefix = $page['page']['module_prefix'];
        $moduleIds = array(
            needPagesUpsertModule(
                $db,
                $modulePrefix . ' Hero',
                needPagesModuleSettings($modulePrefix . ' Hero', $heroHtml, true)
            ),
            needPagesUpsertModule(
                $db,
                $modulePrefix . ' Symptoms',
                needPagesModuleSettings($modulePrefix . ' Symptoms', $symptomsHtml, false)
            ),
            needPagesUpsertModule(
                $db,
                $modulePrefix . ' Products',
                needPagesModuleSettings($modulePrefix . ' Products', $productsHtml, true)
            ),
            $newsletterModuleId,
        );

        needPagesAssignModules($db, $layoutId, $moduleIds);
        $results[$pageKey] = array(
            'url' => '/' . $page['page']['slug'],
            'information_id' => $informationId,
            'layout_id' => $layoutId,
            'module_ids' => $moduleIds,
            'text_entries' => $textCount,
        );
    }

    $bodySelectors = array();

    foreach ($informationIds as $informationId) {
        $bodySelectors[] = 'body.information-information-' . (int) $informationId;
    }

    $pageScope = ':is(' . implode(', ', $bodySelectors) . ')';
    $resolvedCss = str_replace('__PAGE_SCOPE__', $pageScope, $pageCss);
    needPagesUpsertCss($db, $resolvedCss);

    $db->commit();

    echo "Need pages 3-5 applied successfully.\n";
    echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
    echo 'Backup: ' . $backupFile . "\n";
} catch (Throwable $exception) {
    $db->rollback();
    fwrite(STDERR, $exception->getMessage() . "\n");
    exit(1);
} finally {
    $db->close();
}
