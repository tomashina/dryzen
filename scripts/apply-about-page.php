<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the command line.');
}

$projectRoot = dirname(__DIR__);
$configFile = $projectRoot . '/upload/config.php';
$contentFile = $projectRoot . '/database/content/hr/about-page.php';
$styleFile = $projectRoot . '/upload/catalog/view/theme/basel/stylesheet/dryzen-about.css';

foreach (array($configFile, $contentFile, $styleFile) as $requiredFile) {
    if (!is_file($requiredFile)) {
        fwrite(STDERR, 'Missing required file: ' . $requiredFile . "\n");
        exit(1);
    }
}

require_once $configFile;
$content = require $contentFile;
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

function aboutLanguageValues($croatian, $english = '')
{
    return array(
        3 => $croatian,
        1 => $english,
    );
}

function aboutEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function aboutBlockSettings($fullWidth)
{
    return array(
        'title' => 0,
        'title_pl' => aboutLanguageValues(''),
        'title_m' => aboutLanguageValues(''),
        'title_b' => aboutLanguageValues(''),
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

function aboutHtmlColumn($html)
{
    return array(
        'w' => 'custom',
        'w_sm' => 'col-xs-12',
        'w_md' => 'col-sm-12',
        'w_lg' => 'col-md-12',
        'type' => 'html',
        'data1' => aboutLanguageValues($html),
        'data2' => '',
        'data3' => aboutLanguageValues(''),
        'data4' => '',
        'data5' => '',
        'data6' => '',
        'data7' => 'vertical-top text-left',
        'data8' => '',
    );
}

function aboutModuleSettings($name, $html, $fullWidth)
{
    return array(
        'save' => 'stay',
        'name' => $name,
        'status' => 1,
        'b_setting' => aboutBlockSettings($fullWidth),
        'bg_image' => '',
        'c_setting' => array(
            'fw' => 0,
            'block_css' => 0,
            'css' => '',
            'nm' => 0,
            'eh' => 0,
        ),
        'columns' => array(
            aboutHtmlColumn($html),
        ),
    );
}

function aboutFetchAll(mysqli $db, $sql)
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

function aboutSaveBackup(mysqli $db, $projectRoot, $informationId, $layoutId)
{
    $backup = array(
        'created_at' => date(DATE_ATOM),
        'information' => aboutFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'information WHERE information_id = ' . (int) $informationId
        ),
        'information_descriptions' => aboutFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'information_description WHERE information_id = ' . (int) $informationId
        ),
        'information_stores' => aboutFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'information_to_store WHERE information_id = ' . (int) $informationId
        ),
        'information_layouts' => aboutFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'information_to_layout WHERE information_id = ' . (int) $informationId
        ),
        'seo_urls' => aboutFetchAll(
            $db,
            "SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'information_id=" . (int) $informationId . "'"
        ),
        'layout' => aboutFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'layout WHERE layout_id = ' . (int) $layoutId
        ),
        'layout_modules' => aboutFetchAll(
            $db,
            'SELECT * FROM ' . DB_PREFIX . 'layout_module WHERE layout_id = ' . (int) $layoutId
        ),
        'modules' => aboutFetchAll(
            $db,
            "SELECT * FROM " . DB_PREFIX . "module
             WHERE code = 'basel_content'
               AND name LIKE 'DryZen About %'
             ORDER BY module_id"
        ),
        'settings' => aboutFetchAll(
            $db,
            "SELECT * FROM " . DB_PREFIX . "setting
             WHERE store_id = 0
               AND code = 'basel'
               AND `key` = 'basel_custom_css'
             ORDER BY setting_id"
        ),
    );

    $backupDirectory = $projectRoot . '/.local-backup';

    if (!is_dir($backupDirectory) && !mkdir($backupDirectory, 0775, true) && !is_dir($backupDirectory)) {
        throw new RuntimeException('Unable to create the backup directory.');
    }

    $backupFile = $backupDirectory . '/about-page-before-' . date('Ymd-His') . '.json';
    $encoded = json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($encoded === false || file_put_contents($backupFile, $encoded) === false) {
        throw new RuntimeException('Unable to create the page backup.');
    }

    return $backupFile;
}

function aboutRenderParagraphs(array $paragraphs)
{
    $html = '';

    foreach ($paragraphs as $paragraph) {
        $html .= '<p>' . aboutEscape($paragraph) . '</p>';
    }

    return $html;
}

function aboutRenderHero(array $hero)
{
    return '<link rel="stylesheet" href="/catalog/view/theme/basel/stylesheet/dryzen-about.css?v=20260719-13">'
        . '<section class="dryzen-about-hero">'
        . '<header class="dryzen-about-hero-copy">'
        . '<span class="dryzen-about-eyebrow">' . aboutEscape($hero['eyebrow']) . '</span>'
        . '<h1>' . aboutEscape($hero['title']) . '</h1>'
        . '</header>'
        . '<figure class="dryzen-about-hero-media">'
        . '<img src="/image/' . aboutEscape($hero['image']) . '" alt="DryZen tim s proizvodima" width="1800" height="1200" fetchpriority="high" decoding="async">'
        . '</figure>'
        . '</section>';
}

function aboutRenderOverview(array $overview)
{
    return '<section class="dryzen-about-overview">'
        . '<div class="dryzen-about-overview-primary">'
        . '<h2>' . aboutEscape($overview['title']) . '</h2>'
        . aboutRenderParagraphs($overview['paragraphs'])
        . '</div>'
        . '<div class="dryzen-about-overview-secondary">'
        . '<h2>' . aboutEscape($overview['why_title']) . '</h2>'
        . aboutRenderParagraphs($overview['why_paragraphs'])
        . '</div>'
        . '</section>';
}

function aboutRenderRoutine(array $routine)
{
    $html = '<section class="dryzen-about-routine">'
        . '<header class="dryzen-about-section-heading">'
        . '<h2>' . aboutEscape($routine['title']) . '</h2>'
        . '</header>'
        . '<ol class="dryzen-about-routine-list">';

    foreach ($routine['paragraphs'] as $paragraph) {
        $html .= '<li>' . aboutEscape($paragraph) . '</li>';
    }

    return $html . '</ol></section>';
}

function aboutRenderFocus(array $focus)
{
    $html = '<section class="dryzen-about-focus"><h2>';

    foreach ($focus['title_lines'] as $line) {
        $html .= '<span>' . aboutEscape($line) . '</span>';
    }

    return $html . '</h2><div class="dryzen-about-focus-copy">'
        . aboutRenderParagraphs($focus['paragraphs'])
        . '</div></section>';
}

function aboutRenderReasons(array $reasons)
{
    $html = '<section class="dryzen-about-reasons">'
        . '<header>'
        . '<h2>' . aboutEscape($reasons['title']) . '</h2>'
        . '<p>' . aboutEscape($reasons['intro']) . '</p>'
        . '</header>'
        . '<div class="dryzen-about-reason-grid">'
        . '<div class="dryzen-about-reason-list-wrap">'
        . '<h3>' . aboutEscape($reasons['list_title']) . '</h3>'
        . '<ul class="dryzen-about-reason-list">';

    foreach ($reasons['items'] as $item) {
        $html .= '<li>' . aboutEscape($item) . '</li>';
    }

    $html .= '</ul>'
        . '<p class="dryzen-about-reason-closing">' . aboutEscape($reasons['closing']) . '</p>'
        . '</div>'
        . '<aside class="dryzen-about-fit-card">'
        . '<h3>' . aboutEscape($reasons['fit_title']) . '</h3>'
        . aboutRenderParagraphs($reasons['fit_paragraphs'])
        . '</aside>'
        . '</div>'
        . '</section>';

    return $html;
}

function aboutRenderStory(array $story)
{
    return '<section class="dryzen-about-story">'
        . '<header class="dryzen-about-story-header">'
        . '<h2>' . aboutEscape($story['title']) . '</h2>'
        . '</header>'
        . '<div class="dryzen-about-story-layout">'
        . '<figure class="dryzen-about-story-media">'
        . '<img src="/image/' . aboutEscape($story['image']) . '" alt="DryZen proizvodi kao dio svakodnevne rutine" width="1500" height="1000" loading="lazy" decoding="async">'
        . '</figure>'
        . '<div class="dryzen-about-story-copy">'
        . aboutRenderParagraphs($story['paragraphs'])
        . '</div>'
        . '</div>'
        . '</section>';
}

function aboutRenderDevelopment(array $development)
{
    $paragraphs = $development['paragraphs'];
    $intro = array_shift($paragraphs);
    $html = '<section class="dryzen-about-development">'
        . '<header class="dryzen-about-section-heading">'
        . '<h2>' . aboutEscape($development['title']) . '</h2>'
        . '</header>'
        . '<p class="dryzen-about-development-intro">' . aboutEscape($intro) . '</p>'
        . '<div class="dryzen-about-development-grid">';

    foreach ($paragraphs as $paragraph) {
        $html .= '<p class="dryzen-about-development-card">' . aboutEscape($paragraph) . '</p>';
    }

    return $html . '</div></section>';
}

function aboutRenderMerchant(array $merchant)
{
    $html = '<section class="dryzen-about-merchant" data-otp-compliance="merchant-details">'
        . '<header class="dryzen-about-merchant-header">'
        . '<span class="dryzen-about-merchant-eyebrow">' . aboutEscape($merchant['eyebrow']) . '</span>'
        . '<h2>' . aboutEscape($merchant['title']) . '</h2>'
        . '</header>'
        . '<dl class="dryzen-about-merchant-list">';

    foreach ($merchant['items'] as $item) {
        $value = aboutEscape($item['value']);

        if (!empty($item['href'])) {
            $value = '<a href="' . aboutEscape($item['href']) . '">' . $value . '</a>';
        }

        $html .= '<div class="dryzen-about-merchant-item">'
            . '<dt>' . aboutEscape($item['label']) . '</dt>'
            . '<dd>' . $value . '</dd>'
            . '</div>';
    }

    return $html
        . '</dl>'
        . '<p class="dryzen-about-merchant-note">' . aboutEscape($merchant['note']) . '</p>'
        . '</section>';
}

function aboutUpsertModule(mysqli $db, $name, array $settings)
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

function aboutFindNewsletterModuleId(mysqli $db)
{
    $code = 'basel_content';
    $name = 'Newsletter Signup';
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
        throw new RuntimeException('Required newsletter module was not found.');
    }

    $settings = json_decode($module['setting'], true);

    if (!is_array($settings) || empty($settings['status'])) {
        throw new RuntimeException('Required newsletter module is not active.');
    }

    return (int) $module['module_id'];
}

function aboutAssignModules(mysqli $db, $layoutId, array $moduleIds)
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

function aboutUpdateInformation(mysqli $db, array $page, $merchantHtml)
{
    $informationId = (int) $page['information_id'];
    $layoutId = (int) $page['layout_id'];
    $slug = $page['slug'];
    $check = $db->prepare(
        'SELECT id.information_id
         FROM ' . DB_PREFIX . 'information_description id
         INNER JOIN ' . DB_PREFIX . 'seo_url su
           ON su.query = CONCAT(\'information_id=\', id.information_id)
          AND su.store_id = 0
          AND su.language_id = 3
         WHERE id.information_id = ?
           AND id.language_id = 3
           AND su.keyword = ?
         LIMIT 1
         FOR UPDATE'
    );
    $check->bind_param('is', $informationId, $slug);
    $check->execute();
    $existing = $check->get_result()->fetch_assoc();
    $check->close();

    if (!$existing) {
        throw new RuntimeException('Existing O nama page was not found at the expected ID and slug.');
    }

    $update = $db->prepare(
        'UPDATE ' . DB_PREFIX . 'information_description
         SET title = ?, description = ?, meta_title = ?, meta_description = ?, meta_keyword = \'\'
         WHERE information_id = ? AND language_id = 3'
    );
    $title = $page['title'];
    $metaTitle = $page['meta_title'];
    $metaDescription = $page['meta_description'];
    $update->bind_param('ssssi', $title, $merchantHtml, $metaTitle, $metaDescription, $informationId);
    $update->execute();
    $update->close();

    $updateInformation = $db->prepare(
        'UPDATE ' . DB_PREFIX . 'information
         SET status = 1
         WHERE information_id = ?'
    );
    $updateInformation->bind_param('i', $informationId);
    $updateInformation->execute();
    $updateInformation->close();

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
}

function aboutRemoveCustomCssLoader(mysqli $db)
{
    $startMarker = '/* DRYZEN_ABOUT_UX_START */';
    $endMarker = '/* DRYZEN_ABOUT_UX_END */';
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

    $newCss = rtrim($baseCss) . "\n";

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

function aboutNormalizeText($text)
{
    $text = html_entity_decode(strip_tags((string) $text), ENT_QUOTES, 'UTF-8');
    $text = preg_replace('/\s+/u', ' ', $text);

    return trim($text);
}

function aboutAuditText(array $content, $html)
{
    $expected = array(
        $content['hero']['eyebrow'],
        $content['hero']['title'],
        $content['overview']['title'],
        $content['overview']['why_title'],
        $content['routine']['title'],
        $content['reasons']['title'],
        $content['reasons']['intro'],
        $content['reasons']['list_title'],
        $content['reasons']['closing'],
        $content['reasons']['fit_title'],
        $content['story']['title'],
        $content['development']['title'],
        $content['merchant']['eyebrow'],
        $content['merchant']['title'],
        $content['merchant']['note'],
    );

    foreach (array('paragraphs', 'why_paragraphs') as $key) {
        if (!empty($content['overview'][$key])) {
            $expected = array_merge($expected, $content['overview'][$key]);
        }
    }

    $expected = array_merge(
        $expected,
        $content['routine']['paragraphs'],
        $content['focus']['title_lines'],
        $content['focus']['paragraphs'],
        $content['reasons']['items'],
        $content['reasons']['fit_paragraphs'],
        $content['story']['paragraphs'],
        $content['development']['paragraphs']
    );

    foreach ($content['merchant']['items'] as $item) {
        $expected[] = $item['label'];
        $expected[] = $item['value'];
    }

    $visibleText = aboutNormalizeText($html);
    $normalizedExpected = array();

    foreach ($expected as $text) {
        $normalizedExpected[] = aboutNormalizeText($text);
    }

    $missing = array();

    foreach (array_count_values($normalizedExpected) as $text => $requiredCount) {
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
    $page = $content['page'];
    $informationId = (int) $page['information_id'];
    $layoutId = (int) $page['layout_id'];
    $backupFile = aboutSaveBackup($db, $projectRoot, $informationId, $layoutId);
    $db->begin_transaction();

    $sections = array(
        array('name' => 'DryZen About Hero', 'html' => aboutRenderHero($content['hero']), 'full_width' => true),
        array('name' => 'DryZen About Overview', 'html' => aboutRenderOverview($content['overview']), 'full_width' => false),
        array('name' => 'DryZen About Routine', 'html' => aboutRenderRoutine($content['routine']), 'full_width' => true),
        array('name' => 'DryZen About Focus', 'html' => aboutRenderFocus($content['focus']), 'full_width' => true),
        array('name' => 'DryZen About Reasons', 'html' => aboutRenderReasons($content['reasons']), 'full_width' => false),
        array('name' => 'DryZen About Story', 'html' => aboutRenderStory($content['story']), 'full_width' => false),
        array('name' => 'DryZen About Development', 'html' => aboutRenderDevelopment($content['development']), 'full_width' => true),
        array('name' => 'DryZen About Merchant', 'html' => aboutRenderMerchant($content['merchant']), 'full_width' => false),
    );

    $allHtml = '';
    $moduleIds = array();

    foreach ($sections as $section) {
        $allHtml .= $section['html'];
        $moduleIds[] = aboutUpsertModule(
            $db,
            $section['name'],
            aboutModuleSettings($section['name'], $section['html'], $section['full_width'])
        );
    }

    $textCount = aboutAuditText($content, $allHtml);
    $merchantHtml = $sections[7]['html'];
    aboutUpdateInformation($db, $page, $merchantHtml);
    $moduleIds[] = aboutFindNewsletterModuleId($db);
    aboutAssignModules($db, $layoutId, $moduleIds);

    aboutRemoveCustomCssLoader($db);

    $db->commit();

    echo "O nama page applied successfully.\n";
    echo 'URL: /' . $page['slug'] . "\n";
    echo 'Information ID: ' . $informationId . "\n";
    echo 'Layout ID: ' . $layoutId . "\n";
    echo 'Module IDs: ' . implode(', ', $moduleIds) . "\n";
    echo 'Audited text entries: ' . $textCount . "\n";
    echo 'Backup: ' . $backupFile . "\n";
} catch (Throwable $exception) {
    $db->rollback();
    fwrite(STDERR, $exception->getMessage() . "\n");
    exit(1);
} finally {
    $db->close();
}
