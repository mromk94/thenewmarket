<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Coupon
{
    public static function all(): array
    {
        return Database::select("SELECT * FROM coupons ORDER BY created_at DESC", []);
    }

    public static function findById(int $id): ?array
    {
        return Database::first("SELECT * FROM coupons WHERE id = :id", ['id' => $id]);
    }

    public static function findByCode(string $code): ?array
    {
        return Database::first(
            "SELECT * FROM coupons WHERE code = :code AND is_active = 1",
            ['code' => $code]
        );
    }

    public static function isValid(array $coupon, float $subtotal): bool
    {
        if (!$coupon) return false;
        if ((float) $coupon['value'] <= 0) return false;
        if ($subtotal < (float) $coupon['min_order']) return false;
        if ($coupon['max_uses'] !== null && (int) $coupon['uses'] >= (int) $coupon['max_uses']) return false;
        if (!empty($coupon['valid_from']) && date('Y-m-d') < $coupon['valid_from']) return false;
        if (!empty($coupon['valid_to']) && date('Y-m-d') > $coupon['valid_to']) return false;
        return true;
    }

    public static function calculateDiscount(array $coupon, float $subtotal): float
    {
        if (!self::isValid($coupon, $subtotal)) return 0.0;

        if ($coupon['type'] === 'fixed') {
            return min((float) $coupon['value'], $subtotal);
        }

        return round($subtotal * ((float) $coupon['value'] / 100), 4);
    }

    public static function incrementUses(int $id): void
    {
        Database::query("UPDATE coupons SET uses = uses + 1 WHERE id = :id", ['id' => $id]);
    }

    public static function create(array $data): int
    {
        return Database::insert('coupons', $data);
    }

    public static function update(int $id, array $data): void
    {
        Database::update('coupons', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id): void
    {
        Database::query("DELETE FROM coupons WHERE id = :id", ['id' => $id]);
    }
}
