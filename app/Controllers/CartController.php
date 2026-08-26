<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\CartService;
use App\Services\GuestCart;

class CartController
{
    public function index(): string
    {
        $userId = (int) Session::get('user_id');

        if ($userId) {
            $items = CartService::items($userId);
        } else {
            $items = GuestCart::items();
        }

        $coupon = $this->getCoupon();
        $summary = CartService::summary($items, $coupon);

        if (Request::input('format', '') === 'json') {
            Response::json(['items' => $items, 'summary' => $summary]);
        }

        return Response::view('cart/index', [
            'items' => $items,
            'summary' => $summary,
        ]);
    }

    private function getCoupon(): ?array
    {
        $code = Session::get('coupon_code');
        if (empty($code)) {
            return null;
        }
        $coupon = \App\Models\Coupon::findByCode($code);
        if (!$coupon || !(int) $coupon['is_active']) {
            return null;
        }
        return $coupon;
    }

    public function add(): void
    {
        $userId = (int) Session::get('user_id');
        $productId = (int) Request::input('product_id', 0);
        $quantity = (int) Request::input('quantity', 1);
        $affiliateVendorId = (int) Request::input('affiliate_vendor_id', 0);
        if ($affiliateVendorId === 0) {
            $affiliateVendorId = null;
        }
        $return = Request::input('return', '/cart');

        try {
            if ($userId) {
                CartService::add($userId, $productId, $quantity, $affiliateVendorId);
            } else {
                GuestCart::add($productId, $quantity, $affiliateVendorId);
            }
            $message = 'Added to cart.';
            $success = true;
        } catch (HttpException $e) {
            $message = $e->getMessage();
            $success = false;
        }

        if (Request::header('X-Requested-With') === 'XMLHttpRequest' || Request::input('format', '') === 'json') {
            Response::json(['success' => $success, 'message' => $message]);
        }

        if ($success) {
            Session::flash('success', $message);
        } else {
            Session::flash('error', $message);
        }

        Response::redirect($return);
    }

    public function update(string $id): void
    {
        $userId = (int) Session::get('user_id');
        $quantity = (int) Request::input('quantity', 1);

        try {
            if ($userId) {
                CartService::update((int) $id, $quantity, $userId);
            } else {
                GuestCart::update((int) $id, $quantity);
            }
            $message = 'Cart updated.';
            $success = true;
        } catch (HttpException $e) {
            $message = $e->getMessage();
            $success = false;
        }

        if (Request::header('X-Requested-With') === 'XMLHttpRequest' || Request::input('format', '') === 'json') {
            Response::json(['success' => $success, 'message' => $message]);
        }

        if (!$success) {
            Session::flash('error', $message);
        }

        Response::redirect('/cart');
    }

    public function remove(string $id): void
    {
        $userId = (int) Session::get('user_id');

        if ($userId) {
            CartService::remove((int) $id, $userId);
        } else {
            GuestCart::remove((int) $id);
        }

        if (Request::header('X-Requested-With') === 'XMLHttpRequest' || Request::input('format', '') === 'json') {
            Response::json(['success' => true, 'message' => 'Item removed.']);
        }

        Session::flash('success', 'Item removed.');
        Response::redirect('/cart');
    }

    public function applyCoupon(): void
    {
        $code = strtoupper(trim(Request::input('coupon_code', '')));
        if (empty($code)) {
            Session::flash('error', 'Please enter a coupon code.');
            Response::redirect('/cart');
        }

        $coupon = \App\Models\Coupon::findByCode($code);
        if (!$coupon || !(int) $coupon['is_active']) {
            Session::flash('error', 'Invalid coupon code.');
            Session::remove('coupon_code');
            Response::redirect('/cart');
        }

        Session::set('coupon_code', $code);
        Session::flash('success', 'Coupon applied: ' . e($code));
        Response::redirect('/cart');
    }

    public function removeCoupon(): void
    {
        Session::remove('coupon_code');
        Session::flash('success', 'Coupon removed.');
        Response::redirect('/cart');
    }
}
