<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;

class RobotsController
{
    public function index(): string
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /account',
            'Disallow: /vendor/dashboard',
            'Disallow: /cart',
            'Disallow: /checkout',
            'Disallow: /payment/',
            'Allow: /',
            'Allow: /shop',
            'Allow: /product/',
            'Allow: /vendor/',
            'Sitemap: ' . url('/sitemap.xml'),
            '',
        ];

        Response::header('Content-Type', 'text/plain; charset=UTF-8');
        return implode(PHP_EOL, $lines);
    }
}
