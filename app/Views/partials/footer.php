<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <div class="footer-brand">
                    <img src="<?= asset('images/favicon.svg') ?>" alt="" style="height:28px; width:auto;">
                    <?= e(config('app.name')) ?>
                </div>
                <p class="footer-tagline">Discover products from trusted vendors and affiliates in one marketplace.</p>
            </div>

            <div>
                <h4 style="margin:0 0 0.75rem; font-size:0.95rem; color:var(--text);">Shop</h4>
                <a class="footer-link" href="<?= url('/shop') ?>">All products</a>
                <a class="footer-link" href="<?= url('/vendors') ?>">Vendors</a>
                <a class="footer-link" href="<?= url('/shop?sort=newest') ?>">New arrivals</a>
            </div>

            <div>
                <h4 style="margin:0 0 0.75rem; font-size:0.95rem; color:var(--text);">Support</h4>
                <a class="footer-link" href="<?= url('/about') ?>">About</a>
                <a class="footer-link" href="<?= url('/contact') ?>">Contact</a>
                <a class="footer-link" href="<?= url('/terms') ?>">Terms</a>
                <a class="footer-link" href="<?= url('/privacy') ?>">Privacy</a>
            </div>

            <div>
                <h4 style="margin:0 0 0.75rem; font-size:0.95rem; color:var(--text);">Account</h4>
                <a class="footer-link" href="<?= url('/account') ?>">My account</a>
                <a class="footer-link" href="<?= url('/account/orders') ?>">Orders</a>
                <a class="footer-link" href="<?= url('/account/wallet') ?>">Wallet</a>
            </div>
        </div>

        <div class="footer-bottom">
            <p style="margin:0;">&copy; <?= date('Y') ?> <?= e(config('app.name')) ?>. All rights reserved.</p>
            <div class="footer-links" style="display:flex; gap:1rem; flex-wrap:wrap;">
                <a href="<?= url('/terms') ?>" style="color:var(--muted); font-size:inherit;">Terms</a>
                <a href="<?= url('/privacy') ?>" style="color:var(--muted); font-size:inherit;">Privacy</a>
            </div>
        </div>
    </div>
</footer>
