<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Address;
use App\Models\Notification;
use App\Models\Refund;
use App\Models\User;
use App\Services\OrderService;
use App\Services\WalletService;

class AccountController
{
    public function index(): string
    {
        $user = Session::get('user');

        if ($user['role_name'] === 'admin') {
            Response::redirect('/admin');
        }

        if ($user['role_name'] === 'vendor') {
            if (($user['vendor_status'] ?? '') !== 'approved') {
                Response::redirect('/vendor/pending');
            }
            Response::redirect('/vendor/dashboard');
        }

        return Response::view('customer/dashboard');
    }

    public function orders(): string
    {
        $userId = (int) Session::get('user_id');
        $orders = Database::select(
            "SELECT * FROM orders WHERE customer_id = :customer_id ORDER BY created_at DESC",
            ['customer_id' => $userId]
        );

        return Response::view('customer/orders', ['orders' => $orders]);
    }

    public function order(string $id): string
    {
        $orderId = (int) $id;
        $userId = (int) Session::get('user_id');
        $order = Database::first(
            "SELECT * FROM orders WHERE id = :id AND customer_id = :customer_id",
            ['id' => $orderId, 'customer_id' => $userId]
        );

        if (!$order) {
            throw new HttpException('Order not found.', 404);
        }

        $items = OrderService::items($orderId);

        return Response::view('customer/order', [
            'order' => $order,
            'items' => $items,
        ]);
    }

    public function wallet(): string
    {
        $userId = (int) Session::get('user_id');
        $balance = WalletService::balance($userId);
        $transactions = WalletService::transactions($userId);

        return Response::view('customer/wallet', [
            'balance' => $balance,
            'transactions' => $transactions,
        ]);
    }

    public function profile(): string
    {
        $user = User::find((int) Session::get('user_id'));

        return Response::view('customer/profile', [
            'user' => $user,
        ]);
    }

    public function updateProfile(): void
    {
        $userId = (int) Session::get('user_id');

        User::updateProfile($userId, [
            'first_name' => trim(Request::input('first_name', '')),
            'last_name' => trim(Request::input('last_name', '')),
            'phone' => trim(Request::input('phone', '')),
        ]);

        $fresh = User::find($userId);
        if ($fresh) {
            Session::set('user', $fresh);
        }

        Session::flash('success', 'Profile updated.');
        Response::redirect('/account/profile');
    }

    public function changePassword(): void
    {
        $userId = (int) Session::get('user_id');
        $current = Request::input('current_password', '');
        $new = Request::input('new_password', '');
        $confirm = Request::input('confirm_password', '');

        $user = User::find($userId);
        if (!$user || !password_verify($current, (string) $user['password_hash'])) {
            Session::flash('error', 'Current password is incorrect.');
            Response::redirect('/account/profile');
        }

        if (strlen($new) < 8) {
            Session::flash('error', 'New password must be at least 8 characters.');
            Response::redirect('/account/profile');
        }

        if ($new !== $confirm) {
            Session::flash('error', 'Passwords do not match.');
            Response::redirect('/account/profile');
        }

        User::updatePassword($userId, password_hash($new, PASSWORD_DEFAULT));
        Session::flash('success', 'Password changed.');
        Response::redirect('/account/profile');
    }

    public function addresses(): string
    {
        $userId = (int) Session::get('user_id');

        return Response::view('customer/addresses', [
            'shipping' => Address::forUser($userId, 'shipping'),
            'billing' => Address::forUser($userId, 'billing'),
        ]);
    }

    public function storeAddress(): void
    {
        $userId = (int) Session::get('user_id');
        $type = in_array(Request::input('type', ''), ['billing', 'shipping'], true)
            ? Request::input('type')
            : 'shipping';

        $data = [
            'user_id' => $userId,
            'type' => $type,
            'label' => trim(Request::input('label', '')),
            'first_name' => trim(Request::input('first_name', '')),
            'last_name' => trim(Request::input('last_name', '')),
            'phone' => trim(Request::input('phone', '')),
            'address_line_1' => trim(Request::input('address_line_1', '')),
            'address_line_2' => trim(Request::input('address_line_2', '')),
            'city' => trim(Request::input('city', '')),
            'state' => trim(Request::input('state', '')),
            'country' => trim(Request::input('country', '')),
            'zip' => trim(Request::input('zip', '')),
            'is_default' => (int) Request::input('is_default', 0),
        ];

        if (empty($data['first_name']) || empty($data['last_name']) || empty($data['address_line_1']) || empty($data['city']) || empty($data['country']) || empty($data['zip'])) {
            Session::flash('error', 'Please fill in the required address fields.');
            Response::redirect('/account/addresses');
        }

        if ($data['is_default']) {
            Address::clearDefault($userId, $type);
        }

        Address::create($data);
        Session::flash('success', 'Address added.');
        Response::redirect('/account/addresses');
    }

    public function setDefaultAddress(string $id): void
    {
        $userId = (int) Session::get('user_id');
        $address = Database::first(
            "SELECT * FROM addresses WHERE id = :id AND user_id = :user_id",
            ['id' => (int) $id, 'user_id' => $userId]
        );

        if (!$address) {
            throw new HttpException('Address not found.', 404);
        }

        Address::setDefault((int) $id, $userId, $address['type']);
        Session::flash('success', 'Default address updated.');
        Response::redirect('/account/addresses');
    }

    public function deleteAddress(string $id): void
    {
        Address::delete((int) $id, (int) Session::get('user_id'));
        Session::flash('success', 'Address deleted.');
        Response::redirect('/account/addresses');
    }

    public function notifications(): string
    {
        $userId = (int) Session::get('user_id');

        return Response::view('customer/notifications', [
            'notifications' => Notification::forUser($userId),
            'unreadCount' => Notification::unreadCount($userId),
        ]);
    }

    public function markNotification(string $id): void
    {
        Notification::markRead((int) $id, (int) Session::get('user_id'));
        Response::redirect('/account/notifications');
    }

    public function refunds(): string
    {
        $userId = (int) Session::get('user_id');

        return Response::view('customer/refunds', [
            'refunds' => Refund::forCustomer($userId),
            'orders' => Database::select(
                "SELECT * FROM orders WHERE customer_id = :customer_id AND payment_status = 'paid' ORDER BY created_at DESC",
                ['customer_id' => $userId]
            ),
        ]);
    }

    public function requestRefund(): void
    {
        $userId = (int) Session::get('user_id');
        $orderId = (int) Request::input('order_id', 0);
        $amount = (float) Request::input('amount', 0);
        $reason = trim(Request::input('reason', ''));

        if ($orderId <= 0 || $amount <= 0 || empty($reason)) {
            Session::flash('error', 'Order, amount and reason are required.');
            Response::redirect('/account/refunds');
        }

        $order = OrderService::find($orderId);
        if (!$order || (int) $order['customer_id'] !== $userId) {
            throw new HttpException('Order not found.', 404);
        }

        if ($amount > (float) $order['total']) {
            Session::flash('error', 'Refund amount cannot exceed the order total.');
            Response::redirect('/account/refunds');
        }

        Refund::create([
            'order_id' => $orderId,
            'customer_id' => $userId,
            'amount' => $amount,
            'currency' => $order['currency'],
            'reason' => $reason,
            'status' => 'pending',
        ]);

        Session::flash('success', 'Refund request submitted for review.');
        Response::redirect('/account/refunds');
    }
}
