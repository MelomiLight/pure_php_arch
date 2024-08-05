<?php

use bootstrap\App;

define("APP_PATH", dirname(__DIR__));

require_once APP_PATH . '/vendor/autoload.php';

$app = new App();
$app->start();
