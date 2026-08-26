<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <div class="footer-brand">
                    <img src="<?= e(setting('branding', 'favicon_url', asset('images/favicon.svg'))) ?>" alt="" style="height:32px; width:32px;">
                    <?= e(config('app.name')) ?>
                </div>
                <p class="footer-tagline">Discover products from trusted vendors and affiliates in one marketplace.</p>
            </div>

            <div>
                <h4 style="margin:0 0 0.75rem; font-size:0.95rem; color:var(--text);"><?= t('shop') ?></h4>
                <a class="footer-link" href="<?= url('/shop') ?>"><?= t('all_products') ?></a>
                <a class="footer-link" href="<?= url('/vendors') ?>"><?= t('vendors') ?></a>
                <a class="footer-link" href="<?= url('/shop?sort=newest') ?>"><?= t('new_arrivals') ?></a>
            </div>

            <div>
                <h4 style="margin:0 0 0.75rem; font-size:0.95rem; color:var(--text);"><?= t('support') ?></h4>
                <a class="footer-link" href="<?= url('/about') ?>"><?= t('about') ?></a>
                <a class="footer-link" href="<?= url('/contact') ?>"><?= t('contact') ?></a>
                <a class="footer-link" href="<?= url('/terms') ?>"><?= t('terms') ?></a>
                <a class="footer-link" href="<?= url('/privacy') ?>"><?= t('privacy') ?></a>
            </div>

            <div>
                <h4 style="margin:0 0 0.75rem; font-size:0.95rem; color:var(--text);"><?= t('account') ?></h4>
                <a class="footer-link" href="<?= url('/account') ?>"><?= t('account') ?></a>
                <a class="footer-link" href="<?= url('/account/orders') ?>"><?= t('my_orders') ?></a>
                <a class="footer-link" href="<?= url('/account/wallet') ?>"><?= t('wallet') ?></a>
            </div>
            <div>
                <h4 style="margin:0 0 0.75rem; font-size:0.95rem; color:var(--text);"><?= t('currency') ?> / <?= t('language') ?></h4>
                <form action="<?= url('/currency') ?>" method="POST" style="display:flex; flex-direction:column; gap:0.5rem;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="to" value="<?= e($_SERVER['REQUEST_URI'] ?? '/') ?>">
                    <select name="code" class="form-control" onchange="this.form.submit()" style="background:transparent; border:1px solid var(--border); color:var(--text); padding:0.4rem; border-radius:0.35rem;">
                        <?php foreach (\App\Models\Currency::allActive() as $c): ?>
                            <option value="<?= e($c['code']) ?>" <?= (currency()['code'] ?? '') === $c['code'] ? 'selected' : '' ?>><?= e($c['name']) ?> (<?= e($c['symbol']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <form action="<?= url('/language') ?>" method="POST" style="display:flex; flex-direction:column; gap:0.5rem;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="to" value="<?= e($_SERVER['REQUEST_URI'] ?? '/') ?>">
                    <select name="code" class="form-control" onchange="this.form.submit()" style="background:transparent; border:1px solid var(--border); color:var(--text); padding:0.4rem; border-radius:0.35rem;">
                        <?php foreach (\App\Lang::available() as $code => $name): ?>
                            <option value="<?= e($code) ?>" <?= (\App\Lang::current() ?? 'en') === $code ? 'selected' : '' ?>><?= e($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
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
