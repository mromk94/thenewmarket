<?php $title = 'Refund Requests'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Refund Requests</h1>
    <p>Review and approve customer refund requests.</p>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <?php if (empty($refunds)): ?>
        <p style="color:var(--muted);">No pending refund requests.</p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                    <th style="padding:0.75rem 0;">Customer</th>
                    <th>Order</th>
                    <th>Amount</th>
                    <th>Reason</th>
                    <th>Date</th>
                    <th style="text-align:right;"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($refunds as $r): ?>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:0.5rem 0;"><?= e($r['email']) ?></td>
                        <td><?= e($r['order_number']) ?></td>
                        <td><?= e(config('app.currency_symbol')) ?><?= number_format((float) $r['amount'], 2) ?> <?= e($r['currency']) ?></td>
                        <td><?= e($r['reason']) ?></td>
                        <td><?= e($r['created_at']) ?></td>
                        <td style="text-align:right;">
                            <form action="<?= url('/admin/refunds/' . $r['id'] . '/approve') ?>" method="POST" style="display:inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline" style="padding:0.3rem 0.6rem;">Approve</button>
                            </form>
                            <form action="<?= url('/admin/refunds/' . $r['id'] . '/reject') ?>" method="POST" style="display:inline;">
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
