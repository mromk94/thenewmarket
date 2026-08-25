<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class User
{
    public static function find(int $id): ?array
    {
        return Database::first(
            "SELECT u.*, r.name as role_name, up.first_name, up.last_name FROM users u
             JOIN roles r ON r.id = u.role_id
             LEFT JOIN user_profiles up ON up.user_id = u.id
             WHERE u.id = :id",
            ['id' => $id]
        );
    }

    public static function findByEmail(string $email): ?array
    {
        return Database::first(
            "SELECT u.*, r.name as role_name, up.first_name, up.last_name FROM users u
             JOIN roles r ON r.id = u.role_id
             LEFT JOIN user_profiles up ON up.user_id = u.id
             WHERE u.email = :email",
            ['email' => $email]
        );
    }

    public static function create(array $data): int
    {
        return Database::insert('users', $data);
    }

    public static function createProfile(int $userId, array $data): void
    {
        $data['user_id'] = $userId;
        Database::insert('user_profiles', $data);
    }

    public static function getVendor(int $userId): ?array
    {
        return Database::first(
            "SELECT * FROM vendors WHERE user_id = :user_id",
            ['user_id' => $userId]
        );
    }

    public static function createWallet(int $userId): void
    {
        $currency = (string) config('app.currency', 'USD');
        Database::query(
            "INSERT IGNORE INTO wallets (user_id, balance, currency) VALUES (:user_id, 0.0000, :currency)",
            ['user_id' => $userId, 'currency' => $currency]
        );
    }

    public static function updatePassword(int $userId, string $hash): void
    {
        Database::update('users', ['password_hash' => $hash], 'id = ?', [$userId]);
    }

    public static function all(): array
    {
        return Database::select(
            "SELECT u.*, r.name as role_name, up.first_name, up.last_name
             FROM users u
             JOIN roles r ON r.id = u.role_id
             LEFT JOIN user_profiles up ON up.user_id = u.id
             ORDER BY u.created_at DESC",
            []
        );
    }

    public static function allByRole(string $role): array
    {
        return Database::select(
            "SELECT u.*, r.name as role_name, up.first_name, up.last_name
             FROM users u
             JOIN roles r ON r.id = u.role_id
             LEFT JOIN user_profiles up ON up.user_id = u.id
             WHERE r.name = :role
             ORDER BY u.created_at DESC",
            ['role' => $role]
        );
    }

    public static function update(int $userId, array $data): void
    {
        $allowed = ['email', 'password_hash', 'role_id', 'status'];
        $update = [];
        foreach ($allowed as $key) {
            if (array_key_exists($key, $data)) {
                $update[$key] = $data[$key];
            }
        }

        if (!empty($update)) {
            Database::update('users', $update, 'id = ?', [$userId]);
        }
    }

    public static function updateProfile(int $userId, array $data): void
    {
        $allowed = ['first_name', 'last_name', 'phone'];
        $update = [];
        foreach ($allowed as $key) {
            if (array_key_exists($key, $data)) {
                $update[$key] = $data[$key];
            }
        }

        if (empty($update)) {
            return;
        }

        $exists = Database::first(
            "SELECT 1 FROM user_profiles WHERE user_id = :user_id",
            ['user_id' => $userId]
        );

        if ($exists) {
            Database::update('user_profiles', $update, 'user_id = ?', [$userId]);
        } else {
            $update['user_id'] = $userId;
            Database::insert('user_profiles', $update);
        }
    }
}
