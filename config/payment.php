<?php

declare(strict_types=1);

return [
    'provider' => env('PAYMENT_PROVIDER', 'test'),
    'paystack' => [
        'secret' => env('PAYSTACK_SECRET_KEY', ''),
        'public' => env('PAYSTACK_PUBLIC_KEY', ''),
    ],
    'flutterwave' => [
        'secret' => env('FLUTTERWAVE_SECRET_KEY', ''),
    ],
    'stripe' => [
        'secret' => env('STRIPE_SECRET_KEY', ''),
    ],
];
