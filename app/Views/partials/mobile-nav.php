<?php $user = \App\Core\Session::get('user'); ?>
<?php $currentPath = parse_url(\App\Core\Request::uri(), PHP_URL_PATH) ?: '/'; ?>

<nav class="mobile-bottom-nav" aria-label="Mobile navigation">
    <ul>
        <li>
            <a href="<?= url('/') ?>" <?= $currentPath === '/' ? 'aria-current="page"' : '' ?>>
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                Home
            </a>
        </li>
        <li>
            <a href="<?= url('/shop') ?>" <?= $currentPath === '/shop' ? 'aria-current="page"' : '' ?>>
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                Shop
            </a>
        </li>
        <li>
            <a href="<?= url('/vendors') ?>" <?= $currentPath === '/vendors' ? 'aria-current="page"' : '' ?>>
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                Vendors
            </a>
        </li>
        <li>
            <button type="button" data-cart-toggle aria-label="Open cart">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                <span class="cart-count" data-cart-count>0</span>
                Cart
            </button>
        </li>
        <li>
            <?php
                $accountUrl = $user ? ($user['role_name'] === 'vendor' ? '/vendor/dashboard' : '/account') : '/login';
                $accountLabel = $user ? ($user['role_name'] === 'vendor' ? 'Dashboard' : 'Account') : 'Sign in';
            ?>
            <a href="<?= url($accountUrl) ?>" <?= in_array($currentPath, ['/account', '/login', '/vendor/dashboard']) ? 'aria-current="page"' : '' ?>>
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                <?= e($accountLabel) ?>
            </a>
        </li>
    </ul>
</nav>
