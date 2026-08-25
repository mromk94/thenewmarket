<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\PaymentMethod;
use App\Models\PaymentProof;
use App\Services\CartService;
use App\Services\ImageService;
use App\Services\OrderService;
use App\Services\PaymentService;

class CheckoutController
{
    public function show(): string
    {
        $userId = (int) Session::get('user_id');
        $items = CartService::items($userId);
        $summary = CartService::summary($items);

        if (empty($items)) {
            Response::redirect('/cart');
        }

        return Response::view('checkout/show', [
            'items' => $items,
            'summary' => $summary,
            'paymentMethods' => PaymentMethod::allActive(),
        ]);
    }

    public function store(): void
    {
        $userId = (int) Session::get('user_id');
        $items = CartService::items($userId);

        if (empty($items)) {
            Response::redirect('/cart');
        }

        $paymentMethodId = (int) Request::input('payment_method_id', 0);

        try {
            $order = OrderService::create($userId, $items);
            CartService::clear($userId);

            if ($paymentMethodId === 0) {
                $result = PaymentService::initiate((int) $order['id']);
                Response::redirect($result['url']);
            }

            $method = PaymentMethod::find($paymentMethodId);
            if (!$method || !(int) $method['is_active']) {
                Session::flash('error', 'Selected payment method is not available.');
                Response::redirect('/checkout');
            }

            Response::redirect('/checkout/pay/' . $order['id'] . '?method=' . $paymentMethodId);
        } catch (HttpException $e) {
            Session::flash('error', $e->getMessage());
            Response::redirect('/checkout');
        }
    }

    public function manualPay(string $orderId): string
    {
        $userId = (int) Session::get('user_id');
        $order = OrderService::find((int) $orderId);

        if (!$order || (int) $order['customer_id'] !== $userId) {
            throw new HttpException('Order not found.', 404);
        }

        $methodId = (int) Request::input('method', 0);
        $method = PaymentMethod::find($methodId);
        if (!$method) {
            throw new HttpException('Payment method not found.', 404);
        }

        return Response::view('checkout/manual', [
            'order' => $order,
            'method' => $method,
        ]);
    }

    public function submitManual(string $orderId): void
    {
        $userId = (int) Session::get('user_id');
        $order = OrderService::find((int) $orderId);

        if (!$order || (int) $order['customer_id'] !== $userId) {
            throw new HttpException('Order not found.', 404);
        }

        $methodId = (int) Request::input('payment_method_id', 0);
        $method = PaymentMethod::find($methodId);
        if (!$method || !(int) $method['is_active']) {
            Session::flash('error', 'Payment method not available.');
            Response::redirect('/checkout/pay/' . $orderId . '?method=' . $methodId);
        }

        $reference = trim(Request::input('reference', ''));
        $receipt = null;
        if (!empty($_FILES['receipt_image']['tmp_name']) && $_FILES['receipt_image']['error'] === UPLOAD_ERR_OK) {
            $uploaded = ImageService::upload($_FILES['receipt_image'], 'payment-proofs');
            $receipt = $uploaded['path'];
        }

        PaymentProof::create([
            'order_id' => (int) $orderId,
            'payment_method_id' => $methodId,
            'reference' => $reference,
            'receipt_image' => $receipt,
            'status' => 'pending',
        ]);

        Session::flash('success', 'Payment proof submitted. Your order will be reviewed.');
        Response::redirect('/account/orders/' . $orderId);
    }
}
