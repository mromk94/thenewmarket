<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\PaymentService;

class PaymentController
{
    public function callback(string $provider): string
    {
        $reference = (string) Request::input('reference', '');
        $orderId = (int) Request::input('order_id', 0);

        if (empty($reference) || $orderId === 0) {
            throw new HttpException('Invalid payment callback.', 404);
        }

        if ($provider === 'test') {
            return Response::view('payment/test_callback', [
                'reference' => $reference,
                'orderId' => $orderId,
                'total' => Request::input('amount', 0),
            ]);
        }

        try {
            PaymentService::process($reference, $orderId, Request::all());
            Session::flash('success', 'Payment successful. Thank you for your order.');
            Response::redirect('/account/orders/' . $orderId);
        } catch (HttpException $e) {
            Session::flash('error', $e->getMessage());
            Response::redirect('/account/orders/' . $orderId);
        }
    }

    public function verify(string $provider): void
    {
        $reference = (string) Request::input('reference', '');
        $orderId = (int) Request::input('order_id', 0);

        try {
            PaymentService::process($reference, $orderId, Request::all());
            Session::flash('success', 'Payment successful. Thank you for your order.');
        } catch (HttpException $e) {
            Session::flash('error', $e->getMessage());
        }

        Response::redirect('/account/orders/' . $orderId);
    }
}
