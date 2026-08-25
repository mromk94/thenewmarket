<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
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
        $page = max(1, (int) Request::input('page', 1));
        $perPage = 12;

        $filters = [
            'search' => $search,
            'sort' => $sort,
            'page' => $page,
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
        $total = Product::countPublished($filters);
        $lastPage = max(1, (int) ceil($total / $perPage));
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
            'page' => $page,
            'lastPage' => $lastPage,
            'total' => $total,
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

        $reviews = Review::forProduct((int) $product['id']);
        $reviewStats = Review::stats((int) $product['id']);

        $canReview = false;
        $reviewOrderId = 0;
        $customerId = (int) \App\Core\Session::get('user_id');
        if ($customerId > 0) {
            $order = Database::first(
                "SELECT o.id
                 FROM orders o
                 JOIN order_items oi ON oi.order_id = o.id
                 WHERE o.customer_id = :customer_id
                   AND o.payment_status = 'paid'
                   AND oi.product_id = :product_id
                   AND NOT EXISTS (
                       SELECT 1 FROM reviews r
                       WHERE r.customer_id = :customer_id
                         AND r.product_id = :product_id
                         AND r.order_id = o.id
                   )
                 ORDER BY o.created_at DESC
                 LIMIT 1",
                ['customer_id' => $customerId, 'product_id' => $product['id']]
            );
            if ($order) {
                $canReview = true;
                $reviewOrderId = (int) $order['id'];
            }
        }

        return Response::view('shop/show', [
            'product' => $product,
            'images' => $images,
            'thumbnail' => $thumbnail,
            'affiliateVendorId' => $affiliateVendorId,
            'affiliateVendorName' => $affiliateVendorName,
            'vendorSlug' => $vendorSlug,
            'reviews' => $reviews,
            'reviewStats' => $reviewStats,
            'canReview' => $canReview,
            'reviewOrderId' => $reviewOrderId,
        ]);
    }

    public function storeReview(string $slug): void
    {
        $product = Product::findBySlug($slug);
        if (!$product) {
            throw new HttpException('Product not found.', 404);
        }

        $customerId = (int) \App\Core\Session::get('user_id');
        $orderId = (int) Request::input('order_id', 0);
        $rating = (int) Request::input('rating', 0);
        $title = trim(Request::input('title', ''));
        $body = trim(Request::input('body', ''));

        if ($customerId <= 0 || $orderId <= 0 || $rating < 1 || $rating > 5 || empty($body)) {
            Session::flash('error', 'Rating and review text are required.');
            Response::redirect('/product/' . $slug);
        }

        if (!Review::canReview($customerId, (int) $product['id'], $orderId)) {
            Session::flash('error', 'You cannot review this product.');
            Response::redirect('/product/' . $slug);
        }

        Review::create([
            'product_id' => (int) $product['id'],
            'customer_id' => $customerId,
            'order_id' => $orderId,
            'rating' => $rating,
            'title' => $title,
            'body' => $body,
            'status' => 'pending',
            'is_verified_purchase' => 1,
        ]);

        Session::flash('success', 'Thank you! Your review has been submitted for approval.');
        Response::redirect('/product/' . $slug);
    }
}
