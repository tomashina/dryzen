<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the command line.');
}

$projectRoot = dirname(__DIR__);
$requiredFiles = array(
    'config' => $projectRoot . '/upload/config.php',
    'landing' => $projectRoot . '/database/content/en/landing-page.php',
    'needs' => $projectRoot . '/database/content/en/need-pages.php',
    'about' => $projectRoot . '/database/content/en/about-page.php',
    'legal' => $projectRoot . '/database/content/en/legal-pages.php',
    'products' => $projectRoot . '/database/content/en/product-catalog.json',
    'seo' => $projectRoot . '/database/content/en/seo-routes.php',
);

foreach ($requiredFiles as $requiredFile) {
    if (!is_file($requiredFile)) {
        fwrite(STDERR, 'Missing required file: ' . $requiredFile . "\n");
        exit(1);
    }
}

require_once $requiredFiles['config'];
$landing = require $requiredFiles['landing'];
$needPages = require $requiredFiles['needs'];
$about = require $requiredFiles['about'];
$legalPages = require $requiredFiles['legal'];
$seoRoutes = require $requiredFiles['seo'];

try {
    $products = json_decode(
        file_get_contents($requiredFiles['products']),
        true,
        512,
        JSON_THROW_ON_ERROR
    );
} catch (Throwable $exception) {
    fwrite(STDERR, 'Unable to read English product catalog: ' . $exception->getMessage() . "\n");
    exit(1);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, (int) DB_PORT);
$db->set_charset('utf8mb4');

const DRYZEN_ENGLISH_LANGUAGE_ID = 1;
const DRYZEN_STORE_ID = 0;

function englishEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function englishFetchAll(mysqli $db, $sql)
{
    $rows = array();
    $result = $db->query($sql);

    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    return $rows;
}

function englishNormalizeText($value)
{
    $value = html_entity_decode(
        strip_tags(str_replace(array('<br>', '<br/>', '<br />'), ' ', (string) $value)),
        ENT_QUOTES,
        'UTF-8'
    );

    return trim(preg_replace('/\s+/u', ' ', $value));
}

function englishRenderParagraph($paragraph)
{
    $escaped = englishEscape($paragraph);
    $escaped = preg_replace(
        '~(?<![">])(dryzen@dryzen\.eu|info@dryzen\.eu)~',
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

function englishRenderList(array $items, $ordered = false)
{
    if (!$items) {
        return '';
    }

    $tag = $ordered ? 'ol' : 'ul';
    $html = '<' . $tag . '>';

    foreach ($items as $item) {
        $html .= '<li>' . englishEscape($item) . '</li>';
    }

    return $html . '</' . $tag . '>';
}

function englishRenderContentBlock(array $block)
{
    $html = '';

    foreach (isset($block['paragraphs']) ? $block['paragraphs'] : array() as $paragraph) {
        $html .= englishRenderParagraph($paragraph);
    }

    $html .= englishRenderList(
        isset($block['ordered_list']) ? $block['ordered_list'] : array(),
        true
    );
    $html .= englishRenderList(
        isset($block['list']) ? $block['list'] : array(),
        false
    );

    foreach (isset($block['subsections']) ? $block['subsections'] : array() as $subsection) {
        if (!empty($subsection['title'])) {
            $html .= '<h3>' . englishEscape($subsection['title']) . '</h3>';
        }

        $html .= englishRenderContentBlock($subsection);
    }

    return $html;
}

function englishBuildProductDescription(array $product)
{
    $html = '';

    foreach ($product['intro'] as $paragraph) {
        $html .= englishRenderParagraph($paragraph);
    }

    if (!empty($product['description'])) {
        $html .= '<h2>' . englishEscape($product['description']['title']) . '</h2>';
        foreach ($product['description']['paragraphs'] as $paragraph) {
            $html .= englishRenderParagraph($paragraph);
        }
    }

    return $html;
}

function englishSaveBackup(
    mysqli $db,
    $projectRoot,
    array $categoryIds,
    array $productIds,
    array $informationIds,
    array $moduleNames
) {
    $productIdList = implode(',', array_map('intval', $productIds));
    $informationIdList = implode(',', array_map('intval', $informationIds));
    $quotedModules = array();

    foreach ($moduleNames as $name) {
        $quotedModules[] = "'" . $db->real_escape_string($name) . "'";
    }

    $backup = array(
        'created_at' => date(DATE_ATOM),
        'language' => englishFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'language
             WHERE language_id = ' . DRYZEN_ENGLISH_LANGUAGE_ID
        ),
        'product_descriptions' => englishFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'product_description
             WHERE language_id = ' . DRYZEN_ENGLISH_LANGUAGE_ID . '
               AND product_id IN (' . $productIdList . ')
             ORDER BY product_id'
        ),
        'product_tab_descriptions' => englishFetchAll(
            $db,
            'SELECT ptd.*
             FROM ' . DB_PREFIX . 'product_tabs_description ptd
             INNER JOIN ' . DB_PREFIX . 'product_tabs_to_product ptp
               ON ptp.tab_id = ptd.tab_id
             WHERE ptd.language_id = ' . DRYZEN_ENGLISH_LANGUAGE_ID . '
               AND ptp.product_id IN (' . $productIdList . ')
             ORDER BY ptp.product_id, ptd.tab_id'
        ),
        'information_descriptions' => englishFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'information_description
             WHERE language_id = ' . DRYZEN_ENGLISH_LANGUAGE_ID . '
               AND information_id IN (' . $informationIdList . ')
             ORDER BY information_id'
        ),
        'seo_urls' => englishFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'seo_url
             WHERE store_id = ' . DRYZEN_STORE_ID . '
               AND language_id = ' . DRYZEN_ENGLISH_LANGUAGE_ID . '
               AND (
                 query IN ('
                    . implode(',', array_map(
                        function ($id) use ($db) {
                            return "'category_id=" . (int) $id . "'";
                        },
                        $categoryIds
                    ))
                    . ')
                 OR query IN ('
                    . implode(',', array_map(
                        function ($id) use ($db) {
                            return "'product_id=" . (int) $id . "'";
                        },
                        $productIds
                    ))
                    . ')
                 OR query IN ('
                    . implode(',', array_map(
                        function ($id) use ($db) {
                            return "'information_id=" . (int) $id . "'";
                        },
                        $informationIds
                    ))
                    . ')
               )
             ORDER BY seo_url_id'
        ),
        'modules' => englishFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'module
             WHERE code = \'basel_content\'
               AND name IN (' . implode(',', $quotedModules) . ')
             ORDER BY module_id'
        ),
        'footer_settings' => englishFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'setting
             WHERE store_id = ' . DRYZEN_STORE_ID . '
               AND code = \'basel\'
               AND `key` = \'basel_footer_columns\''
        ),
        'main_menu' => englishFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'mega_menu
             WHERE id IN (49, 50, 52, 53)
             ORDER BY id'
        ),
    );

    $backupDirectory = $projectRoot . '/.local-backup';
    if (!is_dir($backupDirectory) && !mkdir($backupDirectory, 0775, true) && !is_dir($backupDirectory)) {
        throw new RuntimeException('Unable to create the backup directory.');
    }

    $backupFile = $backupDirectory . '/english-content-before-'
        . date('Ymd-His') . '-' . getmypid() . '.json';
    $json = json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($json === false || file_put_contents($backupFile, $json) === false) {
        throw new RuntimeException('Unable to write the English content backup.');
    }

    return $backupFile;
}

function englishUpsertSeo(mysqli $db, $query, $keyword)
{
    $conflict = $db->prepare(
        'SELECT query FROM ' . DB_PREFIX . 'seo_url
         WHERE store_id = ? AND language_id = ? AND keyword = ? AND query <> ?
         LIMIT 1'
    );
    $storeId = DRYZEN_STORE_ID;
    $languageId = DRYZEN_ENGLISH_LANGUAGE_ID;
    $conflict->bind_param('iiss', $storeId, $languageId, $keyword, $query);
    $conflict->execute();
    $conflictRow = $conflict->get_result()->fetch_assoc();
    $conflict->close();

    if ($conflictRow) {
        throw new RuntimeException(
            'English SEO slug "' . $keyword . '" is already used by ' . $conflictRow['query'] . '.'
        );
    }

    $delete = $db->prepare(
        'DELETE FROM ' . DB_PREFIX . 'seo_url
         WHERE store_id = ? AND language_id = ? AND query = ?'
    );
    $delete->bind_param('iis', $storeId, $languageId, $query);
    $delete->execute();
    $delete->close();

    $insert = $db->prepare(
        'INSERT INTO ' . DB_PREFIX . 'seo_url
         (store_id, language_id, query, keyword)
         VALUES (?, ?, ?, ?)'
    );
    $insert->bind_param('iiss', $storeId, $languageId, $query, $keyword);
    $insert->execute();
    $insert->close();
}

function englishSetLanguageValue(array &$container, $key, $value)
{
    if (!isset($container[$key]) || !is_array($container[$key])) {
        $container[$key] = array();
    }

    $container[$key][DRYZEN_ENGLISH_LANGUAGE_ID] = $value;
}

function englishApplyNavigationAndFooter(mysqli $db)
{
    $menuLinks = array(
        53 => array(3 => '/', 1 => '/'),
        50 => array(3 => 'shop', 1 => 'shop'),
        49 => array(3 => 'o-nama', 1 => 'about-us'),
        52 => array(3 => 'kontakt', 1 => 'contact'),
    );
    $menuUpdate = $db->prepare(
        'UPDATE ' . DB_PREFIX . 'mega_menu SET link = ? WHERE id = ?'
    );
    foreach ($menuLinks as $menuId => $languageLinks) {
        $serializedMenuLinks = serialize($languageLinks);
        $menuUpdate->bind_param('si', $serializedMenuLinks, $menuId);
        $menuUpdate->execute();
    }
    $menuUpdate->close();

    $settingSelect = $db->prepare(
        'SELECT setting_id, value FROM ' . DB_PREFIX . 'setting
         WHERE store_id = ? AND code = \'basel\' AND `key` = \'basel_footer_columns\'
         LIMIT 1 FOR UPDATE'
    );
    $storeId = DRYZEN_STORE_ID;
    $settingSelect->bind_param('i', $storeId);
    $settingSelect->execute();
    $settingRow = $settingSelect->get_result()->fetch_assoc();
    $settingSelect->close();

    if (!$settingRow) {
        throw new RuntimeException('Basel footer custom-link settings were not found.');
    }

    $footerColumns = json_decode($settingRow['value'], true);
    if (!is_array($footerColumns)) {
        throw new RuntimeException('Basel footer custom-link settings are invalid.');
    }

    $targets = array(
        1 => array(
            1 => array(3 => 'opci-uvjeti-koristenja', 1 => 'general-terms-of-use'),
            2 => array(3 => 'pravila-privatnosti', 1 => 'privacy-policy'),
            3 => array(3 => 'izjava-o-sigurnosti-online-placanja', 1 => 'security-of-online-payments'),
            4 => array(3 => 'pravo-i-nacin-podnosenja-prigovora', 1 => 'right-to-submit-a-complaint'),
            5 => array(3 => 'povrat-robe-zamjena-i-reklamacije', 1 => 'returns-exchanges-and-complaints'),
        ),
        2 => array(
            1 => array(3 => 'index.php?route=account/login', 1 => 'index.php?route=account/login'),
            2 => array(3 => 'index.php?route=account/return/add', 1 => 'index.php?route=account/return/add'),
            3 => array(3 => 'index.php?route=account/order', 1 => 'index.php?route=account/order'),
            4 => array(3 => 'kontakt', 1 => 'contact'),
        ),
    );

    foreach ($targets as $columnId => $links) {
        foreach ($links as $linkId => $languageTargets) {
            if (!isset($footerColumns[$columnId]['links'][$linkId])) {
                throw new RuntimeException(
                    'Required Basel footer link was not found: ' . $columnId . '/' . $linkId
                );
            }
            $footerColumns[$columnId]['links'][$linkId]['target'] = $languageTargets;
        }
    }

    $encodedFooterColumns = json_encode(
        $footerColumns,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    if ($encodedFooterColumns === false) {
        throw new RuntimeException('Unable to encode Basel footer custom-link settings.');
    }

    $settingId = (int) $settingRow['setting_id'];
    $settingUpdate = $db->prepare(
        'UPDATE ' . DB_PREFIX . 'setting SET value = ? WHERE setting_id = ?'
    );
    $settingUpdate->bind_param('si', $encodedFooterColumns, $settingId);
    $settingUpdate->execute();
    $settingUpdate->close();
}

function englishUpdateModule(
    mysqli $db,
    $name,
    $html,
    $title = null,
    $subtitle = null,
    $imageAlt = null
) {
    $select = $db->prepare(
        'SELECT module_id, setting FROM ' . DB_PREFIX . 'module
         WHERE code = \'basel_content\' AND name = ?
         ORDER BY module_id
         LIMIT 1
         FOR UPDATE'
    );
    $select->bind_param('s', $name);
    $select->execute();
    $row = $select->get_result()->fetch_assoc();
    $select->close();

    if (!$row) {
        throw new RuntimeException('Required content module was not found: ' . $name);
    }

    $settings = json_decode($row['setting'], true);
    if (!is_array($settings) || empty($settings['columns'])) {
        throw new RuntimeException('Invalid content module settings: ' . $name);
    }

    if ($title !== null) {
        englishSetLanguageValue($settings['b_setting'], 'title_m', $title);
    }
    if ($subtitle !== null) {
        englishSetLanguageValue($settings['b_setting'], 'title_b', $subtitle);
    }

    $htmlColumns = 0;
    foreach ($settings['columns'] as &$column) {
        if (!is_array($column)) {
            continue;
        }

        if (isset($column['type']) && $column['type'] === 'html') {
            englishSetLanguageValue($column, 'data1', $html);
            $htmlColumns++;
        } elseif (
            $imageAlt !== null
            && isset($column['type'])
            && $column['type'] === 'img'
        ) {
            englishSetLanguageValue(
                $column,
                'data1',
                '<span class="hover-zoom"><span class="sr-only">'
                    . englishEscape($imageAlt)
                    . '</span></span>'
            );
        }
    }
    unset($column);

    if ($htmlColumns !== 1) {
        throw new RuntimeException(
            'Expected exactly one HTML column in ' . $name . ', found ' . $htmlColumns . '.'
        );
    }

    $json = json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        throw new RuntimeException('Unable to encode content module: ' . $name);
    }

    $moduleId = (int) $row['module_id'];
    $update = $db->prepare(
        'UPDATE ' . DB_PREFIX . 'module SET setting = ? WHERE module_id = ?'
    );
    $update->bind_param('si', $json, $moduleId);
    $update->execute();
    $update->close();

    return $moduleId;
}

function englishRenderNeedHero(array $hero)
{
    return '<section class="dryzen-need-hero dryzen-need-hero--'
        . englishEscape($hero['modifier']) . '">'
        . '<div class="dryzen-need-hero-copy">'
        . '<h1>' . englishEscape($hero['title']) . '</h1>'
        . '<p class="dryzen-need-hero-subtitle">' . englishEscape($hero['subtitle']) . '</p>'
        . '<p>' . englishEscape($hero['copy']) . '</p>'
        . '</div>'
        . '<figure class="dryzen-need-hero-media">'
        . '<img src="/image/' . englishEscape($hero['image'])
        . '" alt="" width="1600" height="1066">'
        . '</figure>'
        . '</section>';
}

function englishRenderHandsHero(array $hero)
{
    return '<section class="dryzen-hands-hero">'
        . '<div class="dryzen-hands-hero-copy">'
        . '<h1>' . englishEscape($hero['title'] . ' ' . $hero['subtitle']) . '</h1>'
        . '<p>' . englishEscape($hero['copy']) . '</p>'
        . '</div>'
        . '<figure class="dryzen-hands-hero-media">'
        . '<img src="/image/' . englishEscape($hero['image'])
        . '" alt="" width="1600" height="1066">'
        . '</figure>'
        . '</section>';
}

function englishRenderNeedSymptoms(array $symptoms)
{
    $html = '<section class="dryzen-need-symptoms">'
        . '<h2>' . englishEscape($symptoms['title']) . '</h2>'
        . '<ul class="dryzen-need-symptom-list">';

    foreach ($symptoms['items'] as $item) {
        $html .= '<li>' . englishEscape($item) . '</li>';
    }

    return $html . '</ul></section>';
}

function englishRenderHandsSymptoms(array $symptoms)
{
    $html = '<section class="dryzen-hands-symptoms">'
        . '<h2>' . englishEscape($symptoms['title']) . '</h2>'
        . '<ul class="dryzen-hands-symptom-list">';

    foreach ($symptoms['items'] as $item) {
        $html .= '<li>' . englishEscape($item) . '</li>';
    }

    return $html . '</ul></section>';
}

function englishRenderNeedProducts(array $page, array $productNames, array $productSlugs)
{
    $html = '<section id="odaberi-proizvod" class="dryzen-need-products">'
        . '<header class="dryzen-need-products-header">'
        . '<h2>' . englishEscape($page['products_intro']) . '</h2>'
        . '</header>';

    if (isset($page['products'])) {
        $html .= '<div class="dryzen-need-product-grid">';
        foreach ($page['products'] as $product) {
            $productId = (int) $product['product_id'];
            $href = '/' . $productSlugs[$productId];
            $variantClass = strtolower(
                trim((string) preg_replace('/[^a-z0-9]+/i', '-', $product['label']), '-')
            );
            $html .= '<article class="dryzen-need-product-card dryzen-need-product-card--'
                . englishEscape($variantClass) . '">'
                . '<a class="dryzen-need-product-media" href="' . englishEscape($href)
                . '" aria-label="' . englishEscape($productNames[$productId]) . '">'
                . '<img src="/image/' . englishEscape($product['image']) . '" alt="'
                . englishEscape($productNames[$productId])
                . '" width="1100" height="733" loading="lazy">'
                . '</a>'
                . '<div class="dryzen-need-product-body">'
                . '<h3>' . englishEscape($product['label']) . '</h3>'
                . '<p>' . englishEscape($product['description']) . '</p>'
                . '<a class="dryzen-need-product-link" href="' . englishEscape($href) . '">'
                . englishEscape($page['button_text'])
                . '</a>'
                . '</div>'
                . '</article>';
        }

        return $html . '</div></section>';
    }

    $product = $page['single_product'];
    $productId = (int) $product['product_id'];
    $href = '/' . $productSlugs[$productId];
    $html = str_replace(
        'class="dryzen-need-products"',
        'class="dryzen-need-products dryzen-need-products--single"',
        $html
    );
    $html .= '<article class="dryzen-need-single-product">'
        . '<a class="dryzen-need-product-media" href="' . englishEscape($href)
        . '" aria-label="' . englishEscape($productNames[$productId]) . '">'
        . '<img src="/image/' . englishEscape($product['image']) . '" alt="'
        . englishEscape($productNames[$productId])
        . '" width="1100" height="733" loading="lazy">'
        . '</a>'
        . '<div class="dryzen-need-single-copy">'
        . '<h3>' . englishEscape($product['label']) . '</h3>'
        . '<p>' . englishEscape($product['description']) . '</p>'
        . '<p class="dryzen-need-feature-title">' . englishEscape($product['features_title']) . '</p>'
        . englishRenderList($product['features'])
        . '<a class="dryzen-need-product-link" href="' . englishEscape($href) . '">'
        . englishEscape($page['button_text'])
        . '</a>'
        . '</div>'
        . '</article>'
        . '</section>';

    return str_replace(
        '<ul>',
        '<ul class="dryzen-need-feature-list">',
        $html
    );
}

function englishRenderHandsProducts(array $page, array $productNames, array $productSlugs)
{
    $html = '<section id="odaberi-proizvod" class="dryzen-hands-products">'
        . '<header class="dryzen-hands-products-header">'
        . '<h2>' . englishEscape($page['products_intro']) . '</h2>'
        . '</header>'
        . '<div class="dryzen-hands-product-grid">';

    foreach ($page['products'] as $product) {
        $productId = (int) $product['product_id'];
        $href = '/' . $productSlugs[$productId];
        $html .= '<article class="dryzen-hands-product-card">'
            . '<a class="dryzen-hands-product-media" href="' . englishEscape($href)
            . '" aria-label="' . englishEscape($productNames[$productId]) . '">'
            . '<img src="/image/' . englishEscape($product['image']) . '" alt="'
            . englishEscape($productNames[$productId])
            . '" width="1100" height="733" loading="lazy">'
            . '</a>'
            . '<div class="dryzen-hands-product-body">'
            . '<h3>' . englishEscape($product['label']) . '</h3>'
            . '<p>' . englishEscape($product['description']) . '</p>'
            . '<a class="dryzen-hands-product-link" href="' . englishEscape($href) . '">'
            . englishEscape($page['button_text'])
            . '</a>'
            . '</div>'
            . '</article>';
    }

    return $html . '</div></section>';
}

function englishRenderAboutParagraphs(array $paragraphs)
{
    $html = '';

    foreach ($paragraphs as $paragraph) {
        $html .= '<p>' . englishEscape($paragraph) . '</p>';
    }

    return $html;
}

function englishRenderAboutSections(array $content)
{
    $hero = '<link rel="stylesheet" href="/catalog/view/theme/basel/stylesheet/dryzen-about.css?v=20260721-14">'
        . '<section class="dryzen-about-hero">'
        . '<header class="dryzen-about-hero-copy">'
        . '<span class="dryzen-about-eyebrow">' . englishEscape($content['hero']['eyebrow']) . '</span>'
        . '<h1>' . englishEscape($content['hero']['title']) . '</h1>'
        . '</header>'
        . '<figure class="dryzen-about-hero-media">'
        . '<img src="/image/' . englishEscape($content['hero']['image']) . '" alt="'
        . englishEscape($content['hero']['image_alt'])
        . '" width="1800" height="1200" fetchpriority="high" decoding="async">'
        . '</figure>'
        . '</section>';

    $overview = '<section class="dryzen-about-overview">'
        . '<div class="dryzen-about-overview-primary">'
        . '<h2>' . englishEscape($content['overview']['title']) . '</h2>'
        . englishRenderAboutParagraphs($content['overview']['paragraphs'])
        . '</div>'
        . '<div class="dryzen-about-overview-secondary">'
        . '<h2>' . englishEscape($content['overview']['why_title']) . '</h2>'
        . englishRenderAboutParagraphs($content['overview']['why_paragraphs'])
        . '</div>'
        . '</section>';

    $routine = '<section class="dryzen-about-routine">'
        . '<header class="dryzen-about-section-heading"><h2>'
        . englishEscape($content['routine']['title']) . '</h2></header>'
        . '<ol class="dryzen-about-routine-list">';
    foreach ($content['routine']['paragraphs'] as $paragraph) {
        $routine .= '<li>' . englishEscape($paragraph) . '</li>';
    }
    $routine .= '</ol></section>';

    $focus = '<section class="dryzen-about-focus"><h2>';
    foreach ($content['focus']['title_lines'] as $line) {
        $focus .= '<span>' . englishEscape($line) . '</span>';
    }
    $focus .= '</h2><div class="dryzen-about-focus-copy">'
        . englishRenderAboutParagraphs($content['focus']['paragraphs'])
        . '</div></section>';

    $reasons = '<section class="dryzen-about-reasons"><header><h2>'
        . englishEscape($content['reasons']['title'])
        . '</h2><p>' . englishEscape($content['reasons']['intro']) . '</p></header>'
        . '<div class="dryzen-about-reason-grid"><div class="dryzen-about-reason-list-wrap">'
        . '<h3>' . englishEscape($content['reasons']['list_title']) . '</h3>'
        . '<ul class="dryzen-about-reason-list">';
    foreach ($content['reasons']['items'] as $item) {
        $reasons .= '<li>' . englishEscape($item) . '</li>';
    }
    $reasons .= '</ul><p class="dryzen-about-reason-closing">'
        . englishEscape($content['reasons']['closing'])
        . '</p></div><aside class="dryzen-about-fit-card"><h3>'
        . englishEscape($content['reasons']['fit_title']) . '</h3>'
        . englishRenderAboutParagraphs($content['reasons']['fit_paragraphs'])
        . '</aside></div></section>';

    $story = '<section class="dryzen-about-story">'
        . '<header class="dryzen-about-story-header"><h2>'
        . englishEscape($content['story']['title']) . '</h2></header>'
        . '<div class="dryzen-about-story-layout"><figure class="dryzen-about-story-media">'
        . '<img src="/image/' . englishEscape($content['story']['image']) . '" alt="'
        . englishEscape($content['story']['image_alt'])
        . '" width="1500" height="1000" loading="eager" decoding="async">'
        . '</figure><div class="dryzen-about-story-copy">'
        . englishRenderAboutParagraphs($content['story']['paragraphs'])
        . '</div></div></section>';

    $developmentParagraphs = $content['development']['paragraphs'];
    $developmentIntro = array_shift($developmentParagraphs);
    $development = '<section class="dryzen-about-development">'
        . '<header class="dryzen-about-section-heading"><h2>'
        . englishEscape($content['development']['title']) . '</h2></header>'
        . '<p class="dryzen-about-development-intro">' . englishEscape($developmentIntro) . '</p>'
        . '<div class="dryzen-about-development-grid">';
    foreach ($developmentParagraphs as $paragraph) {
        $development .= '<p class="dryzen-about-development-card">'
            . englishEscape($paragraph) . '</p>';
    }
    $development .= '</div></section>';

    $merchant = '<section class="dryzen-about-merchant" data-otp-compliance="merchant-details">'
        . '<header class="dryzen-about-merchant-header">'
        . '<span class="dryzen-about-merchant-eyebrow">'
        . englishEscape($content['merchant']['eyebrow']) . '</span>'
        . '<h2>' . englishEscape($content['merchant']['title']) . '</h2>'
        . '</header><dl class="dryzen-about-merchant-list">';
    foreach ($content['merchant']['items'] as $item) {
        $value = englishEscape($item['value']);
        if (!empty($item['href'])) {
            $value = '<a href="' . englishEscape($item['href']) . '">' . $value . '</a>';
        }
        $merchant .= '<div class="dryzen-about-merchant-item"><dt>'
            . englishEscape($item['label']) . '</dt><dd>' . $value . '</dd></div>';
    }
    $merchant .= '</dl><p class="dryzen-about-merchant-note">'
        . englishEscape($content['merchant']['note'])
        . '</p></section>';

    return array(
        'DryZen About Hero' => $hero,
        'DryZen About Overview' => $overview,
        'DryZen About Routine' => $routine,
        'DryZen About Focus' => $focus,
        'DryZen About Reasons' => $reasons,
        'DryZen About Story' => $story,
        'DryZen About Development' => $development,
        'DryZen About Merchant' => $merchant,
    );
}

function englishUpdateInformation(mysqli $db, array $page, $description = '')
{
    $informationId = (int) $page['information_id'];
    $select = $db->prepare(
        'SELECT information_id FROM ' . DB_PREFIX . 'information
         WHERE information_id = ? LIMIT 1 FOR UPDATE'
    );
    $select->bind_param('i', $informationId);
    $select->execute();
    $exists = $select->get_result()->fetch_assoc();
    $select->close();

    if (!$exists) {
        throw new RuntimeException('Required information page was not found: ' . $informationId);
    }

    $update = $db->prepare(
        'UPDATE ' . DB_PREFIX . 'information_description
         SET title = ?, description = ?, meta_title = ?, meta_description = ?, meta_keyword = \'\'
         WHERE information_id = ? AND language_id = ?'
    );
    $languageId = DRYZEN_ENGLISH_LANGUAGE_ID;
    $update->bind_param(
        'ssssii',
        $page['title'],
        $description,
        $page['meta_title'],
        $page['meta_description'],
        $informationId,
        $languageId
    );
    $update->execute();

    if ($update->affected_rows === 0) {
        $check = $db->prepare(
            'SELECT information_id FROM ' . DB_PREFIX . 'information_description
             WHERE information_id = ? AND language_id = ?'
        );
        $check->bind_param('ii', $informationId, $languageId);
        $check->execute();
        $rowExists = $check->get_result()->fetch_assoc();
        $check->close();

        if (!$rowExists) {
            $insert = $db->prepare(
                'INSERT INTO ' . DB_PREFIX . 'information_description
                 (information_id, language_id, title, description, meta_title, meta_description, meta_keyword)
                 VALUES (?, ?, ?, ?, ?, ?, \'\')'
            );
            $insert->bind_param(
                'iissss',
                $informationId,
                $languageId,
                $page['title'],
                $description,
                $page['meta_title'],
                $page['meta_description']
            );
            $insert->execute();
            $insert->close();
        }
    }
    $update->close();
}

function englishApplyProducts(mysqli $db, array $products, array $productSeoRoutes)
{
    if (count($products) !== 13) {
        throw new RuntimeException('Expected exactly 13 English DryZen products.');
    }

    $descriptionUpdate = $db->prepare(
        'UPDATE ' . DB_PREFIX . 'product_description
         SET name = ?, subtitle = ?, value_proposition = ?, description = ?,
             meta_title = ?, meta_description = ?,
             image_alt = ?, image_title = ?
         WHERE product_id = ? AND language_id = ?'
    );
    $tabUpsert = $db->prepare(
        'INSERT INTO ' . DB_PREFIX . 'product_tabs_description
         (tab_id, language_id, name, description)
         VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description)'
    );
    $names = array();

    foreach ($products as $product) {
        $productId = (int) $product['product_id'];
        if (empty($productSeoRoutes[$productId])) {
            throw new RuntimeException('Missing English product SEO route: ' . $productId);
        }
        if (mb_strlen($product['meta_description'], 'UTF-8') > 255) {
            throw new RuntimeException('English meta description is too long: ' . $productId);
        }

        $productExists = englishFetchAll(
            $db,
            'SELECT product_id FROM ' . DB_PREFIX . 'product
             WHERE product_id = ' . $productId . ' AND status = 1'
        );
        if (count($productExists) !== 1) {
            throw new RuntimeException('Required active product was not found: ' . $productId);
        }

        $description = englishBuildProductDescription($product);
        $languageId = DRYZEN_ENGLISH_LANGUAGE_ID;
        $descriptionUpdate->bind_param(
            'ssssssssii',
            $product['name'],
            $product['subtitle'],
            $product['value_proposition'],
            $description,
            $product['meta_title'],
            $product['meta_description'],
            $product['name'],
            $product['name'],
            $productId,
            $languageId
        );
        $descriptionUpdate->execute();

        if ($descriptionUpdate->affected_rows === 0) {
            $existingDescription = englishFetchAll(
                $db,
                'SELECT product_id FROM ' . DB_PREFIX . 'product_description
                 WHERE product_id = ' . $productId . '
                   AND language_id = ' . DRYZEN_ENGLISH_LANGUAGE_ID
            );
            if (!$existingDescription) {
                throw new RuntimeException('Missing English product row: ' . $productId);
            }
        }

        $tabRows = englishFetchAll(
            $db,
            'SELECT pt.tab_id
             FROM ' . DB_PREFIX . 'product_tabs_to_product ptp
             INNER JOIN ' . DB_PREFIX . 'product_tabs pt ON pt.tab_id = ptp.tab_id
             WHERE ptp.product_id = ' . $productId . '
               AND pt.status = 1
             ORDER BY pt.sort_order, pt.tab_id'
        );
        if (count($tabRows) !== 7 || count($product['tabs']) !== 7) {
            throw new RuntimeException('Expected seven editable tabs for product ' . $productId . '.');
        }

        foreach ($product['tabs'] as $index => $tab) {
            $tabId = (int) $tabRows[$index]['tab_id'];
            $tabName = $tab['name'];
            $tabDescription = englishRenderContentBlock($tab);
            $tabUpsert->bind_param('iiss', $tabId, $languageId, $tabName, $tabDescription);
            $tabUpsert->execute();
        }

        englishUpsertSeo($db, 'product_id=' . $productId, $productSeoRoutes[$productId]);
        $names[$productId] = $product['name'];
    }

    $descriptionUpdate->close();
    $tabUpsert->close();

    return $names;
}

function englishAuditProductState(mysqli $db, array $products)
{
    foreach ($products as $product) {
        $productId = (int) $product['product_id'];
        $rows = englishFetchAll(
            $db,
            'SELECT name, subtitle, value_proposition, description, meta_title, meta_description
             FROM ' . DB_PREFIX . 'product_description
             WHERE product_id = ' . $productId . '
               AND language_id = ' . DRYZEN_ENGLISH_LANGUAGE_ID
        );
        if (count($rows) !== 1) {
            throw new RuntimeException('English product audit failed: ' . $productId);
        }

        $expected = array(
            $product['name'],
            $product['subtitle'],
            $product['value_proposition'],
            $product['meta_title'],
            $product['meta_description'],
        );
        foreach ($product['intro'] as $paragraph) {
            $expected[] = $paragraph;
        }
        if (!empty($product['description'])) {
            $expected[] = $product['description']['title'];
            $expected = array_merge($expected, $product['description']['paragraphs']);
        }

        $tabRows = englishFetchAll(
            $db,
            'SELECT ptd.name, ptd.description
             FROM ' . DB_PREFIX . 'product_tabs_to_product ptp
             INNER JOIN ' . DB_PREFIX . 'product_tabs pt ON pt.tab_id = ptp.tab_id
             INNER JOIN ' . DB_PREFIX . 'product_tabs_description ptd
               ON ptd.tab_id = pt.tab_id
              AND ptd.language_id = ' . DRYZEN_ENGLISH_LANGUAGE_ID . '
             WHERE ptp.product_id = ' . $productId . '
             ORDER BY pt.sort_order, pt.tab_id'
        );
        if (count($tabRows) !== 7) {
            throw new RuntimeException('English product-tab audit failed: ' . $productId);
        }

        $visible = englishNormalizeText(
            implode(' ', $rows[0]) . ' '
            . implode(' ', array_column($tabRows, 'name')) . ' '
            . implode(' ', array_column($tabRows, 'description'))
        );
        foreach ($product['tabs'] as $tab) {
            $expected[] = $tab['name'];
            foreach (array('paragraphs', 'ordered_list', 'list') as $key) {
                foreach (isset($tab[$key]) ? $tab[$key] : array() as $text) {
                    $expected[] = $text;
                }
            }
            foreach (isset($tab['subsections']) ? $tab['subsections'] : array() as $subsection) {
                $expected[] = $subsection['title'];
                foreach (array('paragraphs', 'ordered_list', 'list') as $key) {
                    foreach (isset($subsection[$key]) ? $subsection[$key] : array() as $text) {
                        $expected[] = $text;
                    }
                }
            }
        }

        foreach ($expected as $text) {
            if (strpos($visible, englishNormalizeText($text)) === false) {
                throw new RuntimeException(
                    'Missing English product text for ' . $productId . ': ' . $text
                );
            }
        }
    }
}

function englishClearCache(array $moduleIds)
{
    if (!defined('DIR_CACHE') || !is_dir(DIR_CACHE)) {
        return 0;
    }

    $cacheDirectory = rtrim(DIR_CACHE, '/\\') . DIRECTORY_SEPARATOR;
    $patterns = array(
        $cacheDirectory . 'cache.dryzen.page.*',
        $cacheDirectory . 'cache.layout.modules.*',
        $cacheDirectory . 'cache.basel_styles_cache_store_0.*',
        $cacheDirectory . 'cache.language.*',
        $cacheDirectory . 'cache.megamenu.*',
    );

    foreach ($moduleIds as $moduleId) {
        $patterns[] = $cacheDirectory . 'cache.module.catalog.' . (int) $moduleId . '.*';
    }

    $removed = 0;
    foreach (array_unique($patterns) as $pattern) {
        foreach (glob($pattern) ?: array() as $file) {
            if (is_file($file) && unlink($file)) {
                $removed++;
            }
        }
    }

    return $removed;
}

$moduleNames = array(
    'DryZen Hero',
    'DryZen About',
    'DryZen Categories',
    'DryZen Approach',
    'DryZen Why Choose',
    'DryZen FAQ',
    'DryZen Product Finder',
    'DryZen Hands Hero',
    'DryZen Hands Symptoms',
    'DryZen Hands Products',
    'DryZen Feet Hero',
    'DryZen Feet Symptoms',
    'DryZen Feet Products',
    'DryZen Armpits Hero',
    'DryZen Armpits Symptoms',
    'DryZen Armpits Products',
    'DryZen Shoes Hero',
    'DryZen Shoes Symptoms',
    'DryZen Shoes Products',
    'DryZen About Hero',
    'DryZen About Overview',
    'DryZen About Routine',
    'DryZen About Focus',
    'DryZen About Reasons',
    'DryZen About Story',
    'DryZen About Development',
    'DryZen About Merchant',
);
$productIds = array_map(
    function ($product) {
        return (int) $product['product_id'];
    },
    $products
);
$informationContentIds = array(13, 18, 19, 20, 21);
$informationIds = array_keys($seoRoutes['information']);
$transactionStarted = false;

try {
    $backupFile = englishSaveBackup(
        $db,
        $projectRoot,
        array_keys($seoRoutes['categories']),
        $productIds,
        $informationIds,
        $moduleNames
    );

    $db->begin_transaction();
    $transactionStarted = true;

    $languageId = DRYZEN_ENGLISH_LANGUAGE_ID;
    $enableLanguage = $db->prepare(
        'UPDATE ' . DB_PREFIX . 'language
         SET status = 1, sort_order = 2
         WHERE language_id = ? AND code = \'en-gb\''
    );
    $enableLanguage->bind_param('i', $languageId);
    $enableLanguage->execute();
    $enableLanguage->close();

    $languageRows = englishFetchAll(
        $db,
        'SELECT language_id FROM ' . DB_PREFIX . 'language
         WHERE language_id = ' . DRYZEN_ENGLISH_LANGUAGE_ID . '
           AND code = \'en-gb\' AND status = 1'
    );
    if (count($languageRows) !== 1) {
        throw new RuntimeException('English language could not be enabled.');
    }

    $productNames = englishApplyProducts($db, $products, $seoRoutes['products']);
    englishApplyNavigationAndFooter($db);
    foreach ($seoRoutes['categories'] as $categoryId => $keyword) {
        englishUpsertSeo($db, 'category_id=' . (int) $categoryId, $keyword);
    }
    $moduleIds = array();

    $landingModules = array(
        'DryZen Hero' => array(
            'html' => $landing['hero'],
            'title' => null,
            'subtitle' => null,
            'image_alt' => $landing['hero_image_alt'],
        ),
        'DryZen About' => array(
            'html' => $landing['what_is_dryzen'],
            'title' => $landing['titles']['about'][0],
            'subtitle' => $landing['titles']['about'][1],
        ),
        'DryZen Categories' => array(
            'html' => $landing['need_grid'],
            'title' => $landing['titles']['categories'][0],
            'subtitle' => $landing['titles']['categories'][1],
        ),
        'DryZen Approach' => array(
            'html' => $landing['how_it_works'],
            'title' => $landing['titles']['approach'][0],
            'subtitle' => $landing['titles']['approach'][1],
        ),
        'DryZen Why Choose' => array(
            'html' => $landing['why_choose'],
            'title' => $landing['titles']['why'][0],
            'subtitle' => $landing['titles']['why'][1],
        ),
        'DryZen FAQ' => array(
            'html' => $landing['faq'],
            'title' => $landing['titles']['faq'][0],
            'subtitle' => $landing['titles']['faq'][1],
        ),
        'DryZen Product Finder' => array(
            'html' => $landing['product_finder'],
            'title' => $landing['titles']['finder'][0],
            'subtitle' => $landing['titles']['finder'][1],
            'image_alt' => $landing['finder_image_alt'],
        ),
    );

    foreach ($landingModules as $name => $definition) {
        $moduleIds[] = englishUpdateModule(
            $db,
            $name,
            $definition['html'],
            $definition['title'],
            $definition['subtitle'],
            isset($definition['image_alt']) ? $definition['image_alt'] : null
        );
    }

    foreach ($needPages as $needKey => $page) {
        englishUpdateInformation($db, array_merge(
            $page['page'],
            array('information_id' => $page['information_id'])
        ));
        if ($needKey === 'hands') {
            $heroHtml = englishRenderHandsHero($page['hero']);
            $symptomsHtml = englishRenderHandsSymptoms($page['symptoms']);
            $productsHtml = englishRenderHandsProducts(
                $page,
                $productNames,
                $seoRoutes['products']
            );
        } else {
            $heroHtml = englishRenderNeedHero($page['hero']);
            $symptomsHtml = englishRenderNeedSymptoms($page['symptoms']);
            $productsHtml = englishRenderNeedProducts(
                $page,
                $productNames,
                $seoRoutes['products']
            );
        }
        $prefix = $page['page']['module_prefix'];
        $moduleIds[] = englishUpdateModule($db, $prefix . ' Hero', $heroHtml);
        $moduleIds[] = englishUpdateModule($db, $prefix . ' Symptoms', $symptomsHtml);
        $moduleIds[] = englishUpdateModule($db, $prefix . ' Products', $productsHtml);
    }

    $aboutSections = englishRenderAboutSections($about);
    englishUpdateInformation(
        $db,
        $about['page'],
        $aboutSections['DryZen About Merchant']
    );
    foreach ($aboutSections as $name => $html) {
        $moduleIds[] = englishUpdateModule($db, $name, $html);
    }

    foreach ($legalPages as $legalPage) {
        $description = isset($legalPage['description']) ? $legalPage['description'] : '';
        if (!empty($legalPage['preserve_description'])) {
            $existingLegalPage = englishFetchAll(
                $db,
                'SELECT description FROM ' . DB_PREFIX . 'information_description
                 WHERE information_id = ' . (int) $legalPage['information_id'] . '
                   AND language_id = ' . DRYZEN_ENGLISH_LANGUAGE_ID
            );
            if (count($existingLegalPage) !== 1) {
                throw new RuntimeException(
                    'English legal page was not found: ' . (int) $legalPage['information_id']
                );
            }
            $description = $existingLegalPage[0]['description'];
        }
        englishUpdateInformation($db, $legalPage, $description);
    }

    foreach ($seoRoutes['information'] as $informationId => $keyword) {
        englishUpsertSeo($db, 'information_id=' . (int) $informationId, $keyword);
    }

    englishAuditProductState($db, $products);

    foreach ($needPages as $page) {
        $informationId = (int) $page['information_id'];
        $rows = englishFetchAll(
            $db,
            'SELECT title, meta_title, meta_description
             FROM ' . DB_PREFIX . 'information_description
             WHERE information_id = ' . $informationId . '
               AND language_id = ' . DRYZEN_ENGLISH_LANGUAGE_ID
        );
        if (
            count($rows) !== 1
            || $rows[0]['title'] !== $page['page']['title']
            || $rows[0]['meta_title'] !== $page['page']['meta_title']
            || $rows[0]['meta_description'] !== $page['page']['meta_description']
        ) {
            throw new RuntimeException('English information-page audit failed: ' . $informationId);
        }
    }

    $db->commit();
    $transactionStarted = false;
    $cacheRemoved = englishClearCache($moduleIds);

    echo "English DryZen content applied successfully.\n";
    echo 'Products updated: ' . count($products) . "\n";
    echo 'Information pages updated: '
        . (count($informationContentIds) + count($legalPages)) . "\n";
    echo 'Content modules updated: ' . count(array_unique($moduleIds)) . "\n";
    echo 'English SEO URLs applied: '
        . (
            count($seoRoutes['categories'])
            + count($seoRoutes['products'])
            + count($seoRoutes['information'])
        ) . "\n";
    echo 'Cache files removed: ' . $cacheRemoved . "\n";
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
