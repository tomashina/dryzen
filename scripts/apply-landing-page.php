<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the command line.');
}

$projectRoot = dirname(__DIR__);
$configFile = $projectRoot . '/upload/config.php';
$contentFile = $projectRoot . '/database/content/hr/landing-page.php';
$styleFile = $projectRoot . '/database/content/hr/landing-page.css';

if (!is_file($configFile)) {
    fwrite(STDERR, "Missing upload/config.php\n");
    exit(1);
}

if (!is_file($contentFile)) {
    fwrite(STDERR, "Missing landing page content file\n");
    exit(1);
}

if (!is_file($styleFile)) {
    fwrite(STDERR, "Missing landing page style file\n");
    exit(1);
}

require_once $configFile;
$content = require $contentFile;
$landingCss = file_get_contents($styleFile);

if ($landingCss === false) {
    fwrite(STDERR, "Unable to read landing page style file\n");
    exit(1);
}

$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, (int) DB_PORT);

if ($db->connect_errno) {
    fwrite(STDERR, "Database connection failed: " . $db->connect_error . "\n");
    exit(1);
}

$db->set_charset('utf8mb4');

function languageValues($croatian, $english = '')
{
    return array(
        3 => $croatian,
        1 => $english,
    );
}

function baseBlockSettings($useTitle, $title, $subtitle, $marginBottom = 70, $fullWidth = 0)
{
    return array(
        'title' => $useTitle ? 1 : 0,
        'title_pl' => languageValues(''),
        'title_m' => languageValues($title),
        'title_b' => languageValues($subtitle),
        'custom_m' => 0,
        'mt' => '',
        'mr' => '',
        'mb' => (string) $marginBottom,
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

function baseContentSettings($equalHeight = 0)
{
    return array(
        'fw' => 0,
        'block_css' => 0,
        'css' => '',
        'nm' => 0,
        'eh' => $equalHeight ? 1 : 0,
    );
}

function htmlColumn($html, $small = 'col-xs-12', $medium = 'col-sm-12', $large = 'col-md-12', $position = 'vertical-top text-left')
{
    return array(
        'w' => 'custom',
        'w_sm' => $small,
        'w_md' => $medium,
        'w_lg' => $large,
        'type' => 'html',
        'data1' => languageValues($html),
        'data2' => '',
        'data3' => languageValues(''),
        'data4' => '',
        'data5' => '',
        'data6' => '',
        'data7' => $position,
        'data8' => '',
    );
}

function imageColumn($image, $link, $overlayHtml, $small, $medium, $large, $position = 'vertical-bottom text-left')
{
    return array(
        'w' => 'custom',
        'w_sm' => $small,
        'w_md' => $medium,
        'w_lg' => $large,
        'type' => 'img',
        'data1' => languageValues($overlayHtml),
        'data2' => $image,
        'data3' => languageValues(''),
        'data5' => $link,
        'data6' => '',
        'data7' => $position,
        'data8' => '',
    );
}

function moduleSettings($name, array $blockSettings, array $contentSettings, array $columns)
{
    return array(
        'save' => 'stay',
        'name' => $name,
        'status' => 1,
        'b_setting' => $blockSettings,
        'bg_image' => '',
        'c_setting' => $contentSettings,
        'columns' => $columns,
    );
}

function saveBackup(mysqli $db, $projectRoot)
{
    $backup = array(
        'created_at' => date(DATE_ATOM),
        'layout_modules' => array(),
        'modules' => array(),
        'settings' => array(),
    );

    $layoutQuery = $db->query(
        'SELECT * FROM ' . DB_PREFIX . 'layout_module
         WHERE layout_id = 1
         ORDER BY position, sort_order, layout_module_id'
    );

    while ($row = $layoutQuery->fetch_assoc()) {
        $backup['layout_modules'][] = $row;
    }

    $moduleQuery = $db->query(
        "SELECT * FROM " . DB_PREFIX . "module
         WHERE code IN ('basel_content', 'basel_products')
         ORDER BY module_id"
    );

    while ($row = $moduleQuery->fetch_assoc()) {
        $backup['modules'][] = $row;
    }

    $settingQuery = $db->query(
        "SELECT * FROM " . DB_PREFIX . "setting
         WHERE store_id = 0
           AND code = 'basel'
           AND `key` = 'basel_custom_css'
         ORDER BY setting_id"
    );

    while ($row = $settingQuery->fetch_assoc()) {
        $backup['settings'][] = $row;
    }

    $backupDirectory = $projectRoot . '/.local-backup';

    if (!is_dir($backupDirectory) && !mkdir($backupDirectory, 0775, true) && !is_dir($backupDirectory)) {
        throw new RuntimeException('Unable to create backup directory.');
    }

    $backupFile = $backupDirectory . '/landing-before-' . date('Ymd-His') . '.json';
    $encoded = json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($encoded === false || file_put_contents($backupFile, $encoded) === false) {
        throw new RuntimeException('Unable to create landing backup.');
    }

    return $backupFile;
}

function upsertLandingCss(mysqli $db, $landingCss)
{
    $startMarker = '/* DRYZEN_LANDING_UX_START */';
    $endMarker = '/* DRYZEN_LANDING_UX_END */';
    $select = $db->prepare(
        'SELECT setting_id, value
         FROM ' . DB_PREFIX . 'setting
         WHERE store_id = 0
           AND code = ?
           AND `key` = ?
         ORDER BY setting_id
         LIMIT 1
         FOR UPDATE'
    );
    $code = 'basel';
    $key = 'basel_custom_css';
    $select->bind_param('ss', $code, $key);
    $select->execute();
    $result = $select->get_result();
    $existing = $result->fetch_assoc();
    $select->close();

    $currentCss = $existing ? $existing['value'] : '';
    $pattern = '~\R?' . preg_quote($startMarker, '~') . '.*?' . preg_quote($endMarker, '~') . '\R?~s';
    $baseCss = preg_replace($pattern, "\n", $currentCss);

    if ($baseCss === null) {
        throw new RuntimeException('Unable to prepare landing page CSS.');
    }

    $newCss = rtrim($baseCss) . "\n\n" . $startMarker . "\n" . trim($landingCss) . "\n" . $endMarker . "\n";

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

function upsertModule(mysqli $db, $code, $name, array $settings)
{
    $select = $db->prepare(
        'SELECT module_id FROM ' . DB_PREFIX . 'module
         WHERE code = ? AND name = ?
         ORDER BY module_id
         LIMIT 1
         FOR UPDATE'
    );
    $select->bind_param('ss', $code, $name);
    $select->execute();
    $result = $select->get_result();
    $existing = $result->fetch_assoc();
    $select->close();

    $json = json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($json === false) {
        throw new RuntimeException('Unable to encode module settings for ' . $name);
    }

    if ($existing) {
        $moduleId = (int) $existing['module_id'];
        $update = $db->prepare(
            'UPDATE ' . DB_PREFIX . 'module
             SET setting = ?
             WHERE module_id = ?'
        );
        $update->bind_param('si', $json, $moduleId);
        $update->execute();
        $update->close();

        return $moduleId;
    }

    $insert = $db->prepare(
        'INSERT INTO ' . DB_PREFIX . 'module (name, code, setting)
         VALUES (?, ?, ?)'
    );
    $insert->bind_param('sss', $name, $code, $json);
    $insert->execute();
    $moduleId = (int) $insert->insert_id;
    $insert->close();

    return $moduleId;
}

function findModuleId(mysqli $db, $code, $name)
{
    $select = $db->prepare(
        'SELECT module_id FROM ' . DB_PREFIX . 'module
         WHERE code = ? AND name = ?
         ORDER BY module_id
         LIMIT 1'
    );
    $select->bind_param('ss', $code, $name);
    $select->execute();
    $result = $select->get_result();
    $module = $result->fetch_assoc();
    $select->close();

    if (!$module) {
        throw new RuntimeException('Required module was not found: ' . $name);
    }

    return (int) $module['module_id'];
}

$hero = moduleSettings(
    'DryZen Hero',
    baseBlockSettings(false, '', '', 96, 1),
    baseContentSettings(false),
    array(
        imageColumn(
            'catalog/landing-2026/hero.webp',
            '#sto-zelis-rijesiti',
            '<span class="hover-zoom"><span class="sr-only">DryZen proizvodi za žene, muškarce, aktivne osobe i djecu</span></span>',
            'hidden-xs',
            'col-sm-6',
            'col-md-6',
            'vertical-middle text-center'
        ),
        imageColumn(
            'catalog/landing-2026/hero.webp',
            '#sto-zelis-rijesiti',
            '<span class="hover-zoom"><span class="sr-only">DryZen proizvodi za žene, muškarce, aktivne osobe i djecu</span></span>',
            'col-xs-12',
            'hidden-sm',
            'hidden-md hidden-lg',
            'vertical-middle text-center'
        ),
        htmlColumn($content['hero'], 'col-xs-12', 'col-sm-6', 'col-md-6', 'vertical-middle text-left'),
    )
);

$whatIsDryzen = moduleSettings(
    'DryZen About',
    baseBlockSettings(true, 'Što je DryZen?', '', 96),
    baseContentSettings(false),
    array(
        htmlColumn($content['what_is_dryzen']),
    )
);

$needs = moduleSettings(
    'DryZen Categories',
    baseBlockSettings(true, 'Što želiš riješiti?', 'Odaberi područje za koje tražiš rješenje.', 96),
    baseContentSettings(false),
    array(
        htmlColumn($content['need_grid']),
    )
);

$howItWorks = moduleSettings(
    'DryZen Approach',
    baseBlockSettings(true, 'Kako funkcionira DryZen?', 'Jednostavna rutina u tri koraka.', 96),
    baseContentSettings(false),
    array(
        htmlColumn($content['how_it_works']),
    )
);

$whyChoose = moduleSettings(
    'DryZen Why Choose',
    baseBlockSettings(true, 'Zašto odabrati DryZen?', '', 96, 1),
    baseContentSettings(false),
    array(
        htmlColumn($content['why_choose']),
    )
);

$faq = moduleSettings(
    'DryZen FAQ',
    baseBlockSettings(true, 'Često postavljena pitanja', '', 96),
    baseContentSettings(false),
    array(
        htmlColumn($content['faq']),
    )
);

$productFinder = moduleSettings(
    'DryZen Product Finder',
    baseBlockSettings(true, 'Pronađi pravi DryZen proizvod', '', 96, 1),
    baseContentSettings(false),
    array(
        imageColumn(
            'catalog/landing-2026/product-finder.webp',
            '#sto-zelis-rijesiti',
            '<span class="hover-zoom"><span class="sr-only">DryZen proizvodi za različite potrebe i životne situacije</span></span>',
            'col-xs-12',
            'col-sm-6',
            'col-md-6',
            'vertical-middle text-center'
        ),
        htmlColumn($content['product_finder'], 'col-xs-12', 'col-sm-6', 'col-md-6', 'vertical-middle text-left'),
    )
);

try {
    $backupFile = saveBackup($db, $projectRoot);
    $db->begin_transaction();
    upsertLandingCss($db, $landingCss);

    $moduleIds = array(
        'hero' => upsertModule($db, 'basel_content', 'DryZen Hero', $hero),
        'about' => upsertModule($db, 'basel_content', 'DryZen About', $whatIsDryzen),
        'categories' => upsertModule($db, 'basel_content', 'DryZen Categories', $needs),
        'approach' => upsertModule($db, 'basel_content', 'DryZen Approach', $howItWorks),
        'why' => upsertModule($db, 'basel_content', 'DryZen Why Choose', $whyChoose),
        'faq' => upsertModule($db, 'basel_content', 'DryZen FAQ', $faq),
        'finder' => upsertModule($db, 'basel_content', 'DryZen Product Finder', $productFinder),
        'products' => findModuleId($db, 'basel_products', 'Novo u ponudi'),
        'newsletter' => findModuleId($db, 'basel_content', 'Newsletter Signup'),
    );

    $layoutQuery = $db->query(
        "SELECT l.layout_id
         FROM " . DB_PREFIX . "layout l
         INNER JOIN " . DB_PREFIX . "layout_route lr ON lr.layout_id = l.layout_id
         WHERE lr.route = 'common/home'
         ORDER BY l.layout_id
         LIMIT 1
         FOR UPDATE"
    );
    $layout = $layoutQuery->fetch_assoc();

    if (!$layout) {
        throw new RuntimeException('Home layout was not found.');
    }

    $layoutId = (int) $layout['layout_id'];
    $delete = $db->prepare(
        'DELETE FROM ' . DB_PREFIX . 'layout_module
         WHERE layout_id = ? AND position = ?'
    );
    $position = 'top';
    $delete->bind_param('is', $layoutId, $position);
    $delete->execute();
    $delete->close();

    $orderedModules = array(
        'basel_content.' . $moduleIds['hero'],
        'basel_content.' . $moduleIds['about'],
        'basel_content.' . $moduleIds['categories'],
        'basel_content.' . $moduleIds['approach'],
        'basel_content.' . $moduleIds['why'],
        'basel_content.' . $moduleIds['faq'],
        'basel_content.' . $moduleIds['finder'],
        'basel_products.' . $moduleIds['products'],
        'basel_content.' . $moduleIds['newsletter'],
    );

    $insert = $db->prepare(
        'INSERT INTO ' . DB_PREFIX . 'layout_module (layout_id, code, position, sort_order)
         VALUES (?, ?, ?, ?)'
    );

    foreach ($orderedModules as $index => $code) {
        $sortOrder = $index + 1;
        $insert->bind_param('issi', $layoutId, $code, $position, $sortOrder);
        $insert->execute();
    }

    $insert->close();
    $db->commit();

    echo "Landing page modules applied successfully.\n";
    echo "Backup: " . $backupFile . "\n";
    echo "Module IDs: " . json_encode($moduleIds, JSON_UNESCAPED_SLASHES) . "\n";
} catch (Throwable $exception) {
    $db->rollback();
    fwrite(STDERR, $exception->getMessage() . "\n");
    exit(1);
} finally {
    $db->close();
}
