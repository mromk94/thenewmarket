<?php $title = 'Create Product'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Create Product</h1>
    <p>Add a new marketplace product.</p>
</section>

<form action="<?= url('/admin/products') ?>" method="POST" enctype="multipart/form-data" class="glass-card mt-4">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="name">Product name</label>
        <input type="text" id="name" name="name" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="short_description">Short description</label>
        <input type="text" id="short_description" name="short_description" class="form-control">
    </div>

    <div class="form-group">
        <label for="description">Full description</label>
        <textarea id="description" name="description" class="form-control" rows="5"></textarea>
    </div>

    <div class="form-group">
        <label for="sku">SKU</label>
        <input type="text" id="sku" name="sku" class="form-control">
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:1rem;">
        <div class="form-group" style="margin-bottom:0;">
            <label for="price">Price</label>
            <input type="number" step="0.01" min="0" id="price" name="price" class="form-control" required>
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label for="compare_at_price">Compare-at price</label>
            <input type="number" step="0.01" min="0" id="compare_at_price" name="compare_at_price" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label for="sale_price">Sale price</label>
            <input type="number" step="0.01" min="0" id="sale_price" name="sale_price" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label for="stock_qty">Stock</label>
            <input type="number" min="0" id="stock_qty" name="stock_qty" class="form-control" value="0">
        </div>
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:1rem; margin-top:1rem;">
        <div class="form-group" style="margin-bottom:0;">
            <label for="inventory_status">Inventory status</label>
            <select id="inventory_status" name="inventory_status" class="form-control">
                <option value="in_stock">In stock</option>
                <option value="low_stock">Low stock</option>
                <option value="out_of_stock">Out of stock</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id" class="form-control">
                <option value="">None</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= e($c['id']) ?>"><?= e($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label for="affiliate_commission_type">Commission type</label>
            <select id="affiliate_commission_type" name="affiliate_commission_type" class="form-control">
                <option value="percentage">Percentage</option>
                <option value="fixed">Fixed amount</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label for="affiliate_commission_value">Commission value</label>
            <input type="number" step="0.01" id="affiliate_commission_value" name="affiliate_commission_value" class="form-control" value="0">
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label for="affiliate_require_min_balance">Min vendor balance</label>
            <input type="number" step="0.01" id="affiliate_require_min_balance" name="affiliate_require_min_balance" class="form-control" value="0">
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label for="affiliate_require_min_sales">Min vendor sales</label>
            <input type="number" min="0" id="affiliate_require_min_sales" name="affiliate_require_min_sales" class="form-control" value="0">
        </div>
    </div>

    <div style="display:flex; gap:1.5rem; flex-wrap:wrap; margin-top:1rem; align-items:center;">
        <label style="display:flex; align-items:center; gap:0.5rem;">
            <input type="checkbox" name="is_affiliate_eligible" value="1" checked>
            Affiliate eligible
        </label>
        <label style="display:flex; align-items:center; gap:0.5rem;">
            <input type="checkbox" name="affiliate_require_kyc" value="1">
            Require KYC
        </label>
        <label style="display:flex; align-items:center; gap:0.5rem;">
            <input type="checkbox" name="featured" value="1">
            Featured
        </label>
    </div>

    <div class="form-group">
        <label for="images">Images</label>
        <input type="file" id="images" name="images[]" class="form-control" multiple accept="image/*">
        <small style="color:var(--muted); display:block; margin-top:0.25rem;">First image will be the thumbnail. JPG, PNG, GIF, WebP, max 2MB each.</small>
    </div>

    <div style="display:flex; gap:0.5rem;">
        <button type="submit" class="btn btn-primary">Create product</button>
        <a href="<?= url('/admin/products') ?>" class="btn btn-outline">Cancel</a>
    </div>
</form>
