<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the command line.');
}

$projectRoot = dirname(__DIR__);
require_once $projectRoot . '/upload/config.php';
$config = require $projectRoot . '/database/content/hr/seo-routes.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, (int) DB_PORT);
$db->set_charset('utf8mb4');

function dryzenSeoAssert($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function dryzenSeoFetch($url)
{
    $context = stream_context_create(array(
        'http' => array(
            'timeout' => 90,
            'ignore_errors' => true,
            'header' => "User-Agent: DryZen-SEOT test\r\n",
        ),
    ));
    $html = file_get_contents($url, false, $context);
    $status = isset($http_response_header[0]) ? $http_response_header[0] : '';

    dryzenSeoAssert($html !== false, 'Unable to fetch ' . $url);
    dryzenSeoAssert(strpos($status, '200') !== false, 'Unexpected HTTP status for ' . $url . ': ' . $status);

    return $html;
}

function dryzenSeoHeadValue($html, $tag, $attribute, $value, $contentAttribute)
{
    $pattern = '~<' . preg_quote($tag, '~') . '\b[^>]*'
        . preg_quote($attribute, '~') . '=["\']' . preg_quote($value, '~') . '["\'][^>]*>~i';

    if (!preg_match($pattern, $html, $match)) {
        return '';
    }

    return preg_match(
        '~\b' . preg_quote($contentAttribute, '~') . '=["\']([^"\']+)["\']~i',
        $match[0],
        $content
    ) ? html_entity_decode($content[1], ENT_QUOTES | ENT_HTML5, 'UTF-8') : '';
}

function dryzenSeoJsonLd($html)
{
    preg_match_all(
        '~<script[^>]+type=["\']application/ld\+json["\'][^>]*>(.*?)</script>~si',
        $html,
        $matches
    );
    $items = array();

    foreach ($matches[1] as $json) {
        $decoded = json_decode(
            html_entity_decode(trim($json), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            true
        );
        dryzenSeoAssert(json_last_error() === JSON_ERROR_NONE, 'Invalid JSON-LD: ' . json_last_error_msg());
        $items[] = $decoded;
    }

    return $items;
}

function dryzenSeoSchemaByType(array $items, $type)
{
    return array_values(array_filter($items, static function ($item) use ($type) {
        return is_array($item) && isset($item['@type']) && $item['@type'] === $type;
    }));
}

function dryzenSeoAuditPage($url, $canonical, $expectedSchemaType = '')
{
    $html = dryzenSeoFetch($url);

    dryzenSeoAssert(
        dryzenSeoHeadValue($html, 'link', 'rel', 'canonical', 'href') === $canonical,
        'Canonical mismatch for ' . $url
    );
    dryzenSeoAssert(
        strpos($html, 'rel="alternate" hreflang="hr-HR" href="' . $canonical . '"') !== false,
        'Missing self hreflang for ' . $url
    );
    dryzenSeoAssert(
        strpos($html, 'rel="alternate" hreflang="x-default" href="' . $canonical . '"') !== false,
        'Missing x-default hreflang for ' . $url
    );
    dryzenSeoAssert(
        dryzenSeoHeadValue($html, 'meta', 'property', 'og:title', 'content') !== '',
        'Missing Open Graph title for ' . $url
    );
    dryzenSeoAssert(
        dryzenSeoHeadValue($html, 'meta', 'property', 'og:image', 'content') !== '',
        'Missing Open Graph image for ' . $url
    );
    dryzenSeoAssert(
        dryzenSeoHeadValue($html, 'meta', 'name', 'twitter:card', 'content') === 'summary_large_image',
        'Missing Twitter large image card for ' . $url
    );
    dryzenSeoAssert(preg_match_all('~<h1\b~i', $html) === 1, 'Expected exactly one H1 on ' . $url);
    dryzenSeoAssert(preg_match('~<style\b~i', $html) === 0, 'Inline style block found on ' . $url);

    $schemas = dryzenSeoJsonLd($html);
    dryzenSeoAssert($schemas, 'Missing JSON-LD on ' . $url);

    if ($expectedSchemaType !== '') {
        dryzenSeoAssert(
            count(dryzenSeoSchemaByType($schemas, $expectedSchemaType)) >= 1,
            'Missing ' . $expectedSchemaType . ' JSON-LD on ' . $url
        );
    }

    return array($html, $schemas);
}

try {
    foreach ($config['products'] as $productId => $keyword) {
        $statement = $db->prepare(
            'SELECT keyword FROM ' . DB_PREFIX . 'seo_url
             WHERE query = ? AND language_id = ? AND store_id = ? LIMIT 1'
        );
        $query = 'product_id=' . (int) $productId;
        $statement->bind_param('sii', $query, $config['language_id'], $config['store_id']);
        $statement->execute();
        $row = $statement->get_result()->fetch_assoc();
        $statement->close();

        dryzenSeoAssert($row && $row['keyword'] === $keyword, 'SEO URL mismatch for product ' . $productId);
    }

    foreach ($config['routes'] as $route => $keyword) {
        $statement = $db->prepare(
            'SELECT keyword FROM ' . DB_PREFIX . 'hb_url
             WHERE route = ? AND language_id = ? AND store_id = ? LIMIT 1'
        );
        $statement->bind_param('sii', $route, $config['language_id'], $config['store_id']);
        $statement->execute();
        $row = $statement->get_result()->fetch_assoc();
        $statement->close();

        dryzenSeoAssert($row && $row['keyword'] === $keyword, 'SEO URL mismatch for route ' . $route);
    }

    foreach ($config['settings'] as $key => $value) {
        $statement = $db->prepare(
            'SELECT value FROM ' . DB_PREFIX . 'setting
             WHERE `key` = ? AND store_id = ? LIMIT 1'
        );
        $statement->bind_param('si', $key, $config['store_id']);
        $statement->execute();
        $row = $statement->get_result()->fetch_assoc();
        $statement->close();

        dryzenSeoAssert($row && (string) $row['value'] === (string) $value, 'Setting mismatch for ' . $key);
    }

    $baseUrl = rtrim(HTTP_SERVER, '/');

    foreach ($config['products'] as $productId => $keyword) {
        $canonical = $baseUrl . '/' . $keyword;
        list($html, $schemas) = dryzenSeoAuditPage($canonical, $canonical, 'Product');
        $products = dryzenSeoSchemaByType($schemas, 'Product');
        dryzenSeoAssert(count($products) === 1, 'Expected one Product JSON-LD on ' . $canonical);
        $product = $products[0];
        dryzenSeoAssert(($product['brand']['name'] ?? '') === 'DryZen', 'Invalid product brand on ' . $canonical);
        dryzenSeoAssert(($product['offers']['url'] ?? '') === $canonical, 'Offer URL mismatch on ' . $canonical);
        dryzenSeoAssert(($product['offers']['priceCurrency'] ?? '') === 'EUR', 'Invalid currency on ' . $canonical);
        dryzenSeoAssert((float)($product['offers']['price'] ?? 0) > 0, 'Invalid price on ' . $canonical);
        $breadcrumbs = dryzenSeoSchemaByType($schemas, 'BreadcrumbList');
        dryzenSeoAssert($breadcrumbs, 'Missing breadcrumb JSON-LD on ' . $canonical);
        dryzenSeoAssert(
            count($breadcrumbs[0]['itemListElement'] ?? array()) >= 2,
            'Breadcrumb JSON-LD is too short on ' . $canonical
        );
        dryzenSeoAssert(strpos($html, 'srcset=') !== false, 'Missing responsive gallery sources on ' . $canonical);
    }

    $keyPages = array(
        '/' => 'OnlineStore',
        '/shop' => 'ItemList',
        '/o-nama' => 'BreadcrumbList',
        '/kontakt' => 'ContactPage',
    );

    foreach ($keyPages as $path => $schemaType) {
        $canonical = $path === '/' ? $baseUrl . '/' : $baseUrl . $path;
        dryzenSeoAuditPage($canonical, $canonical, $schemaType);
    }

    echo 'PASS SEO route database audit: ' . count($config['products']) . " products\n";
    echo 'PASS custom routes: ' . count($config['routes']) . "\n";
    echo 'PASS rendered SEO audit: ' . (count($config['products']) + count($keyPages)) . " pages\n";
} catch (Throwable $exception) {
    fwrite(STDERR, 'FAIL: ' . $exception->getMessage() . "\n");
    exit(1);
} finally {
    $db->close();
}
