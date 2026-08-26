<nav class="navbar">
    <div class="container">
        <a class="brand" href="<?= url('/') ?>">
            <img src="<?= e(setting('branding', 'logo_url', asset('images/logo.svg'))) ?>" alt="<?= e(setting('branding', 'site_name', config('app.name'))) ?>" style="height:32px; width:auto; display:block;">
        </a>

        <button id="menu-toggle" class="menu-toggle" aria-label="Open menu" aria-expanded="false" aria-controls="mobile-menu">
            <span></span><span></span><span></span>
        </button>

        <ul class="nav-links">
            <li><a href="<?= url('/shop') ?>">Shop</a></li>
            <li><a href="<?= url('/vendors') ?>">Vendors</a></li>
            <li>
                <button type="button" class="theme-toggle" data-theme-toggle aria-label="Toggle dark mode" aria-pressed="false" style="width:2rem;height:2rem;">
                    <span class="theme-icon" aria-hidden="true">☾</span>
                </button>
            </li>

            <?php $user = session('user'); ?>
            <?php if ($user): ?>
                <?php if ($user['role_name'] === 'admin'): ?>
                    <li><a href="<?= url('/admin') ?>">Admin</a></li>
                <?php endif; ?>
                <?php if ($user['role_name'] === 'vendor'): ?>
                    <li><a href="<?= url('/vendor/dashboard') ?>">Vendor</a></li>
                <?php endif; ?>
                <?php
                    $unreadCount = \App\Models\Notification::unreadCount((int) $user['id']);
                ?>
                <li>
                    <a href="<?= url('/account/notifications') ?>">Notifications<?= $unreadCount > 0 ? ' <span class="badge" style="background:#ef4444; color:#fff;">' . $unreadCount . '</span>' : '' ?></a>
                </li>
                <li><a href="<?= url('/cart') ?>">Cart</a></li>
                <li><a href="<?= url('/account') ?>">Account</a></li>
                <li>
                    <form action="<?= url('/logout') ?>" method="POST" style="display:inline;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline" style="padding:0.4rem 0.8rem;">Logout</button>
                    </form>
                </li>
            <?php else: ?>
                <li><a href="<?= url('/login') ?>">Sign in</a></li>
                <li><a href="<?= url('/register') ?>" class="btn btn-primary" style="padding:0.4rem 0.8rem;">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <div id="mobile-menu" class="mobile-menu" style="display:none;">
        <div class="container" style="flex-direction:column; align-items:flex-start;">
            <a href="<?= url('/shop') ?>">Shop</a>
            <a href="<?= url('/vendors') ?>">Vendors</a>
            <button type="button" class="theme-toggle" data-theme-toggle aria-label="Toggle dark mode" aria-pressed="false" style="justify-content:flex-start; width:100%; border-radius:0.65rem; padding:0.85rem 1rem;">
                <span class="theme-icon" aria-hidden="true">☾</span>
                <span style="margin-left:0.5rem;">Theme</span>
            </button>
            <?php if ($user): ?>
                <?php if ($user['role_name'] === 'admin'): ?><a href="<?= url('/admin') ?>">Admin</a><?php endif; ?>
                <?php if ($user['role_name'] === 'vendor'): ?><a href="<?= url('/vendor/dashboard') ?>">Vendor</a><?php endif; ?>
                <a href="<?= url('/account/notifications') ?>">Notifications<?= $unreadCount > 0 ? ' (' . $unreadCount . ')' : '' ?></a>
                <a href="<?= url('/cart') ?>">Cart</a>
                <a href="<?= url('/account') ?>">Account</a>
                <form action="<?= url('/logout') ?>" method="POST" style="margin-top:0.5rem;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline">Logout</button>
                </form>
            <?php else: ?>
                <a href="<?= url('/login') ?>">Sign in</a>
                <a href="<?= url('/register') ?>" class="btn btn-primary" style="margin-top:0.5rem;">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
