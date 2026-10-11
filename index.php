<?php

use Core\App;

define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

require_once BASE_PATH . '/core/__helpers.php';

$app = new App();
$app->boot();
