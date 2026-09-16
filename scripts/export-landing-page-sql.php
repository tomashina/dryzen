<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the command line.');
}

$projectRoot = dirname(__DIR__);
$configFile = $projectRoot . '/upload/config.php';
$outputFile = $projectRoot . '/database/migrations/20260916_homepage_restructure.sql';

if (!is_file($configFile)) {
    fwrite(STDERR, "Missing upload/config.php\n");
    exit(1);
}

require_once $configFile;

$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, (int) DB_PORT);

if ($db->connect_errno) {
    fwrite(STDERR, 'Database connection failed: ' . $db->connect_error . "\n");
    exit(1);
}

$db->set_charset('utf8mb4');

function sqlHex($value)
{
    return "CONVERT(0x" . bin2hex((string) $value) . " USING utf8mb4)";
}

function sqlIdentifier($value)
{
    return '`' . str_replace('`', '``', $value) . '`';
}

$moduleKeys = array(
    'hero' => 'DryZen Hero',
    'products_intro' => 'DryZen Product Categories',
    'how' => 'DryZen How It Works',
    'evening' => 'DryZen Evening Routine',
    'comparison' => 'DryZen Deodorant Comparison',
    'chooser' => 'DryZen Product Chooser',
    'emotional' => 'DryZen Emotional Story',
    'why' => 'DryZen Why DryZen',
    'product_cta' => 'DryZen Product CTA',
    'reviews' => 'DryZen Reviews',
    'faq' => 'DryZen FAQ',
    'final' => 'DryZen Final Banner',
);

$modules = array();
$selectModule = $db->prepare(
    'SELECT name, code, setting FROM ' . DB_PREFIX . 'module
     WHERE code = ? AND name = ? ORDER BY module_id LIMIT 1'
);
$moduleCode = 'basel_content';

foreach ($moduleKeys as $key => $name) {
    $selectModule->bind_param('ss', $moduleCode, $name);
    $selectModule->execute();
    $row = $selectModule->get_result()->fetch_assoc();

    if (!$row) {
        fwrite(STDERR, 'Missing module: ' . $name . "\n");
        exit(1);
    }

    $modules[$key] = $row;
}

$selectModule->close();

$settingKeys = array(
    'basel_footer_columns',
    'footer_block_1',
    'footer_block_2',
    'footer_block_title',
    'overwrite_footer_links',
);
$settings = array();
$selectSetting = $db->prepare(
    'SELECT `key`, value, serialized FROM ' . DB_PREFIX . 'setting
     WHERE store_id = 0 AND code = ? AND `key` = ? ORDER BY setting_id LIMIT 1'
);
$settingCode = 'basel';

foreach ($settingKeys as $key) {
    $selectSetting->bind_param('ss', $settingCode, $key);
    $selectSetting->execute();
    $row = $selectSetting->get_result()->fetch_assoc();

    if (!$row) {
        fwrite(STDERR, 'Missing setting: ' . $key . "\n");
        exit(1);
    }

    $settings[$key] = $row;
}

$selectSetting->close();
$db->close();

$sql = array();
$sql[] = '-- DryZen / OpenCart 3';
$sql[] = '-- Preslagivanje naslovnice prema odobrenom sadržaju, bez promjene postojećeg vizualnog smjera.';
$sql[] = '-- Uključuje HR/EN sadržaj, poredak modula, stvarne recenzije, newsletter i footer.';
$sql[] = '-- Skripta je idempotentna i može se sigurno pokrenuti više puta.';
$sql[] = '-- Projekt koristi prefiks tablica `oc_`; prilagodite ga ako je na live bazi drukčiji.';
$sql[] = '-- Prvo deployajte kod iz iste grane, zatim importajte ovu datoteku.';
$sql[] = '';
$sql[] = 'START TRANSACTION;';
$sql[] = '';
$sql[] = '-- Ukloni samo zastarjeli homepage CSS iz postavke; ostali custom CSS ostaje netaknut.';
$sql[] = 'SET @dryzen_custom_css := COALESCE((';
$sql[] = '  SELECT `value` FROM `oc_setting`';
$sql[] = "  WHERE `store_id` = 0 AND `code` = 'basel' AND `key` = 'basel_custom_css'";
$sql[] = '  ORDER BY `setting_id` LIMIT 1';
$sql[] = "), '');";
$sql[] = "SET @dryzen_marker_start := LOCATE('/* DRYZEN_LANDING_UX_START */', @dryzen_custom_css);";
$sql[] = "SET @dryzen_marker_end := LOCATE('/* DRYZEN_LANDING_UX_END */', @dryzen_custom_css);";
$sql[] = 'SET @dryzen_custom_css := IF(';
$sql[] = '  @dryzen_marker_start > 0 AND @dryzen_marker_end > @dryzen_marker_start,';
$sql[] = "  CONCAT(SUBSTRING(@dryzen_custom_css, 1, @dryzen_marker_start - 1), SUBSTRING(@dryzen_custom_css, @dryzen_marker_end + LENGTH('/* DRYZEN_LANDING_UX_END */'))),";
$sql[] = '  @dryzen_custom_css';
$sql[] = ');';
$sql[] = "SET @dryzen_legacy_start := LOCATE('#mod2 .type-img,', @dryzen_custom_css);";
$sql[] = "SET @dryzen_legacy_end := LOCATE('/* DRYZEN_NEED_HANDS_UX_START */', @dryzen_custom_css);";
$sql[] = 'SET @dryzen_custom_css := IF(';
$sql[] = '  @dryzen_legacy_start > 0 AND @dryzen_legacy_end > @dryzen_legacy_start,';
$sql[] = '  CONCAT(SUBSTRING(@dryzen_custom_css, 1, @dryzen_legacy_start - 1), SUBSTRING(@dryzen_custom_css, @dryzen_legacy_end)),';
$sql[] = '  @dryzen_custom_css';
$sql[] = ');';
$sql[] = 'UPDATE `oc_setting` SET `value` = TRIM(@dryzen_custom_css)';
$sql[] = "WHERE `store_id` = 0 AND `code` = 'basel' AND `key` = 'basel_custom_css';";
$sql[] = '';
$sql[] = '-- Kreiraj ili ažuriraj homepage module prema stabilnom nazivu.';

foreach ($modules as $key => $module) {
    $variable = '@dryzen_module_' . $key;
    $sql[] = 'SET ' . $variable . '_setting := ' . sqlHex($module['setting']) . ';';
    $sql[] = 'UPDATE `oc_module` SET `setting` = ' . $variable . '_setting';
    $sql[] = 'WHERE `code` = ' . sqlHex($module['code']) . ' AND `name` = ' . sqlHex($module['name']) . ';';
    $sql[] = 'INSERT INTO `oc_module` (`name`, `code`, `setting`)';
    $sql[] = 'SELECT ' . sqlHex($module['name']) . ', ' . sqlHex($module['code']) . ', ' . $variable . '_setting';
    $sql[] = 'WHERE NOT EXISTS (';
    $sql[] = '  SELECT 1 FROM `oc_module` WHERE `code` = ' . sqlHex($module['code']) . ' AND `name` = ' . sqlHex($module['name']);
    $sql[] = ');';
    $sql[] = 'SET ' . $variable . ' := (';
    $sql[] = '  SELECT MIN(`module_id`) FROM `oc_module`';
    $sql[] = '  WHERE `code` = ' . sqlHex($module['code']) . ' AND `name` = ' . sqlHex($module['name']);
    $sql[] = ');';
    $sql[] = '';
}

$sql[] = '-- Zamijeni samo top module na Home layoutu, u traženom redoslijedu.';
$sql[] = 'SET @dryzen_home_layout := (';
$sql[] = '  SELECT MIN(`layout_id`) FROM `oc_layout_route` WHERE `route` = ' . sqlHex('common/home');
$sql[] = ');';
$sql[] = "DELETE FROM `oc_layout_module` WHERE `layout_id` = @dryzen_home_layout AND `position` = 'top';";

$sortOrder = 1;
foreach (array_keys($modules) as $key) {
    $sql[] = 'INSERT INTO `oc_layout_module` (`layout_id`, `code`, `position`, `sort_order`) VALUES';
    $sql[] = '(@dryzen_home_layout, CONCAT(' . sqlHex('basel_content.') . ', @dryzen_module_' . $key . "), 'top', " . $sortOrder . ');';
    $sortOrder++;
}

$sql[] = '';
$sql[] = '-- Footer i kontaktni blokovi.';

foreach ($settings as $key => $setting) {
    $valueVariable = '@dryzen_setting_' . preg_replace('/[^a-z0-9_]/', '_', strtolower($key));
    $sql[] = 'SET ' . $valueVariable . ' := ' . sqlHex($setting['value']) . ';';
    $sql[] = 'UPDATE `oc_setting` SET `value` = ' . $valueVariable . ', `serialized` = ' . (int) $setting['serialized'];
    $sql[] = 'WHERE `store_id` = 0 AND `code` = ' . sqlHex('basel') . ' AND `key` = ' . sqlHex($key) . ';';
    $sql[] = 'INSERT INTO `oc_setting` (`store_id`, `code`, `key`, `value`, `serialized`)';
    $sql[] = 'SELECT 0, ' . sqlHex('basel') . ', ' . sqlHex($key) . ', ' . $valueVariable . ', ' . (int) $setting['serialized'];
    $sql[] = 'WHERE NOT EXISTS (';
    $sql[] = '  SELECT 1 FROM `oc_setting` WHERE `store_id` = 0 AND `code` = ' . sqlHex('basel') . ' AND `key` = ' . sqlHex($key);
    $sql[] = ');';
    $sql[] = '';
}

$sql[] = 'COMMIT;';
$sql[] = '';
$sql[] = '-- Nakon importa: Extensions > Modifications > Refresh, zatim očistite Theme/SASS i system cache.';

$contents = implode("\n", $sql) . "\n";

if (file_put_contents($outputFile, $contents) === false) {
    fwrite(STDERR, 'Unable to write migration: ' . $outputFile . "\n");
    exit(1);
}

echo 'Created ' . $outputFile . "\n";
