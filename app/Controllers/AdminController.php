<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\HttpException;
use App\Core\Logger;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Category;
use App\Models\DepositMethod;
use App\Models\Notification;
use App\Models\PaymentMethod;
use App\Models\PaymentProof;
use App\Models\Product;
use App\Models\Refund;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorDeposit;
use App\Models\Withdrawal;
use App\Core\Database;
use App\Services\ImageService;
use App\Services\Mailer;
use App\Services\WalletService;

class AdminController
{
    public function dashboard(): string
    {
        $stats = [
            'orders' => (int) (Database::first("SELECT COUNT(*) as c FROM orders", [])['c'] ?? 0),
            'customers' => (int) (Database::first("SELECT COUNT(*) as c FROM users u JOIN roles r ON r.id = u.role_id WHERE r.name = 'customer'", [])['c'] ?? 0),
            'vendors' => (int) (Database::first("SELECT COUNT(*) as c FROM vendors WHERE status = 'approved'", [])['c'] ?? 0),
            'products' => (int) (Database::first("SELECT COUNT(*) as c FROM products", [])['c'] ?? 0),
            'pending_vendors' => (int) (Database::first("SELECT COUNT(*) as c FROM vendors WHERE status = 'pending'", [])['c'] ?? 0),
            'pending_products' => (int) (Database::first("SELECT COUNT(*) as c FROM products WHERE status = 'pending'", [])['c'] ?? 0),
            'total_revenue' => (float) (Database::first("SELECT COALESCE(SUM(total), 0) as c FROM orders WHERE payment_status = 'paid'", [])['c'] ?? 0.0),
        ];

        return Response::view('admin/dashboard', [
            'stats' => $stats,
            'pendingVendors' => Vendor::findPending(),
            'pendingProducts' => Product::findAll(['status' => 'pending']),
        ]);
    }

    public function products(): string
    {
        $filters = [];
        $status = Request::input('status');
        if (in_array($status, ['pending', 'published', 'approved', 'rejected', 'suspended', 'draft'], true)) {
            $filters['status'] = $status;
        }

        return Response::view('admin/products', [
            'products' => Product::findAll($filters),
            'status' => $status,
        ]);
    }

    public function updateProduct(string $id): void
    {
        $productId = (int) $id;
        $action = Request::input('action');

        switch ($action) {
            case 'approve':
            case 'publish':
                Product::setStatus($productId, $action === 'approve' ? 'approved' : 'published');
                $product = Product::findById($productId);
                if ($product) {
                    $vendor = Vendor::findById((int) $product['vendor_id']);
                    $user = $vendor ? Database::first("SELECT email FROM users WHERE id = :id", ['id' => $vendor['user_id']]) : null;
                    if ($user) {
                        try {
                            Mailer::sendTemplate('vendor_product_approved', $user['email'], [
                                'product_name' => $product['name'],
                            ]);
                        } catch (\Throwable $e) {
                            Logger::error('Product approved email failed: ' . $e->getMessage());
                        }
                    }
                }
                break;
            case 'reject':
                Product::setStatus($productId, 'rejected');
                break;
            case 'suspend':
                Product::setStatus($productId, 'suspended');
                break;
            case 'feature':
                Product::setFeatured($productId, true);
                break;
            case 'unfeature':
                Product::setFeatured($productId, false);
                break;
            case 'hide':
                Product::setVisibility($productId, 'hidden');
                break;
            case 'show':
                Product::setVisibility($productId, 'public');
                break;
            default:
                throw new HttpException('Unknown action.', 400);
        }

        Session::flash('success', 'Product updated.');
        Response::redirect('/admin/products');
    }

    public function vendors(): string
    {
        return Response::view('admin/vendors', [
            'vendors' => Vendor::findAll(),
        ]);
    }

    public function updateVendor(string $id): void
    {
        $vendorId = (int) $id;
        $action = Request::input('action');
        $reason = trim(Request::input('rejection_reason', ''));

        switch ($action) {
            case 'approve':
                Vendor::setStatus($vendorId, 'approved');
                $vendor = Vendor::findById($vendorId);
                $user = $vendor ? Database::first("SELECT email FROM users WHERE id = :id", ['id' => $vendor['user_id']]) : null;
                if ($user) {
                    try {
                        Mailer::sendTemplate('vendor_approved', $user['email'], [
                            'business_name' => $vendor['business_name'] ?? 'your business',
                        ]);
                    } catch (\Throwable $e) {
                        Logger::error('Vendor approved email failed: ' . $e->getMessage());
                    }
                }
                break;
            case 'reject':
                Vendor::setStatus($vendorId, 'rejected', $reason);
                break;
            case 'suspend':
                Vendor::setStatus($vendorId, 'suspended', $reason);
                break;
            default:
                throw new HttpException('Unknown action.', 400);
        }

        Session::flash('success', 'Vendor updated.');
        Response::redirect('/admin/vendors');
    }

    public function categories(): string
    {
        return Response::view('admin/categories', [
            'categories' => Category::all(),
        ]);
    }

    public function storeCategory(): void
    {
        $name = trim(Request::input('name', ''));
        if (empty($name)) {
            Session::flash('error', 'Category name is required.');
            Response::redirect('/admin/categories');
        }

        $image = null;
        if (!empty($_FILES['image']['tmp_name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploaded = ImageService::upload($_FILES['image'], 'categories');
            $image = $uploaded['path'];
        }

        Category::create([
            'name' => $name,
            'description' => Request::input('description', ''),
            'image' => $image,
            'is_visible' => (int) Request::input('is_visible', 1),
            'sort_order' => (int) Request::input('sort_order', 0),
        ]);

        Session::flash('success', 'Category created.');
        Response::redirect('/admin/categories');
    }

    public function editCategory(string $id): string
    {
        $category = Category::findById((int) $id);
        if (!$category) {
            throw new HttpException('Category not found.', 404);
        }

        return Response::view('admin/categories/edit', [
            'category' => $category,
        ]);
    }

    public function updateCategory(string $id): void
    {
        $categoryId = (int) $id;
        $category = Category::findById($categoryId);
        if (!$category) {
            throw new HttpException('Category not found.', 404);
        }

        $name = trim(Request::input('name', ''));
        if (empty($name)) {
            Session::flash('error', 'Category name is required.');
            Response::redirect('/admin/categories/' . $categoryId . '/edit');
        }

        $image = $category['image'];
        if (!empty($_FILES['image']['tmp_name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            if ($image) {
                $oldPath = PUBLIC_PATH . '/assets/' . $image;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $uploaded = ImageService::upload($_FILES['image'], 'categories');
            $image = $uploaded['path'];
        }

        Category::update($categoryId, [
            'name' => $name,
            'description' => Request::input('description', ''),
            'image' => $image,
            'is_visible' => (int) Request::input('is_visible', 1),
            'sort_order' => (int) Request::input('sort_order', 0),
        ]);

        Session::flash('success', 'Category updated.');
        Response::redirect('/admin/categories');
    }

    public function deleteCategory(string $id): void
    {
        Category::delete((int) $id);
        Session::flash('success', 'Category deleted.');
        Response::redirect('/admin/categories');
    }

    public function settings(string $group = 'general'): string
    {
        $allowed = ['general', 'branding', 'mail', 'commerce', 'payment', 'security', 'seo', 'legal'];
        if (!in_array($group, $allowed, true)) {
            throw new HttpException('Invalid settings group.', 404);
        }

        $values = Setting::grouped()[$group] ?? [];

        $defaults = match ($group) {
            'general' => [
                'site_name' => config('app.name'),
                'site_url' => config('app.url'),
                'currency' => config('app.currency'),
                'currency_symbol' => config('app.currency_symbol'),
                'timezone' => config('app.timezone'),
                'contact_email' => 'support@thenewage.local',
            ],
            'branding' => [
                'logo_url' => asset('images/logo.svg'),
                'favicon_url' => asset('images/favicon.svg'),
                'icon_url' => asset('images/icon.svg'),
                'tagline' => 'A premium marketplace for curated products and affiliate storefronts.',
                'description' => 'Discover trusted vendors and affiliate products.',
                'sender_name' => config('app.name'),
                'sender_email' => 'noreply@thenewage.local',
                'support_email' => 'support@thenewage.local',
                'contact_email' => 'support@thenewage.local',
                'phone' => '',
                'address' => '',
                'social_facebook' => '',
                'social_instagram' => '',
                'social_twitter' => '',
                'social_linkedin' => '',
            ],
            'mail' => [
                'mailer' => 'log',
                'host' => '',
                'port' => '587',
                'username' => '',
                'password' => '',
                'encryption' => 'tls',
                'from_address' => 'noreply@thenewage.local',
                'from_name' => config('app.name'),
                'reply_to' => '',
            ],
            'commerce' => [
                'shipping_rate' => '10.00',
                'free_shipping_threshold' => '100.00',
                'tax_rate' => '0.00',
                'discount_percent' => '0.00',
            ],
            'security' => [
                'session_lifetime' => '120',
                'login_attempts' => '5',
                'login_decay' => '900',
            ],
            'seo' => [
                'default_title' => config('app.name'),
                'default_description' => 'A premium multi-vendor marketplace.',
                'canonical_url' => config('app.url'),
                'og_image' => asset('images/icon.svg'),
            ],
            default => [],
        };

        foreach ($defaults as $key => $value) {
            if (!isset($values[$key])) {
                $values[$key] = $value;
            }
        }

        return Response::view('admin/settings/index', [
            'group' => $group,
            'values' => $values,
            'groups' => $allowed,
        ]);
    }

    public function updateSettings(): void
    {
        $group = Request::input('group', 'general');
        $allowed = ['general', 'branding', 'mail', 'commerce', 'payment', 'security', 'seo', 'legal'];
        if (!in_array($group, $allowed, true)) {
            throw new HttpException('Invalid settings group.', 400);
        }

        $settings = [];
        foreach (Request::all() as $key => $value) {
            if (str_starts_with($key, 's_')) {
                $settings[substr($key, 2)] = $value;
            }
        }

        Setting::setMany($group, $settings);
        Setting::clearCache();

        Session::flash('success', ucfirst($group) . ' settings saved.');
        Response::redirect('/admin/settings/' . $group);
    }

    public function sendTestEmail(): void
    {
        $to = trim(Request::input('to', ''));
        if (empty($to)) {
            Session::flash('error', 'Recipient email is required.');
            Response::redirect('/admin/settings/mail');
        }

        try {
            Mailer::send($to, 'Test email from ' . config('app.name'), '<p>This is a test email from your marketplace.</p>');
            Session::flash('success', 'Test email sent to ' . e($to) . '. Check your inbox or logs.');
        } catch (HttpException $e) {
            Session::flash('error', $e->getMessage());
        }

        Response::redirect('/admin/settings/mail');
    }

    public function createProduct(): string
    {
        return Response::view('admin/products/create', [
            'categories' => Category::all(),
        ]);
    }

    public function storeProduct(): void
    {
        $userId = (int) session('user')['id'];

        $data = [
            'owner_id' => $userId,
            'name' => trim(Request::input('name', '')),
            'description' => trim(Request::input('description', '')),
            'short_description' => trim(Request::input('short_description', '')),
            'sku' => trim(Request::input('sku', '')),
            'price' => (float) Request::input('price', 0),
            'compare_at_price' => Request::input('compare_at_price', '') !== '' ? (float) Request::input('compare_at_price') : null,
            'sale_price' => Request::input('sale_price', '') !== '' ? (float) Request::input('sale_price') : null,
            'stock_qty' => (int) Request::input('stock_qty', 0),
            'inventory_status' => Request::input('inventory_status', 'in_stock'),
            'category_id' => (int) Request::input('category_id', 0),
            'is_affiliate_eligible' => (int) Request::input('is_affiliate_eligible', 0),
            'affiliate_commission_type' => Request::input('affiliate_commission_type', 'percentage'),
            'affiliate_commission_value' => (float) Request::input('affiliate_commission_value', 0),
            'affiliate_require_min_balance' => (float) Request::input('affiliate_require_min_balance', 0),
            'affiliate_require_kyc' => (int) Request::input('affiliate_require_kyc', 0),
            'affiliate_require_min_sales' => (int) Request::input('affiliate_require_min_sales', 0),
            'status' => 'published',
            'visibility' => 'public',
            'featured' => (int) Request::input('featured', 0),
        ];

        if (empty($data['name']) || (float) $data['price'] <= 0) {
            Session::flash('error', 'Product name and a positive price are required.');
            Response::redirect('/admin/products/create');
        }

        $productId = Product::create($data);

        if (!empty($_FILES['images']['tmp_name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
                if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                    $uploaded = ImageService::upload([
                        'tmp_name' => $tmp,
                        'name' => $_FILES['images']['name'][$i],
                        'type' => $_FILES['images']['type'][$i],
                        'error' => $_FILES['images']['error'][$i],
                        'size' => $_FILES['images']['size'][$i],
                    ], 'products');
                    Product::attachImage($productId, $uploaded['path'], $i === 0, $i);
                }
            }
        }

        Session::flash('success', 'Product created.');
        Response::redirect('/admin/products/' . $productId . '/edit');
    }

    public function editProduct(string $id): string
    {
        $product = Product::findByIdAdmin((int) $id);
        if (!$product) {
            throw new HttpException('Product not found.', 404);
        }

        return Response::view('admin/products/edit', [
            'product' => $product,
            'categories' => Category::all(),
            'images' => Product::images((int) $id),
        ]);
    }

    public function updateProductFull(string $id): void
    {
        $productId = (int) $id;
        $product = Product::findByIdAdmin($productId);
        if (!$product) {
            throw new HttpException('Product not found.', 404);
        }

        $data = [
            'name' => trim(Request::input('name', '')),
            'description' => trim(Request::input('description', '')),
            'short_description' => trim(Request::input('short_description', '')),
            'sku' => trim(Request::input('sku', '')),
            'price' => (float) Request::input('price', 0),
            'compare_at_price' => Request::input('compare_at_price', '') !== '' ? (float) Request::input('compare_at_price') : null,
            'sale_price' => Request::input('sale_price', '') !== '' ? (float) Request::input('sale_price') : null,
            'stock_qty' => (int) Request::input('stock_qty', 0),
            'inventory_status' => Request::input('inventory_status', 'in_stock'),
            'category_id' => (int) Request::input('category_id', 0),
            'is_affiliate_eligible' => (int) Request::input('is_affiliate_eligible', 0),
            'affiliate_commission_type' => Request::input('affiliate_commission_type', 'percentage'),
            'affiliate_commission_value' => (float) Request::input('affiliate_commission_value', 0),
            'affiliate_require_min_balance' => (float) Request::input('affiliate_require_min_balance', 0),
            'affiliate_require_kyc' => (int) Request::input('affiliate_require_kyc', 0),
            'affiliate_require_min_sales' => (int) Request::input('affiliate_require_min_sales', 0),
            'status' => Request::input('status', 'published'),
            'visibility' => Request::input('visibility', 'public'),
            'featured' => (int) Request::input('featured', 0),
        ];

        if (empty($data['name']) || (float) $data['price'] <= 0) {
            Session::flash('error', 'Product name and a positive price are required.');
            Response::redirect('/admin/products/' . $productId . '/edit');
        }

        Product::update($productId, $data);

        // Add more images
        if (!empty($_FILES['images']['tmp_name'][0])) {
            $currentCount = count(Product::images($productId));
            foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
                if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                    $uploaded = ImageService::upload([
                        'tmp_name' => $tmp,
                        'name' => $_FILES['images']['name'][$i],
                        'type' => $_FILES['images']['type'][$i],
                        'error' => $_FILES['images']['error'][$i],
                        'size' => $_FILES['images']['size'][$i],
                    ], 'products');
                    Product::attachImage($productId, $uploaded['path'], false, $currentCount + $i);
                }
            }
        }

        // Update sort orders
        foreach (Request::all() as $key => $value) {
            if (str_starts_with($key, 'sort_order_')) {
                $imageId = (int) str_replace('sort_order_', '', $key);
                Database::query(
                    "UPDATE product_images SET sort_order = :sort_order WHERE id = :id",
                    ['sort_order' => (int) $value, 'id' => $imageId]
                );
            }
        }

        Session::flash('success', 'Product updated.');
        Response::redirect('/admin/products/' . $productId . '/edit');
    }

    public function deleteProduct(string $id): void
    {
        Product::delete((int) $id);
        Session::flash('success', 'Product deleted.');
        Response::redirect('/admin/products');
    }

    public function setProductThumbnail(string $productId, string $imageId): void
    {
        Product::setAllImagesNotThumbnail((int) $productId);
        Database::query(
            "UPDATE product_images SET is_thumbnail = 1 WHERE id = :id AND product_id = :product_id",
            ['id' => (int) $imageId, 'product_id' => (int) $productId]
        );
        Session::flash('success', 'Thumbnail updated.');
        Response::redirect('/admin/products/' . $productId . '/edit');
    }

    public function deleteProductImage(string $productId, string $imageId): void
    {
        $image = Database::first(
            "SELECT * FROM product_images WHERE id = :id AND product_id = :product_id",
            ['id' => (int) $imageId, 'product_id' => (int) $productId]
        );
        if ($image) {
            $fullPath = PUBLIC_PATH . '/assets/' . $image['file_path'];
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            Database::query("DELETE FROM product_images WHERE id = :id", ['id' => (int) $imageId]);
        }
        Session::flash('success', 'Image removed.');
        Response::redirect('/admin/products/' . $productId . '/edit');
    }

    public function depositMethods(): string
    {
        return Response::view('admin/deposit_methods/index', [
            'methods' => DepositMethod::all(),
        ]);
    }

    public function createDepositMethod(): string
    {
        return Response::view('admin/deposit_methods/create');
    }

    public function storeDepositMethod(): void
    {
        $data = [
            'type' => Request::input('type', 'crypto'),
            'name' => trim(Request::input('name', '')),
            'currency' => trim(Request::input('currency', '')),
            'network' => trim(Request::input('network', '')),
            'wallet_address' => trim(Request::input('wallet_address', '')),
            'bank_name' => trim(Request::input('bank_name', '')),
            'account_name' => trim(Request::input('account_name', '')),
            'account_number' => trim(Request::input('account_number', '')),
            'instructions' => trim(Request::input('instructions', '')),
            'is_active' => (int) Request::input('is_active', 1),
            'sort_order' => (int) Request::input('sort_order', 0),
        ];

        if (empty($data['name']) || empty($data['currency'])) {
            Session::flash('error', 'Name and currency are required.');
            Response::redirect('/admin/deposit-methods/create');
        }

        if (!empty($_FILES['qr_image']['tmp_name']) && $_FILES['qr_image']['error'] === UPLOAD_ERR_OK) {
            $uploaded = ImageService::upload($_FILES['qr_image'], 'deposit-qr');
            $data['qr_image'] = $uploaded['path'];
        }

        DepositMethod::create($data);
        Session::flash('success', 'Deposit method saved.');
        Response::redirect('/admin/deposit-methods');
    }

    public function editDepositMethod(string $id): string
    {
        $method = DepositMethod::find((int) $id);
        if (!$method) {
            throw new HttpException('Deposit method not found.', 404);
        }

        return Response::view('admin/deposit_methods/edit', [
            'method' => $method,
        ]);
    }

    public function updateDepositMethod(string $id): void
    {
        $method = DepositMethod::find((int) $id);
        if (!$method) {
            throw new HttpException('Deposit method not found.', 404);
        }

        $data = [
            'type' => Request::input('type', 'crypto'),
            'name' => trim(Request::input('name', '')),
            'currency' => trim(Request::input('currency', '')),
            'network' => trim(Request::input('network', '')),
            'wallet_address' => trim(Request::input('wallet_address', '')),
            'bank_name' => trim(Request::input('bank_name', '')),
            'account_name' => trim(Request::input('account_name', '')),
            'account_number' => trim(Request::input('account_number', '')),
            'instructions' => trim(Request::input('instructions', '')),
            'is_active' => (int) Request::input('is_active', 1),
            'sort_order' => (int) Request::input('sort_order', 0),
        ];

        if (empty($data['name']) || empty($data['currency'])) {
            Session::flash('error', 'Name and currency are required.');
            Response::redirect('/admin/deposit-methods/' . $id . '/edit');
        }

        if (!empty($_FILES['qr_image']['tmp_name']) && $_FILES['qr_image']['error'] === UPLOAD_ERR_OK) {
            if ($method['qr_image']) {
                $oldPath = PUBLIC_PATH . '/assets/' . $method['qr_image'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $uploaded = ImageService::upload($_FILES['qr_image'], 'deposit-qr');
            $data['qr_image'] = $uploaded['path'];
        }

        DepositMethod::update((int) $id, $data);
        Session::flash('success', 'Deposit method updated.');
        Response::redirect('/admin/deposit-methods');
    }

    public function deleteDepositMethod(string $id): void
    {
        $method = DepositMethod::find((int) $id);
        if ($method && $method['qr_image']) {
            $fullPath = PUBLIC_PATH . '/assets/' . $method['qr_image'];
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
        DepositMethod::delete((int) $id);
        Session::flash('success', 'Deposit method deleted.');
        Response::redirect('/admin/deposit-methods');
    }

    public function vendorDeposits(): string
    {
        return Response::view('admin/vendor_deposits', [
            'deposits' => VendorDeposit::pending(),
        ]);
    }

    public function allVendorDeposits(): string
    {
        return Response::view('admin/vendor_deposits', [
            'deposits' => VendorDeposit::all(),
        ]);
    }

    public function approveVendorDeposit(string $id): void
    {
        $deposit = VendorDeposit::find((int) $id);
        if (!$deposit || $deposit['status'] !== 'pending') {
            throw new HttpException('Deposit not found.', 404);
        }

        WalletService::credit((int) $deposit['user_id'], (float) $deposit['amount'], 'credit', 'vendor_deposit', (int) $id, 'Manual top-up approved');
        VendorDeposit::setStatus((int) $id, 'approved', trim(Request::input('admin_note', '')));

        Session::flash('success', 'Deposit approved and wallet credited.');
        Response::redirect('/admin/vendor-deposits');
    }

    public function rejectVendorDeposit(string $id): void
    {
        $deposit = VendorDeposit::find((int) $id);
        if (!$deposit || $deposit['status'] !== 'pending') {
            throw new HttpException('Deposit not found.', 404);
        }

        VendorDeposit::setStatus((int) $id, 'rejected', trim(Request::input('admin_note', '')));
        Session::flash('success', 'Deposit rejected.');
        Response::redirect('/admin/vendor-deposits');
    }

    public function withdrawals(): string
    {
        return Response::view('admin/withdrawals', [
            'withdrawals' => Withdrawal::pending(),
        ]);
    }

    public function allWithdrawals(): string
    {
        return Response::view('admin/withdrawals', [
            'withdrawals' => Withdrawal::all(),
        ]);
    }

    public function approveWithdrawal(string $id): void
    {
        $withdrawal = Withdrawal::find((int) $id);
        if (!$withdrawal || $withdrawal['status'] !== 'pending') {
            throw new HttpException('Withdrawal not found.', 404);
        }

        $balance = WalletService::balance((int) $withdrawal['user_id']);
        if ((float) $withdrawal['amount'] > $balance) {
            Session::flash('error', 'Insufficient wallet balance to approve this withdrawal.');
            Response::redirect('/admin/withdrawals');
        }

        WalletService::debit((int) $withdrawal['user_id'], (float) $withdrawal['amount'], 'debit', 'vendor_withdrawal', (int) $id, 'Manual withdrawal approved');
        Withdrawal::setStatus((int) $id, 'approved', trim(Request::input('admin_note', '')));

        $user = Database::first("SELECT email FROM users WHERE id = :id", ['id' => (int) $withdrawal['user_id']]);
        if ($user) {
            try {
                Mailer::sendTemplate('vendor_withdrawal_approved', $user['email'], [
                    'currency_symbol' => (string) config('app.currency_symbol'),
                    'amount' => number_format((float) $withdrawal['amount'], 2),
                ]);
            } catch (\Throwable $e) {
                Logger::error('Withdrawal approved email failed: ' . $e->getMessage());
            }
        }

        Session::flash('success', 'Withdrawal approved and wallet debited.');
        Response::redirect('/admin/withdrawals');
    }

    public function rejectWithdrawal(string $id): void
    {
        $withdrawal = Withdrawal::find((int) $id);
        if (!$withdrawal || $withdrawal['status'] !== 'pending') {
            throw new HttpException('Withdrawal not found.', 404);
        }

        Withdrawal::setStatus((int) $id, 'rejected', trim(Request::input('admin_note', '')));
        Session::flash('success', 'Withdrawal rejected.');
        Response::redirect('/admin/withdrawals');
    }

    public function users(): string
    {
        return Response::view('admin/users/index', [
            'users' => User::all(),
            'roles' => Database::select('SELECT * FROM roles ORDER BY name', []),
        ]);
    }

    public function editUser(string $id): string
    {
        $user = User::find((int) $id);
        if (!$user) {
            throw new HttpException('User not found.', 404);
        }

        return Response::view('admin/users/edit', [
            'user' => $user,
            'roles' => Database::select('SELECT * FROM roles ORDER BY name', []),
        ]);
    }

    public function updateUser(string $id): void
    {
        $user = User::find((int) $id);
        if (!$user) {
            throw new HttpException('User not found.', 404);
        }

        $data = [
            'email' => trim(Request::input('email', '')),
            'status' => Request::input('status', 'active'),
            'role_id' => (int) Request::input('role_id', 0),
        ];

        if (empty($data['email'])) {
            Session::flash('error', 'Email is required.');
            Response::redirect('/admin/users/' . $id . '/edit');
        }

        if (!empty(Request::input('password'))) {
            $data['password_hash'] = password_hash(Request::input('password'), PASSWORD_DEFAULT);
        }

        User::update((int) $id, $data);
        Session::flash('success', 'User updated.');
        Response::redirect('/admin/users');
    }

    public function editVendor(string $id): string
    {
        $vendor = Vendor::findById((int) $id);
        if (!$vendor) {
            throw new HttpException('Vendor not found.', 404);
        }

        return Response::view('admin/vendors/edit', [
            'vendor' => $vendor,
            'user' => User::find((int) $vendor['user_id']),
        ]);
    }

    public function saveVendor(string $id): void
    {
        $vendor = Vendor::findById((int) $id);
        if (!$vendor) {
            throw new HttpException('Vendor not found.', 404);
        }

        $data = [
            'business_name' => trim(Request::input('business_name', '')),
            'slug' => trim(Request::input('slug', '')),
            'description' => trim(Request::input('description', '')),
            'default_commission_rate' => (float) Request::input('default_commission_rate', 0) / 100,
            'kyc_verified' => (int) Request::input('kyc_verified', 0),
        ];

        if (empty($data['business_name']) || empty($data['slug'])) {
            Session::flash('error', 'Business name and slug are required.');
            Response::redirect('/admin/vendors/' . $id . '/edit');
        }

        Vendor::update((int) $id, $data);
        Session::flash('success', 'Vendor updated.');
        Response::redirect('/admin/vendors');
    }

    public function notifications(): string
    {
        return Response::view('admin/notifications', [
            'users' => Database::select('SELECT id, email FROM users ORDER BY email', []),
            'recent' => Database::select('SELECT n.*, u.email FROM notifications n JOIN users u ON u.id = n.user_id ORDER BY n.created_at DESC LIMIT 50', []),
        ]);
    }

    public function sendNotification(): void
    {
        $title = trim(Request::input('title', ''));
        $message = trim(Request::input('message', ''));
        $userId = (int) Request::input('user_id', 0);
        $sendAll = (int) Request::input('send_all', 0);
        $type = in_array(Request::input('type', ''), ['info', 'success', 'warning'], true)
            ? Request::input('type')
            : 'info';

        if (empty($title) || empty($message)) {
            Session::flash('error', 'Title and message are required.');
            Response::redirect('/admin/notifications');
        }

        if ($sendAll) {
            $users = Database::select('SELECT id FROM users', []);
            foreach ($users as $u) {
                Notification::create([
                    'user_id' => (int) $u['id'],
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'is_read' => 0,
                ]);
            }
            Session::flash('success', 'Notification sent to all users.');
        } elseif ($userId > 0) {
            Notification::create([
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'is_read' => 0,
            ]);
            Session::flash('success', 'Notification sent.');
        } else {
            Session::flash('error', 'Select a user or choose send to all.');
        }

        Response::redirect('/admin/notifications');
    }

    public function refunds(): string
    {
        return Response::view('admin/refunds', [
            'refunds' => Refund::pending(),
        ]);
    }

    public function approveRefund(string $id): void
    {
        $refund = Refund::find((int) $id);
        if (!$refund || $refund['status'] !== 'pending') {
            throw new HttpException('Refund not found.', 404);
        }

        WalletService::credit((int) $refund['customer_id'], (float) $refund['amount'], 'credit', 'refund', (int) $id, 'Refund approved for order ' . $refund['order_number']);
        Refund::setStatus((int) $id, 'approved', trim(Request::input('admin_note', '')));

        $user = Database::first("SELECT email FROM users WHERE id = :id", ['id' => (int) $refund['customer_id']]);
        if ($user) {
            try {
                Mailer::sendTemplate('customer_refund_approved', $user['email'], [
                    'order_number' => $refund['order_number'],
                    'currency_symbol' => (string) config('app.currency_symbol'),
                    'amount' => number_format((float) $refund['amount'], 2),
                ]);
            } catch (\Throwable $e) {
                Logger::error('Refund approved email failed: ' . $e->getMessage());
            }
        }

        Session::flash('success', 'Refund approved and customer wallet credited.');
        Response::redirect('/admin/refunds');
    }

    public function rejectRefund(string $id): void
    {
        $refund = Refund::find((int) $id);
        if (!$refund || $refund['status'] !== 'pending') {
            throw new HttpException('Refund not found.', 404);
        }

        Refund::setStatus((int) $id, 'rejected', trim(Request::input('admin_note', '')));
        Session::flash('success', 'Refund rejected.');
        Response::redirect('/admin/refunds');
    }

    public function paymentMethods(): string
    {
        return Response::view('admin/payment_methods/index', [
            'methods' => PaymentMethod::all(),
        ]);
    }

    public function createPaymentMethod(): string
    {
        return Response::view('admin/payment_methods/create');
    }

    public function storePaymentMethod(): void
    {
        $data = [
            'type' => Request::input('type', 'crypto'),
            'name' => trim(Request::input('name', '')),
            'currency' => trim(Request::input('currency', '')),
            'network' => trim(Request::input('network', '')),
            'wallet_address' => trim(Request::input('wallet_address', '')),
            'bank_name' => trim(Request::input('bank_name', '')),
            'account_name' => trim(Request::input('account_name', '')),
            'account_number' => trim(Request::input('account_number', '')),
            'instructions' => trim(Request::input('instructions', '')),
            'is_active' => (int) Request::input('is_active', 1),
            'sort_order' => (int) Request::input('sort_order', 0),
        ];

        if (empty($data['name']) || empty($data['currency'])) {
            Session::flash('error', 'Name and currency are required.');
            Response::redirect('/admin/payment-methods/create');
        }

        if (!empty($_FILES['qr_image']['tmp_name']) && $_FILES['qr_image']['error'] === UPLOAD_ERR_OK) {
            $uploaded = ImageService::upload($_FILES['qr_image'], 'payment-qr');
            $data['qr_image'] = $uploaded['path'];
        }

        PaymentMethod::create($data);
        Session::flash('success', 'Payment method saved.');
        Response::redirect('/admin/payment-methods');
    }

    public function editPaymentMethod(string $id): string
    {
        $method = PaymentMethod::find((int) $id);
        if (!$method) {
            throw new HttpException('Payment method not found.', 404);
        }

        return Response::view('admin/payment_methods/edit', [
            'method' => $method,
        ]);
    }

    public function updatePaymentMethod(string $id): void
    {
        $method = PaymentMethod::find((int) $id);
        if (!$method) {
            throw new HttpException('Payment method not found.', 404);
        }

        $data = [
            'type' => Request::input('type', 'crypto'),
            'name' => trim(Request::input('name', '')),
            'currency' => trim(Request::input('currency', '')),
            'network' => trim(Request::input('network', '')),
            'wallet_address' => trim(Request::input('wallet_address', '')),
            'bank_name' => trim(Request::input('bank_name', '')),
            'account_name' => trim(Request::input('account_name', '')),
            'account_number' => trim(Request::input('account_number', '')),
            'instructions' => trim(Request::input('instructions', '')),
            'is_active' => (int) Request::input('is_active', 1),
            'sort_order' => (int) Request::input('sort_order', 0),
        ];

        if (empty($data['name']) || empty($data['currency'])) {
            Session::flash('error', 'Name and currency are required.');
            Response::redirect('/admin/payment-methods/' . $id . '/edit');
        }

        if (!empty($_FILES['qr_image']['tmp_name']) && $_FILES['qr_image']['error'] === UPLOAD_ERR_OK) {
            if ($method['qr_image']) {
                $oldPath = PUBLIC_PATH . '/assets/' . $method['qr_image'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $uploaded = ImageService::upload($_FILES['qr_image'], 'payment-qr');
            $data['qr_image'] = $uploaded['path'];
        }

        PaymentMethod::update((int) $id, $data);
        Session::flash('success', 'Payment method updated.');
        Response::redirect('/admin/payment-methods');
    }

    public function deletePaymentMethod(string $id): void
    {
        $method = PaymentMethod::find((int) $id);
        if ($method && $method['qr_image']) {
            $fullPath = PUBLIC_PATH . '/assets/' . $method['qr_image'];
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
        PaymentMethod::delete((int) $id);
        Session::flash('success', 'Payment method deleted.');
        Response::redirect('/admin/payment-methods');
    }

    public function paymentProofs(): string
    {
        return Response::view('admin/payment_proofs', [
            'proofs' => PaymentProof::pending(),
        ]);
    }

    public function approvePaymentProof(string $id): void
    {
        $proof = PaymentProof::find((int) $id);
        if (!$proof || $proof['status'] !== 'pending') {
            throw new HttpException('Payment proof not found.', 404);
        }

        OrderService::markPaid((int) $proof['order_id'], 'manual:' . $proof['id']);
        PaymentProof::setStatus((int) $id, 'approved', trim(Request::input('admin_note', '')));

        Session::flash('success', 'Payment proof approved. Order marked as paid.');
        Response::redirect('/admin/payment-proofs');
    }

    public function rejectPaymentProof(string $id): void
    {
        $proof = PaymentProof::find((int) $id);
        if (!$proof || $proof['status'] !== 'pending') {
            throw new HttpException('Payment proof not found.', 404);
        }

        PaymentProof::setStatus((int) $id, 'rejected', trim(Request::input('admin_note', '')));
        Session::flash('success', 'Payment proof rejected.');
        Response::redirect('/admin/payment-proofs');
    }

    public function reviews(): string
    {
        return Response::view('admin/reviews', [
            'reviews' => Review::pending(50),
        ]);
    }

    public function approveReview(string $id): void
    {
        $review = Database::first("SELECT * FROM reviews WHERE id = :id", ['id' => (int) $id]);
        if (!$review || $review['status'] !== 'pending') {
            throw new HttpException('Review not found.', 404);
        }

        Review::setStatus((int) $id, 'approved');
        Session::flash('success', 'Review approved.');
        Response::redirect('/admin/reviews');
    }

    public function rejectReview(string $id): void
    {
        $review = Database::first("SELECT * FROM reviews WHERE id = :id", ['id' => (int) $id]);
        if (!$review || $review['status'] !== 'pending') {
            throw new HttpException('Review not found.', 404);
        }

        Review::setStatus((int) $id, 'rejected');
        Session::flash('success', 'Review rejected.');
        Response::redirect('/admin/reviews');
    }
}
