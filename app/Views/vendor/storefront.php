<section class="hero" style="padding: 2rem 0;">
    <h1><?= e($vendor['business_name']) ?></h1>
    <p><?= e($vendor['description'] ?: 'Welcome to our store.') ?></p>
</section>

<?php if (!empty($products) || !empty($affiliates)): ?>
    <section class="card-grid">
        <?php foreach ($products as $p): ?>
            <div class="glass-card" style="display:flex; flex-direction:column; justify-content:space-between;">
                <div>
                    <h3><a href="<?= url('/product/' . $p['slug']) ?>"><?= e($p['name']) ?></a></h3>
                    <p style="color:var(--muted); font-size:0.9rem;"><?= e($p['category_name'] ?? '') ?></p>
                    <p style="font-weight:700;"><?= e(config('app.currency_symbol')) ?><?= number_format((float) $p['price'], 2) ?></p>
                </div>
                <a href="<?= url('/product/' . $p['slug']) ?>" class="btn btn-primary" style="margin-top:0.75rem;">View</a>
            </div>
        <?php endforeach; ?>

        <?php foreach ($affiliates as $p): ?>
            <div class="glass-card" style="display:flex; flex-direction:column; justify-content:space-between; border-color:var(--primary);">
                <div>
                    <h3><a href="<?= url('/product/' . $p['slug'] . '?vendor=' . $vendor['slug']) ?>"><?= e($p['name']) ?></a></h3>
                    <p style="color:var(--muted); font-size:0.9rem;">Affiliate product · <?= e($p['category_name'] ?? '') ?></p>
                    <p style="font-weight:700;"><?= e(config('app.currency_symbol')) ?><?= number_format((float) $p['price'], 2) ?></p>
                </div>
                <a href="<?= url('/product/' . $p['slug'] . '?vendor=' . $vendor['slug']) ?>" class="btn btn-outline" style="margin-top:0.75rem;">View</a>
            </div>
        <?php endforeach; ?>
    </section>
<?php else: ?>
    <p class="text-center" style="color:var(--muted);">This store has no products yet.</p>
<?php endif; ?>
