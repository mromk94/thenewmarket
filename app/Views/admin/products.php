<section class="hero" style="padding: 2rem 0;">
    <h1>Products</h1>
    <p>Manage all marketplace products.</p>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom:1rem; gap:0.75rem;">
        <form method="GET" action="<?= url('/admin/products') ?>" style="display:flex; gap:0.75rem; align-items:center;">
            <select name="status" class="form-control" style="max-width:180px;">
                <option value="">All statuses</option>
                <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Approved</option>
                <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Published</option>
                <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                <option value="suspended" <?= $status === 'suspended' ? 'selected' : '' ?>>Suspended</option>
            </select>
            <button type="submit" class="btn btn-outline">Filter</button>
        </form>
        <a href="<?= url('/admin/products/create') ?>" class="btn btn-primary">Create product</a>
    </div>

    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                <th style="padding:0.75rem 0;">Product</th>
                <th>Vendor</th>
                <th>Price</th>
                <th>Status</th>
                <th>Featured</th>
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
                    <td><?= e($p['vendor_name'] ?? 'Marketplace') ?></td>
                    <td><?= e(config('app.currency_symbol')) ?><?= number_format((float) $p['price'], 2) ?></td>
                    <td><?= e($p['status']) ?></td>
                    <td><?= (int) $p['featured'] ? 'Yes' : 'No' ?></td>
                    <td style="text-align:right;">
                        <div style="display:flex; gap:0.4rem; justify-content:flex-end; flex-wrap:wrap;">
                            <a href="<?= url('/admin/products/' . $p['id'] . '/edit') ?>" class="btn btn-outline" style="padding:0.3rem 0.6rem;">Edit</a>
                            <?php if ($p['status'] !== 'published'): ?>
                                <form action="<?= url('/admin/products/' . $p['id'] . '/update') ?>" method="POST" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="publish">
                                    <button type="submit" class="btn btn-outline" style="padding:0.3rem 0.6rem;">Publish</button>
                                </form>
                            <?php else: ?>
                                <form action="<?= url('/admin/products/' . $p['id'] . '/update') ?>" method="POST" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="suspend">
                                    <button type="submit" class="btn btn-outline" style="padding:0.3rem 0.6rem;">Suspend</button>
                                </form>
                            <?php endif; ?>
                            <form action="<?= url('/admin/products/' . $p['id'] . '/update') ?>" method="POST" style="display:inline;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="<?= (int) $p['featured'] ? 'unfeature' : 'feature' ?>">
                                <button type="submit" class="btn btn-outline" style="padding:0.3rem 0.6rem;"><?= (int) $p['featured'] ? 'Unfeature' : 'Feature' ?></button>
                            </form>
                            <form action="<?= url('/admin/products/' . $p['id'] . '/delete') ?>" method="POST" style="display:inline;" onsubmit="return confirm('Delete this product?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline" style="padding:0.3rem 0.6rem; color:#dc2626; border-color:#dc2626;">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
