<?php $title = 'Edit Vendor'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Edit Vendor</h1>
    <p><?= e($vendor['business_name']) ?> · <?= e($user['email']) ?></p>
</section>

<form action="<?= url('/admin/vendors/' . $vendor['id'] . '/save') ?>" method="POST" class="glass-card mt-4" style="padding:1.5rem;">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="business_name">Business name</label>
        <input type="text" id="business_name" name="business_name" class="form-control" value="<?= e($vendor['business_name']) ?>" required>
    </div>

    <div class="form-group">
        <label for="slug">Slug</label>
        <input type="text" id="slug" name="slug" class="form-control" value="<?= e($vendor['slug']) ?>" required>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" class="form-control" rows="4"><?= e($vendor['description'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label for="default_commission_rate">Default affiliate commission (%)</label>
        <input type="number" step="0.01" min="0" max="100" id="default_commission_rate" name="default_commission_rate" class="form-control" value="<?= number_format(((float) $vendor['default_commission_rate']) * 100, 2) ?>">
    </div>

    <label style="display:flex; align-items:center; gap:0.5rem; margin:1rem 0;">
        <input type="checkbox" name="kyc_verified" value="1" <?= (int) $vendor['kyc_verified'] ? 'checked' : '' ?>>
        KYC verified
    </label>

    <?php if (!empty($vendor['rejection_reason'])): ?>
        <div class="form-group" style="color:#991b1b;">
            <strong>Last rejection/suspension reason:</strong>
            <p style="margin:0.25rem 0 0;"><?= e($vendor['rejection_reason']) ?></p>
        </div>
    <?php endif; ?>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">Save vendor</button>
        <a href="<?= url('/admin/vendors') ?>" class="btn btn-outline">Cancel</a>
    </div>
</form>
