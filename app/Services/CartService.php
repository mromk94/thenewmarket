<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\HttpException;
use App\Models\Product;

class CartService
{
    public static function getOrCreateCart(int $userId): int
    {
        $existing = Database::first(
            "SELECT id FROM cart WHERE user_id = :user_id",
            ['user_id' => $userId]
        );

        if ($existing) {
            return (int) $existing['id'];
        }

        return Database::insert('cart', ['user_id' => $userId]);
    }

    public static function add(int $userId, int $productId, int $quantity, ?int $affiliateVendorId = null): void
    {
        $product = Product::findById($productId);
        if (!$product) {
            throw new HttpException('Product not found.', 404);
        }

        if ((int) $product['stock_qty'] < $quantity) {
            throw new HttpException('Not enough stock available.', 422);
        }

        $cartId = self::getOrCreateCart($userId);

        $existing = Database::first(
            "SELECT id, quantity FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id",
            ['cart_id' => $cartId, 'product_id' => $productId]
        );

        if ($existing) {
            $newQty = (int) $existing['quantity'] + $quantity;
            if ($newQty > (int) $product['stock_qty']) {
                throw new HttpException('Not enough stock available.', 422);
            }
            Database::update(
                'cart_items',
                ['quantity' => $newQty],
                'id = ?',
                [(int) $existing['id']]
            );
        } else {
            Database::insert('cart_items', [
                'cart_id' => $cartId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $product['price'],
                'affiliate_vendor_id' => $affiliateVendorId,
            ]);
        }
    }

    public static function items(int $userId): array
    {
        $cart = Database::first(
            "SELECT id FROM cart WHERE user_id = :user_id",
            ['user_id' => $userId]
        );

        if (!$cart) {
            return [];
        }

        return Database::select(
            "SELECT ci.id as cart_item_id, ci.quantity, ci.unit_price, ci.affiliate_vendor_id,
                    p.id as product_id, p.name, p.slug, p.stock_qty,
                    v.business_name as vendor_name,
                    av.business_name as affiliate_vendor_name,
                    (SELECT file_path FROM product_images WHERE product_id = p.id AND file_path != '' ORDER BY is_thumbnail DESC, sort_order LIMIT 1) as thumbnail
             FROM cart_items ci
             JOIN products p ON p.id = ci.product_id
             LEFT JOIN vendors v ON v.id = p.vendor_id
             LEFT JOIN vendors av ON av.id = ci.affiliate_vendor_id
             WHERE ci.cart_id = :cart_id",
            ['cart_id' => $cart['id']]
        );
    }

    public static function update(int $cartItemId, int $quantity, int $userId): void
    {
        $item = Database::first(
            "SELECT ci.*, p.stock_qty FROM cart_items ci
             JOIN products p ON p.id = ci.product_id
             WHERE ci.id = :id AND ci.cart_id = (SELECT id FROM cart WHERE user_id = :user_id)",
            ['id' => $cartItemId, 'user_id' => $userId]
        );

        if (!$item) {
            throw new HttpException('Cart item not found.', 404);
        }

        if ($quantity < 1) {
            self::remove($cartItemId, $userId);
            return;
        }

        if ($quantity > (int) $item['stock_qty']) {
            throw new HttpException('Not enough stock available.', 422);
        }

        Database::update(
            'cart_items',
            ['quantity' => $quantity],
            'id = ?',
            [$cartItemId]
        );
    }

    public static function remove(int $cartItemId, int $userId): void
    {
        Database::query(
            "DELETE ci FROM cart_items ci
             JOIN cart c ON c.id = ci.cart_id
             WHERE ci.id = :id AND c.user_id = :user_id",
            ['id' => $cartItemId, 'user_id' => $userId]
        );
    }

    public static function clear(int $userId): void
    {
        Database::query(
            "DELETE ci FROM cart_items ci
             JOIN cart c ON c.id = ci.cart_id
             WHERE c.user_id = :user_id",
            ['user_id' => $userId]
        );
    }

    public static function count(int $userId): int
    {
        $row = Database::first(
            "SELECT COALESCE(SUM(ci.quantity), 0) as c
             FROM cart c
             JOIN cart_items ci ON ci.cart_id = c.id
             WHERE c.user_id = :user_id",
            ['user_id' => $userId]
        );
        return (int) ($row['c'] ?? 0);
    }

    public static function total(array $items): float
    {
        $total = 0.0;
        foreach ($items as $item) {
            $total += (float) $item['unit_price'] * (int) $item['quantity'];
        }
        return $total;
    }

    public static function summary(array $items): array
    {
        $subtotal = self::total($items);

        $shippingRate = (float) setting('commerce', 'shipping_rate', 0);
        $freeThreshold = (float) setting('commerce', 'free_shipping_threshold', 0);
        $taxRate = (float) setting('commerce', 'tax_rate', 0);
        $discountPercent = (float) setting('commerce', 'discount_percent', 0);

        $shipping = ($freeThreshold > 0 && $subtotal >= $freeThreshold) ? 0.0 : $shippingRate;
        $tax = $subtotal * ($taxRate / 100);
        $discount = $subtotal * ($discountPercent / 100);

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $subtotal + $shipping + $tax - $discount,
        ];
    }
}
