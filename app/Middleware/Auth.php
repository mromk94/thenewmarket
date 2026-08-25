<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\HttpException;
use App\Core\Middleware;
use App\Core\Session;

class Auth extends Middleware
{
    public function handle(): void
    {
        if (!Session::has('user_id')) {
            throw new HttpException('Please log in to continue.', 403);
        }
    }
}
