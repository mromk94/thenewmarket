<?php $title = 'Edit Coupon'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Edit coupon</h1>
    <p><?= e($coupon['code']) ?></p>
</section>

<section class="glass-card mt-4" style="padding:1.5rem;">
    <form action="<?= url('/admin/coupons/' . $coupon['id']) ?>" method="POST">
        <?= csrf_field() ?>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:1rem;">
            <div class="form-group">
                <label for="code">Code</label>
                <input type="text" id="code" class="form-control" value="<?= e($coupon['code']) ?>" disabled>
            </div>
            <div class="form-group">
                <label for="type">Type</label>
                <select id="type" name="type" class="form-control">
                    <option value="percentage" <?= $coupon['type'] === 'percentage' ? 'selected' : '' ?>>Percentage (%)</option>
                    <option value="fixed" <?= $coupon['type'] === 'fixed' ? 'selected' : '' ?>>Fixed amount</option>
                </select>
            </div>
            <div class="form-group">
                <label for="value">Value</label>
                <input type="number" step="0.01" id="value" name="value" class="form-control" value="<?= e($coupon['value']) ?>" required>
            </div>
            <div class="form-group">
                <label for="min_order">Min. order</label>
                <input type="number" step="0.01" id="min_order" name="min_order" class="form-control" value="<?= e($coupon['min_order']) ?>">
            </div>
            <div class="form-group">
                <label for="max_uses">Max uses</label>
                <input type="number" id="max_uses" name="max_uses" class="form-control" value="<?= e($coupon['max_uses'] ?? '') ?>" placeholder="Unlimited">
            </div>
            <div class="form-group">
                <label for="valid_from">Valid from</label>
                <input type="date" id="valid_from" name="valid_from" class="form-control" value="<?= e($coupon['valid_from'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="valid_to">Valid to</label>
                <input type="date" id="valid_to" name="valid_to" class="form-control" value="<?= e($coupon['valid_to'] ?? '') ?>">
            </div>
        </div>
        <label style="display:flex; align-items:center; gap:0.5rem; margin:1rem 0;">
            <input type="checkbox" name="is_active" value="1" <?= (int) $coupon['is_active'] ? 'checked' : '' ?>>
            Active
        </label>
        <div style="display:flex; gap:0.5rem;">
            <button type="submit" class="btn btn-primary">Save coupon</button>
            <a href="<?= url('/admin/coupons') ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</section>
