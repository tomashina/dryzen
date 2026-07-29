<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the command line.');
}

$projectRoot = dirname(__DIR__);
require_once $projectRoot . '/upload/config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, (int)DB_PORT);
$db->set_charset('utf8mb4');

function sitemapTestAssert($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function sitemapTestAssetUrl($baseUrl, $path)
{
    $segments = array_map('rawurlencode', explode('/', ltrim((string)$path, '/')));

    return $baseUrl . 'image/' . implode('/', $segments);
}

try {
    $baseUrl = 'https://www.dryzen.eu/';
    $languages = array();
    $result = $db->query(
        'SELECT language_id, code FROM ' . DB_PREFIX . 'language WHERE status = 1 ORDER BY language_id'
    );

    while ($row = $result->fetch_assoc()) {
        $languages[(int)$row['language_id']] = strtolower(str_replace('_', '-', $row['code']));
    }

    sitemapTestAssert(count($languages) >= 2, 'At least two active languages are required.');

    $productImages = array();
    $result = $db->query(
        'SELECT p.product_id, p.image AS main_image, pi.image AS additional_image
         FROM ' . DB_PREFIX . 'product p
         JOIN ' . DB_PREFIX . 'product_to_store p2s
           ON p2s.product_id = p.product_id AND p2s.store_id = 0
         LEFT JOIN ' . DB_PREFIX . 'product_image pi ON pi.product_id = p.product_id
         WHERE p.status = 1 AND p.date_available <= NOW()
         ORDER BY p.product_id, pi.sort_order, pi.product_image_id'
    );

    while ($row = $result->fetch_assoc()) {
        $productId = (int)$row['product_id'];

        if (!isset($productImages[$productId])) {
            $productImages[$productId] = array();

            if ($row['main_image']) {
                $productImages[$productId][] = sitemapTestAssetUrl($baseUrl, $row['main_image']);
            }
        }

        if ($row['additional_image']) {
            $additionalImageUrl = sitemapTestAssetUrl($baseUrl, $row['additional_image']);

            if (!in_array($additionalImageUrl, $productImages[$productId], true)) {
                $productImages[$productId][] = $additionalImageUrl;
            }
        }
    }

    $expectedProductUrls = array();
    $result = $db->query(
        "SELECT `query`, keyword
         FROM " . DB_PREFIX . "seo_url
         WHERE store_id = 0 AND `query` LIKE 'product_id=%' AND keyword <> ''"
    );

    while ($row = $result->fetch_assoc()) {
        $productId = (int)substr($row['query'], strlen('product_id='));

        if (isset($productImages[$productId])) {
            $expectedProductUrls[$baseUrl . ltrim($row['keyword'], '/')] = $productImages[$productId];
        }
    }

    $files = array();

    foreach (array_keys($languages) as $languageId) {
        foreach (array('category', 'information', 'product') as $type) {
            $file = $projectRoot . '/upload/sitemaps/sitemap_0_' . $languageId . '_' . $type . '.xml';
            sitemapTestAssert(is_file($file), 'Missing sitemap: ' . $file);
            $files[] = $file;
        }
    }

    $locs = array();
    $documents = array();

    foreach ($files as $file) {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_file($file);
        sitemapTestAssert($xml !== false, 'Invalid XML: ' . $file);
        $xml->registerXPathNamespace('sm', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xml->registerXPathNamespace('xhtml', 'http://www.w3.org/1999/xhtml');
        $xml->registerXPathNamespace('image', 'http://www.google.com/schemas/sitemap-image/1.1');
        $documents[$file] = $xml;

        foreach ($xml->xpath('//sm:url/sm:loc') as $loc) {
            $url = (string)$loc;
            sitemapTestAssert(
                strpos($url, 'https://www.dryzen.eu/') === 0,
                'Non-canonical origin in ' . $file . ': ' . $url
            );
            sitemapTestAssert(
                strpos($url, '/index.php') === false && parse_url($url, PHP_URL_QUERY) === null,
                'Parameter/non-SEO URL in ' . $file . ': ' . $url
            );
            sitemapTestAssert(!isset($locs[$url]), 'Duplicate <loc>: ' . $url);
            $locs[$url] = true;
        }

        foreach ($xml->xpath('//image:loc') as $imageLoc) {
            $url = (string)$imageLoc;
            sitemapTestAssert(
                strpos($url, $baseUrl . 'image/') === 0
                    && preg_match('/\s/', $url) === 0,
                'Invalid image URL in ' . $file . ': ' . $url
            );
        }

        if (substr($file, -strlen('_product.xml')) === '_product.xml') {
            foreach ($xml->xpath('//sm:url') as $urlNode) {
                $urlNode->registerXPathNamespace('sm', 'http://www.sitemaps.org/schemas/sitemap/0.9');
                $urlNode->registerXPathNamespace('image', 'http://www.google.com/schemas/sitemap-image/1.1');
                $loc = (string)$urlNode->xpath('./sm:loc')[0];
                $actualImages = array_map('strval', $urlNode->xpath('./image:image/image:loc'));

                sitemapTestAssert(isset($expectedProductUrls[$loc]), 'Unexpected product URL: ' . $loc);
                sitemapTestAssert(
                    $actualImages === $expectedProductUrls[$loc],
                    'Incomplete or incorrectly ordered product gallery for ' . $loc
                );
            }
        }
    }

    foreach ($documents as $file => $xml) {
        foreach ($xml->xpath('//sm:url') as $urlNode) {
            $urlNode->registerXPathNamespace('sm', 'http://www.sitemaps.org/schemas/sitemap/0.9');
            $urlNode->registerXPathNamespace('xhtml', 'http://www.w3.org/1999/xhtml');
            $loc = (string)$urlNode->xpath('./sm:loc')[0];
            $alternates = $urlNode->xpath('./xhtml:link');
            $codes = array();

            sitemapTestAssert(
                count($alternates) === count($languages) + 1,
                'Incomplete hreflang cluster for ' . $loc
            );

            foreach ($alternates as $alternate) {
                $attributes = $alternate->attributes();
                $code = (string)$attributes['hreflang'];
                $href = (string)$attributes['href'];
                sitemapTestAssert(!isset($codes[$code]), 'Duplicate hreflang ' . $code . ' for ' . $loc);
                sitemapTestAssert(isset($locs[$href]), 'hreflang target is absent from sitemaps: ' . $href);
                $codes[$code] = true;
            }

            foreach ($languages as $code) {
                sitemapTestAssert(isset($codes[$code]), 'Missing hreflang ' . $code . ' for ' . $loc);
            }

            sitemapTestAssert(isset($codes['x-default']), 'Missing x-default for ' . $loc);
        }
    }

    echo 'PASS canonical sitemap audit: ' . count($files) . ' files, ' . count($locs) . " URLs\n";
} catch (Throwable $exception) {
    fwrite(STDERR, 'FAIL: ' . $exception->getMessage() . "\n");
    exit(1);
} finally {
    $db->close();
}
