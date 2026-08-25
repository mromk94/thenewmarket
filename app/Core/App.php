<?php

declare(strict_types=1);

namespace App\Core;

use Dotenv\Dotenv;
use Throwable;

class App
{
    public static function bootstrap(): void
    {
        self::loadEnvironment();
        self::loadConfig();
        self::setErrorHandling();

        date_default_timezone_set((string) config('app.timezone', 'UTC'));
    }

    public static function run(): void
    {
        self::bootstrap();
        self::setSecurityHeaders();

        Session::start();

        $routesFile = BASE_PATH . '/routes/web.php';
        if (file_exists($routesFile)) {
            require $routesFile;
        }

        try {
            echo Router::dispatch();
        } catch (HttpException $e) {
            Logger::warning('HTTP ' . $e->getStatusCode() . ': ' . $e->getMessage());
            echo Response::error($e->getStatusCode(), $e->getMessage());
        } catch (Throwable $e) {
            Logger::error($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            if (config('app.debug', false)) {
                throw $e;
            }
            echo Response::error(500);
        }
    }

    private static function loadEnvironment(): void
    {
        $envFile = BASE_PATH . '/.env';

        if (!file_exists($envFile) && file_exists(BASE_PATH . '/.env.example')) {
            copy(BASE_PATH . '/.env.example', $envFile);
        }

        if (file_exists($envFile)) {
            Dotenv::createImmutable(BASE_PATH)->load();
        }
    }

    private static function loadConfig(): void
    {
        Config::load();
    }

    private static function setErrorHandling(): void
    {
        if (!config('app.debug', false)) {
            ini_set('display_errors', '0');
            ini_set('log_errors', '1');
        } else {
            ini_set('display_errors', '1');
        }

        set_error_handler(function ($level, $message, $file, $line) {
            Logger::error("Error {$level}: {$message} in {$file}:{$line}");
            return true;
        });

        set_exception_handler(function (Throwable $e) {
            Logger::error('Uncaught: ' . $e->getMessage());
        });
    }

    private static function setSecurityHeaders(): void
    {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('X-XSS-Protection: 1; mode=block');
    }
}
