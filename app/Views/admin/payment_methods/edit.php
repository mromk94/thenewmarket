<?php $title = 'Edit Payment Method'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Edit Payment Method</h1>
    <p>Update a customer manual payment option.</p>
</section>

<form action="<?= url('/admin/payment-methods/' . $method['id']) ?>" method="POST" enctype="multipart/form-data" class="glass-card mt-4" style="padding:1.5rem;">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="type">Type</label>
        <select id="type" name="type" class="form-control" onchange="toggleFields(this.value)">
            <option value="crypto" <?= $method['type'] === 'crypto' ? 'selected' : '' ?>>Cryptocurrency</option>
            <option value="bank" <?= $method['type'] === 'bank' ? 'selected' : '' ?>>Bank transfer</option>
        </select>
    </div>

    <div class="form-group">
        <label for="name">Method name</label>
        <input type="text" id="name" name="name" class="form-control" value="<?= e($method['name']) ?>" required>
    </div>

    <div class="form-group">
        <label for="currency">Currency / Coin</label>
        <input type="text" id="currency" name="currency" class="form-control" value="<?= e($method['currency']) ?>" required>
    </div>

    <div id="crypto-fields" style="<?= $method['type'] === 'bank' ? 'display:none;' : '' ?>">
        <div class="form-group">
            <label for="network">Network</label>
            <input type="text" id="network" name="network" class="form-control" value="<?= e($method['network'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="wallet_address">Wallet address</label>
            <input type="text" id="wallet_address" name="wallet_address" class="form-control" value="<?= e($method['wallet_address'] ?? '') ?>">
        </div>
        <?php if ($method['qr_image']): ?>
            <img src="<?= e(asset($method['qr_image'])) ?>" alt="QR" style="width:120px; height:120px; object-fit:cover; border-radius:0.5rem; margin-bottom:0.5rem;">
        <?php endif; ?>
        <div class="form-group">
            <label for="qr_image">Replace QR code image</label>
            <input type="file" id="qr_image" name="qr_image" class="form-control" accept="image/*">
        </div>
    </div>

    <div id="bank-fields" style="<?= $method['type'] === 'crypto' ? 'display:none;' : '' ?>">
        <div class="form-group">
            <label for="bank_name">Bank name</label>
            <input type="text" id="bank_name" name="bank_name" class="form-control" value="<?= e($method['bank_name'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="account_name">Account name</label>
            <input type="text" id="account_name" name="account_name" class="form-control" value="<?= e($method['account_name'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="account_number">Account / IBAN number</label>
            <input type="text" id="account_number" name="account_number" class="form-control" value="<?= e($method['account_number'] ?? '') ?>">
        </div>
    </div>

    <div class="form-group">
        <label for="instructions">Instructions for customers</label>
        <textarea id="instructions" name="instructions" class="form-control" rows="3"><?= e($method['instructions'] ?? '') ?></textarea>
    </div>

    <div style="display:flex; gap:1rem; flex-wrap:wrap; align-items:center;">
        <div class="form-group" style="margin:0;">
            <label for="sort_order">Sort order</label>
            <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?= e($method['sort_order']) ?>" style="width:120px;">
        </div>
        <label style="display:flex; align-items:center; gap:0.5rem;">
            <input type="checkbox" name="is_active" value="1" <?= (int) $method['is_active'] ? 'checked' : '' ?>>
            Active
        </label>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">Update method</button>
        <a href="<?= url('/admin/payment-methods') ?>" class="btn btn-outline">Cancel</a>
    </div>
</form>

<script>
function toggleFields(type) {
    document.getElementById('crypto-fields').style.display = type === 'crypto' ? 'block' : 'none';
    document.getElementById('bank-fields').style.display = type === 'bank' ? 'block' : 'none';
}
</script>
