<?php $title = 'Vendor Top-ups'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Vendor Top-ups</h1>
    <p>Review and approve vendor fund requests.</p>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <?php if (empty($deposits)): ?>
        <p style="color:var(--muted);">No pending top-up requests.</p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                    <th style="padding:0.75rem 0;">Vendor</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Reference</th>
                    <th>Date</th>
                    <th style="text-align:right;"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($deposits as $d): ?>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:0.5rem 0;"><?= e($d['email']) ?></td>
                        <td><?= e($d['method_name']) ?> (<?= e(ucfirst($d['method_type'])) ?>)</td>
                        <td><?= e(config('app.currency_symbol')) ?><?= number_format((float) $d['amount'], 2) ?> <?= e($d['currency']) ?></td>
                        <td><?= e($d['reference'] ?: '—') ?></td>
                        <td><?= e($d['created_at']) ?></td>
                        <td style="text-align:right;">
                            <form action="<?= url('/admin/vendor-deposits/' . $d['id'] . '/approve') ?>" method="POST" style="display:inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline" style="padding:0.3rem 0.6rem;">Approve</button>
                            </form>
                            <form action="<?= url('/admin/vendor-deposits/' . $d['id'] . '/reject') ?>" method="POST" style="display:inline;">
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
