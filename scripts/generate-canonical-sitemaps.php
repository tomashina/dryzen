<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the command line.');
}

$projectRoot = dirname(__DIR__);
require_once $projectRoot . '/upload/config.php';

$options = getopt('', array('base-url::', 'output-dir::'));
$baseUrl = isset($options['base-url']) ? trim($options['base-url']) : 'https://www.dryzen.eu/';
$outputDirectory = isset($options['output-dir'])
    ? rtrim($options['output-dir'], '/\\')
    : $projectRoot . '/upload/sitemaps';

if (!preg_match('#^https?://[^/]+(?:/.*)?$#i', $baseUrl)) {
    fwrite(STDERR, "Invalid --base-url.\n");
    exit(1);
}

$baseUrl = preg_replace('#^http://#i', 'https://', $baseUrl);
$baseUrl = rtrim($baseUrl, '/') . '/';

if (!is_dir($outputDirectory) && !mkdir($outputDirectory, 0775, true) && !is_dir($outputDirectory)) {
    fwrite(STDERR, "Unable to create sitemap directory: {$outputDirectory}\n");
    exit(1);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, (int)DB_PORT);
$db->set_charset('utf8mb4');

function sitemapXmlEscape($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_XML1, 'UTF-8');
}

function sitemapAssetUrl($baseUrl, $path)
{
    $segments = array_map('rawurlencode', explode('/', ltrim((string)$path, '/')));

    return $baseUrl . implode('/', $segments);
}

function sitemapFormatDate($value)
{
    if (!$value || substr((string)$value, 0, 10) === '0000-00-00') {
        return '';
    }

    $timestamp = strtotime($value);

    return $timestamp === false ? '' : date('c', $timestamp);
}

function sitemapHeader($withImages)
{
    $output = '<?xml version="1.0" encoding="UTF-8"?>';
    $output .= '<?xml-stylesheet type="text/xsl" href="/sitemaps/sitemap-style.xml"?>';
    $output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
    $output .= ' xmlns:xhtml="http://www.w3.org/1999/xhtml"';

    if ($withImages) {
        $output .= ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"';
    }

    return $output . '>';
}

function sitemapWriteAtomic($directory, $filename, $content)
{
    $target = $directory . DIRECTORY_SEPARATOR . basename($filename);
    $temporary = tempnam($directory, '.dryzen-sitemap-');

    if ($temporary === false || file_put_contents($temporary, $content, LOCK_EX) !== strlen($content)) {
        if ($temporary && is_file($temporary)) {
            unlink($temporary);
        }

        throw new RuntimeException('Unable to write ' . $target);
    }

    chmod($temporary, 0644);

    if (!rename($temporary, $target)) {
        unlink($temporary);
        throw new RuntimeException('Unable to replace ' . $target);
    }
}

function sitemapAlternates(array $aliases, array $languages, $defaultLanguageId, $baseUrl)
{
    $output = '';

    foreach ($languages as $language) {
        $languageId = (int)$language['language_id'];
        $output .= '<xhtml:link rel="alternate" hreflang="'
            . sitemapXmlEscape(strtolower(str_replace('_', '-', $language['code'])))
            . '" href="' . sitemapXmlEscape($baseUrl . ltrim($aliases[$languageId], '/')) . '"/>';
    }

    if (isset($aliases[$defaultLanguageId])) {
        $output .= '<xhtml:link rel="alternate" hreflang="x-default" href="'
            . sitemapXmlEscape($baseUrl . ltrim($aliases[$defaultLanguageId], '/')) . '"/>';
    }

    return $output;
}

function sitemapAliasesFor(array $seoAliases, $query, array $languages)
{
    $aliases = array();

    foreach ($languages as $language) {
        $languageId = (int)$language['language_id'];

        if (empty($seoAliases[$query][$languageId])) {
            return array();
        }

        $aliases[$languageId] = $seoAliases[$query][$languageId];
    }

    return $aliases;
}

try {
    $languages = array();
    $result = $db->query(
        'SELECT language_id, code FROM ' . DB_PREFIX . 'language WHERE status = 1 ORDER BY sort_order, name'
    );

    while ($row = $result->fetch_assoc()) {
        $languages[] = $row;
    }

    if (!$languages) {
        throw new RuntimeException('No active storefront languages were found.');
    }

    $defaultLanguageCodeResult = $db->query(
        "SELECT value FROM " . DB_PREFIX . "setting
         WHERE store_id = 0 AND code = 'config' AND `key` = 'config_language' LIMIT 1"
    );
    $defaultLanguageCodeRow = $defaultLanguageCodeResult->fetch_assoc();
    $defaultLanguageCode = $defaultLanguageCodeResult->num_rows
        ? $defaultLanguageCodeRow['value']
        : $languages[0]['code'];
    $defaultLanguageId = (int)$languages[0]['language_id'];

    foreach ($languages as $language) {
        if (strtolower($language['code']) === strtolower($defaultLanguageCode)) {
            $defaultLanguageId = (int)$language['language_id'];
            break;
        }
    }

    $seoAliases = array();
    $result = $db->query(
        'SELECT `query`, keyword, language_id FROM ' . DB_PREFIX . "seo_url
         WHERE store_id = 0 AND keyword <> ''"
    );

    while ($row = $result->fetch_assoc()) {
        $seoAliases[$row['query']][(int)$row['language_id']] = $row['keyword'];
    }

    $types = array(
        'category' => array(
            'query' => "SELECT c.category_id AS entity_id, c.date_modified, c.image
                        FROM " . DB_PREFIX . "category c
                        JOIN " . DB_PREFIX . "category_to_store c2s ON c2s.category_id = c.category_id
                        WHERE c2s.store_id = 0 AND c.status = 1
                        ORDER BY c.sort_order, c.category_id",
            'alias_prefix' => 'category_id=',
            'images' => true,
        ),
        'product' => array(
            'query' => "SELECT p.product_id AS entity_id, p.date_modified, p.image
                        FROM " . DB_PREFIX . "product p
                        JOIN " . DB_PREFIX . "product_to_store p2s ON p2s.product_id = p.product_id
                        WHERE p2s.store_id = 0 AND p.status = 1 AND p.date_available <= NOW()
                        ORDER BY p.product_id",
            'additional_images_query' => "SELECT pi.product_id AS entity_id, pi.image
                                          FROM " . DB_PREFIX . "product_image pi
                                          ORDER BY pi.product_id, pi.sort_order, pi.product_image_id",
            'alias_prefix' => 'product_id=',
            'images' => true,
        ),
        'information' => array(
            'query' => "SELECT i.information_id AS entity_id, '' AS date_modified, '' AS image
                        FROM " . DB_PREFIX . "information i
                        JOIN " . DB_PREFIX . "information_to_store i2s ON i2s.information_id = i.information_id
                        WHERE i2s.store_id = 0 AND i.status = 1
                        ORDER BY i.sort_order, i.information_id",
            'alias_prefix' => 'information_id=',
            'images' => false,
        ),
    );

    $generated = 0;

    foreach ($types as $type => $definition) {
        $entities = array();
        $entityIndexes = array();
        $result = $db->query($definition['query']);

        while ($row = $result->fetch_assoc()) {
            $row['images'] = array();

            if ($definition['images'] && $row['image']) {
                $row['images'][] = $row['image'];
            }

            $entityIndexes[(int)$row['entity_id']] = count($entities);
            $entities[] = $row;
        }

        if (!empty($definition['additional_images_query'])) {
            $result = $db->query($definition['additional_images_query']);

            while ($row = $result->fetch_assoc()) {
                $entityId = (int)$row['entity_id'];
                $image = trim((string)$row['image']);

                if (
                    $image === ''
                    || !isset($entityIndexes[$entityId])
                    || in_array($image, $entities[$entityIndexes[$entityId]]['images'], true)
                ) {
                    continue;
                }

                $entities[$entityIndexes[$entityId]]['images'][] = $image;
            }
        }

        foreach ($languages as $language) {
            $languageId = (int)$language['language_id'];
            $output = sitemapHeader($definition['images']);

            foreach ($entities as $entity) {
                $entityId = (int)$entity['entity_id'];
                $query = $definition['alias_prefix'] . $entityId;
                $aliases = sitemapAliasesFor($seoAliases, $query, $languages);

                if (!$aliases) {
                    fwrite(STDERR, "Skipped {$query}: at least one language alias is missing.\n");
                    continue;
                }

                $output .= '<url><loc>'
                    . sitemapXmlEscape($baseUrl . ltrim($aliases[$languageId], '/'))
                    . '</loc>';
                $output .= sitemapAlternates($aliases, $languages, $defaultLanguageId, $baseUrl);

                $lastmod = sitemapFormatDate($entity['date_modified']);

                if ($lastmod !== '') {
                    $output .= '<lastmod>' . sitemapXmlEscape($lastmod) . '</lastmod>';
                }

                foreach ($entity['images'] as $image) {
                    $output .= '<image:image><image:loc>'
                        . sitemapXmlEscape(sitemapAssetUrl($baseUrl . 'image/', $image))
                        . '</image:loc></image:image>';
                }

                $output .= '</url>';
            }

            $output .= '</urlset>';
            $filename = 'sitemap_0_' . $languageId . '_' . $type . '.xml';
            sitemapWriteAtomic($outputDirectory, $filename, $output);
            echo "Generated {$filename}\n";
            $generated++;
        }
    }

    echo "Generated {$generated} canonical sitemap files in {$outputDirectory}\n";
} catch (Throwable $exception) {
    fwrite(STDERR, 'FAIL: ' . $exception->getMessage() . "\n");
    exit(1);
} finally {
    $db->close();
}
