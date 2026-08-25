<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);

require_once BASE_PATH . '/vendor/autoload.php';

App\Core\App::run();
