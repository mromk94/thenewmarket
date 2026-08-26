<?php

declare(strict_types=1);

use App\Controllers\AccountController;
use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\HomeController;
use App\Controllers\PagesController;
use App\Controllers\PaymentController;
use App\Controllers\RobotsController;
use App\Controllers\ShopController;
use App\Controllers\SitemapController;
use App\Controllers\VendorController;
use App\Core\Router;

Router::get('/', [HomeController::class, 'index'])->name('home');
Router::get('/vendors', [HomeController::class, 'vendors'])->name('vendors');

Router::get('/shop', [ShopController::class, 'index'])->name('shop');
Router::get('/product/{slug}', [ShopController::class, 'show'])->name('product');
Router::post('/product/{slug}/review', [ShopController::class, 'storeReview'])->name('product.review')->middleware(['auth', 'csrf']);

Router::get('/cart', [CartController::class, 'index'])->name('cart');
Router::post('/cart/add', [CartController::class, 'add'])->name('cart.add')->middleware('csrf');
Router::post('/cart/{id}/update', [CartController::class, 'update'])->name('cart.update')->middleware('csrf');
Router::post('/cart/{id}/remove', [CartController::class, 'remove'])->name('cart.remove')->middleware('csrf');
Router::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply')->middleware('csrf');
Router::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove')->middleware('csrf');

Router::get('/checkout', [CheckoutController::class, 'show'])->name('checkout')->middleware('auth');
Router::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store')->middleware(['auth', 'csrf']);
Router::get('/checkout/pay/{orderId}', [CheckoutController::class, 'manualPay'])->name('checkout.manual')->middleware('auth');
Router::post('/checkout/pay/{orderId}', [CheckoutController::class, 'submitManual'])->name('checkout.manual.submit')->middleware(['auth', 'csrf']);

Router::get('/payment/{provider}/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Router::post('/payment/{provider}/pay', [PaymentController::class, 'verify'])->name('payment.pay')->middleware('csrf');

Router::get('/login', [AuthController::class, 'loginShow'])->name('login')->middleware('guest');
Router::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware(['guest', 'csrf']);
Router::get('/register', [AuthController::class, 'registerShow'])->name('register')->middleware('guest');
Router::post('/register', [AuthController::class, 'register'])->name('register.post')->middleware(['guest', 'csrf']);
Router::get('/forgot-password', [AuthController::class, 'forgotShow'])->name('forgot')->middleware('guest');
Router::post('/forgot-password', [AuthController::class, 'forgot'])->name('forgot.post')->middleware(['guest', 'csrf']);
Router::get('/reset-password', [AuthController::class, 'resetShow'])->name('reset');
Router::post('/reset-password', [AuthController::class, 'reset'])->name('reset.post')->middleware('csrf');
Router::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware(['auth', 'csrf']);

Router::get('/account', [AccountController::class, 'index'])->name('account')->middleware('auth');
Router::get('/account/profile', [AccountController::class, 'profile'])->name('account.profile')->middleware('auth');
Router::post('/account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update')->middleware(['auth', 'csrf']);
Router::post('/account/change-password', [AccountController::class, 'changePassword'])->name('account.change-password')->middleware(['auth', 'csrf']);
Router::get('/account/notifications', [AccountController::class, 'notifications'])->name('account.notifications')->middleware('auth');
Router::post('/account/notifications/{id}/read', [AccountController::class, 'markNotification'])->name('account.notification.read')->middleware(['auth', 'csrf']);
Router::get('/account/refunds', [AccountController::class, 'refunds'])->name('account.refunds')->middleware('auth');
Router::post('/account/refunds', [AccountController::class, 'requestRefund'])->name('account.refund.request')->middleware(['auth', 'csrf']);
Router::get('/account/addresses', [AccountController::class, 'addresses'])->name('account.addresses')->middleware('auth');
Router::post('/account/addresses', [AccountController::class, 'storeAddress'])->name('account.address.store')->middleware(['auth', 'csrf']);
Router::post('/account/addresses/{id}/default', [AccountController::class, 'setDefaultAddress'])->name('account.address.default')->middleware(['auth', 'csrf']);
Router::post('/account/addresses/{id}/delete', [AccountController::class, 'deleteAddress'])->name('account.address.delete')->middleware(['auth', 'csrf']);
Router::get('/account/orders', [AccountController::class, 'orders'])->name('account.orders')->middleware('auth');
Router::get('/account/orders/{id}', [AccountController::class, 'order'])->name('account.order')->middleware('auth');
Router::get('/account/wallet', [AccountController::class, 'wallet'])->name('account.wallet')->middleware('auth');

Router::get('/vendor/pending', [VendorController::class, 'pending'])->name('vendor.pending')->middleware('auth');
Router::get('/vendor/dashboard', [VendorController::class, 'dashboard'])->name('vendor.dashboard')->middleware(['auth', 'vendor']);
Router::get('/vendor/affiliates', [VendorController::class, 'affiliates'])->name('vendor.affiliates')->middleware(['auth', 'vendor']);
Router::post('/vendor/affiliates/{productId}/add', [VendorController::class, 'addAffiliate'])->name('vendor.affiliate.add')->middleware(['auth', 'vendor', 'csrf']);
Router::post('/vendor/affiliates/{productId}/remove', [VendorController::class, 'removeAffiliate'])->name('vendor.affiliate.remove')->middleware(['auth', 'vendor', 'csrf']);
Router::get('/vendor/wallet', [VendorController::class, 'wallet'])->name('vendor.wallet')->middleware(['auth', 'vendor']);
Router::get('/vendor/deposits', [VendorController::class, 'deposits'])->name('vendor.deposits')->middleware(['auth', 'vendor']);
Router::post('/vendor/deposits', [VendorController::class, 'requestDeposit'])->name('vendor.deposit.request')->middleware(['auth', 'vendor', 'csrf']);
Router::get('/vendor/withdrawals', [VendorController::class, 'withdrawals'])->name('vendor.withdrawals')->middleware(['auth', 'vendor']);
Router::post('/vendor/withdrawals', [VendorController::class, 'requestWithdrawal'])->name('vendor.withdrawal.request')->middleware(['auth', 'vendor', 'csrf']);
Router::get('/vendor/sales', [VendorController::class, 'sales'])->name('vendor.sales')->middleware(['auth', 'vendor']);

Router::get('/vendor/products', [VendorController::class, 'products'])->name('vendor.products')->middleware(['auth', 'vendor']);
Router::get('/vendor/products/create', [VendorController::class, 'createProduct'])->name('vendor.product.create')->middleware(['auth', 'vendor']);
Router::post('/vendor/products', [VendorController::class, 'storeProduct'])->name('vendor.product.store')->middleware(['auth', 'vendor', 'csrf']);
Router::get('/vendor/products/{id}/edit', [VendorController::class, 'editProduct'])->name('vendor.product.edit')->middleware(['auth', 'vendor']);
Router::post('/vendor/products/{id}', [VendorController::class, 'updateProduct'])->name('vendor.product.update')->middleware(['auth', 'vendor', 'csrf']);
Router::post('/vendor/products/{id}/delete', [VendorController::class, 'deleteProduct'])->name('vendor.product.delete')->middleware(['auth', 'vendor', 'csrf']);

Router::get('/vendor/{slug}', [VendorController::class, 'storefront'])->name('vendor.storefront');

Router::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard')->middleware(['auth', 'admin']);
Router::get('/admin/products', [AdminController::class, 'products'])->name('admin.products')->middleware(['auth', 'admin']);
Router::get('/admin/products/create', [AdminController::class, 'createProduct'])->name('admin.product.create')->middleware(['auth', 'admin']);
Router::post('/admin/products', [AdminController::class, 'storeProduct'])->name('admin.product.store')->middleware(['auth', 'admin', 'csrf']);
Router::get('/admin/products/{id}/edit', [AdminController::class, 'editProduct'])->name('admin.product.edit')->middleware(['auth', 'admin']);
Router::post('/admin/products/{id}', [AdminController::class, 'updateProductFull'])->name('admin.product.update.full')->middleware(['auth', 'admin', 'csrf']);
Router::post('/admin/products/{id}/delete', [AdminController::class, 'deleteProduct'])->name('admin.product.delete')->middleware(['auth', 'admin', 'csrf']);
Router::post('/admin/products/{id}/update', [AdminController::class, 'updateProduct'])->name('admin.product.update')->middleware(['auth', 'admin', 'csrf']);
Router::post('/admin/products/{productId}/images/{imageId}/thumbnail', [AdminController::class, 'setProductThumbnail'])->name('admin.product.image.thumbnail')->middleware(['auth', 'admin', 'csrf']);
Router::post('/admin/products/{productId}/images/{imageId}/delete', [AdminController::class, 'deleteProductImage'])->name('admin.product.image.delete')->middleware(['auth', 'admin', 'csrf']);
Router::get('/admin/vendors', [AdminController::class, 'vendors'])->name('admin.vendors')->middleware(['auth', 'admin']);
Router::post('/admin/vendors/{id}/update', [AdminController::class, 'updateVendor'])->name('admin.vendor.update')->middleware(['auth', 'admin', 'csrf']);
Router::get('/admin/vendors/{id}/edit', [AdminController::class, 'editVendor'])->name('admin.vendor.edit')->middleware(['auth', 'admin']);
Router::post('/admin/vendors/{id}/save', [AdminController::class, 'saveVendor'])->name('admin.vendor.save')->middleware(['auth', 'admin', 'csrf']);

Router::get('/admin/users', [AdminController::class, 'users'])->name('admin.users')->middleware(['auth', 'admin']);
Router::get('/admin/users/{id}/edit', [AdminController::class, 'editUser'])->name('admin.user.edit')->middleware(['auth', 'admin']);
Router::post('/admin/users/{id}', [AdminController::class, 'updateUser'])->name('admin.user.update')->middleware(['auth', 'admin', 'csrf']);

Router::get('/admin/deposit-methods', [AdminController::class, 'depositMethods'])->name('admin.deposit_methods')->middleware(['auth', 'admin']);
Router::get('/admin/deposit-methods/create', [AdminController::class, 'createDepositMethod'])->name('admin.deposit_method.create')->middleware(['auth', 'admin']);
Router::post('/admin/deposit-methods', [AdminController::class, 'storeDepositMethod'])->name('admin.deposit_method.store')->middleware(['auth', 'admin', 'csrf']);
Router::get('/admin/deposit-methods/{id}/edit', [AdminController::class, 'editDepositMethod'])->name('admin.deposit_method.edit')->middleware(['auth', 'admin']);
Router::post('/admin/deposit-methods/{id}', [AdminController::class, 'updateDepositMethod'])->name('admin.deposit_method.update')->middleware(['auth', 'admin', 'csrf']);
Router::post('/admin/deposit-methods/{id}/delete', [AdminController::class, 'deleteDepositMethod'])->name('admin.deposit_method.delete')->middleware(['auth', 'admin', 'csrf']);

Router::get('/admin/vendor-deposits', [AdminController::class, 'vendorDeposits'])->name('admin.vendor_deposits')->middleware(['auth', 'admin']);
Router::get('/admin/vendor-deposits/all', [AdminController::class, 'allVendorDeposits'])->name('admin.vendor_deposits.all')->middleware(['auth', 'admin']);
Router::post('/admin/vendor-deposits/{id}/approve', [AdminController::class, 'approveVendorDeposit'])->name('admin.vendor_deposit.approve')->middleware(['auth', 'admin', 'csrf']);
Router::post('/admin/vendor-deposits/{id}/reject', [AdminController::class, 'rejectVendorDeposit'])->name('admin.vendor_deposit.reject')->middleware(['auth', 'admin', 'csrf']);

Router::get('/admin/withdrawals', [AdminController::class, 'withdrawals'])->name('admin.withdrawals')->middleware(['auth', 'admin']);
Router::get('/admin/withdrawals/all', [AdminController::class, 'allWithdrawals'])->name('admin.withdrawals.all')->middleware(['auth', 'admin']);
Router::post('/admin/withdrawals/{id}/approve', [AdminController::class, 'approveWithdrawal'])->name('admin.withdrawal.approve')->middleware(['auth', 'admin', 'csrf']);
Router::post('/admin/withdrawals/{id}/reject', [AdminController::class, 'rejectWithdrawal'])->name('admin.withdrawal.reject')->middleware(['auth', 'admin', 'csrf']);

Router::get('/admin/notifications', [AdminController::class, 'notifications'])->name('admin.notifications')->middleware(['auth', 'admin']);
Router::post('/admin/notifications', [AdminController::class, 'sendNotification'])->name('admin.notification.send')->middleware(['auth', 'admin', 'csrf']);

Router::get('/admin/refunds', [AdminController::class, 'refunds'])->name('admin.refunds')->middleware(['auth', 'admin']);
Router::post('/admin/refunds/{id}/approve', [AdminController::class, 'approveRefund'])->name('admin.refund.approve')->middleware(['auth', 'admin', 'csrf']);
Router::post('/admin/refunds/{id}/reject', [AdminController::class, 'rejectRefund'])->name('admin.refund.reject')->middleware(['auth', 'admin', 'csrf']);

Router::get('/admin/payment-methods', [AdminController::class, 'paymentMethods'])->name('admin.payment_methods')->middleware(['auth', 'admin']);
Router::get('/admin/payment-methods/create', [AdminController::class, 'createPaymentMethod'])->name('admin.payment_method.create')->middleware(['auth', 'admin']);
Router::post('/admin/payment-methods', [AdminController::class, 'storePaymentMethod'])->name('admin.payment_method.store')->middleware(['auth', 'admin', 'csrf']);
Router::get('/admin/payment-methods/{id}/edit', [AdminController::class, 'editPaymentMethod'])->name('admin.payment_method.edit')->middleware(['auth', 'admin']);
Router::post('/admin/payment-methods/{id}', [AdminController::class, 'updatePaymentMethod'])->name('admin.payment_method.update')->middleware(['auth', 'admin', 'csrf']);
Router::post('/admin/payment-methods/{id}/delete', [AdminController::class, 'deletePaymentMethod'])->name('admin.payment_method.delete')->middleware(['auth', 'admin', 'csrf']);

Router::get('/admin/payment-proofs', [AdminController::class, 'paymentProofs'])->name('admin.payment_proofs')->middleware(['auth', 'admin']);
Router::post('/admin/payment-proofs/{id}/approve', [AdminController::class, 'approvePaymentProof'])->name('admin.payment_proof.approve')->middleware(['auth', 'admin', 'csrf']);
Router::post('/admin/payment-proofs/{id}/reject', [AdminController::class, 'rejectPaymentProof'])->name('admin.payment_proof.reject')->middleware(['auth', 'admin', 'csrf']);

Router::get('/admin/reviews', [AdminController::class, 'reviews'])->name('admin.reviews')->middleware(['auth', 'admin']);
Router::post('/admin/reviews/{id}/approve', [AdminController::class, 'approveReview'])->name('admin.review.approve')->middleware(['auth', 'admin', 'csrf']);
Router::post('/admin/reviews/{id}/reject', [AdminController::class, 'rejectReview'])->name('admin.review.reject')->middleware(['auth', 'admin', 'csrf']);

Router::get('/admin/coupons', [AdminController::class, 'coupons'])->name('admin.coupons')->middleware(['auth', 'admin']);
Router::post('/admin/coupons', [AdminController::class, 'storeCoupon'])->name('admin.coupon.store')->middleware(['auth', 'admin', 'csrf']);
Router::get('/admin/coupons/{id}/edit', [AdminController::class, 'editCoupon'])->name('admin.coupon.edit')->middleware(['auth', 'admin']);

Router::get('/admin/email-templates', [AdminController::class, 'emailTemplates'])->name('admin.email_templates')->middleware(['auth', 'admin']);
Router::get('/admin/email-templates/{id}/edit', [AdminController::class, 'editEmailTemplate'])->name('admin.email_template.edit')->middleware(['auth', 'admin']);
Router::post('/admin/email-templates/{id}', [AdminController::class, 'updateEmailTemplate'])->name('admin.email_template.update')->middleware(['auth', 'admin', 'csrf']);

Router::get('/admin/pages', [AdminController::class, 'pages'])->name('admin.pages')->middleware(['auth', 'admin']);
Router::get('/admin/pages/{id}/edit', [AdminController::class, 'editPage'])->name('admin.page.edit')->middleware(['auth', 'admin']);
Router::post('/admin/pages/{id}', [AdminController::class, 'updatePage'])->name('admin.page.update')->middleware(['auth', 'admin', 'csrf']);
Router::post('/admin/coupons/{id}', [AdminController::class, 'updateCoupon'])->name('admin.coupon.update')->middleware(['auth', 'admin', 'csrf']);
Router::post('/admin/coupons/{id}/delete', [AdminController::class, 'deleteCoupon'])->name('admin.coupon.delete')->middleware(['auth', 'admin', 'csrf']);

Router::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories')->middleware(['auth', 'admin']);
Router::post('/admin/categories', [AdminController::class, 'storeCategory'])->name('admin.category.store')->middleware(['auth', 'admin', 'csrf']);
Router::get('/admin/categories/{id}/edit', [AdminController::class, 'editCategory'])->name('admin.category.edit')->middleware(['auth', 'admin']);
Router::post('/admin/categories/{id}', [AdminController::class, 'updateCategory'])->name('admin.category.update')->middleware(['auth', 'admin', 'csrf']);
Router::post('/admin/categories/{id}/delete', [AdminController::class, 'deleteCategory'])->name('admin.category.delete')->middleware(['auth', 'admin', 'csrf']);

Router::get('/admin/settings/{group}', [AdminController::class, 'settings'])->name('admin.settings')->middleware(['auth', 'admin']);
Router::post('/admin/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update')->middleware(['auth', 'admin', 'csrf']);
Router::post('/admin/settings/mail/test', [AdminController::class, 'sendTestEmail'])->name('admin.settings.mail.test')->middleware(['auth', 'admin', 'csrf']);

Router::get('/about', [PagesController::class, 'about'])->name('about');
Router::get('/contact', [PagesController::class, 'contact'])->name('contact');
Router::get('/terms', [PagesController::class, 'terms'])->name('terms');
Router::get('/privacy', [PagesController::class, 'privacy'])->name('privacy');

Router::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Router::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');
