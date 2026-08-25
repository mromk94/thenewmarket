<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\HttpException;
use App\Core\Session;
use App\Models\User;
use App\Services\LoginProtection;
use App\Services\Mailer;

class AuthService
{
    public static function register(array $input): array
    {
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $firstName = trim($input['first_name'] ?? '');
        $lastName = trim($input['last_name'] ?? '');
        $role = strtolower($input['role'] ?? 'customer');
        $businessName = trim($input['business_name'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new HttpException('Please enter a valid email address.', 422);
        }

        if (strlen($password) < 8) {
            throw new HttpException('Password must be at least 8 characters.', 422);
        }

        $allowed = ['customer', 'vendor'];
        if (!in_array($role, $allowed, true)) {
            $role = 'customer';
        }

        if ($role === 'vendor' && empty($businessName)) {
            throw new HttpException('Business name is required for vendors.', 422);
        }

        if (User::findByEmail($email)) {
            throw new HttpException('An account with this email already exists.', 422);
        }

        $roleId = (int) Database::first(
            "SELECT id FROM roles WHERE name = :name",
            ['name' => $role]
        )['id'] ?? 0;

        if (!$roleId) {
            throw new HttpException('Invalid role.', 422);
        }

        $status = $role === 'vendor' ? 'pending' : 'active';

        Database::beginTransaction();
        try {
            $userId = User::create([
                'role_id' => $roleId,
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'status' => $status,
                'email_verified_at' => null,
            ]);

            User::createProfile($userId, [
                'first_name' => $firstName,
                'last_name' => $lastName,
            ]);

            User::createWallet($userId);

            if ($role === 'vendor') {
                $slug = self::slugify($businessName);
                Database::insert('vendors', [
                    'user_id' => $userId,
                    'business_name' => $businessName,
                    'slug' => $slug,
                    'status' => 'pending',
                ]);
            }

            Database::commit();

            return User::find($userId);
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }
    }

    public static function login(string $email, string $password): array
    {
        LoginProtection::check($email);

        $user = User::findByEmail($email);
        if (!$user) {
            throw new HttpException('Invalid email or password.', 422);
        }

        if ($user['status'] !== 'active') {
            throw new HttpException('This account is not active.', 403);
        }

        if (!password_verify($password, $user['password_hash'])) {
            throw new HttpException('Invalid email or password.', 422);
        }

        if ($user['role_name'] === 'vendor') {
            $vendor = User::getVendor((int) $user['id']);
            $user['vendor_id'] = $vendor['id'] ?? null;
            $user['vendor_status'] = $vendor['status'] ?? 'pending';
        } else {
            $user['vendor_id'] = null;
            $user['vendor_status'] = null;
        }

        return $user;
    }

    public static function setUserSession(array $user): void
    {
        LoginProtection::clear($user['email']);
        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('user', $user);
    }

    public static function logout(): void
    {
        Session::destroy();
    }

    public static function forgotPassword(string $email): void
    {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new HttpException('Please enter a valid email.', 422);
        }

        $user = User::findByEmail($email);
        if (!$user) {
            return;
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        Database::query(
            "INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires)",
            ['email' => $email, 'token' => hash('sha256', $token), 'expires' => $expires]
        );

        $resetUrl = url('/reset-password?token=' . $token . '&email=' . urlencode($email));
        $subject = 'Password reset request';
        $body = "<p>Hello,</p><p>Click the link below to reset your password:</p><p><a href=\"{$resetUrl}\">{$resetUrl}</a></p><p>This link expires in one hour.</p>";

        Mailer::send($email, $subject, $body);
    }

    public static function resetPassword(string $email, string $token, string $password): void
    {
        if (strlen($password) < 8) {
            throw new HttpException('Password must be at least 8 characters.', 422);
        }

        $record = Database::first(
            "SELECT * FROM password_resets
             WHERE email = :email AND token = :token AND used = 0 AND expires_at > NOW()",
            ['email' => $email, 'token' => hash('sha256', $token)]
        );

        if (!$record) {
            throw new HttpException('Invalid or expired reset link.', 400);
        }

        $user = User::findByEmail($email);
        if (!$user) {
            throw new HttpException('Invalid or expired reset link.', 400);
        }

        Database::update(
            'password_resets',
            ['used' => 1],
            'id = ?',
            [(int) $record['id']]
        );

        User::updatePassword((int) $user['id'], password_hash($password, PASSWORD_DEFAULT));
    }

    private static function slugify(string $text): string
    {
        $text = preg_replace('/[^a-z0-9-]+/i', '-', $text) ?? '';
        $text = trim($text, '-');
        $text = strtolower($text);
        return $text ?: 'vendor-' . time();
    }
}
