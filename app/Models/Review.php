<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Review
{
    public static function forProduct(int $productId, int $limit = 20): array
    {
        return Database::select(
            "SELECT r.*, up.first_name, up.last_name, u.email
             FROM reviews r
             JOIN users u ON u.id = r.customer_id
             LEFT JOIN user_profiles up ON up.user_id = u.id
             WHERE r.product_id = :product_id
               AND r.status = 'approved'
             ORDER BY r.created_at DESC
             LIMIT {$limit}",
            ['product_id' => $productId]
        );
    }

    public static function forCustomer(int $customerId, int $limit = 50): array
    {
        return Database::select(
            "SELECT r.*, p.name as product_name, p.slug as product_slug
             FROM reviews r
             JOIN products p ON p.id = r.product_id
             WHERE r.customer_id = :customer_id
             ORDER BY r.created_at DESC
             LIMIT {$limit}",
            ['customer_id' => $customerId]
        );
    }

    public static function pending(int $limit = 50): array
    {
        return Database::select(
            "SELECT r.*, p.name as product_name, p.slug as product_slug, u.email
             FROM reviews r
             JOIN products p ON p.id = r.product_id
             JOIN users u ON u.id = r.customer_id
             WHERE r.status = 'pending'
             ORDER BY r.created_at DESC
             LIMIT {$limit}",
            []
        );
    }

    public static function stats(int $productId): array
    {
        $row = Database::first(
            "SELECT COALESCE(AVG(rating), 0) as average, COUNT(*) as count
             FROM reviews
             WHERE product_id = :product_id AND status = 'approved'",
            ['product_id' => $productId]
        );
        return [
            'average' => round((float) ($row['average'] ?? 0), 2),
            'count' => (int) ($row['count'] ?? 0),
        ];
    }

    public static function canReview(int $customerId, int $productId, int $orderId): bool
    {
        $order = Database::first(
            "SELECT * FROM orders WHERE id = :order_id AND customer_id = :customer_id AND payment_status = 'paid'",
            ['order_id' => $orderId, 'customer_id' => $customerId]
        );
        if (!$order) {
            return false;
        }

        $item = Database::first(
            "SELECT * FROM order_items WHERE order_id = :order_id AND product_id = :product_id",
            ['order_id' => $orderId, 'product_id' => $productId]
        );
        if (!$item) {
            return false;
        }

        $existing = Database::first(
            "SELECT id FROM reviews WHERE customer_id = :customer_id AND product_id = :product_id AND order_id = :order_id",
            ['customer_id' => $customerId, 'product_id' => $productId, 'order_id' => $orderId]
        );
        return !$existing;
    }

    public static function create(array $data): int
    {
        return Database::insert('reviews', $data);
    }

    public static function setStatus(int $id, string $status): void
    {
        Database::update('reviews', ['status' => $status], 'id = ?', [$id]);
    }

    public static function delete(int $id): void
    {
        Database::query("DELETE FROM reviews WHERE id = :id", ['id' => $id]);
    }
}
