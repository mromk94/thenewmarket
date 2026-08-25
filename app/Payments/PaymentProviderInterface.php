<?php

declare(strict_types=1);

namespace App\Payments;

interface PaymentProviderInterface
{
    public function initialize(float $amount, string $currency, string $reference, array $meta): array;
    public function verify(string $reference, array $payload = []): array;
    public function getName(): string;
}
