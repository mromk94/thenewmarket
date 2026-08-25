<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\HttpException;
use App\Payments\PaymentProviderInterface;
use App\Payments\TestPaymentProvider;

class PaymentService
{
    public static function provider(string $name): PaymentProviderInterface
    {
        return match ($name) {
            'test' => new TestPaymentProvider(),
            default => throw new HttpException('Payment provider not configured: ' . $name, 500),
        };
    }

    public static function initiate(int $orderId): array
    {
        $order = OrderService::find($orderId);
        if (!$order) {
            throw new HttpException('Order not found.', 404);
        }

        $providerName = (string) config('payment.provider', 'test');
        $provider = self::provider($providerName);
        $reference = $order['order_number'] . '-' . time();

        Database::insert('payments', [
            'order_id' => $orderId,
            'provider' => $providerName,
            'provider_reference' => $reference,
            'amount' => $order['total'],
            'currency' => $order['currency'],
            'status' => 'initiated',
        ]);

        return $provider->initialize(
            (float) $order['total'],
            $order['currency'],
            $reference,
            ['order_id' => $orderId]
        );
    }

    public static function process(string $reference, int $orderId, array $payload = []): void
    {
        $order = OrderService::find($orderId);
        if (!$order) {
            throw new HttpException('Order not found.', 404);
        }

        $payment = Database::first(
            "SELECT * FROM payments WHERE provider_reference = :ref AND order_id = :order_id",
            ['ref' => $reference, 'order_id' => $orderId]
        );

        if (!$payment) {
            throw new HttpException('Payment record not found.', 404);
        }

        if ($payment['status'] === 'success') {
            throw new HttpException('Payment already processed.', 422);
        }

        $providerName = (string) config('payment.provider', 'test');
        $provider = self::provider($providerName);
        $result = $provider->verify($reference, $payload);

        if (($result['status'] ?? '') !== 'success') {
            throw new HttpException('Payment verification failed.', 422);
        }

        OrderService::markPaid($orderId, $reference);
    }
}
