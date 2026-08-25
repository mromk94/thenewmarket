<section class="hero" style="padding: 2rem 0;">
    <h1>Affiliate Marketplace</h1>
    <p>Browse products you can promote in your store and earn commission.</p>
</section>

<section class="card-grid">
    <?php if (empty($products)): ?>
        <p class="text-center" style="color:var(--muted);">No affiliate products available right now.</p>
    <?php else: ?>
        <?php foreach ($products as $p):
            $locked = false;
            $reasons = [];
            if ((float) $p['affiliate_require_min_balance'] > 0) {
                if ($balance < (float) $p['affiliate_require_min_balance']) {
                    $locked = true;
                    $reasons[] = 'Min balance ' . e(config('app.currency_symbol')) . number_format((float) $p['affiliate_require_min_balance'], 2);
                }
            }
            if ((int) $p['affiliate_require_kyc'] && !(int) $vendor['kyc_verified']) {
                $locked = true;
                $reasons[] = 'KYC required';
            }
            if ((int) $p['affiliate_require_min_sales'] > 0) {
                if ($salesCount < (int) $p['affiliate_require_min_sales']) {
                    $locked = true;
                    $reasons[] = 'Min sales ' . (int) $p['affiliate_require_min_sales'];
                }
            }
        ?>
            <div class="glass-card" style="display:flex; flex-direction:column; justify-content:space-between; <?= $locked ? 'opacity:0.65;' : '' ?>">
                <div>
                    <h3><?= e($p['name']) ?></h3>
                    <p style="color:var(--muted); font-size:0.9rem;"><?= e($p['category_name'] ?? '') ?></p>
                    <p style="font-weight:700;"><?= e(config('app.currency_symbol')) ?><?= number_format((float) $p['price'], 2) ?></p>
                    <?php if ($p['affiliate_commission_type'] === 'percentage'): ?>
                        <p style="color:var(--primary); font-size:0.9rem;">Earn <?= (float) $p['affiliate_commission_value'] ?>% per sale</p>
                    <?php else: ?>
                        <p style="color:var(--primary); font-size:0.9rem;">Earn <?= e(config('app.currency_symbol')) ?><?= number_format((float) $p['affiliate_commission_value'], 2) ?> per sale</p>
                    <?php endif; ?>
                    <?php if (!empty($reasons)): ?>
                        <div style="margin-top:0.5rem;">
                            <?php foreach ($reasons as $r): ?>
                                <span class="badge" style="background:#f59e0b; color:#fff; margin:0.15rem;"><?= e($r) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <form action="<?= url('/vendor/affiliates/' . $p['id'] . '/add') ?>" method="POST" style="margin-top:0.75rem;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary w-full" <?= $locked ? 'disabled' : '' ?> style="<?= $locked ? 'opacity:0.5; cursor:not-allowed;' : '' ?>">
                        <?= $locked ? 'Locked' : 'Promote in my store' ?>
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
