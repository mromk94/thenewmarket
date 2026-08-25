<?php

declare(strict_types=1);

namespace App\Core;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_samesite', 'Lax');
            ini_set('session.use_strict_mode', '1');

            $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
            if (config('app.env') === 'production' && $isHttps) {
                ini_set('session.cookie_secure', '1');
            }

            session_start();
            self::ageFlash();
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function all(): array
    {
        return $_SESSION;
    }

    public static function destroy(): void
    {
        session_destroy();
        $_SESSION = [];
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash_new'][$key] = $value;
    }

    public static function old(string $key, mixed $default = ''): mixed
    {
        $input = self::getFlash('old', []);
        return $input[$key] ?? $default;
    }

    public static function setOld(array $input): void
    {
        self::flash('old', $input);
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        return $_SESSION['_flash_old'][$key] ?? $default;
    }

    public static function keepOld(): void
    {
        if (isset($_SESSION['_flash_old']['old'])) {
            $_SESSION['_flash_new']['old'] = $_SESSION['_flash_old']['old'];
        }
    }

    private static function ageFlash(): void
    {
        $_SESSION['_flash_old'] = $_SESSION['_flash_new'] ?? [];
        $_SESSION['_flash_new'] = [];
    }
}
