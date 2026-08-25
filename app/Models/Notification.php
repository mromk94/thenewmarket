<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Notification
{
    public static function forUser(int $userId): array
    {
        return Database::select(
            "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY is_read, created_at DESC",
            ['user_id' => $userId]
        );
    }

    public static function unreadCount(int $userId): int
    {
        $row = Database::first(
            "SELECT COUNT(*) as c FROM notifications WHERE user_id = :user_id AND is_read = 0",
            ['user_id' => $userId]
        );
        return (int) ($row['c'] ?? 0);
    }

    public static function create(array $data): int
    {
        return Database::insert('notifications', $data);
    }

    public static function markRead(int $id, int $userId): void
    {
        Database::query(
            "UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :user_id",
            ['id' => $id, 'user_id' => $userId]
        );
    }

    public static function markAllRead(int $userId): void
    {
        Database::query(
            "UPDATE notifications SET is_read = 1 WHERE user_id = :user_id",
            ['user_id' => $userId]
        );
    }
}
