<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\CartService;

class CartController
{
    public function index(): string
    {
        $userId = (int) Session::get('user_id');
        $items = CartService::items($userId);
        $summary = CartService::summary($items);

        if (Request::input('format', '') === 'json') {
            Response::json(['items' => $items, 'summary' => $summary]);
        }

        return Response::view('cart/index', [
            'items' => $items,
            'summary' => $summary,
        ]);
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
            CartService::add($userId, $productId, $quantity, $affiliateVendorId);
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
            CartService::update((int) $id, $quantity, $userId);
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
        CartService::remove((int) $id, $userId);

        if (Request::header('X-Requested-With') === 'XMLHttpRequest' || Request::input('format', '') === 'json') {
            Response::json(['success' => true, 'message' => 'Item removed.']);
        }

        Session::flash('success', 'Item removed.');
        Response::redirect('/cart');
    }
}
