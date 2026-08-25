<?php $title = 'Edit Product'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Edit Product</h1>
    <p>Update product details and images.</p>
</section>

<form action="<?= url('/admin/products/' . $product['id']) ?>" method="POST" enctype="multipart/form-data" class="glass-card mt-4">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="name">Product name</label>
        <input type="text" id="name" name="name" class="form-control" value="<?= e($product['name'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="short_description">Short description</label>
        <input type="text" id="short_description" name="short_description" class="form-control" value="<?= e($product['short_description'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label for="description">Full description</label>
        <textarea id="description" name="description" class="form-control" rows="5"><?= e($product['description'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label for="sku">SKU</label>
        <input type="text" id="sku" name="sku" class="form-control" value="<?= e($product['sku'] ?? '') ?>">
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:1rem;">
        <div class="form-group" style="margin-bottom:0;">
            <label for="price">Price</label>
            <input type="number" step="0.01" min="0" id="price" name="price" class="form-control" value="<?= e($product['price'] ?? '') ?>" required>
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label for="compare_at_price">Compare-at price</label>
            <input type="number" step="0.01" min="0" id="compare_at_price" name="compare_at_price" class="form-control" value="<?= e($product['compare_at_price'] ?? '') ?>">
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label for="sale_price">Sale price</label>
            <input type="number" step="0.01" min="0" id="sale_price" name="sale_price" class="form-control" value="<?= e($product['sale_price'] ?? '') ?>">
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label for="stock_qty">Stock</label>
            <input type="number" min="0" id="stock_qty" name="stock_qty" class="form-control" value="<?= e($product['stock_qty'] ?? 0) ?>">
        </div>
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:1rem; margin-top:1rem;">
        <div class="form-group" style="margin-bottom:0;">
            <label for="inventory_status">Inventory status</label>
            <select id="inventory_status" name="inventory_status" class="form-control">
                <option value="in_stock" <?= ($product['inventory_status'] ?? '') === 'in_stock' ? 'selected' : '' ?>>In stock</option>
                <option value="low_stock" <?= ($product['inventory_status'] ?? '') === 'low_stock' ? 'selected' : '' ?>>Low stock</option>
                <option value="out_of_stock" <?= ($product['inventory_status'] ?? '') === 'out_of_stock' ? 'selected' : '' ?>>Out of stock</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id" class="form-control">
                <option value="">None</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= e($c['id']) ?>" <?= ($product['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label for="status">Status</label>
            <select id="status" name="status" class="form-control">
                <option value="published" <?= ($product['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                <option value="draft" <?= ($product['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="pending" <?= ($product['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="suspended" <?= ($product['status'] ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label for="visibility">Visibility</label>
            <select id="visibility" name="visibility" class="form-control">
                <option value="public" <?= ($product['visibility'] ?? '') === 'public' ? 'selected' : '' ?>>Public</option>
                <option value="hidden" <?= ($product['visibility'] ?? '') === 'hidden' ? 'selected' : '' ?>>Hidden</option>
            </select>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:1rem; margin-top:1rem;">
        <div class="form-group" style="margin-bottom:0;">
            <label for="affiliate_commission_type">Commission type</label>
            <select id="affiliate_commission_type" name="affiliate_commission_type" class="form-control">
                <option value="percentage" <?= ($product['affiliate_commission_type'] ?? '') === 'percentage' ? 'selected' : '' ?>>Percentage</option>
                <option value="fixed" <?= ($product['affiliate_commission_type'] ?? '') === 'fixed' ? 'selected' : '' ?>>Fixed amount</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label for="affiliate_commission_value">Commission value</label>
            <input type="number" step="0.01" id="affiliate_commission_value" name="affiliate_commission_value" class="form-control" value="<?= e($product['affiliate_commission_value'] ?? 0) ?>">
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label for="affiliate_require_min_balance">Min vendor balance</label>
            <input type="number" step="0.01" id="affiliate_require_min_balance" name="affiliate_require_min_balance" class="form-control" value="<?= e($product['affiliate_require_min_balance'] ?? 0) ?>">
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label for="affiliate_require_min_sales">Min vendor sales</label>
            <input type="number" min="0" id="affiliate_require_min_sales" name="affiliate_require_min_sales" class="form-control" value="<?= e($product['affiliate_require_min_sales'] ?? 0) ?>">
        </div>
    </div>

    <div style="display:flex; gap:1.5rem; flex-wrap:wrap; margin-top:1rem; align-items:center;">
        <label style="display:flex; align-items:center; gap:0.5rem;">
            <input type="checkbox" name="is_affiliate_eligible" value="1" <?= (int) ($product['is_affiliate_eligible'] ?? 0) ? 'checked' : '' ?>>
            Affiliate eligible
        </label>
        <label style="display:flex; align-items:center; gap:0.5rem;">
            <input type="checkbox" name="affiliate_require_kyc" value="1" <?= (int) ($product['affiliate_require_kyc'] ?? 0) ? 'checked' : '' ?>>
            Require KYC
        </label>
        <label style="display:flex; align-items:center; gap:0.5rem;">
            <input type="checkbox" name="featured" value="1" <?= (int) ($product['featured'] ?? 0) ? 'checked' : '' ?>>
            Featured
        </label>
    </div>

    <h3 class="mt-4">Images</h3>
    <?php if (!empty($images)): ?>
        <div class="card-grid" style="grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); margin-bottom:1rem;">
            <?php foreach ($images as $img): ?>
                <div class="glass-card" style="padding:0.75rem; text-align:center;">
                    <img src="<?= e(asset($img['file_path'])) ?>" alt="" style="width:100%; height:120px; object-fit:cover; border-radius:0.5rem;">
                    <div class="form-group" style="margin:0.5rem 0 0;">
                        <label style="font-size:0.8rem;">Sort</label>
                        <input type="number" name="sort_order_<?= e($img['id']) ?>" class="form-control" value="<?= e($img['sort_order']) ?>" style="padding:0.25rem; text-align:center;">
                    </div>
                    <div style="display:flex; gap:0.35rem; justify-content:center; flex-wrap:wrap; margin-top:0.5rem;">
                        <?php if (!$img['is_thumbnail']): ?>
                            <form action="<?= url('/admin/products/' . $product['id'] . '/images/' . $img['id'] . '/thumbnail') ?>" method="POST" style="display:inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline" style="padding:0.25rem 0.5rem; font-size:0.8rem;">Thumb</button>
                            </form>
                        <?php else: ?>
                            <span class="btn btn-primary" style="padding:0.25rem 0.5rem; font-size:0.8rem;">Thumbnail</span>
                        <?php endif; ?>
                        <form action="<?= url('/admin/products/' . $product['id'] . '/images/' . $img['id'] . '/delete') ?>" method="POST" style="display:inline;" onsubmit="return confirm('Delete this image?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline" style="padding:0.25rem 0.5rem; font-size:0.8rem; color:#dc2626; border-color:#dc2626;">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="color:var(--muted);">No images yet. Upload below.</p>
    <?php endif; ?>

    <div class="form-group">
        <label for="images">Add images</label>
        <input type="file" id="images" name="images[]" class="form-control" multiple accept="image/*">
    </div>

    <div style="display:flex; gap:0.5rem;">
        <button type="submit" class="btn btn-primary">Update product</button>
        <a href="<?= url('/admin/products') ?>" class="btn btn-outline">Cancel</a>
    </div>
</form>
