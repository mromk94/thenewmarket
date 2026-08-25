<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Withdrawal
{
    public static function forUser(int $userId): array
    {
        return Database::select(
            "SELECT * FROM vendor_withdrawals WHERE user_id = :user_id ORDER BY created_at DESC",
            ['user_id' => $userId]
        );
    }

    public static function pending(): array
    {
        return Database::select(
            "SELECT vw.*, u.email FROM vendor_withdrawals vw
             JOIN users u ON u.id = vw.user_id
             WHERE vw.status = 'pending'
             ORDER BY vw.created_at DESC",
            []
        );
    }

    public static function all(): array
    {
        return Database::select(
            "SELECT vw.*, u.email FROM vendor_withdrawals vw
             JOIN users u ON u.id = vw.user_id
             ORDER BY vw.created_at DESC",
            []
        );
    }

    public static function find(int $id): ?array
    {
        return Database::first(
            "SELECT vw.*, u.email FROM vendor_withdrawals vw
             JOIN users u ON u.id = vw.user_id
             WHERE vw.id = :id",
            ['id' => $id]
        );
    }

    public static function create(array $data): int
    {
        return Database::insert('vendor_withdrawals', $data);
    }

    public static function setStatus(int $id, string $status, ?string $adminNote = null): void
    {
        $update = ['status' => $status];
        if ($adminNote !== null) {
            $update['admin_note'] = $adminNote;
        }
        Database::update('vendor_withdrawals', $update, 'id = ?', [$id]);
    }
}
