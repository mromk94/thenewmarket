<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Csrf as CsrfCore;
use App\Core\HttpException;
use App\Core\Middleware;
use App\Core\Request;

class Csrf extends Middleware
{
    public function handle(): void
    {
        CsrfCore::token();

        if (in_array(Request::method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $token = Request::input('csrf_token');
            if (!CsrfCore::verify($token)) {
                throw new HttpException('Your session has expired. Please try again.', 419);
            }
        }
    }
}
