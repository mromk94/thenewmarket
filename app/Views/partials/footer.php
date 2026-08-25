<footer class="footer">
    <div class="container" style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:1rem;">
        <div style="display:flex; align-items:center; gap:0.75rem;">
            <img src="<?= asset('images/favicon.svg') ?>" alt="" style="height:28px; width:auto;">
            <p style="margin:0; color:var(--muted);">&copy; <?= date('Y') ?> <?= e(config('app.name')) ?>. All rights reserved.</p>
        </div>
        <div class="footer-links">
            <a href="<?= url('/sitemap.xml') ?>">Sitemap</a>
            <a href="<?= url('/robots.txt') ?>">Robots</a>
            <a href="<?= url('/shop') ?>">Shop</a>
            <a href="<?= url('/vendors') ?>">Vendors</a>
        </div>
    </div>
</footer>
