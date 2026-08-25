<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class EmailTemplate
{
    public static function findByKey(string $key): ?array
    {
        return Database::first(
            "SELECT * FROM email_templates WHERE `key` = :key AND is_active = 1",
            ['key' => $key]
        );
    }

    public static function all(): array
    {
        return Database::select(
            "SELECT * FROM email_templates ORDER BY `key`",
            []
        );
    }

    public static function create(array $data): int
    {
        return Database::insert('email_templates', $data);
    }

    public static function update(int $id, array $data): void
    {
        Database::update('email_templates', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id): void
    {
        Database::query("DELETE FROM email_templates WHERE id = :id", ['id' => $id]);
    }
}
