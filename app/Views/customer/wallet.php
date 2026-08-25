<section class="hero" style="padding: 2rem 0;">
    <h1>Money history</h1>
    <p style="font-size:1.5rem; font-weight:700;">Balance: <?= e(config('app.currency_symbol')) ?><?= number_format($balance, 2) ?></p>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <?php if (empty($transactions)): ?>
        <p style="color:var(--muted);">No transactions yet.</p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                    <th>Date</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th style="text-align:right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $tx): ?>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:0.5rem 0;"><?= e($tx['created_at']) ?></td>
                        <td><?= e($tx['type']) ?></td>
                        <td><?= e($tx['description']) ?></td>
                        <td style="text-align:right; color:<?= $tx['direction'] === 'in' ? '#4ade80' : '#f87171' ?>;">
                            <?= $tx['direction'] === 'in' ? '+' : '-' ?><?= e(config('app.currency_symbol')) ?><?= number_format((float) $tx['amount'], 2) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
