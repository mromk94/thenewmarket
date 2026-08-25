<section class="hero" style="padding: 2rem 0;">
    <h1>My Products</h1>
    <p>Manage your product listings.</p>
</section>

<div style="text-align:right; margin-bottom:1rem;">
    <a href="<?= url('/vendor/products/create') ?>" class="btn btn-primary">Add product</a>
</div>

<section class="glass-card" style="padding: 1.5rem;">
    <?php if (empty($products)): ?>
        <p class="text-center" style="color:var(--muted);">You have not added any products yet.</p>
        <p class="text-center"><a href="<?= url('/vendor/products/create') ?>" class="btn btn-primary">Add your first product</a></p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                    <th style="padding:0.75rem 0;">Product</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:0.5rem 0;">
                            <strong><?= e($p['name']) ?></strong>
                            <p style="color:var(--muted); font-size:0.85rem; margin:0;"><?= e($p['category_name'] ?? '') ?></p>
                        </td>
                        <td><?= e(config('app.currency_symbol')) ?><?= number_format((float) $p['price'], 2) ?></td>
                        <td><?= (int) $p['stock_qty'] ?></td>
                        <td><?= e($p['status']) ?></td>
                        <td style="text-align:right;">
                            <a href="<?= url('/vendor/products/' . $p['id'] . '/edit') ?>" class="btn btn-outline" style="padding:0.3rem 0.6rem;">Edit</a>
                            <form action="<?= url('/vendor/products/' . $p['id'] . '/delete') ?>" method="POST" style="display:inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline" style="padding:0.3rem 0.6rem; color:#ef4444; border-color:#ef4444;" onclick="return confirm('Delete this product?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
