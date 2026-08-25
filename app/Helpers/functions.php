<?php

declare(strict_types=1);

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? null;
        if ($value === null) {
            $value = getenv($key);
        }
        if ($value === false || $value === null) {
            return $default;
        }
        $str = trim((string) $value);
        return match (strtolower($str)) {
            'true' => true,
            'false' => false,
            'null' => null,
            '' => $default,
            default => $str,
        };
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        return App\Core\Config::get($key, $default);
    }
}

if (!function_exists('e')) {
    function e(?string $text): string
    {
        return htmlspecialchars((string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        return rtrim((string) config('app.url'), '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return App\Core\Csrf::token();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed
    {
        return App\Core\Session::old($key, $default);
    }
}

if (!function_exists('setting')) {
    function setting(string $group, string $key, mixed $default = null): mixed
    {
        return App\Models\Setting::get($group, $key, $default);
    }
}

if (!function_exists('session')) {
    function session(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return App\Core\Session::all();
        }
        return App\Core\Session::get($key, $default);
    }
}

if (!function_exists('redirect')) {
    function redirect(string $to): never
    {
        App\Core\Response::redirect($to);
    }
}

if (!function_exists('route')) {
    function route(string $name, array $params = []): string
    {
        return App\Core\Router::route($name, $params);
    }
}
