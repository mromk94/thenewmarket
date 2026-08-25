<?php $title = 'Coupons'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Coupons</h1>
    <p>Create and manage discount codes.</p>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <h2 class="section-title" style="font-size:1.25rem; margin-bottom:1rem;">Add coupon</h2>
    <form action="<?= url('/admin/coupons') ?>" method="POST">
        <?= csrf_field() ?>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:1rem;">
            <div class="form-group">
                <label for="code">Code</label>
                <input type="text" id="code" name="code" class="form-control" placeholder="SAVE20" required>
            </div>
            <div class="form-group">
                <label for="type">Type</label>
                <select id="type" name="type" class="form-control">
                    <option value="percentage">Percentage (%)</option>
                    <option value="fixed">Fixed amount</option>
                </select>
            </div>
            <div class="form-group">
                <label for="value">Value</label>
                <input type="number" step="0.01" id="value" name="value" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="min_order">Min. order</label>
                <input type="number" step="0.01" id="min_order" name="min_order" class="form-control" value="0">
            </div>
            <div class="form-group">
                <label for="max_uses">Max uses</label>
                <input type="number" id="max_uses" name="max_uses" class="form-control" placeholder="Unlimited">
            </div>
            <div class="form-group">
                <label for="valid_from">Valid from</label>
                <input type="date" id="valid_from" name="valid_from" class="form-control">
            </div>
            <div class="form-group">
                <label for="valid_to">Valid to</label>
                <input type="date" id="valid_to" name="valid_to" class="form-control">
            </div>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:1rem;">Create coupon</button>
    </form>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem; overflow-x:auto;">
    <h2 class="section-title" style="font-size:1.25rem; margin-bottom:1rem;">Existing coupons</h2>
    <?php if (empty($coupons)): ?>
        <p style="color:var(--muted);">No coupons yet.</p>
    <?php else: ?>
        <table style="width:100%; min-width:700px; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                    <th style="padding:0.75rem 0;">Code</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Min order</th>
                    <th>Uses</th>
                    <th>Max</th>
                    <th>Valid</th>
                    <th>Active</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($coupons as $c): ?>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:0.5rem 0;"><?= e($c['code']) ?></td>
                        <td><?= e($c['type']) ?></td>
                        <td><?= e($c['type'] === 'percentage' ? $c['value'] . '%' : config('app.currency_symbol') . number_format((float) $c['value'], 2)) ?></td>
                        <td><?= e(config('app.currency_symbol')) ?><?= number_format((float) $c['min_order'], 2) ?></td>
                        <td><?= (int) $c['uses'] ?></td>
                        <td><?= $c['max_uses'] ?? '—' ?></td>
                        <td><?= e($c['valid_from'] ?? '—') ?> - <?= e($c['valid_to'] ?? '—') ?></td>
                        <td><?= (int) $c['is_active'] ? 'Yes' : 'No' ?></td>
                        <td style="text-align:right;">
                            <form action="<?= url('/admin/coupons/' . $c['id'] . '/delete') ?>" method="POST" style="display:inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline" style="color:#dc2626; border-color:#dc2626;">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
