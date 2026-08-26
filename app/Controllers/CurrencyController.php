<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Currency;

class CurrencyController
{
    public function switch(): void
    {
        $code = Request::input('code', '');
        $currency = Currency::findByCode($code);
        if ($currency) {
            Currency::setCurrent($currency['code']);
        }
        $to = Request::input('to', '/');
        Response::redirect($to);
    }
}
