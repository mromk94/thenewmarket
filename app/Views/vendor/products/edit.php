<section class="hero" style="padding: 2rem 0;">
    <h1>Edit Product</h1>
    <p>Update your product details.</p>
</section>

<section class="glass-card max-w-lg" style="padding: 1.5rem;">
    <form action="<?= url('/vendor/products/' . $product['id']) ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="name" class="form-label">Product name</label>
            <input class="form-control" type="text" id="name" name="name" value="<?= e($product['name']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="short_description" class="form-label">Short description</label>
            <input class="form-control" type="text" id="short_description" name="short_description" value="<?= e($product['short_description'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Full description</label>
            <textarea class="form-control" id="description" name="description" rows="4"><?= e($product['description'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label for="category_id" class="form-label">Category</label>
            <select class="form-control" id="category_id" name="category_id">
                <option value="">Select category</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= (int) $c['id'] ?>" <?= ($product['category_id'] ?? null) == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="sku" class="form-label">SKU</label>
            <input class="form-control" type="text" id="sku" name="sku" value="<?= e($product['sku'] ?? '') ?>">
        </div>

        <div class="mb-3" style="display:flex; gap:1rem;">
            <div style="flex:1;">
                <label for="price" class="form-label">Price</label>
                <input class="form-control" type="number" id="price" name="price" step="0.01" min="0" value="<?= e($product['price']) ?>" required>
            </div>
            <div style="flex:1;">
                <label for="compare_at_price" class="form-label">Compare-at price</label>
                <input class="form-control" type="number" id="compare_at_price" name="compare_at_price" step="0.01" min="0" value="<?= e($product['compare_at_price'] ?? '') ?>">
            </div>
        </div>

        <div class="mb-3">
            <label for="stock_qty" class="form-label">Stock quantity</label>
            <input class="form-control" type="number" id="stock_qty" name="stock_qty" min="0" value="<?= (int) $product['stock_qty'] ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label" style="display:flex; align-items:center; gap:0.5rem;">
                <input type="checkbox" name="is_affiliate_eligible" value="1" <?= (int) ($product['is_affiliate_eligible'] ?? 0) ? 'checked' : '' ?>>
                Allow affiliates to promote this product
            </label>
        </div>

        <div class="mb-3" style="display:flex; gap:1rem; align-items:flex-end;">
            <div style="flex:1;">
                <label for="affiliate_commission_type" class="form-label">Commission type</label>
                <select class="form-control" id="affiliate_commission_type" name="affiliate_commission_type">
                    <option value="percentage" <?= ($product['affiliate_commission_type'] ?? '') === 'percentage' ? 'selected' : '' ?>>Percentage</option>
                    <option value="fixed" <?= ($product['affiliate_commission_type'] ?? '') === 'fixed' ? 'selected' : '' ?>>Fixed amount</option>
                </select>
            </div>
            <div style="flex:1;">
                <label for="affiliate_commission_value" class="form-label">Commission value</label>
                <input class="form-control" type="number" id="affiliate_commission_value" name="affiliate_commission_value" step="0.01" min="0" value="<?= e($product['affiliate_commission_value'] ?? 0) ?>">
            </div>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Product image</label>
            <input class="form-control" type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
            <p style="color:var(--muted); font-size:0.8rem; margin-top:0.25rem;">JPG, PNG, GIF, WebP. Max 2MB. Leave blank to keep current image.</p>
        </div>

        <button type="submit" class="btn btn-primary w-full">Update product</button>
    </form>
</section>
