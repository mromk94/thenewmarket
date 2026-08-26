<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\AuthService;
use App\Services\GuestCart;
use App\Services\LoginProtection;

class AuthController
{
    public function loginShow(): string
    {
        return Response::view('auth/login');
    }

    public function login(): void
    {
        $email = trim(Request::input('email', ''));
        $password = Request::input('password', '');

        try {
            $user = AuthService::login($email, $password);
            AuthService::setUserSession($user);
            GuestCart::migrateToUser((int) $user['id']);
            Response::redirect('/account');
        } catch (HttpException $e) {
            LoginProtection::record($email);
            Session::flash('error', $e->getMessage());
            Session::setOld(Request::all());
            Response::redirect('/login');
        }
    }

    public function registerShow(): string
    {
        return Response::view('auth/register');
    }

    public function register(): void
    {
        try {
            $user = AuthService::register(Request::all());
            AuthService::setUserSession($user);
            GuestCart::migrateToUser((int) $user['id']);
            Response::redirect('/account');
        } catch (HttpException $e) {
            Session::flash('error', $e->getMessage());
            Session::setOld(Request::all());
            Response::redirect('/register');
        }
    }

    public function forgotShow(): string
    {
        return Response::view('auth/forgot');
    }

    public function forgot(): void
    {
        $email = trim(Request::input('email', ''));

        try {
            AuthService::forgotPassword($email);
            Session::flash('success', 'If the email exists, a reset link has been sent.');
            Response::redirect('/login');
        } catch (HttpException $e) {
            Session::flash('error', $e->getMessage());
            Response::redirect('/forgot-password');
        }
    }

    public function resetShow(): string
    {
        $email = (string) Request::input('email', '');
        $token = (string) Request::input('token', '');

        return Response::view('auth/reset', [
            'email' => $email,
            'token' => $token,
        ]);
    }

    public function reset(): void
    {
        $email = trim(Request::input('email', ''));
        $token = (string) Request::input('token', '');
        $password = Request::input('password', '');
        $passwordConfirm = Request::input('password_confirmation', '');

        if ($password !== $passwordConfirm) {
            Session::flash('error', 'Passwords do not match.');
            Response::redirect('/reset-password?token=' . urlencode($token) . '&email=' . urlencode($email));
        }

        try {
            AuthService::resetPassword($email, $token, $password);
            Session::flash('success', 'Password has been reset. You can now log in.');
            Response::redirect('/login');
        } catch (HttpException $e) {
            Session::flash('error', $e->getMessage());
            Response::redirect('/forgot-password');
        }
    }

    public function logout(): void
    {
        AuthService::logout();
        Response::redirect('/');
    }
}
