<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Setting
{
    private static ?array $cache = null;

    public static function all(): array
    {
        return Database::select(
            "SELECT * FROM settings ORDER BY setting_group, setting_key",
            []
        );
    }

    public static function grouped(): array
    {
        $all = self::all();
        $grouped = [];
        foreach ($all as $row) {
            $grouped[$row['setting_group']][$row['setting_key']] = $row['setting_value'];
        }
        return $grouped;
    }

    public static function get(string $group, string $key, mixed $default = null): mixed
    {
        if (self::$cache === null) {
            self::$cache = self::grouped();
        }

        return self::$cache[$group][$key] ?? $default;
    }

    public static function set(string $group, string $key, mixed $value): void
    {
        if (self::$cache === null) {
            self::$cache = self::grouped();
        }

        self::$cache[$group][$key] = $value;

        Database::query(
            "INSERT INTO settings (setting_group, setting_key, setting_value)
             VALUES (:group, :key, :value)
             ON DUPLICATE KEY UPDATE setting_value = :value",
            [
                'group' => $group,
                'key' => $key,
                'value' => is_array($value) ? json_encode($value) : (string) $value,
            ]
        );
    }

    public static function setMany(string $group, array $values): void
    {
        foreach ($values as $key => $value) {
            self::set($group, $key, $value);
        }
    }

    public static function clearCache(): void
    {
        self::$cache = null;
    }
}
