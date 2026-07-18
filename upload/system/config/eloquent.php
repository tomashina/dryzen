<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    "driver"    => "mysql",
    "host"      => DB_HOSTNAME,
    "database"  => DB_DATABASE,
    "username"  => DB_USERNAME,
    "password"  => DB_PASSWORD,
    'charset'   => 'utf8',
    'collation' => 'utf8_general_ci',
    'prefix'    => 'oc_',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();