<?php

declare(strict_types=1);

namespace App\Core;

class Config
{
    private static array $data = [];
    private static bool $loaded = false;

    public static function load(): void
    {
        if (self::$loaded) {
            return;
        }

        $dir = BASE_PATH . '/config';
        $files = glob($dir . '/*.php') ?: [];
        foreach ($files as $file) {
            $name = basename($file, '.php');
            self::$data[$name] = require $file;
        }

        self::$loaded = true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $parts = explode('.', $key);
        $value = self::$data[$parts[0]] ?? null;

        if (!isset($parts[1])) {
            return $value ?? $default;
        }

        array_shift($parts);
        foreach ($parts as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return $default;
            }
            $value = $value[$part];
        }

        return $value;
    }

    public static function set(string $key, mixed $value): void
    {
        $parts = explode('.', $key);
        $ref = &self::$data;
        foreach ($parts as $part) {
            $ref = &$ref[$part];
        }
        $ref = $value;
    }

    public static function all(): array
    {
        return self::$data;
    }
}
