<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the command line.');
}

$projectRoot = dirname(__DIR__);
require_once $projectRoot . '/upload/config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$database = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, (int) DB_PORT);
$database->set_charset('utf8mb4');

const DRYZEN_META_PIXEL_ID = '3630339670455864';
const DRYZEN_META_AD_ACCOUNT_ID = '2473286479801468';

function upsertSetting(mysqli $database, $code, $key, $value, $storeId = 0)
{
    $table = DB_PREFIX . 'setting';
    $select = $database->prepare(
        "SELECT `setting_id` FROM `{$table}` WHERE `store_id` = ? AND `code` = ? AND `key` = ? LIMIT 1"
    );
    $select->bind_param('iss', $storeId, $code, $key);
    $select->execute();
    $result = $select->get_result();

    if ($result->num_rows) {
        $settingId = (int) $result->fetch_assoc()['setting_id'];
        $update = $database->prepare(
            "UPDATE `{$table}` SET `value` = ?, `serialized` = 0 WHERE `setting_id` = ?"
        );
        $update->bind_param('si', $value, $settingId);
        $update->execute();
        return 'updated';
    }

    $insert = $database->prepare(
        "INSERT INTO `{$table}` (`store_id`, `code`, `key`, `value`, `serialized`) VALUES (?, ?, ?, ?, 0)"
    );
    $insert->bind_param('isss', $storeId, $code, $key, $value);
    $insert->execute();

    return 'inserted';
}

function patchMetaPixelModification(mysqli $database)
{
    $table = DB_PREFIX . 'modification';
    $statement = $database->prepare(
        "SELECT `modification_id`, `xml` FROM `{$table}` WHERE `code` = 'javv_meta_pixel_events' LIMIT 1"
    );
    $statement->execute();
    $result = $statement->get_result();

    if (!$result->num_rows) {
        throw new RuntimeException('JAVV Meta Pixel Events modification is not installed.');
    }

    $row = $result->fetch_assoc();
    $xml = $row['xml'];
    $replacements = array(
        "\$data['javv_meta_pixel'] = \$this->load->controller('extension/module/javv_meta_pixel');" =>
            "\$data['javv_meta_pixel_encoded'] = base64_encode(\$this->load->controller('extension/module/javv_meta_pixel'));",
        "{% if javv_meta_pixel %}\n{{ javv_meta_pixel }}\n{% endif %}" =>
            "{% if javv_meta_pixel_encoded %}\n<script>window.gkMetaPixelEncoded='{{ javv_meta_pixel_encoded }}';</script>\n{% endif %}",
    );

    foreach ($replacements as $original => $replacement) {
        if (strpos($xml, $original) !== false) {
            $xml = str_replace($original, $replacement, $xml);
        } elseif (strpos($xml, $replacement) === false) {
            throw new RuntimeException('Unexpected JAVV Meta Pixel modification format; refusing a partial patch.');
        }
    }

    $update = $database->prepare(
        "UPDATE `{$table}` SET `xml` = ?, `status` = 1 WHERE `modification_id` = ?"
    );
    $modificationId = (int) $row['modification_id'];
    $update->bind_param('si', $xml, $modificationId);
    $update->execute();
}

function refreshModifications(mysqli $database)
{
    if (!function_exists('curl_init')) {
        throw new RuntimeException('cURL is required for --refresh.');
    }

    $userTable = DB_PREFIX . 'user';
    $userResult = $database->query(
        "SELECT `user_id` FROM `{$userTable}` WHERE `status` = 1 AND `user_group_id` = 1 ORDER BY `user_id` LIMIT 1"
    );

    if (!$userResult->num_rows) {
        throw new RuntimeException('No active super-admin user is available for OCMOD refresh.');
    }

    $sessionId = bin2hex(random_bytes(16));
    $userToken = bin2hex(random_bytes(16));
    $userId = (int) $userResult->fetch_assoc()['user_id'];
    $sessionData = json_encode(array('user_id' => $userId, 'user_token' => $userToken));
    $sessionTable = DB_PREFIX . 'session';
    $session = $database->prepare(
        "REPLACE INTO `{$sessionTable}` (`session_id`, `data`, `expire`) VALUES (?, ?, DATE_ADD(UTC_TIMESTAMP(), INTERVAL 10 MINUTE))"
    );
    $session->bind_param('ss', $sessionId, $sessionData);
    $session->execute();

    try {
        $url = rtrim(HTTP_SERVER, '/')
            . '/admin/index.php?route=marketplace/modification/refresh&user_token=' . rawurlencode($userToken);
        $curl = curl_init($url);
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIE => 'OCSESSID=' . $sessionId,
            CURLOPT_TIMEOUT => 180,
        ));
        $response = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($response === false || $error !== '' || $status >= 400) {
            throw new RuntimeException('OCMOD refresh failed (HTTP ' . $status . '): ' . $error);
        }
    } finally {
        $delete = $database->prepare("DELETE FROM `{$sessionTable}` WHERE `session_id` = ?");
        $delete->bind_param('s', $sessionId);
        $delete->execute();
    }
}

$settings = array(
    'module_javv_meta_pixel_status' => '1',
    'module_javv_meta_pixel_pixel_id' => DRYZEN_META_PIXEL_ID,
    'module_javv_meta_pixel_page_view_status' => '1',
    'module_javv_meta_pixel_add_to_cart_status' => '1',
    'module_javv_meta_pixel_purchase_status' => '1',
    'module_javv_meta_pixel_debug_status' => '0',
);

$committed = false;

try {
    $database->begin_transaction();

    foreach ($settings as $key => $value) {
        $status = upsertSetting($database, 'module_javv_meta_pixel', $key, $value);
        echo strtoupper($status) . ' ' . $key . PHP_EOL;
    }

    // The new manager owns the consent UI. Keeping the legacy banner enabled
    // would render two competing dialogs after an OCMOD refresh.
    echo strtoupper(upsertSetting($database, 'mpgdpr', 'mpgdpr_cbstatus', '0'))
        . ' mpgdpr_cbstatus' . PHP_EOL;

    patchMetaPixelModification($database);
    echo 'PATCHED javv_meta_pixel_events for deferred consent loading' . PHP_EOL;

	$database->commit();
	$committed = true;

    if (in_array('--refresh', $argv, true)) {
        refreshModifications($database);
        echo 'REFRESHED OCMOD cache' . PHP_EOL;
    } else {
        echo 'Next: refresh Extensions > Modifications in OpenCart admin.' . PHP_EOL;
    }

    echo 'Meta Pixel ' . DRYZEN_META_PIXEL_ID . ' configured.' . PHP_EOL;
    echo 'Meta ad account ' . DRYZEN_META_AD_ACCOUNT_ID . ' is an account-side identifier and is not sent by browser pixel code.' . PHP_EOL;
} catch (Throwable $exception) {
	if (!$committed) {
		$database->rollback();
	}

    fwrite(STDERR, 'FAIL: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
} finally {
    $database->close();
}
