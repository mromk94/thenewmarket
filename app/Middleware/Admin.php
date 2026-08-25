<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\HttpException;
use App\Core\Middleware;
use App\Core\Session;

class Admin extends Middleware
{
    public function handle(): void
    {
        $user = Session::get('user');
        if (empty($user) || ($user['role_name'] ?? '') !== 'admin') {
            throw new HttpException('Admin access required.', 403);
        }
    }
}
