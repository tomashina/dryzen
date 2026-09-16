<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the command line.');
}

$projectRoot = dirname(__DIR__);
$configFile = $projectRoot . '/upload/config.php';
$croatianContentFile = $projectRoot . '/database/content/hr/landing-page.php';
$englishContentFile = $projectRoot . '/database/content/en/landing-page.php';

foreach (array($configFile, $croatianContentFile, $englishContentFile) as $requiredFile) {
    if (!is_file($requiredFile)) {
        fwrite(STDERR, 'Missing required file: ' . $requiredFile . "\n");
        exit(1);
    }
}

require_once $configFile;
$contentHr = require $croatianContentFile;
$contentEn = require $englishContentFile;

$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, (int) DB_PORT);

if ($db->connect_errno) {
    fwrite(STDERR, 'Database connection failed: ' . $db->connect_error . "\n");
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

function baseBlockSettings(
    $useTitle,
    $croatianTitle,
    $englishTitle,
    $croatianSubtitle = '',
    $englishSubtitle = '',
    $marginBottom = 92,
    $fullWidth = 0
) {
    return array(
        'title' => $useTitle ? 1 : 0,
        'title_pl' => languageValues(''),
        'title_m' => languageValues($croatianTitle, $englishTitle),
        'title_b' => languageValues($croatianSubtitle, $englishSubtitle),
        'custom_m' => 1,
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

function baseContentSettings($fullWidth = 0, $noMargin = 0, $equalHeight = 0)
{
    return array(
        'fw' => $fullWidth ? 1 : 0,
        'block_css' => 0,
        'css' => '',
        'nm' => $noMargin ? 1 : 0,
        'eh' => $equalHeight ? 1 : 0,
    );
}

function htmlColumn(
    $croatianHtml,
    $englishHtml,
    $small = 'col-xs-12',
    $medium = 'col-sm-12',
    $large = 'col-md-12',
    $position = 'vertical-top text-left'
) {
    return array(
        'w' => 'custom',
        'w_sm' => $small,
        'w_md' => $medium,
        'w_lg' => $large,
        'type' => 'html',
        'data1' => languageValues($croatianHtml, $englishHtml),
        'data2' => '',
        'data3' => languageValues(''),
        'data4' => '',
        'data5' => '',
        'data6' => '',
        'data7' => $position,
        'data8' => '',
    );
}

function imageColumn(
    $image,
    $link,
    $croatianAlt,
    $englishAlt,
    $small,
    $medium,
    $large,
    $position = 'vertical-middle text-center'
) {
    return array(
        'w' => 'custom',
        'w_sm' => $small,
        'w_md' => $medium,
        'w_lg' => $large,
        'type' => 'img',
        'data1' => languageValues(
            '<span class="hover-zoom"><span class="sr-only">' . $croatianAlt . '</span></span>',
            '<span class="hover-zoom"><span class="sr-only">' . $englishAlt . '</span></span>'
        ),
        'data2' => $image,
        'data3' => languageValues(''),
        'data4' => '',
        'data5' => $link,
        'data6' => '',
        'data7' => $position,
        'data8' => '',
    );
}

function testimonialColumn($limit = 4, $columns = 3)
{
    return array(
        'w' => 'custom',
        'w_sm' => 'col-xs-12',
        'w_md' => 'col-sm-12',
        'w_lg' => 'col-md-12',
        'type' => 'tm',
        'data1' => (string) $limit,
        'data2' => '',
        'data3' => languageValues(''),
        'data4' => '',
        'data5' => '',
        'data6' => '',
        'data7' => (string) $columns,
        'data8' => 'block',
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
         WHERE layout_id IN (
           SELECT layout_id FROM ' . DB_PREFIX . "layout_route WHERE route = 'common/home'
         )
         ORDER BY position, sort_order, layout_module_id"
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
           AND `key` IN (
             'basel_custom_css',
             'basel_footer_columns',
             'footer_block_1',
             'footer_block_2',
             'footer_block_title',
             'overwrite_footer_links'
           )
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

function removeLegacyLandingCss(mysqli $db)
{
    $startMarker = '/* DRYZEN_LANDING_UX_START */';
    $endMarker = '/* DRYZEN_LANDING_UX_END */';
    $select = $db->prepare(
        'SELECT setting_id, value
         FROM ' . DB_PREFIX . 'setting
         WHERE store_id = 0 AND code = ? AND `key` = ?
         ORDER BY setting_id LIMIT 1 FOR UPDATE'
    );
    $code = 'basel';
    $key = 'basel_custom_css';
    $select->bind_param('ss', $code, $key);
    $select->execute();
    $existing = $select->get_result()->fetch_assoc();
    $select->close();

    $currentCss = $existing ? $existing['value'] : '';
    $pattern = '~\R?' . preg_quote($startMarker, '~') . '.*?' . preg_quote($endMarker, '~') . '\R?~s';
    $baseCss = preg_replace($pattern, "\n", $currentCss);

    if ($baseCss === null) {
        throw new RuntimeException('Unable to prepare landing page CSS.');
    }

    $legacyStart = strpos($baseCss, '#mod2 .type-img,');
    $legacyEnd = strpos($baseCss, '/* DRYZEN_NEED_HANDS_UX_START */');

    if ($legacyStart !== false && $legacyEnd !== false && $legacyEnd > $legacyStart) {
        $baseCss = substr($baseCss, 0, $legacyStart) . "\n\n" . substr($baseCss, $legacyEnd);
    }

    upsertSetting($db, 'basel', 'basel_custom_css', trim($baseCss) . "\n", 0);
}

function upsertSetting(mysqli $db, $code, $key, $value, $serialized)
{
    $select = $db->prepare(
        'SELECT setting_id FROM ' . DB_PREFIX . 'setting
         WHERE store_id = 0 AND code = ? AND `key` = ?
         ORDER BY setting_id LIMIT 1 FOR UPDATE'
    );
    $select->bind_param('ss', $code, $key);
    $select->execute();
    $existing = $select->get_result()->fetch_assoc();
    $select->close();

    if ($existing) {
        $settingId = (int) $existing['setting_id'];
        $update = $db->prepare(
            'UPDATE ' . DB_PREFIX . 'setting SET value = ?, serialized = ? WHERE setting_id = ?'
        );
        $update->bind_param('sii', $value, $serialized, $settingId);
        $update->execute();
        $update->close();
        return;
    }

    $insert = $db->prepare(
        'INSERT INTO ' . DB_PREFIX . 'setting (store_id, code, `key`, value, serialized)
         VALUES (0, ?, ?, ?, ?)'
    );
    $insert->bind_param('sssi', $code, $key, $value, $serialized);
    $insert->execute();
    $insert->close();
}

function upsertJsonSetting(mysqli $db, $key, $value)
{
    $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($json === false) {
        throw new RuntimeException('Unable to encode setting: ' . $key);
    }

    upsertSetting($db, 'basel', $key, $json, 1);
}

function upsertModule(mysqli $db, $code, $name, array $settings)
{
    $select = $db->prepare(
        'SELECT module_id FROM ' . DB_PREFIX . 'module
         WHERE code = ? AND name = ? ORDER BY module_id LIMIT 1 FOR UPDATE'
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
        $update = $db->prepare('UPDATE ' . DB_PREFIX . 'module SET setting = ? WHERE module_id = ?');
        $update->bind_param('si', $json, $moduleId);
        $update->execute();
        $update->close();
        return $moduleId;
    }

    $insert = $db->prepare('INSERT INTO ' . DB_PREFIX . 'module (name, code, setting) VALUES (?, ?, ?)');
    $insert->bind_param('sss', $name, $code, $json);
    $insert->execute();
    $moduleId = (int) $insert->insert_id;
    $insert->close();
    return $moduleId;
}

function footerLink($sort, $croatianTitle, $englishTitle, $croatianTarget, $englishTarget)
{
    return array(
        'sort' => (string) $sort,
        'title' => languageValues($croatianTitle, $englishTitle),
        'target' => languageValues($croatianTarget, $englishTarget),
    );
}

function invalidateLandingCaches($layoutId, array $moduleIds)
{
    if (!defined('DIR_CACHE') || !is_dir(DIR_CACHE)) {
        return;
    }

    $patterns = array(
        DIR_CACHE . 'cache.layout.modules.' . (int) $layoutId . '.*',
        DIR_CACHE . 'cache.dryzen.page.*',
        DIR_CACHE . 'cache.dryzen.page.version.*',
    );

    foreach ($moduleIds as $moduleId) {
        $patterns[] = DIR_CACHE . 'cache.module.catalog.' . (int) $moduleId . '.*';
    }

    foreach ($patterns as $pattern) {
        $files = glob($pattern);

        if (!$files) {
            continue;
        }

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}

$modules = array(
    'hero' => moduleSettings(
        'DryZen Hero',
        baseBlockSettings(false, '', '', '', '', 92, 1),
        baseContentSettings(false),
        array(
            imageColumn(
                'catalog/landing-2026/hero.webp',
                '#pronadji-dryzen',
                'DryZen proizvodi za žene, muškarce, aktivne osobe i djecu',
                'DryZen products for women, men, active people and children',
                'hidden-xs',
                'col-sm-6',
                'col-md-6'
            ),
            imageColumn(
                'catalog/landing-2026/hero.webp',
                '#pronadji-dryzen',
                'DryZen proizvodi za žene, muškarce, aktivne osobe i djecu',
                'DryZen products for women, men, active people and children',
                'col-xs-12',
                'hidden-sm',
                'hidden-md hidden-lg'
            ),
            htmlColumn($contentHr['hero'], $contentEn['hero'], 'col-xs-12', 'col-sm-6', 'col-md-6', 'vertical-middle text-left'),
        )
    ),
    'products_intro' => moduleSettings(
        'DryZen Product Categories',
        baseBlockSettings(true, 'Pronađite DryZen za svoje potrebe', 'Find the DryZen that fits your needs'),
        baseContentSettings(false),
        array(htmlColumn($contentHr['product_intro'], $contentEn['product_intro']))
    ),
    'how' => moduleSettings(
        'DryZen How It Works',
        baseBlockSettings(true, 'Kako funkcionira DryZen antiperspirant?', 'How does DryZen antiperspirant work?', '', '', 92, 1),
        baseContentSettings(false),
        array(htmlColumn($contentHr['how_it_works'], $contentEn['how_it_works']))
    ),
    'evening' => moduleSettings(
        'DryZen Evening Routine',
        baseBlockSettings(true, 'Zašto se DryZen koristi navečer?', 'Why is DryZen used in the evening?'),
        baseContentSettings(false),
        array(htmlColumn($contentHr['why_evening'], $contentEn['why_evening']))
    ),
    'comparison' => moduleSettings(
        'DryZen Deodorant Comparison',
        baseBlockSettings(true, 'Dezodorans ili antiperspirant?', 'Deodorant or antiperspirant?', '', '', 92, 1),
        baseContentSettings(false),
        array(htmlColumn($contentHr['comparison'], $contentEn['comparison']))
    ),
    'chooser' => moduleSettings(
        'DryZen Product Chooser',
        baseBlockSettings(true, 'Koji DryZen odabrati?', 'Which DryZen should you choose?'),
        baseContentSettings(false),
        array(htmlColumn($contentHr['product_chooser'], $contentEn['product_chooser']))
    ),
    'emotional' => moduleSettings(
        'DryZen Emotional Story',
        baseBlockSettings(false, '', '', '', '', 92, 1),
        baseContentSettings(false),
        array(htmlColumn($contentHr['emotional'], $contentEn['emotional']))
    ),
    'why' => moduleSettings(
        'DryZen Why DryZen',
        baseBlockSettings(true, 'Zašto DryZen?', 'Why DryZen?', '', '', 92, 1),
        baseContentSettings(false),
        array(htmlColumn($contentHr['why_dryzen'], $contentEn['why_dryzen']))
    ),
    'product_cta' => moduleSettings(
        'DryZen Product CTA',
        baseBlockSettings(
            true,
            'Vaš problem. Vaš proizvod. Vaš DryZen.',
            'Your concern. Your product. Your DryZen.',
            'Odaberite ciljano rješenje za područje koje želite držati pod kontrolom.',
            'Choose a targeted solution for the area you want to keep under control.'
        ),
        baseContentSettings(false),
        array(htmlColumn($contentHr['product_cta'], $contentEn['product_cta']))
    ),
    'reviews' => moduleSettings(
        'DryZen Reviews',
        baseBlockSettings(
            true,
            'Iskustva koja govore više od obećanja.',
            'Experiences that say more than promises.',
            'Stvarna iskustva korisnika koji su DryZen uključili u svoju svakodnevnu rutinu.<br><a href="/proizvodi">Pogledaj sve proizvode</a>',
            'Real experiences from customers who made DryZen part of their routine.<br><a href="/shop">View all products</a>',
            92,
            1
        ),
        baseContentSettings(false),
        array(testimonialColumn(4, 3))
    ),
    'faq' => moduleSettings(
        'DryZen FAQ',
        baseBlockSettings(true, 'Često postavljana pitanja', 'Frequently asked questions'),
        baseContentSettings(false),
        array(htmlColumn($contentHr['faq'], $contentEn['faq']))
    ),
    'final' => moduleSettings(
        'DryZen Final Banner',
        baseBlockSettings(false, '', '', '', '', 92, 1),
        baseContentSettings(false),
        array(htmlColumn($contentHr['final_banner'], $contentEn['final_banner']))
    ),
);

$footerColumns = array(
    '1' => array(
        'sort' => '1',
        'title' => languageValues('Proizvodi', 'Products'),
        'links' => array(
            '1' => footerLink(1, 'Roll-On', 'Roll-On', 'znoje-mi-se-pazusi', 'sweaty-underarms'),
            '2' => footerLink(2, 'Maramice za ruke', 'Hand Wipes', 'znoje-mi-se-dlanovi', 'sweaty-palms'),
            '3' => footerLink(3, 'Maramice za stopala', 'Foot Wipes', 'znoje-mi-se-stopala', 'sweaty-feet'),
            '4' => footerLink(4, 'Women', 'Women', 'women', 'women'),
            '5' => footerLink(5, 'Men', 'Men', 'men', 'men'),
            '6' => footerLink(6, 'Sport', 'Sport', 'sport', 'sport'),
            '7' => footerLink(7, 'Kids', 'Kids', 'kids', 'kids'),
        ),
    ),
    '2' => array(
        'sort' => '2',
        'title' => languageValues('DryZen', 'DryZen'),
        'links' => array(
            '1' => footerLink(1, 'Kako funkcionira', 'How it works', '/#kako-funkcionira', '/#how-it-works'),
            '2' => footerLink(2, 'O DryZenu', 'About DryZen', 'o-nama', 'about-us'),
            '3' => footerLink(3, 'Česta pitanja', 'Frequently asked questions', '/#cesta-pitanja', '/#frequently-asked-questions'),
            '4' => footerLink(4, 'Kontakt', 'Contact', 'kontakt', 'contact'),
        ),
    ),
    '3' => array(
        'sort' => '3',
        'title' => languageValues('Pomoć i informacije', 'Help and information'),
        'links' => array(
            '1' => footerLink(1, 'Uvjeti kupnje', 'Terms of purchase', 'opci-uvjeti-koristenja', 'general-terms-of-use'),
            '2' => footerLink(2, 'Dostava i plaćanje', 'Delivery and payment', 'nacini-placanja-i-dostava', 'payment-and-delivery'),
            '3' => footerLink(3, 'Povrat i reklamacije', 'Returns and complaints', 'povrat-robe-zamjena-i-reklamacije', 'returns-exchanges-and-complaints'),
            '4' => footerLink(4, 'Politika privatnosti', 'Privacy policy', 'pravila-privatnosti', 'privacy-policy'),
            '5' => footerLink(5, 'Politika kolačića', 'Cookie policy', '#cookie-settings', '#cookie-settings'),
        ),
    ),
);

$footerBrand = languageValues(
    '<div class="dryzen-footer-brand"><strong>DRYZEN</strong><span>Kontroliranije znojenje. Više slobode u svakodnevnom životu.</span></div>',
    '<div class="dryzen-footer-brand"><strong>DRYZEN</strong><span>More controlled sweating. More freedom in everyday life.</span></div>'
);

$footerContact = languageValues(
    '<div class="dryzen-footer-contact"><p><a href="mailto:info@dryzen.eu">info@dryzen.eu</a><br><a href="https://dryzen.eu/">DryZen.eu</a></p><div class="dryzen-footer-socials"><a href="https://www.instagram.com/dryzen.eu" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fa fa-instagram" aria-hidden="true"></i></a><a href="https://www.tiktok.com/@dryzen.eu" target="_blank" rel="noopener noreferrer" aria-label="TikTok"><i class="fa fa-tiktok" aria-hidden="true"></i></a><a href="https://www.facebook.com/profile.php?id=61589608989938" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a></div><p>GORDOM USLUGE d.o.o.<br>Wickerhauserova ulica 52, 10000 Zagreb<br>OIB: 59379806135<br>Tel: +385 98 177 7049</p></div>',
    '<div class="dryzen-footer-contact"><p><a href="mailto:info@dryzen.eu">info@dryzen.eu</a><br><a href="https://dryzen.eu/">DryZen.eu</a></p><div class="dryzen-footer-socials"><a href="https://www.instagram.com/dryzen.eu" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fa fa-instagram" aria-hidden="true"></i></a><a href="https://www.tiktok.com/@dryzen.eu" target="_blank" rel="noopener noreferrer" aria-label="TikTok"><i class="fa fa-tiktok" aria-hidden="true"></i></a><a href="https://www.facebook.com/profile.php?id=61589608989938" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a></div><p>GORDOM USLUGE d.o.o.<br>Wickerhauserova ulica 52, 10000 Zagreb, Croatia<br>Company ID: 59379806135<br>Phone: +385 98 177 7049</p></div>'
);

try {
    $backupFile = saveBackup($db, $projectRoot);
    $db->begin_transaction();

    removeLegacyLandingCss($db);
    upsertJsonSetting($db, 'basel_footer_columns', $footerColumns);
    upsertJsonSetting($db, 'footer_block_1', $footerBrand);
    upsertJsonSetting($db, 'footer_block_2', $footerContact);
    upsertJsonSetting($db, 'footer_block_title', languageValues('Kontakt', 'Contact'));
    upsertSetting($db, 'basel', 'overwrite_footer_links', '1', 0);

    $moduleIds = array();
    foreach ($modules as $key => $settings) {
        $moduleIds[$key] = upsertModule($db, 'basel_content', $settings['name'], $settings);
    }

    $layoutQuery = $db->query(
        "SELECT l.layout_id
         FROM " . DB_PREFIX . "layout l
         INNER JOIN " . DB_PREFIX . "layout_route lr ON lr.layout_id = l.layout_id
         WHERE lr.route = 'common/home'
         ORDER BY l.layout_id LIMIT 1 FOR UPDATE"
    );
    $layout = $layoutQuery->fetch_assoc();

    if (!$layout) {
        throw new RuntimeException('Home layout was not found.');
    }

    $layoutId = (int) $layout['layout_id'];
    $delete = $db->prepare('DELETE FROM ' . DB_PREFIX . 'layout_module WHERE layout_id = ? AND position = ?');
    $position = 'top';
    $delete->bind_param('is', $layoutId, $position);
    $delete->execute();
    $delete->close();

    $insert = $db->prepare(
        'INSERT INTO ' . DB_PREFIX . 'layout_module (layout_id, code, position, sort_order)
         VALUES (?, ?, ?, ?)'
    );

    foreach (array_keys($modules) as $index => $key) {
        $code = 'basel_content.' . $moduleIds[$key];
        $sortOrder = $index + 1;
        $insert->bind_param('issi', $layoutId, $code, $position, $sortOrder);
        $insert->execute();
    }

    $insert->close();
    $db->commit();
    invalidateLandingCaches($layoutId, $moduleIds);

    echo "Landing page and footer applied successfully.\n";
    echo 'Backup: ' . $backupFile . "\n";
    echo 'Module IDs: ' . json_encode($moduleIds, JSON_UNESCAPED_SLASHES) . "\n";
} catch (Throwable $exception) {
    $db->rollback();
    fwrite(STDERR, $exception->getMessage() . "\n");
    exit(1);
} finally {
    $db->close();
}
