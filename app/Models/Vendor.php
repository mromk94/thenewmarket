<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Vendor
{
    public static function findApproved(): array
    {
        return Database::select(
            "SELECT * FROM vendors WHERE status = 'approved' ORDER BY business_name",
            []
        );
    }

    public static function findAll(array $filters = []): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'v.status = :status';
            $params['status'] = $filters['status'];
        }

        $whereSql = implode(' AND ', $where);

        return Database::select(
            "SELECT v.*, u.email, up.first_name, up.last_name, u.created_at as applied_at
             FROM vendors v
             JOIN users u ON u.id = v.user_id
             LEFT JOIN user_profiles up ON up.user_id = u.id
             WHERE {$whereSql}
             ORDER BY v.created_at DESC",
            $params
        );
    }

    public static function findPending(): array
    {
        return self::findAll(['status' => 'pending']);
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::first(
            "SELECT * FROM vendors WHERE slug = :slug AND status = 'approved'",
            ['slug' => $slug]
        );
    }

    public static function findById(int $id): ?array
    {
        return Database::first(
            "SELECT v.*, u.email FROM vendors v JOIN users u ON u.id = v.user_id WHERE v.id = :id",
            ['id' => $id]
        );
    }

    public static function setStatus(int $id, string $status, ?string $reason = null): void
    {
        $data = ['status' => $status];
        if ($reason !== null) {
            $data['rejection_reason'] = $reason;
        }
        Database::update('vendors', $data, 'id = ?', [$id]);
        $vendor = self::findById($id);
        if ($vendor) {
            $userStatus = $status === 'approved' ? 'active' : ($status === 'suspended' || $status === 'banned' ? 'suspended' : 'pending');
            Database::update('users', ['status' => $userStatus], 'id = ?', [(int) $vendor['user_id']]);
        }
    }

    public static function update(int $id, array $data): void
    {
        Database::update('vendors', $data, 'id = ?', [$id]);
    }

    public static function salesCount(int $vendorId): int
    {
        $vendor = self::findById($vendorId);
        if (!$vendor) {
            return 0;
        }

        $row = Database::first(
            "SELECT COALESCE(SUM(oi.quantity), 0) as c
             FROM order_items oi
             JOIN orders o ON o.id = oi.order_id
             WHERE (oi.affiliate_vendor_id = :vendor_id OR oi.product_owner_id = :owner_id)
               AND o.payment_status = 'paid'",
            ['vendor_id' => $vendorId, 'owner_id' => (int) $vendor['user_id']]
        );
        return (int) ($row['c'] ?? 0);
    }
}
