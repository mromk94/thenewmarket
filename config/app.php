<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'The New Age Marketplace'),
    'env' => env('APP_ENV', 'local'),
    'url' => rtrim(env('APP_URL', 'http://localhost:8000'), '/'),
    'debug' => filter_var(env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'currency' => env('APP_CURRENCY', 'USD'),
    'currency_symbol' => env('APP_CURRENCY_SYMBOL', '$'),
];
