<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\HttpException;
use App\Core\Middleware;
use App\Core\Session;

class Vendor extends Middleware
{
    public function handle(): void
    {
        $user = Session::get('user');
        if (empty($user) || ($user['role_name'] ?? '') !== 'vendor') {
            throw new HttpException('Vendor access required.', 403);
        }

        if (($user['vendor_status'] ?? '') !== 'approved') {
            throw new HttpException('Your vendor account is not approved yet.', 403);
        }
    }
}
