<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Response;
use App\Models\Category;
use App\Models\Product;

class SitemapController
{
    public function index(): string
    {
        $urls = [];

        $urls[] = ['loc' => url('/'), 'priority' => '1.0'];
        $urls[] = ['loc' => url('/shop'), 'priority' => '0.9'];
        $urls[] = ['loc' => url('/vendors'), 'priority' => '0.8'];
        $urls[] = ['loc' => url('/login'), 'priority' => '0.3'];
        $urls[] = ['loc' => url('/register'), 'priority' => '0.3'];

        foreach (Category::allVisible() as $category) {
            $urls[] = ['loc' => url('/shop?category=' . $category['slug']), 'priority' => '0.7'];
        }

        foreach (Product::findPublished() as $product) {
            $urls[] = ['loc' => url('/product/' . $product['slug']), 'priority' => '0.8'];
        }

        $vendors = Database::select(
            "SELECT slug FROM vendors WHERE status = 'approved'",
            []
        );
        foreach ($vendors as $vendor) {
            $urls[] = ['loc' => url('/vendor/' . $vendor['slug']), 'priority' => '0.7'];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($urls as $url) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . e($url['loc']) . '</loc>' . PHP_EOL;
            $xml .= '    <priority>' . $url['priority'] . '</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        Response::header('Content-Type', 'application/xml; charset=UTF-8');
        return $xml;
    }
}
