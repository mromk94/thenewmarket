<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class PaymentProof
{
    public static function find(int $id): ?array
    {
        return Database::first(
            "SELECT pp.*, pm.name as method_name, pm.type as method_type
             FROM payment_proofs pp
             JOIN payment_methods pm ON pm.id = pp.payment_method_id
             WHERE pp.id = :id",
            ['id' => $id]
        );
    }

    public static function forOrder(int $orderId): ?array
    {
        return Database::first(
            "SELECT pp.*, pm.name as method_name, pm.type as method_type
             FROM payment_proofs pp
             JOIN payment_methods pm ON pm.id = pp.payment_method_id
             WHERE pp.order_id = :order_id",
            ['order_id' => $orderId]
        );
    }

    public static function pending(): array
    {
        return Database::select(
            "SELECT pp.*, o.order_number, o.total, o.customer_id, u.email, pm.name as method_name
             FROM payment_proofs pp
             JOIN orders o ON o.id = pp.order_id
             JOIN users u ON u.id = o.customer_id
             JOIN payment_methods pm ON pm.id = pp.payment_method_id
             WHERE pp.status = 'pending'
             ORDER BY pp.created_at DESC",
            []
        );
    }

    public static function create(array $data): int
    {
        return Database::insert('payment_proofs', $data);
    }

    public static function setStatus(int $id, string $status, ?string $adminNote = null): void
    {
        $update = ['status' => $status];
        if ($adminNote !== null) {
            $update['admin_note'] = $adminNote;
        }
        Database::update('payment_proofs', $update, 'id = ?', [$id]);
    }
}
