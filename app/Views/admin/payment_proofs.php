<?php $title = 'Payment Proofs'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Payment Proofs</h1>
    <p>Review customer manual payment uploads.</p>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <?php if (empty($proofs)): ?>
        <p style="color:var(--muted);">No pending payment proofs.</p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                    <th style="padding:0.75rem 0;">Customer</th>
                    <th>Order</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Reference</th>
                    <th>Receipt</th>
                    <th style="text-align:right;"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($proofs as $p): ?>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:0.5rem 0;"><?= e($p['email']) ?></td>
                        <td><?= e($p['order_number']) ?></td>
                        <td><?= e($p['method_name']) ?></td>
                        <td><?= e(config('app.currency_symbol')) ?><?= number_format((float) $p['total'], 2) ?></td>
                        <td><?= e($p['reference'] ?: '—') ?></td>
                        <td>
                            <?php if ($p['receipt_image']): ?>
                                <a href="<?= e(asset($p['receipt_image'])) ?>" target="_blank" class="btn btn-outline" style="padding:0.3rem 0.6rem;">View</a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td style="text-align:right;">
                            <form action="<?= url('/admin/payment-proofs/' . $p['id'] . '/approve') ?>" method="POST" style="display:inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline" style="padding:0.3rem 0.6rem;">Approve</button>
                            </form>
                            <form action="<?= url('/admin/payment-proofs/' . $p['id'] . '/reject') ?>" method="POST" style="display:inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline" style="padding:0.3rem 0.6rem; color:#dc2626; border-color:#dc2626;">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
