<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Category;
use App\Models\DepositMethod;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\VendorDeposit;
use App\Models\Withdrawal;
use App\Services\ImageService;
use App\Services\WalletService;

class VendorController
{
    private static function currentVendor(): array
    {
        $vendorId = (int) (Session::get('user')['vendor_id'] ?? 0);
        $vendor = Vendor::findById($vendorId);
        if (!$vendor) {
            throw new HttpException('Vendor not found.', 404);
        }
        return $vendor;
    }

    public function dashboard(): string
    {
        $vendor = self::currentVendor();
        $products = Product::findByOwner((int) $vendor['user_id']);
        $userId = (int) $vendor['user_id'];

        $stats = [
            'products' => count($products),
            'published' => count(array_filter($products, fn($p) => $p['status'] === 'published')),
            'pending' => count(array_filter($products, fn($p) => $p['status'] === 'pending')),
            'balance' => WalletService::balance($userId),
            'sales' => Vendor::salesCount((int) $vendor['id']),
            'revenue' => Vendor::revenue((int) $vendor['id']),
            'orders' => Vendor::ordersCount((int) $vendor['id']),
        ];

        $transactions = array_slice(WalletService::transactions($userId), 0, 5);

        return Response::view('vendor/dashboard', [
            'vendor' => $vendor,
            'stats' => $stats,
            'products' => array_slice($products, 0, 6),
            'transactions' => $transactions,
        ]);
    }

    public function storefront(string $slug): string
    {
        $vendor = Vendor::findBySlug($slug);
        if (!$vendor) {
            throw new HttpException('Storefront not found.', 404);
        }

        $products = Product::findByVendor((int) $vendor['id']);
        $affiliates = Product::findAffiliateProducts((int) $vendor['id']);
        $balance = WalletService::balance((int) $vendor['user_id']);

        return Response::view('vendor/storefront', [
            'vendor' => $vendor,
            'products' => $products,
            'affiliates' => $affiliates,
            'balance' => $balance,
        ]);
    }

    public function affiliates(): string
    {
        $vendor = self::currentVendor();
        $products = Product::affiliateEligibleForVendor((int) $vendor['id']);
        $balance = WalletService::balance((int) $vendor['user_id']);
        $salesCount = Vendor::salesCount((int) $vendor['id']);

        return Response::view('vendor/affiliates', [
            'products' => $products,
            'vendor' => $vendor,
            'balance' => $balance,
            'salesCount' => $salesCount,
        ]);
    }

    public function addAffiliate(string $productId): void
    {
        $vendor = self::currentVendor();
        $product = Product::findByIdAdmin((int) $productId);

        if (!$product || !(int) $product['is_affiliate_eligible']) {
            Session::flash('error', 'Product is not available for affiliate promotion.');
            Response::redirect('/vendor/affiliates');
        }

        $balance = WalletService::balance((int) $vendor['user_id']);
        $salesCount = Vendor::salesCount((int) $vendor['id']);

        $messages = [];
        if ((float) $product['affiliate_require_min_balance'] > 0 && $balance < (float) $product['affiliate_require_min_balance']) {
            $messages[] = 'You need at least ' . e(config('app.currency_symbol')) . number_format((float) $product['affiliate_require_min_balance'], 2) . ' wallet balance.';
        }
        if ((int) $product['affiliate_require_kyc'] && !(int) $vendor['kyc_verified']) {
            $messages[] = 'KYC verification is required.';
        }
        if ((int) $product['affiliate_require_min_sales'] > 0 && $salesCount < (int) $product['affiliate_require_min_sales']) {
            $messages[] = 'You need at least ' . (int) $product['affiliate_require_min_sales'] . ' sales.';
        }

        if (!empty($messages)) {
            Session::flash('error', 'You do not meet the requirements for this product: ' . implode(' ', $messages));
            Response::redirect('/vendor/affiliates');
        }

        Database::query(
            "INSERT IGNORE INTO vendor_affiliate_products (vendor_id, product_id) VALUES (:vendor_id, :product_id)",
            ['vendor_id' => $vendor['id'], 'product_id' => (int) $productId]
        );

        Session::flash('success', 'Product promoted in your store.');
        Response::redirect('/vendor/affiliates');
    }

    public function removeAffiliate(string $productId): void
    {
        $vendor = self::currentVendor();

        Database::query(
            "DELETE FROM vendor_affiliate_products WHERE vendor_id = :vendor_id AND product_id = :product_id",
            ['vendor_id' => $vendor['id'], 'product_id' => (int) $productId]
        );

        Session::flash('success', 'Product removed from your store.');
        Response::redirect('/vendor/affiliates');
    }

    public function products(): string
    {
        $vendor = self::currentVendor();
        $items = Product::findByOwner((int) $vendor['user_id']);

        return Response::view('vendor/products/index', [
            'products' => $items,
        ]);
    }

    public function createProduct(): string
    {
        $vendor = self::currentVendor();
        $categories = Category::allVisible();

        return Response::view('vendor/products/create', [
            'categories' => $categories,
            'vendor' => $vendor,
        ]);
    }

    public function storeProduct(): void
    {
        $vendor = self::currentVendor();

        $data = [
            'owner_id' => (int) $vendor['user_id'],
            'vendor_id' => (int) $vendor['id'],
            'name' => trim(Request::input('name', '')),
            'description' => Request::input('description', ''),
            'short_description' => Request::input('short_description', ''),
            'sku' => Request::input('sku', ''),
            'price' => (float) Request::input('price', 0),
            'compare_at_price' => Request::input('compare_at_price') ? (float) Request::input('compare_at_price') : null,
            'stock_qty' => (int) Request::input('stock_qty', 0),
            'category_id' => Request::input('category_id') ? (int) Request::input('category_id') : null,
            'is_affiliate_eligible' => (int) Request::input('is_affiliate_eligible', 0),
            'affiliate_commission_type' => Request::input('affiliate_commission_type', 'percentage'),
            'affiliate_commission_value' => (float) Request::input('affiliate_commission_value', 0),
            'status' => 'pending',
            'visibility' => 'public',
            'featured' => 0,
        ];

        if (empty($data['name']) || $data['price'] <= 0) {
            Session::flash('error', 'Product name and a valid price are required.');
            Session::setOld(Request::all());
            Response::redirect('/vendor/products/create');
        }

        $productId = Product::create($data);

        if (!empty($_FILES['image']['tmp_name'])) {
            try {
                $upload = ImageService::upload($_FILES['image'], 'products');
                Product::attachImage($productId, $upload['path'], true, 1);
            } catch (HttpException $e) {
                Session::flash('error', $e->getMessage());
                Response::redirect('/vendor/products');
            }
        }

        Session::flash('success', 'Product submitted for review.');
        Response::redirect('/vendor/products');
    }

    public function editProduct(string $id): string
    {
        $vendor = self::currentVendor();
        $product = Product::findByIdAdmin((int) $id);

        if (!$product || (int) $product['owner_id'] !== (int) $vendor['user_id']) {
            throw new HttpException('Product not found.', 404);
        }

        return Response::view('vendor/products/edit', [
            'product' => $product,
            'categories' => Category::allVisible(),
        ]);
    }

    public function updateProduct(string $id): void
    {
        $vendor = self::currentVendor();
        $product = Product::findByIdAdmin((int) $id);

        if (!$product || (int) $product['owner_id'] !== (int) $vendor['user_id']) {
            throw new HttpException('Product not found.', 404);
        }

        $update = [
            'name' => trim(Request::input('name', '')),
            'description' => Request::input('description', ''),
            'short_description' => Request::input('short_description', ''),
            'sku' => Request::input('sku', ''),
            'price' => (float) Request::input('price', 0),
            'compare_at_price' => Request::input('compare_at_price') ? (float) Request::input('compare_at_price') : null,
            'stock_qty' => (int) Request::input('stock_qty', 0),
            'category_id' => Request::input('category_id') ? (int) Request::input('category_id') : null,
            'is_affiliate_eligible' => (int) Request::input('is_affiliate_eligible', 0),
            'affiliate_commission_type' => Request::input('affiliate_commission_type', 'percentage'),
            'affiliate_commission_value' => (float) Request::input('affiliate_commission_value', 0),
        ];

        if (empty($update['name']) || $update['price'] <= 0) {
            Session::flash('error', 'Product name and a valid price are required.');
            Response::redirect('/vendor/products/' . $id . '/edit');
        }

        Product::update((int) $id, $update);

        if (!empty($_FILES['image']['tmp_name'])) {
            try {
                $upload = ImageService::upload($_FILES['image'], 'products');
                Product::attachImage((int) $id, $upload['path'], true, 1);
            } catch (HttpException $e) {
                Session::flash('error', $e->getMessage());
                Response::redirect('/vendor/products');
            }
        }

        Session::flash('success', 'Product updated.');
        Response::redirect('/vendor/products');
    }

    public function deleteProduct(string $id): void
    {
        $vendor = self::currentVendor();
        $product = Product::findByIdAdmin((int) $id);

        if (!$product || (int) $product['owner_id'] !== (int) $vendor['user_id']) {
            throw new HttpException('Product not found.', 404);
        }

        Product::delete((int) $id);
        Session::flash('success', 'Product deleted.');
        Response::redirect('/vendor/products');
    }

    public function wallet(): string
    {
        $userId = (int) Session::get('user_id');
        $balance = WalletService::balance($userId);
        $transactions = WalletService::transactions($userId);

        return Response::view('vendor/wallet', [
            'balance' => $balance,
            'transactions' => $transactions,
        ]);
    }

    public function deposits(): string
    {
        $vendor = self::currentVendor();

        return Response::view('vendor/deposits', [
            'methods' => DepositMethod::allActive(),
            'deposits' => VendorDeposit::forUser((int) $vendor['user_id']),
            'balance' => WalletService::balance((int) $vendor['user_id']),
        ]);
    }

    public function requestDeposit(): void
    {
        $vendor = self::currentVendor();
        $methodId = (int) Request::input('deposit_method_id', 0);
        $amount = (float) Request::input('amount', 0);
        $reference = trim(Request::input('reference', ''));

        if ($amount <= 0) {
            Session::flash('error', 'Please enter a positive amount.');
            Response::redirect('/vendor/deposits');
        }

        $method = DepositMethod::find($methodId);
        if (!$method || !(int) $method['is_active']) {
            Session::flash('error', 'Selected top-up method is not available.');
            Response::redirect('/vendor/deposits');
        }

        $receipt = null;
        if (!empty($_FILES['receipt_image']['tmp_name']) && $_FILES['receipt_image']['error'] === UPLOAD_ERR_OK) {
            $uploaded = ImageService::upload($_FILES['receipt_image'], 'deposit-receipts');
            $receipt = $uploaded['path'];
        }

        VendorDeposit::create([
            'user_id' => (int) $vendor['user_id'],
            'deposit_method_id' => $methodId,
            'amount' => $amount,
            'currency' => $method['currency'],
            'reference' => $reference,
            'receipt_image' => $receipt,
            'status' => 'pending',
        ]);

        Session::flash('success', 'Top-up request submitted. It will be reviewed soon.');
        Response::redirect('/vendor/deposits');
    }

    public function withdrawals(): string
    {
        $vendor = self::currentVendor();

        return Response::view('vendor/withdrawals', [
            'withdrawals' => Withdrawal::forUser((int) $vendor['user_id']),
            'balance' => WalletService::balance((int) $vendor['user_id']),
        ]);
    }

    public function requestWithdrawal(): void
    {
        $vendor = self::currentVendor();
        $amount = (float) Request::input('amount', 0);
        $method = trim(Request::input('method', ''));
        $currency = (string) config('app.currency', 'USD');

        if ($amount <= 0) {
            Session::flash('error', 'Please enter a positive amount.');
            Response::redirect('/vendor/withdrawals');
        }

        if (empty($method)) {
            Session::flash('error', 'Please provide a payout method (e.g. bank or crypto address).');
            Response::redirect('/vendor/withdrawals');
        }

        $balance = WalletService::balance((int) $vendor['user_id']);
        if ($amount > $balance) {
            Session::flash('error', 'Withdrawal amount exceeds your wallet balance.');
            Response::redirect('/vendor/withdrawals');
        }

        Withdrawal::create([
            'user_id' => (int) $vendor['user_id'],
            'amount' => $amount,
            'currency' => $currency,
            'method' => $method,
            'status' => 'pending',
        ]);

        Session::flash('success', 'Withdrawal request submitted. It will be reviewed soon.');
        Response::redirect('/vendor/withdrawals');
    }

    public function sales(): string
    {
        $vendor = self::currentVendor();
        $userId = (int) $vendor['user_id'];
        $vendorId = (int) $vendor['id'];

        $orders = Database::select(
            "SELECT DISTINCT o.*
             FROM orders o
             JOIN order_items oi ON oi.order_id = o.id
             WHERE oi.product_owner_id = :owner_id
                OR oi.affiliate_vendor_id = :vendor_id
             ORDER BY o.created_at DESC",
            ['owner_id' => $userId, 'vendor_id' => $vendorId]
        );

        $orderIds = array_map(fn($o) => (int) $o['id'], $orders);
        $items = [];
        if (!empty($orderIds)) {
            $in = implode(',', $orderIds);
            $items = Database::select(
                "SELECT oi.*, p.slug FROM order_items oi
                 JOIN products p ON p.id = oi.product_id
                 WHERE oi.order_id IN ({$in})
                   AND (oi.product_owner_id = :owner_id OR oi.affiliate_vendor_id = :vendor_id)
                 ORDER BY oi.order_id, oi.id",
                ['owner_id' => $userId, 'vendor_id' => $vendorId]
            );
        }

        $totals = [];
        foreach ($orders as $o) {
            $totals[(int) $o['id']] = array_sum(array_map(
                fn($i) => (float) $i['subtotal'],
                array_filter($items, fn($i) => (int) $i['order_id'] === (int) $o['id'])
            ));
        }

        return Response::view('vendor/sales', [
            'orders' => $orders,
            'items' => $items,
            'totals' => $totals,
        ]);
    }

    public function pending(): string
    {
        return Response::view('vendor/pending');
    }
}
