<section class="vendor-header">
    <div class="vendor-header-info">
        <div>
            <h1>My Products</h1>
            <p>Manage your product listings.</p>
        </div>
    </div>
    <a href="<?= url('/vendor/products/create') ?>" class="btn btn-primary">+ Add product</a>
</section>

<?php if (empty($products)): ?>
    <section class="glass-card" style="padding:2rem; text-align:center; margin-top:1.5rem;">
        <p style="color:var(--muted); margin-bottom:1rem;">You have not added any products yet.</p>
        <a href="<?= url('/vendor/products/create') ?>" class="btn btn-primary">Add your first product</a>
    </section>
<?php else: ?>
    <section class="vendor-products-grid">
        <?php foreach ($products as $p): ?>
            <div class="vendor-product-card glass-card">
                <div class="vendor-product-thumb" style="height:160px;">
                    <?php if (!empty($p['thumbnail'])): ?>
                        <img src="<?= e(asset($p['thumbnail'])) ?>" alt="" style="width:100%; height:100%; object-fit:cover; border-radius:0.5rem;">
                    <?php else: ?>
                        <span class="vendor-thumb-placeholder"></span>
                    <?php endif; ?>
                </div>
                <div class="vendor-product-card-body">
                    <p class="vendor-product-name"><?= e($p['name']) ?></p>
                    <p class="vendor-product-sub"><?= e($p['category_name'] ?? '') ?></p>
                    <div class="vendor-product-row">
                        <span class="vendor-product-price"><?= e(config('app.currency_symbol')) ?><?= number_format((float) $p['price'], 2) ?></span>
                        <span class="vendor-product-stock">Stock: <?= (int) $p['stock_qty'] ?></span>
                    </div>
                    <span class="vendor-product-status" data-status="<?= e($p['status']) ?>"><?= e(ucfirst($p['status'])) ?></span>
                </div>
                <div class="vendor-product-card-foot">
                    <a href="<?= url('/vendor/products/' . $p['id'] . '/edit') ?>" class="btn btn-outline" style="flex:1;">Edit</a>
                    <form action="<?= url('/vendor/products/' . $p['id'] . '/delete') ?>" method="POST" style="flex:1;" onsubmit="return confirm('Delete this product?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline" style="width:100%; color:#ef4444; border-color:#ef4444;">Delete</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
