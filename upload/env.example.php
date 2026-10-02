<?php
define('OC_ENV', [
    'env' => 'development',
    'eurosender' => [
        // Use the sandbox key while testing. Production and sandbox keys are separate.
        // Keep the real key in env.php only; env.php is ignored by Git.
        'api_key' => '',
    ],
    'import' => [
        'api' => [
            // e-Racuni: Postavke > Postavke tvrtke > API Web services.
            // Keep real credentials in env.php only; env.php is ignored by Git.
            'username' => '',
            'password' => '', // API secret key, not the regular login password
            'token' => '',    // organization API token
            'url' => 'https://e-racuni.com/ORGANIZATION_ID/API-CLI/',
            'url_image_suffix' => 'ProductImageGet',
        ],
    ],
]);
