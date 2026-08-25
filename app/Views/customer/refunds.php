<?php $title = 'Refunds'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Refund Requests</h1>
    <p>Request a refund for a paid order.</p>
</section>

<section class="card-grid mt-4">
    <form action="<?= url('/account/refunds') ?>" method="POST" class="glass-card" style="padding:1.5rem;">
        <h3 class="mb-2">Request a refund</h3>
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="order_id">Order</label>
            <select id="order_id" name="order_id" class="form-control" required>
                <option value="">-- Select order --</option>
                <?php foreach ($orders as $o): ?>
                    <option value="<?= (int) $o['id'] ?>"><?= e($o['order_number']) ?> · <?= e(config('app.currency_symbol')) ?><?= number_format((float) $o['total'], 2) ?> · <?= e($o['payment_status']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="amount">Refund amount</label>
            <input type="number" step="0.01" min="0.01" id="amount" name="amount" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="reason">Reason</label>
            <textarea id="reason" name="reason" class="form-control" rows="3" required></textarea>
        </div>

        <p style="color:var(--muted); font-size:0.85rem;">Refunds are processed manually after review.</p>
        <button type="submit" class="btn btn-primary">Submit request</button>
    </form>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <h2 class="mb-2">Your refund requests</h2>
    <?php if (empty($refunds)): ?>
        <p style="color:var(--muted);">No refund requests yet.</p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                    <th style="padding:0.75rem 0;">Order</th>
                    <th>Amount</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($refunds as $r): ?>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:0.5rem 0;"><?= e($r['order_number']) ?></td>
                        <td><?= e(config('app.currency_symbol')) ?><?= number_format((float) $r['amount'], 2) ?> <?= e($r['currency']) ?></td>
                        <td><?= e($r['reason']) ?></td>
                        <td><span class="badge" style="text-transform:capitalize; background:<?= $r['status'] === 'approved' ? 'var(--success)' : ($r['status'] === 'rejected' ? '#dc2626' : '#f59e0b') ?>; color:#fff;"><?= e($r['status']) ?></span></td>
                        <td><?= e($r['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
