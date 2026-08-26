<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Address
{
    public static function forUser(int $userId, ?string $type = null): array
    {
        $sql = "SELECT * FROM addresses WHERE user_id = :user_id";
        $params = ['user_id' => $userId];

        if ($type !== null) {
            $sql .= " AND type = :type";
            $params['type'] = $type;
        }

        $sql .= " ORDER BY is_default DESC, created_at DESC";

        return Database::select($sql, $params);
    }

    public static function findDefault(int $userId, string $type): ?array
    {
        return Database::first(
            "SELECT * FROM addresses WHERE user_id = :user_id AND type = :type ORDER BY is_default DESC, created_at DESC",
            ['user_id' => $userId, 'type' => $type]
        );
    }

    public static function create(array $data): int
    {
        return Database::insert('addresses', $data);
    }

    public static function delete(int $id, int $userId): void
    {
        Database::query(
            "DELETE FROM addresses WHERE id = :id AND user_id = :user_id",
            ['id' => $id, 'user_id' => $userId]
        );
    }

    public static function clearDefault(int $userId, string $type): void
    {
        Database::query(
            "UPDATE addresses SET is_default = 0 WHERE user_id = :user_id AND type = :type",
            ['user_id' => $userId, 'type' => $type]
        );
    }

    public static function setDefault(int $id, int $userId, string $type): void
    {
        self::clearDefault($userId, $type);
        Database::query(
            "UPDATE addresses SET is_default = 1 WHERE id = :id AND user_id = :user_id",
            ['id' => $id, 'user_id' => $userId]
        );
    }
}
