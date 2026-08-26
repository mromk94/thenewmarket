<?php

declare(strict_types=1);

namespace App;

use App\Core\Session;

class Lang
{
    private static ?array $cache = null;

    public static function available(): array
    {
        return [
            'en' => 'English',
            'zh' => '中文',
            'es' => 'Español',
            'fr' => 'Français',
            'de' => 'Deutsch',
        ];
    }

    public static function current(): string
    {
        $code = Session::get('lang');
        if ($code && isset(self::available()[$code])) {
            return $code;
        }
        $code = (string) config('app.locale', 'en');
        return isset(self::available()[$code]) ? $code : 'en';
    }

    public static function set(string $code): void
    {
        if (isset(self::available()[$code])) {
            Session::set('lang', $code);
        }
    }

    public static function line(string $key, string $default = ''): string
    {
        $strings = self::load(self::current());
        return (string) ($strings[$key] ?? (self::load('en')[$key] ?? ($default ?: $key)));
    }

    private static function load(string $code): array
    {
        if (self::$cache === null) {
            self::$cache = [];
        }

        if (isset(self::$cache[$code])) {
            return self::$cache[$code];
        }

        $path = BASE_PATH . '/app/Lang/' . $code . '.php';
        if (file_exists($path)) {
            self::$cache[$code] = (array) require $path;
        } else {
            self::$cache[$code] = [];
        }

        return self::$cache[$code];
    }
}
