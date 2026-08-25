<section class="hero" style="padding: 2rem 0;">
    <h1>Categories</h1>
    <p>Organize products into categories.</p>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <h2 class="mb-2">Add category</h2>
    <form action="<?= url('/admin/categories') ?>" method="POST" enctype="multipart/form-data" style="display:flex; gap:0.75rem; flex-wrap:wrap; align-items:flex-end;">
        <?= csrf_field() ?>
        <div>
            <label for="name" style="display:block; color:var(--muted); font-size:0.85rem; margin-bottom:0.25rem;">Name</label>
            <input class="form-control" type="text" id="name" name="name" required style="min-width:200px;">
        </div>
        <div>
            <label for="description" style="display:block; color:var(--muted); font-size:0.85rem; margin-bottom:0.25rem;">Description</label>
            <input class="form-control" type="text" id="description" name="description" style="min-width:240px;">
        </div>
        <div>
            <label for="sort_order" style="display:block; color:var(--muted); font-size:0.85rem; margin-bottom:0.25rem;">Sort order</label>
            <input class="form-control" type="number" id="sort_order" name="sort_order" value="0" style="width:120px;">
        </div>
        <div>
            <label for="image" style="display:block; color:var(--muted); font-size:0.85rem; margin-bottom:0.25rem;">Image</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*" style="width:200px;">
        </div>
        <div>
            <label style="display:flex; align-items:center; gap:0.5rem; color:var(--muted); font-size:0.85rem; margin-bottom:0.25rem;">
                <input type="checkbox" name="is_visible" value="1" checked>
                Visible
            </label>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                <th style="padding:0.75rem 0;">Image</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Sort</th>
                <th>Visible</th>
                <th style="text-align:right;"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $c): ?>
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:0.5rem 0;">
                        <?php if ($c['image']): ?>
                            <img src="<?= e(asset($c['image'])) ?>" alt="" style="width:48px; height:48px; object-fit:cover; border-radius:0.5rem;">
                        <?php else: ?>
                            <span style="color:var(--muted); font-size:0.85rem;">—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= e($c['name']) ?></td>
                    <td><?= e($c['slug']) ?></td>
                    <td><?= e($c['sort_order']) ?></td>
                    <td><?= (int) $c['is_visible'] ? 'Yes' : 'No' ?></td>
                    <td style="text-align:right;">
                        <a href="<?= url('/admin/categories/' . $c['id'] . '/edit') ?>" class="btn btn-outline" style="padding:0.3rem 0.6rem;">Edit</a>
                        <form action="<?= url('/admin/categories/' . $c['id'] . '/delete') ?>" method="POST" style="display:inline;">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline" style="padding:0.3rem 0.6rem; color:#ef4444; border-color:#ef4444;">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
