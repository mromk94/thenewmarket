<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Session;

class Currency
{
    public static function all(): array
    {
        return Database::select(
            "SELECT * FROM currencies ORDER BY is_default DESC, code",
            []
        );
    }

    public static function allActive(): array
    {
        return Database::select(
            "SELECT * FROM currencies WHERE is_active = 1 ORDER BY is_default DESC, code",
            []
        );
    }

    public static function find(int $id): ?array
    {
        return Database::first(
            "SELECT * FROM currencies WHERE id = :id",
            ['id' => $id]
        );
    }

    public static function findByCode(string $code): ?array
    {
        return Database::first(
            "SELECT * FROM currencies WHERE code = :code AND is_active = 1",
            ['code' => strtoupper($code)]
        );
    }

    public static function default(): array
    {
        $row = Database::first(
            "SELECT * FROM currencies WHERE is_default = 1 AND is_active = 1 LIMIT 1",
            []
        );
        return $row ?: ['code' => 'USD', 'symbol' => '$', 'exchange_rate' => 1.0];
    }

    public static function current(): array
    {
        $sessionCode = Session::get('currency');
        if ($sessionCode) {
            $currency = self::findByCode((string) $sessionCode);
            if ($currency) {
                return $currency;
            }
        }
        return self::default();
    }

    public static function setCurrent(string $code): void
    {
        $currency = self::findByCode($code);
        if ($currency) {
            Session::set('currency', $currency['code']);
        }
    }

    public static function create(array $data): int
    {
        $data['code'] = strtoupper(trim($data['code']));
        return Database::insert('currencies', $data);
    }

    public static function update(int $id, array $data): void
    {
        if (isset($data['code'])) {
            $data['code'] = strtoupper(trim($data['code']));
        }
        Database::update('currencies', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id): void
    {
        Database::query("DELETE FROM currencies WHERE id = :id AND is_default = 0", ['id' => $id]);
    }

    public static function setDefault(int $id): void
    {
        Database::query("UPDATE currencies SET is_default = 0", []);
        Database::update('currencies', ['is_default' => 1], 'id = ?', [$id]);
    }

    public static function convert(float $amount, ?array $from = null, ?array $to = null): float
    {
        $fromRate = (float) ($from['exchange_rate'] ?? self::default()['exchange_rate']);
        $toRate = (float) ($to['exchange_rate'] ?? self::default()['exchange_rate']);
        if ($fromRate <= 0 || $toRate <= 0) {
            return $amount;
        }
        return $amount * ($toRate / $fromRate);
    }

    public static function toCurrent(float $amount, ?array $from = null): float
    {
        return self::convert($amount, $from, self::current());
    }
}
