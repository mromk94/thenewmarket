<?php $title = 'Withdraw Funds'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Withdraw Funds</h1>
    <p>Request a payout from your wallet balance.</p>
</section>

<section class="card-grid mt-4">
    <div class="glass-card" style="padding:1.5rem;">
        <h3 style="margin:0;"><?= e(config('app.currency_symbol')) ?><?= number_format($balance, 2) ?></h3>
        <p style="color:var(--muted); margin:0.25rem 0 0;">Available balance</p>
    </div>

    <form action="<?= url('/vendor/withdrawals') ?>" method="POST" class="glass-card" style="padding:1.5rem;">
        <h3 class="mb-2">Request a withdrawal</h3>
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="amount">Amount to withdraw</label>
            <input type="number" step="0.01" min="0.01" id="amount" name="amount" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="method">Payout method</label>
            <input type="text" id="method" name="method" class="form-control" placeholder="Bank account, BTC address, PayPal email, etc." required>
        </div>
        <button type="submit" class="btn btn-primary">Submit request</button>
        <p class="mt-2" style="color:var(--muted); font-size:0.85rem;">Withdrawals are processed manually after admin review.</p>
    </form>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <h2 class="mb-2">Your withdrawal requests</h2>
    <?php if (empty($withdrawals)): ?>
        <p style="color:var(--muted);">No withdrawal requests yet.</p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                    <th style="padding:0.75rem 0;">Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($withdrawals as $w): ?>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:0.5rem 0;"><?= e(config('app.currency_symbol')) ?><?= number_format((float) $w['amount'], 2) ?> <?= e($w['currency']) ?></td>
                        <td><?= e($w['method']) ?></td>
                        <td><span class="badge" style="text-transform:capitalize; background:<?= $w['status'] === 'approved' ? 'var(--success)' : ($w['status'] === 'rejected' ? '#dc2626' : '#f59e0b') ?>; color:#fff;"><?= e($w['status']) ?></span></td>
                        <td><?= e($w['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
