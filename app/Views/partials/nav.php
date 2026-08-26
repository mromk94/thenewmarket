<nav class="navbar">
    <div class="container">
        <a class="brand" href="<?= url('/') ?>">
            <?php $lightLogo = e(setting('branding', 'logo_url', asset('images/logo.svg'))); ?>
            <img data-logo src="<?= $lightLogo ?>" data-src-light="<?= $lightLogo ?>" data-src-dark="<?= e(setting('branding', 'logo_dark_url', asset('images/logo-dark.svg'))) ?>" alt="<?= e(setting('branding', 'site_name', config('app.name'))) ?>" style="height:32px; width:auto; display:block;">
        </a>

        <button type="button" class="theme-toggle theme-toggle-mobile" data-theme-toggle aria-label="Toggle dark mode" aria-pressed="false" style="width:2rem; height:2rem; background:transparent; border:none; cursor:pointer; color:var(--text); font-size:1.25rem; align-items:center; justify-content:center;">
            <span class="theme-icon" aria-hidden="true">☾</span>
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

</nav>
