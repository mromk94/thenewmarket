<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\HttpException;
use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class Auth extends Middleware
{
    public function handle(): void
    {
        if (!Session::has('user_id')) {
            if (Request::header('X-Requested-With') === 'XMLHttpRequest' || Request::input('format', '') === 'json') {
                Response::json(['success' => false, 'message' => 'Please log in to continue.'], 403);
            }

            throw new HttpException('Please log in to continue.', 403);
        }
    }
}
