<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Refund
{
    public static function forCustomer(int $customerId): array
    {
        return Database::select(
            "SELECT r.*, o.order_number
             FROM refunds r
             JOIN orders o ON o.id = r.order_id
             WHERE r.customer_id = :customer_id
             ORDER BY r.created_at DESC",
            ['customer_id' => $customerId]
        );
    }

    public static function pending(): array
    {
        return Database::select(
            "SELECT r.*, o.order_number, u.email
             FROM refunds r
             JOIN orders o ON o.id = r.order_id
             JOIN users u ON u.id = r.customer_id
             WHERE r.status = 'pending'
             ORDER BY r.created_at DESC",
            []
        );
    }

    public static function all(): array
    {
        return Database::select(
            "SELECT r.*, o.order_number, u.email
             FROM refunds r
             JOIN orders o ON o.id = r.order_id
             JOIN users u ON u.id = r.customer_id
             ORDER BY r.created_at DESC",
            []
        );
    }

    public static function find(int $id): ?array
    {
        return Database::first(
            "SELECT r.*, o.order_number, u.email
             FROM refunds r
             JOIN orders o ON o.id = r.order_id
             JOIN users u ON u.id = r.customer_id
             WHERE r.id = :id",
            ['id' => $id]
        );
    }

    public static function create(array $data): int
    {
        return Database::insert('refunds', $data);
    }

    public static function setStatus(int $id, string $status, ?string $adminNote = null): void
    {
        $update = ['status' => $status];
        if ($adminNote !== null) {
            $update['admin_note'] = $adminNote;
        }
        Database::update('refunds', $update, 'id = ?', [$id]);
    }
}
