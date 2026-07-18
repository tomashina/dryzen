<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the command line.');
}

$projectRoot = dirname(__DIR__);
$configFile = $projectRoot . '/upload/config.php';

if (!is_file($configFile)) {
    fwrite(STDERR, "Missing upload/config.php\n");
    exit(1);
}

require_once $configFile;

$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, (int) DB_PORT);

if ($db->connect_errno) {
    fwrite(STDERR, "Database connection failed: " . $db->connect_error . "\n");
    exit(1);
}

$db->set_charset('utf8mb4');
$contentRoot = $projectRoot . '/database/content/hr';
$languageId = 3;

function readContent($path)
{
    $content = file_get_contents($path);

    if ($content === false) {
        throw new RuntimeException('Unable to read content file: ' . $path);
    }

    return trim($content);
}

function updateInformation(mysqli $db, $informationId, $languageId, $title, $metaTitle, $file)
{
    $html = readContent($file);
    $statement = $db->prepare(
        'UPDATE ' . DB_PREFIX . 'information_description
         SET title = ?, description = ?, meta_title = ?
         WHERE information_id = ? AND language_id = ?'
    );
    $statement->bind_param('sssii', $title, $html, $metaTitle, $informationId, $languageId);
    $statement->execute();

    if ($statement->affected_rows < 0) {
        throw new RuntimeException('Unable to update information page ID ' . $informationId);
    }

    $statement->close();
}

function updateSetting(mysqli $db, $key, $value)
{
    $statement = $db->prepare(
        'UPDATE ' . DB_PREFIX . 'setting SET value = ? WHERE store_id = 0 AND `key` = ?'
    );
    $statement->bind_param('ss', $value, $key);
    $statement->execute();
    $statement->close();
}

try {
    $db->begin_transaction();

    $merchantBlock = readContent($contentRoot . '/merchant-details.html');
    $query = $db->prepare(
        'SELECT description FROM ' . DB_PREFIX . 'information_description
         WHERE information_id = 13 AND language_id = ? FOR UPDATE'
    );
    $query->bind_param('i', $languageId);
    $query->execute();
    $result = $query->get_result();
    $about = $result->fetch_assoc();
    $query->close();

    if (!$about) {
        throw new RuntimeException('About Us page ID 13 was not found.');
    }

    if (strpos(html_entity_decode($about['description'], ENT_QUOTES, 'UTF-8'), 'data-otp-compliance="merchant-details"') === false) {
        $description = $merchantBlock . "\n" . $about['description'];
        $statement = $db->prepare(
            'UPDATE ' . DB_PREFIX . 'information_description
             SET description = ?, meta_title = ?
             WHERE information_id = 13 AND language_id = ?'
        );
        $aboutMetaTitle = 'O nama - DryZen i podaci o trgovcu';
        $statement->bind_param('ssi', $description, $aboutMetaTitle, $languageId);
        $statement->execute();
        $statement->close();
    }

    updateInformation(
        $db,
        5,
        $languageId,
        'Opći uvjeti kupnje',
        'Opći uvjeti kupnje - DryZen',
        $contentRoot . '/general-terms.html'
    );
    updateInformation(
        $db,
        7,
        $languageId,
        'Načini plaćanja i dostava',
        'Načini plaćanja i dostava - DryZen',
        $contentRoot . '/payment-and-delivery.html'
    );
    updateInformation(
        $db,
        12,
        $languageId,
        'Sigurnost plaćanja debitnim/kreditnim karticama',
        'Sigurnost plaćanja debitnim/kreditnim karticama - DryZen',
        $contentRoot . '/payment-security.html'
    );
    updateInformation(
        $db,
        17,
        $languageId,
        'Povrat robe, zamjena i reklamacije',
        'Povrat robe, zamjena i reklamacije - DryZen',
        $contentRoot . '/returns-and-complaints.html'
    );

    updateSetting($db, 'config_owner', 'GORDOM USLUGE d.o.o.');
    updateSetting($db, 'config_telephone', '+385 98 177 7049');
    updateSetting(
        $db,
        'config_address',
        "GORDOM USLUGE d.o.o.\nWickerhauserova ulica 52\n10000 Zagreb, Hrvatska\nOIB: 59379806135\nMBS: 080540845"
    );
    updateSetting($db, 'payment_revolut_card_title', 'Debitna/kreditna kartica (Revolut)');

    $db->commit();
    echo "OTP compliance content applied successfully.\n";
} catch (Throwable $exception) {
    $db->rollback();
    fwrite(STDERR, $exception->getMessage() . "\n");
    exit(1);
} finally {
    $db->close();
}
