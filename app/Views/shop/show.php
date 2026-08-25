<section class="glass-card mt-4" style="display:grid; grid-template-columns: 1fr 1fr; gap: 2rem; padding: 2rem;">
    <div>
        <div style="width:100%; height:360px; background:rgba(255,255,255,0.05); border-radius:0.75rem; display:flex; align-items:center; justify-content:center; color:var(--muted);">
            <?= $thumbnail ? '<img src="' . e(asset($thumbnail)) . '" alt="" style="max-height:100%; max-width:100%; border-radius:0.75rem;">' : 'No image' ?>
        </div>
    </div>

    <div style="display:flex; flex-direction:column; justify-content:center;">
        <p style="color:var(--muted);"><?= e($product['category_name'] ?? '') ?></p>
        <h1 style="margin:0 0 1rem;"><?= e($product['name']) ?></h1>
        <p style="color:var(--muted);"><?= e($product['description']) ?></p>

        <p style="font-size:1.75rem; font-weight:700; margin:1rem 0;">
            <?= e(config('app.currency_symbol')) ?><?= number_format((float) $product['price'], 2) ?>
        </p>

        <p style="color:var(--muted); font-size:0.9rem;">
            Sold by <?= e($product['vendor_name'] ?? 'The New Age Marketplace') ?>
            <?php if (!empty($affiliateVendorName)): ?>
                · Referred by <a href="<?= url('/vendor/' . $vendorSlug) ?>"><?= e($affiliateVendorName) ?></a>
            <?php endif; ?>
        </p>

        <?php if (count($images) > 1): ?>
            <div style="display:flex; gap:0.5rem; margin:1rem 0;">
                <?php foreach ($images as $img): ?>
                    <div style="width:60px; height:60px; background:rgba(255,255,255,0.05); border-radius:0.4rem; overflow:hidden; flex-shrink:0;">
                        <?= !empty($img['file_path']) ? '<img src="' . e(asset($img['file_path'])) . '" alt="" style="width:100%; height:100%; object-fit:cover; border-radius:0.4rem;">' : '<span style="color:var(--muted); font-size:0.6rem;">No image</span>' ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?= url('/cart/add') ?>" method="POST" data-ajax-cart="add" style="margin-top:1.5rem;">
            <?= csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
            <input type="hidden" name="affiliate_vendor_id" value="<?= (int) ($affiliateVendorId ?? 0) ?>">
            <input type="hidden" name="return" value="<?= url('/product/' . $product['slug'] . (empty($vendorSlug) ? '' : '?vendor=' . $vendorSlug)) ?>">
            <label for="qty" style="color:var(--muted);">Quantity</label>
            <div style="display:flex; gap:0.75rem; align-items:center;">
                <input class="form-control" type="number" id="qty" name="quantity" value="1" min="1" max="<?= (int) $product['stock_qty'] ?>" style="max-width:80px;">
                <button type="submit" class="btn btn-primary">Add to cart</button>
            </div>
        </form>
    </div>
</section>
