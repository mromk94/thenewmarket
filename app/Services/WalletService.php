<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\HttpException;

class WalletService
{
    public static function ensureWallet(int $userId): int
    {
        $wallet = Database::first(
            "SELECT id FROM wallets WHERE user_id = :user_id",
            ['user_id' => $userId]
        );

        if ($wallet) {
            return (int) $wallet['id'];
        }

        return Database::insert('wallets', [
            'user_id' => $userId,
            'balance' => 0.0000,
            'currency' => (string) config('app.currency', 'USD'),
        ]);
    }

    public static function balance(int $userId): float
    {
        $wallet = Database::first(
            "SELECT balance FROM wallets WHERE user_id = :user_id",
            ['user_id' => $userId]
        );
        return (float) ($wallet['balance'] ?? 0.0);
    }

    public static function transactions(int $userId): array
    {
        return Database::select(
            "SELECT wt.* FROM wallet_transactions wt
             JOIN wallets w ON w.id = wt.wallet_id
             WHERE w.user_id = :user_id
             ORDER BY wt.created_at DESC",
            ['user_id' => $userId]
        );
    }

    public static function credit(int $userId, float $amount, string $type, ?string $referenceType, ?int $referenceId, string $description): void
    {
        self::transaction($userId, $amount, $type, $referenceType, $referenceId, $description, 'in');
    }

    public static function debit(int $userId, float $amount, string $type, ?string $referenceType, ?int $referenceId, string $description): void
    {
        self::transaction($userId, $amount, $type, $referenceType, $referenceId, $description, 'out');
    }

    private static function transaction(int $userId, float $amount, string $type, ?string $referenceType, ?int $referenceId, string $description, string $direction): void
    {
        if ($amount <= 0) {
            throw new HttpException('Invalid transaction amount.', 422);
        }

        $walletId = self::ensureWallet($userId);
        $currency = (string) config('app.currency', 'USD');
        $ownTransaction = !Database::inTransaction();

        if ($ownTransaction) {
            Database::beginTransaction();
        }

        try {
            Database::insert('wallet_transactions', [
                'wallet_id' => $walletId,
                'user_id' => $userId,
                'type' => $type,
                'amount' => $amount,
                'currency' => $currency,
                'direction' => $direction,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'description' => $description,
                'status' => 'completed',
            ]);

            $sign = $direction === 'in' ? '+' : '-';
            Database::query(
                "UPDATE wallets SET balance = balance {$sign} :amount WHERE id = :id",
                ['amount' => $amount, 'id' => $walletId]
            );

            if ($ownTransaction) {
                Database::commit();
            }
        } catch (\Throwable $e) {
            if ($ownTransaction) {
                Database::rollBack();
            }
            throw $e;
        }
    }
}
