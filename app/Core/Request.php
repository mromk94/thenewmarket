<?php

declare(strict_types=1);

namespace App\Core;

class Request
{
    public static function uri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $uri = explode('?', $uri)[0];
        return '/' . ltrim($uri, '/');
    }

    public static function method(): string
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if (strtoupper($method) === 'POST' && isset($_POST['_method'])) {
            return strtoupper((string) $_POST['_method']);
        }

        return strtoupper($method);
    }

    public static function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public static function all(): array
    {
        return array_merge($_GET, $_POST);
    }

    public static function has(string $key): bool
    {
        return isset($_POST[$key]) || isset($_GET[$key]);
    }

    public static function only(array $keys): array
    {
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = self::input($key);
        }
        return $values;
    }

    public static function header(string $name, mixed $default = null): mixed
    {
        $name = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $_SERVER[$name] ?? $default;
    }

    public static function isPost(): bool
    {
        return self::method() === 'POST';
    }

    public static function isGet(): bool
    {
        return self::method() === 'GET';
    }

    public static function isAjax(): bool
    {
        return self::header('X-Requested-With') === 'XMLHttpRequest';
    }

    public static function ip(): ?string
    {
        return $_SERVER['REMOTE_ADDR'] ?? null;
    }
}
