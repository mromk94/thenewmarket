<?php
    $cartCount = 0;
    if (\App\Core\Session::has('user_id')) {
        $cartCount = \App\Services\CartService::count((int) \App\Core\Session::get('user_id'));
    } else {
        $cartCount = \App\Services\GuestCart::count();
    }
    $loggedIn = \App\Core\Session::has('user_id');
?>

<a href="#" class="cart-float" id="cart-float" data-cart-toggle aria-label="Open cart">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:24px; height:24px;">
        <circle cx="9" cy="21" r="1"></circle>
        <circle cx="20" cy="21" r="1"></circle>
        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
    </svg>
    <span class="cart-float-count" data-cart-count><?= (int) $cartCount ?></span>
</a>

<div class="cart-drawer" id="cart-drawer" data-cart-drawer style="display:none;">
    <div class="cart-drawer-backdrop" data-cart-close></div>
    <div class="cart-drawer-panel">
        <div class="cart-drawer-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; padding-bottom:1rem; border-bottom:1px solid var(--border);">
            <h2 style="margin:0; font-size:1.25rem;">Your cart</h2>
            <button type="button" class="cart-drawer-close" data-cart-close aria-label="Close cart">&times;</button>
        </div>
        <div data-cart-items>
            <p style="color:var(--muted);">Loading cart...</p>
        </div>
        <div class="cart-drawer-footer" style="margin-top:auto; padding-top:1rem; border-top:1px solid var(--border);">
            <a href="<?= url('/cart') ?>" class="btn btn-outline w-full" style="margin-bottom:0.5rem;">View full cart</a>
            <?php if ($loggedIn): ?>
                <a href="<?= url('/checkout') ?>" class="btn btn-primary w-full">Checkout</a>
            <?php else: ?>
                <a href="<?= url('/checkout') ?>" class="btn btn-primary w-full">Sign in to checkout</a>
            <?php endif; ?>
        </div>
    </div>
</div>
