<?php

declare(strict_types=1);

namespace App\Core;

class Csrf
{
    private const KEY = '_csrf_token';

    public static function token(): string
    {
        if (!Session::has(self::KEY)) {
            Session::set(self::KEY, bin2hex(random_bytes(32)));
        }
        return (string) Session::get(self::KEY);
    }

    public static function verify(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }
        return hash_equals(self::token(), (string) $token);
    }
}
