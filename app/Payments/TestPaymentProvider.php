<?php

declare(strict_types=1);

namespace App\Payments;

class TestPaymentProvider implements PaymentProviderInterface
{
    public function initialize(float $amount, string $currency, string $reference, array $meta = []): array
    {
        return [
            'status' => 'success',
            'reference' => $reference,
            'url' => url('/payment/test/callback?reference=' . urlencode($reference) . '&order_id=' . (int) ($meta['order_id'] ?? 0)),
        ];
    }

    public function verify(string $reference, array $payload = []): array
    {
        return [
            'status' => 'success',
            'reference' => $reference,
            'amount' => (float) ($payload['amount'] ?? 0),
            'currency' => $payload['currency'] ?? 'USD',
        ];
    }

    public function getName(): string
    {
        return 'test';
    }
}
