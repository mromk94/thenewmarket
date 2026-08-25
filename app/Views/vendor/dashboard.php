<section class="hero" style="padding: 2rem 0;">
    <h1>Vendor Dashboard</h1>
    <p>Welcome back, <?= e($vendor['business_name']) ?>.</p>
</section>

<section class="card-grid mt-4">
    <div class="glass-card">
        <h3><?= number_format($stats['products']) ?></h3>
        <p style="color:var(--muted);">Total products</p>
    </div>
    <div class="glass-card">
        <h3><?= number_format($stats['published']) ?></h3>
        <p style="color:var(--muted);">Published</p>
    </div>
    <div class="glass-card">
        <h3><?= number_format($stats['pending']) ?></h3>
        <p style="color:var(--muted);">Pending review</p>
    </div>
    <div class="glass-card">
        <h3><?= e(config('app.currency_symbol')) ?><?= number_format($stats['balance'], 2) ?></h3>
        <p style="color:var(--muted);">Wallet balance</p>
    </div>
</section>

<section class="mt-4">
    <h2 class="mb-2">Quick actions</h2>
    <div class="card-grid">
        <div class="glass-card">
            <h3>My Products</h3>
            <p style="color:var(--muted);">View and manage your products.</p>
            <a href="<?= url('/vendor/products') ?>" class="btn btn-outline">Manage products</a>
        </div>
        <div class="glass-card">
            <h3>Affiliate Marketplace</h3>
            <p style="color:var(--muted);">Promote admin products and earn commission.</p>
            <a href="<?= url('/vendor/affiliates') ?>" class="btn btn-outline">Browse products</a>
        </div>
        <div class="glass-card">
            <h3>My Wallet</h3>
            <p style="color:var(--muted);">Track your earnings and withdrawals.</p>
            <a href="<?= url('/vendor/wallet') ?>" class="btn btn-outline">View wallet</a>
            <a href="<?= url('/vendor/deposits') ?>" class="btn btn-primary" style="margin-left:0.5rem;">Add funds</a>
        </div>
    </div>
</section>
