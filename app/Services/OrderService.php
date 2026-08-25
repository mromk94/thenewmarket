<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\HttpException;
use App\Models\Product;
use App\Services\CartService;

class OrderService
{
    public static function find(int $orderId): ?array
    {
        return Database::first(
            "SELECT * FROM orders WHERE id = :id",
            ['id' => $orderId]
        );
    }

    public static function findByNumber(string $number): ?array
    {
        return Database::first(
            "SELECT * FROM orders WHERE order_number = :number",
            ['number' => $number]
        );
    }

    public static function items(int $orderId): array
    {
        return Database::select(
            "SELECT * FROM order_items WHERE order_id = :order_id",
            ['order_id' => $orderId]
        );
    }

    public static function create(int $customerId, array $cartItems): array
    {
        if (empty($cartItems)) {
            throw new HttpException('Your cart is empty.', 422);
        }

        $currency = (string) config('app.currency', 'USD');
        $orderNumber = 'ORD-' . strtoupper(uniqid());

        Database::beginTransaction();
        try {
            $orderId = Database::insert('orders', [
                'order_number' => $orderNumber,
                'customer_id' => $customerId,
                'subtotal' => 0,
                'discount' => 0,
                'tax' => 0,
                'shipping' => 0,
                'total' => 0,
                'currency' => $currency,
                'status' => 'pending_payment',
                'payment_status' => 'pending',
            ]);

            $subtotal = 0.0;

            foreach ($cartItems as $item) {
                $product = Product::findById((int) $item['product_id']);
                if (!$product) {
                    throw new HttpException('A product in your cart is no longer available.', 422);
                }

                $qty = (int) $item['quantity'];
                if ((int) $product['stock_qty'] < $qty) {
                    throw new HttpException('Not enough stock for ' . $product['name'], 422);
                }

                $unitPrice = (float) $product['price'];
                $lineTotal = round($unitPrice * $qty, 4);
                $subtotal += $lineTotal;

                $affiliateVendorId = !empty($item['affiliate_vendor_id']) ? (int) $item['affiliate_vendor_id'] : null;
                $commissionRate = null;
                $commissionAmount = null;

                if ($affiliateVendorId !== null && (int) $product['is_affiliate_eligible'] === 1) {
                    $commissionValue = (float) $product['affiliate_commission_value'];
                    if ($commissionValue > 0) {
                        if ($product['affiliate_commission_type'] === 'percentage') {
                            $commissionAmount = round($lineTotal * $commissionValue / 100, 4);
                            $commissionRate = round($commissionValue / 100, 4);
                        } else {
                            $commissionAmount = round($commissionValue * $qty, 4);
                            $commissionRate = 0;
                        }
                        $commissionAmount = min($commissionAmount, $lineTotal);
                    }
                }

                Database::insert('order_items', [
                    'order_id' => $orderId,
                    'product_id' => $product['id'],
                    'product_owner_id' => $product['owner_id'],
                    'product_name' => $product['name'],
                    'product_sku' => $product['sku'] ?? null,
                    'unit_price' => $unitPrice,
                    'quantity' => $qty,
                    'subtotal' => $lineTotal,
                    'affiliate_vendor_id' => $affiliateVendorId,
                    'affiliate_commission_rate' => $commissionRate,
                    'affiliate_commission_amount' => $commissionAmount,
                ]);
            }

            $subtotal = round($subtotal, 4);
            $summary = CartService::summary($cartItems);

            Database::update(
                'orders',
                [
                    'subtotal' => $summary['subtotal'],
                    'discount' => $summary['discount'],
                    'tax' => $summary['tax'],
                    'shipping' => $summary['shipping'],
                    'total' => $summary['total'],
                ],
                'id = ?',
                [$orderId]
            );

            Database::commit();

            return self::find($orderId);
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }
    }

    public static function markPaid(int $orderId, string $reference): void
    {
        $order = self::find($orderId);
        if (!$order) {
            throw new HttpException('Order not found.', 404);
        }

        if ($order['payment_status'] === 'paid') {
            return;
        }

        Database::beginTransaction();
        try {
            Database::update(
                'orders',
                ['status' => 'paid', 'payment_status' => 'paid'],
                'id = ?',
                [$orderId]
            );

            $payment = Database::first(
                "SELECT id FROM payments WHERE order_id = :order_id AND provider_reference = :ref",
                ['order_id' => $orderId, 'ref' => $reference]
            );

            if ($payment) {
                Database::update(
                    'payments',
                    ['status' => 'success', 'paid_at' => date('Y-m-d H:i:s')],
                    'id = ?',
                    [(int) $payment['id']]
                );
            }

            foreach (self::items($orderId) as $item) {
                if (!empty($item['affiliate_vendor_id']) && !empty($item['affiliate_commission_amount'])) {
                    self::createAffiliateCommission($item);
                }
            }

            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }
    }

    private static function createAffiliateCommission(array $item): void
    {
        $vendor = Database::first(
            "SELECT * FROM vendors WHERE id = :id",
            ['id' => $item['affiliate_vendor_id']]
        );

        if (!$vendor || $vendor['status'] !== 'approved') {
            return;
        }

        Database::insert('affiliate_commissions', [
            'order_item_id' => $item['id'],
            'vendor_id' => $item['affiliate_vendor_id'],
            'product_id' => $item['product_id'],
            'commission_rate' => $item['affiliate_commission_rate'],
            'commission_amount' => $item['affiliate_commission_amount'],
            'status' => 'available',
            'paid_at' => null,
        ]);

        WalletService::credit(
            (int) $vendor['user_id'],
            (float) $item['affiliate_commission_amount'],
            'commission',
            'order_item',
            (int) $item['id'],
            'Affiliate commission for ' . $item['product_name']
        );
    }
}
