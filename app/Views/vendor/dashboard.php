<section class="vendor-header">
    <div class="vendor-header-info">
        <div>
            <h1><?= e($vendor['business_name']) ?></h1>
            <p><?= e($vendor['description'] ?: 'Your vendor store dashboard.') ?></p>
        </div>
        <span class="vendor-status" data-status="<?= e($vendor['status']) ?>"><?= e(ucfirst($vendor['status'])) ?></span>
    </div>
    <a href="<?= url('/vendor/' . $vendor['slug']) ?>" class="btn btn-outline">View storefront</a>
</section>

<section class="vendor-balance glass-card">
    <div class="vendor-balance-info">
        <p class="vendor-balance-label">Wallet balance</p>
        <h2 class="vendor-balance-amount"><?= e(config('app.currency_symbol')) ?><?= number_format($stats['balance'], 2) ?></h2>
    </div>
    <div class="vendor-balance-actions">
        <a href="<?= url('/vendor/deposits') ?>" class="btn btn-primary">+ Add funds</a>
        <a href="<?= url('/vendor/withdrawals') ?>" class="btn btn-outline">Withdraw</a>
    </div>
</section>

<section class="vendor-stats">
    <div class="vendor-stat glass-card">
        <p class="vendor-stat-value"><?= number_format($stats['products']) ?></p>
        <p class="vendor-stat-label">Products</p>
    </div>
    <div class="vendor-stat glass-card">
        <p class="vendor-stat-value"><?= number_format($stats['published']) ?></p>
        <p class="vendor-stat-label">Published</p>
    </div>
    <div class="vendor-stat glass-card">
        <p class="vendor-stat-value"><?= number_format($stats['pending']) ?></p>
        <p class="vendor-stat-label">Pending review</p>
    </div>
    <div class="vendor-stat glass-card">
        <p class="vendor-stat-value"><?= number_format($stats['sales']) ?></p>
        <p class="vendor-stat-label">Sales</p>
    </div>
    <div class="vendor-stat glass-card">
        <p class="vendor-stat-value"><?= number_format($stats['orders']) ?></p>
        <p class="vendor-stat-label">Orders</p>
    </div>
    <div class="vendor-stat glass-card">
        <p class="vendor-stat-value"><?= e(config('app.currency_symbol')) ?><?= number_format($stats['revenue'], 2) ?></p>
        <p class="vendor-stat-label">Revenue</p>
    </div>
</section>

<section class="vendor-actions">
    <a href="<?= url('/vendor/products/create') ?>" class="vendor-action glass-card">
        <span class="vendor-action-icon">+</span>
        <span class="vendor-action-title">Add product</span>
        <span class="vendor-action-hint">Create a new listing</span>
    </a>
    <a href="<?= url('/vendor/products') ?>" class="vendor-action glass-card">
        <span class="vendor-action-icon">&#9638;</span>
        <span class="vendor-action-title">My products</span>
        <span class="vendor-action-hint">Manage listings</span>
    </a>
    <a href="<?= url('/vendor/affiliates') ?>" class="vendor-action glass-card">
        <span class="vendor-action-icon">&#9829;</span>
        <span class="vendor-action-title">Affiliates</span>
        <span class="vendor-action-hint">Promote & earn</span>
    </a>
    <a href="<?= url('/vendor/wallet') ?>" class="vendor-action glass-card">
        <span class="vendor-action-icon">$</span>
        <span class="vendor-action-title">Wallet</span>
        <span class="vendor-action-hint">History & balance</span>
    </a>
    <a href="<?= url('/vendor/sales') ?>" class="vendor-action glass-card">
        <span class="vendor-action-icon">&#128196;</span>
        <span class="vendor-action-title">Sales</span>
        <span class="vendor-action-hint">Orders & payouts</span>
    </a>
    <a href="<?= url('/vendor/support') ?>" class="vendor-action glass-card">
        <span class="vendor-action-icon">?</span>
        <span class="vendor-action-title">Support</span>
        <span class="vendor-action-hint">Request help</span>
    </a>
    <a href="<?= url('/vendor/' . $vendor['slug']) ?>" class="vendor-action glass-card">
        <span class="vendor-action-icon">&#127760;</span>
        <span class="vendor-action-title">Storefront</span>
        <span class="vendor-action-hint">View your store</span>
    </a>
</section>

<section class="vendor-recent">
    <div class="vendor-recent-section glass-card">
        <h3 class="vendor-recent-title">Recent products</h3>
        <?php if (empty($products)): ?>
            <p style="color:var(--muted);">No products yet. <a href="<?= url('/vendor/products/create') ?>">Add your first product</a>.</p>
        <?php else: ?>
            <div class="vendor-product-list">
                <?php foreach ($products as $p): ?>
                    <div class="vendor-product-item">
                        <div class="vendor-product-thumb">
                            <?php if (!empty($p['thumbnail'])): ?>
                                <img src="<?= e(asset($p['thumbnail'])) ?>" alt="">
                            <?php else: ?>
                                <span class="vendor-thumb-placeholder"></span>
                            <?php endif; ?>
                        </div>
                        <div class="vendor-product-meta">
                            <p class="vendor-product-name"><?= e($p['name']) ?></p>
                            <p class="vendor-product-sub"><?= e($p['category_name'] ?? '') ?> · <?= e(config('app.currency_symbol')) ?><?= number_format((float) $p['price'], 2) ?></p>
                        </div>
                        <div class="vendor-product-side">
                            <span class="vendor-product-status" data-status="<?= e($p['status']) ?>"><?= e(ucfirst($p['status'])) ?></span>
                            <a href="<?= url('/vendor/products/' . $p['id'] . '/edit') ?>" class="btn btn-outline" style="padding:0.3rem 0.6rem; font-size:0.8rem;">Edit</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="vendor-recent-section glass-card">
        <h3 class="vendor-recent-title">Recent activity</h3>
        <?php if (empty($transactions)): ?>
            <p style="color:var(--muted);">No wallet activity yet.</p>
        <?php else: ?>
            <div class="vendor-tx-list">
                <?php foreach ($transactions as $tx): ?>
                    <div class="vendor-tx-item">
                        <div class="vendor-tx-main">
                            <p class="vendor-tx-type"><?= e($tx['type']) ?></p>
                            <p class="vendor-tx-desc"><?= e($tx['description']) ?></p>
                            <p class="vendor-tx-date"><?= e($tx['created_at']) ?></p>
                        </div>
                        <p class="vendor-tx-amount <?= $tx['direction'] === 'in' ? 'vendor-tx-in' : 'vendor-tx-out' ?>">
                            <?= $tx['direction'] === 'in' ? '+' : '-' ?><?= e(config('app.currency_symbol')) ?><?= number_format((float) $tx['amount'], 2) ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
