<?php $title = 'Delivery'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Delivery</h1>
    <p>Track and update order delivery stages.</p>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <form action="<?= url('/admin/delivery') ?>" method="GET" style="display:flex; gap:0.5rem; margin-bottom:1rem; flex-wrap:wrap;">
        <select name="status" class="form-control" style="min-width:160px;">
            <option value="" <?= empty($status) ? 'selected' : '' ?>>All statuses</option>
            <option value="pending" <?= ($status ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="shipped" <?= ($status ?? '') === 'shipped' ? 'selected' : '' ?>>Shipped</option>
            <option value="delivered" <?= ($status ?? '') === 'delivered' ? 'selected' : '' ?>>Delivered</option>
            <option value="cancelled" <?= ($status ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>
        <button type="submit" class="btn btn-outline">Filter</button>
    </form>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Tracking</th>
                    <th>Updated</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:0.5rem 0;"><?= e($o['order_number']) ?></td>
                        <td><?= e($o['email']) ?></td>
                        <td><?= e(config('app.currency_symbol')) ?><?= number_format((float) $o['total'], 2) ?></td>
                        <td style="text-transform:capitalize;"><?= e($o['delivery_status'] ?? 'pending') ?></td>
                        <td><?= e($o['tracking_number'] ?? '-') ?></td>
                        <td><?= e($o['updated_at'] ?? '') ?></td>
                        <td>
                            <a href="#order-<?= (int) $o['id'] ?>" class="btn btn-outline" style="padding:0.35rem 0.7rem;" onclick="document.getElementById('order-<?= (int) $o['id'] ?>').style.display='block'; return false;">Update</a>
                        </td>
                    </tr>
                    <tr id="order-<?= (int) $o['id'] ?>" style="display:none; background:rgba(0,0,0,0.02);">
                        <td colspan="7" style="padding:1rem;">
                            <form action="<?= url('/admin/delivery/' . $o['id'] . '/update') ?>" method="POST" style="display:grid; gap:0.75rem;">
                                <?= csrf_field() ?>
                                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:0.75rem;">
                                    <div class="form-group" style="margin-bottom:0;">
                                        <label>Delivery status</label>
                                        <select name="delivery_status" class="form-control">
                                            <option value="pending" <?= ($o['delivery_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="shipped" <?= ($o['delivery_status'] ?? '') === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                            <option value="delivered" <?= ($o['delivery_status'] ?? '') === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                            <option value="cancelled" <?= ($o['delivery_status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                    </div>
                                    <div class="form-group" style="margin-bottom:0;">
                                        <label>Delivery stage</label>
                                        <input type="text" name="delivery_stage" class="form-control" value="<?= e($o['delivery_stage'] ?? '') ?>" placeholder="e.g. Packed, In transit, At local depot">
                                    </div>
                                    <div class="form-group" style="margin-bottom:0;">
                                        <label>Tracking number</label>
                                        <input type="text" name="tracking_number" class="form-control" value="<?= e($o['tracking_number'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label>Delivery notes</label>
                                    <textarea name="delivery_notes" class="form-control" rows="2"><?= e($o['delivery_notes'] ?? '') ?></textarea>
                                </div>
                                <div style="display:flex; gap:0.5rem;">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <button type="button" class="btn btn-outline" onclick="document.getElementById('order-<?= (int) $o['id'] ?>').style.display='none';">Cancel</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
