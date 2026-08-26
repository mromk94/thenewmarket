<section class="vendor-header">
    <div class="vendor-header-info">
        <div>
            <h1>Wallet</h1>
            <p>Track your earnings, deposits and withdrawals.</p>
        </div>
    </div>
    <a href="<?= url('/vendor/deposits') ?>" class="btn btn-primary">+ Add funds</a>
</section>

<section class="vendor-balance glass-card">
    <div class="vendor-balance-info">
        <p class="vendor-balance-label">Available balance</p>
        <h2 class="vendor-balance-amount"><?= e(config('app.currency_symbol')) ?><?= number_format($balance, 2) ?></h2>
    </div>
    <div class="vendor-balance-actions">
        <a href="<?= url('/vendor/withdrawals') ?>" class="btn btn-outline">Withdraw</a>
    </div>
</section>

<section class="glass-card" style="padding:1.5rem; margin-top:1.5rem;">
    <h3 class="vendor-recent-title" style="margin-bottom:1rem;">Transaction history</h3>
    <?php if (empty($transactions)): ?>
        <p style="color:var(--muted);">No transactions yet.</p>
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
</section>
