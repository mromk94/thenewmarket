<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\HttpException;
use App\Core\Session;
use App\Models\Product;
use App\Models\Vendor;

class GuestCart
{
    private const CART_KEY = 'guest_cart';

    public static function items(): array
    {
        $raw = Session::get(self::CART_KEY, []);
        $items = [];

        foreach ($raw as $productId => $entry) {
            $product = Product::findById((int) $productId);
            if (!$product) {
                continue;
            }

            $affiliateName = null;
            if (!empty($entry['affiliate_vendor_id'])) {
                $affiliate = Vendor::findById((int) $entry['affiliate_vendor_id']);
                $affiliateName = $affiliate['business_name'] ?? null;
            }

            $items[] = [
                'cart_item_id' => (int) $productId,
                'product_id' => (int) $productId,
                'quantity' => (int) $entry['quantity'],
                'unit_price' => (float) $entry['unit_price'],
                'affiliate_vendor_id' => !empty($entry['affiliate_vendor_id']) ? (int) $entry['affiliate_vendor_id'] : null,
                'affiliate_vendor_name' => $affiliateName,
                'name' => (string) $product['name'],
                'slug' => (string) $product['slug'],
                'stock_qty' => (int) $product['stock_qty'],
                'vendor_name' => $product['vendor_name'] ?? null,
                'thumbnail' => Product::thumbnail((int) $productId),
            ];
        }

        return $items;
    }

    public static function count(): int
    {
        $raw = Session::get(self::CART_KEY, []);
        return (int) array_sum(array_column($raw, 'quantity'));
    }

    public static function add(int $productId, int $quantity, ?int $affiliateVendorId = null): void
    {
        if ($quantity < 1) {
            throw new HttpException('Quantity must be at least 1.', 422);
        }

        $product = Product::findById($productId);
        if (!$product) {
            throw new HttpException('Product not found.', 404);
        }

        $cart = Session::get(self::CART_KEY, []);

        if (isset($cart[$productId])) {
            $newQty = $cart[$productId]['quantity'] + $quantity;
            if ((int) $product['stock_qty'] < $newQty) {
                throw new HttpException('Not enough stock available.', 422);
            }
            $cart[$productId]['quantity'] = $newQty;
        } else {
            if ((int) $product['stock_qty'] < $quantity) {
                throw new HttpException('Not enough stock available.', 422);
            }
            $cart[$productId] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => (float) $product['price'],
                'affiliate_vendor_id' => $affiliateVendorId,
            ];
        }

        Session::set(self::CART_KEY, $cart);
    }

    public static function update(int $productId, int $quantity): void
    {
        $cart = Session::get(self::CART_KEY, []);
        if (!isset($cart[$productId])) {
            throw new HttpException('Cart item not found.', 404);
        }

        if ($quantity < 1) {
            self::remove($productId);
            return;
        }

        $product = Product::findById($productId);
        if (!$product) {
            self::remove($productId);
            throw new HttpException('Product no longer available.', 404);
        }

        if ((int) $product['stock_qty'] < $quantity) {
            throw new HttpException('Not enough stock available.', 422);
        }

        $cart[$productId]['quantity'] = $quantity;
        Session::set(self::CART_KEY, $cart);
    }

    public static function remove(int $productId): void
    {
        $cart = Session::get(self::CART_KEY, []);
        unset($cart[$productId]);
        Session::set(self::CART_KEY, $cart);
    }

    public static function clear(): void
    {
        Session::remove(self::CART_KEY);
    }

    public static function migrateToUser(int $userId): void
    {
        $raw = Session::get(self::CART_KEY, []);
        if (empty($raw)) {
            return;
        }

        foreach ($raw as $productId => $entry) {
            try {
                CartService::add(
                    $userId,
                    (int) $productId,
                    (int) ($entry['quantity'] ?? 1),
                    !empty($entry['affiliate_vendor_id']) ? (int) $entry['affiliate_vendor_id'] : null
                );
            } catch (HttpException) {
                // Skip items that fail (e.g. out of stock or unavailable)
            }
        }

        self::clear();
    }
}
