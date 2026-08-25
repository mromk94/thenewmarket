<?php

/**
 * cPanel cron entry:
 * /usr/local/bin/php /home/yourcpaneluser/marketplace/cron/cron.php >> /home/yourcpaneluser/marketplace/storage/logs/cron.log 2>&1
 */

define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', BASE_PATH . '/public');

require BASE_PATH . '/vendor/autoload.php';

use App\Core\App;

// Only allow command line execution
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Cron may only be run from the command line.');
}

echo '[' . date('Y-m-d H:i:s') . '] Cron started' . PHP_EOL;

// Bootstrap configuration
App::bootstrap();

// Place scheduled tasks here:
// - Expire abandoned guest carts
// - Move eligible pending commissions to available
// - Send queued emails
// - Clean temporary files

echo '[' . date('Y-m-d H:i:s') . '] Cron completed' . PHP_EOL;
