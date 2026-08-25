<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class VendorDeposit
{
    public static function forUser(int $userId): array
    {
        return Database::select(
            "SELECT vd.*, dm.name as method_name, dm.type as method_type
             FROM vendor_deposits vd
             JOIN deposit_methods dm ON dm.id = vd.deposit_method_id
             WHERE vd.user_id = :user_id
             ORDER BY vd.created_at DESC",
            ['user_id' => $userId]
        );
    }

    public static function pending(): array
    {
        return Database::select(
            "SELECT vd.*, u.email, dm.name as method_name, dm.type as method_type
             FROM vendor_deposits vd
             JOIN users u ON u.id = vd.user_id
             JOIN deposit_methods dm ON dm.id = vd.deposit_method_id
             WHERE vd.status = 'pending'
             ORDER BY vd.created_at DESC",
            []
        );
    }

    public static function all(): array
    {
        return Database::select(
            "SELECT vd.*, u.email, dm.name as method_name, dm.type as method_type
             FROM vendor_deposits vd
             JOIN users u ON u.id = vd.user_id
             JOIN deposit_methods dm ON dm.id = vd.deposit_method_id
             ORDER BY vd.created_at DESC",
            []
        );
    }

    public static function find(int $id): ?array
    {
        return Database::first(
            "SELECT vd.*, u.email, dm.name as method_name, dm.type as method_type
             FROM vendor_deposits vd
             JOIN users u ON u.id = vd.user_id
             JOIN deposit_methods dm ON dm.id = vd.deposit_method_id
             WHERE vd.id = :id",
            ['id' => $id]
        );
    }

    public static function create(array $data): int
    {
        return Database::insert('vendor_deposits', $data);
    }

    public static function setStatus(int $id, string $status, ?string $adminNote = null): void
    {
        $update = ['status' => $status];
        if ($adminNote !== null) {
            $update['admin_note'] = $adminNote;
        }
        Database::update('vendor_deposits', $update, 'id = ?', [$id]);
    }
}
