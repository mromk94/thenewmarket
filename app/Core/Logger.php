<?php

declare(strict_types=1);

namespace App\Core;

use DateTime;

class Logger
{
    private const LEVELS = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];

    public static function log(string $message, string $level = 'info', array $context = []): void
    {
        if (!in_array(strtolower($level), self::LEVELS, true)) {
            $level = 'info';
        }

        $date = (new DateTime())->format('Y-m-d H:i:s');
        $ctx = empty($context) ? '' : ' | ' . json_encode($context);
        $line = "[{$date}] [{$level}] {$message}{$ctx}" . PHP_EOL;

        $dir = BASE_PATH . '/storage/logs';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($dir . '/app.log', $line, FILE_APPEND | LOCK_EX);
    }

    public static function debug(string $message, array $context = []): void { self::log($message, 'debug', $context); }
    public static function info(string $message, array $context = []): void { self::log($message, 'info', $context); }
    public static function warning(string $message, array $context = []): void { self::log($message, 'warning', $context); }
    public static function error(string $message, array $context = []): void { self::log($message, 'error', $context); }
    public static function critical(string $message, array $context = []): void { self::log($message, 'critical', $context); }
}
