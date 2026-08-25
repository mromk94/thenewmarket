<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;

class ShopController
{
    public function index(): string
    {
        $category = Request::input('category');
        $search = trim(Request::input('search', ''));
        $sort = Request::input('sort', 'featured');
        $minPrice = Request::input('min_price', '');
        $maxPrice = Request::input('max_price', '');
        $vendorId = Request::input('vendor', '');
        $availability = Request::input('availability', '');

        $filters = [
            'search' => $search,
            'sort' => $sort,
        ];

        if (!empty($category)) {
            $cat = Category::findBySlug($category);
            if ($cat) {
                $filters['category_id'] = $cat['id'];
            }
        }

        if ($minPrice !== '' && is_numeric($minPrice)) {
            $filters['min_price'] = (float) $minPrice;
        }

        if ($maxPrice !== '' && is_numeric($maxPrice)) {
            $filters['max_price'] = (float) $maxPrice;
        }

        if (!empty($vendorId) && is_numeric($vendorId)) {
            $filters['vendor_id'] = (int) $vendorId;
        }

        if (in_array($availability, ['in_stock', 'low_stock', 'out_of_stock'], true)) {
            $filters['availability'] = $availability;
        }

        $products = Product::findPublished($filters);
        $categories = Category::allVisible();
        $vendors = Vendor::findApproved();

        return Response::view('shop/index', [
            'products' => $products,
            'categories' => $categories,
            'vendors' => $vendors,
            'currentCategory' => $category,
            'search' => $search,
            'sort' => $sort,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'currentVendor' => $vendorId,
            'availability' => $availability,
        ]);
    }

    public function show(string $slug): string
    {
        $product = Product::findBySlug($slug);
        if (!$product) {
            throw new HttpException('Product not found.', 404);
        }

        $images = Product::images((int) $product['id']);
        $thumbnail = Product::thumbnail((int) $product['id']);

        $affiliateVendorId = null;
        $affiliateVendorName = null;
        $vendorSlug = Request::input('vendor');
        if (!empty($vendorSlug)) {
            $vendor = Database::first(
                "SELECT * FROM vendors WHERE slug = :slug AND status = 'approved'",
                ['slug' => $vendorSlug]
            );
            if ($vendor) {
                $isAffiliate = Database::exists(
                    "SELECT 1 FROM vendor_affiliate_products
                     WHERE vendor_id = :vendor_id AND product_id = :product_id",
                    ['vendor_id' => $vendor['id'], 'product_id' => $product['id']]
                );
                $isOwner = (int) $product['vendor_id'] === (int) $vendor['id'];
                if ($isAffiliate || $isOwner) {
                    $affiliateVendorId = (int) $vendor['id'];
                    $affiliateVendorName = $vendor['business_name'];
                }
            }
        }

        return Response::view('shop/show', [
            'product' => $product,
            'images' => $images,
            'thumbnail' => $thumbnail,
            'affiliateVendorId' => $affiliateVendorId,
            'affiliateVendorName' => $affiliateVendorName,
            'vendorSlug' => $vendorSlug,
        ]);
    }
}
