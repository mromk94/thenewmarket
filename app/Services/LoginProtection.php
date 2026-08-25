<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\HttpException;
use App\Core\Session;

class LoginProtection
{
    private const SESSION_KEY = '_login_attempts';

    public static function check(string $email): void
    {
        $attempts = Session::get(self::SESSION_KEY, []);
        $key = self::key($email);

        if (!isset($attempts[$key])) {
            return;
        }

        $limit = (int) config('security.login_attempts', 5);
        $decay = (int) config('security.login_decay', 900);

        if ($attempts[$key]['count'] >= $limit && (time() - $attempts[$key]['last']) < $decay) {
            throw new HttpException('Too many login attempts. Please try again later.', 429);
        }

        if ((time() - $attempts[$key]['last']) >= $decay) {
            $attempts[$key]['count'] = 0;
            Session::set(self::SESSION_KEY, $attempts);
        }
    }

    public static function record(string $email): void
    {
        $attempts = Session::get(self::SESSION_KEY, []);
        $key = self::key($email);

        $attempts[$key] = [
            'count' => ($attempts[$key]['count'] ?? 0) + 1,
            'last' => time(),
        ];

        Session::set(self::SESSION_KEY, $attempts);
    }

    public static function clear(string $email): void
    {
        $attempts = Session::get(self::SESSION_KEY, []);
        unset($attempts[self::key($email)]);
        Session::set(self::SESSION_KEY, $attempts);
    }

    private static function key(string $email): string
    {
        return md5(strtolower(trim($email)));
    }
}
