<?php $title = 'Withdraw Funds'; ?>

<section class="vendor-header">
    <div class="vendor-header-info">
        <div>
            <h1>Withdraw Funds</h1>
            <p>Request a payout from your wallet balance.</p>
        </div>
    </div>
    <a href="<?= url('/vendor/wallet') ?>" class="btn btn-outline">Wallet</a>
</section>

<section class="vendor-balance glass-card">
    <div class="vendor-balance-info">
        <p class="vendor-balance-label">Available balance</p>
        <h2 class="vendor-balance-amount"><?= e(config('app.currency_symbol')) ?><?= number_format($balance, 2) ?></h2>
    </div>
</section>

<section class="card-grid mt-4">
    <form action="<?= url('/vendor/withdrawals') ?>" method="POST" class="glass-card" style="padding:1.5rem;">
        <h3 class="vendor-recent-title" style="margin-bottom:1rem;">Request a withdrawal</h3>
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="amount">Amount to withdraw</label>
            <input type="number" step="0.01" min="0.01" id="amount" name="amount" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="method">Payout method</label>
            <input type="text" id="method" name="method" class="form-control" placeholder="Bank account, BTC address, PayPal email, etc." required>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;">Submit request</button>
        <p class="mt-2" style="color:var(--muted); font-size:0.85rem;">Withdrawals are processed manually after admin review.</p>
    </form>

    <section class="glass-card" style="padding:1.5rem;">
        <h3 class="vendor-recent-title" style="margin-bottom:1rem;">Quick tips</h3>
        <ul style="color:var(--muted); padding-left:1.25rem; line-height:1.7;">
            <li>Double-check your payout address or account details.</li>
            <li>Requests usually take 1-2 business days to review.</li>
            <li>Minimum withdrawal may apply depending on your payout method.</li>
        </ul>
    </section>
</section>

<section class="glass-card mt-4" style="padding:1.5rem;">
    <h3 class="vendor-recent-title" style="margin-bottom:1rem;">Your withdrawal requests</h3>
    <?php if (empty($withdrawals)): ?>
        <p style="color:var(--muted);">No withdrawal requests yet.</p>
    <?php else: ?>
        <div class="vendor-tx-list">
            <?php foreach ($withdrawals as $w): ?>
                <div class="vendor-tx-item">
                    <div class="vendor-tx-main">
                        <p class="vendor-tx-type"><?= e($w['method']) ?></p>
                        <p class="vendor-tx-desc"><?= e($w['created_at']) ?></p>
                    </div>
                    <div class="vendor-tx-side">
                        <p class="vendor-tx-amount" style="font-weight:700;">-<?= e(config('app.currency_symbol')) ?><?= number_format((float) $w['amount'], 2) ?> <?= e($w['currency']) ?></p>
                        <span class="badge" style="text-transform:capitalize; background:<?= $w['status'] === 'approved' ? 'var(--success)' : ($w['status'] === 'rejected' ? '#dc2626' : '#f59e0b') ?>; color:#fff;"><?= e($w['status']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
