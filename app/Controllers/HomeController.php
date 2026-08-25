<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;
use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;

class HomeController
{
    public function index(): string
    {
        return Response::view('home', [
            'title' => config('app.name'),
            'description' => 'A premium multi-vendor marketplace for curated products and affiliate storefronts.',
            'heroSlides' => Product::findFeatured(5),
            'featured' => Product::findFeatured(8),
            'onSale' => Product::findOnSale(8),
            'newest' => Product::findNewest(8),
            'trending' => Product::findFeatured(8),
            'categories' => Category::allVisible(),
            'vendors' => Vendor::findApproved(),
        ]);
    }

    public function shop(): string
    {
        return Response::view('coming_soon', ['title' => 'Shop']);
    }

    public function vendors(): string
    {
        return Response::view('vendors/index', [
            'title' => 'Vendors',
            'vendors' => Vendor::findApproved(),
        ]);
    }
}
