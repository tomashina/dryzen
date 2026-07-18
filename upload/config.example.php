<?php
$root = rtrim(__DIR__, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
$storage = rtrim(dirname(__DIR__), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'storagedijana/';

// HTTP
define('HTTP_SERVER', 'http://dryzen.test/');

// HTTPS
define('HTTPS_SERVER', 'http://dryzen.test/');

// DIR
define('DIR_APPLICATION', $root . 'catalog/');
define('DIR_SYSTEM', $root . 'system/');
define('DIR_IMAGE', $root . 'image/');
define('DIR_STORAGE', $storage);
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/theme/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', '127.0.0.1');
define('DB_USERNAME', 'dryzen_local');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'dryzegit');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');
