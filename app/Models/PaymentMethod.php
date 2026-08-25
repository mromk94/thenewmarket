<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class PaymentMethod
{
    public static function allActive(): array
    {
        return Database::select(
            "SELECT * FROM payment_methods WHERE is_active = 1 ORDER BY sort_order, name",
            []
        );
    }

    public static function all(): array
    {
        return Database::select(
            "SELECT * FROM payment_methods ORDER BY sort_order, name",
            []
        );
    }

    public static function find(int $id): ?array
    {
        return Database::first(
            "SELECT * FROM payment_methods WHERE id = :id",
            ['id' => $id]
        );
    }

    public static function create(array $data): int
    {
        return Database::insert('payment_methods', $data);
    }

    public static function update(int $id, array $data): void
    {
        Database::update('payment_methods', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id): void
    {
        Database::query("DELETE FROM payment_methods WHERE id = :id", ['id' => $id]);
    }
}
