<?php $title = 'Edit Category'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Edit Category</h1>
    <p>Update category details and image.</p>
</section>

<form action="<?= url('/admin/categories/' . $category['id']) ?>" method="POST" enctype="multipart/form-data" class="glass-card mt-4">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" class="form-control" value="<?= e($category['name'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <input type="text" id="description" name="description" class="form-control" value="<?= e($category['description'] ?? '') ?>">
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:1rem;">
        <div class="form-group" style="margin-bottom:0;">
            <label for="sort_order">Sort order</label>
            <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?= e($category['sort_order'] ?? 0) ?>">
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label style="display:flex; align-items:center; gap:0.5rem; margin-top:1.8rem;">
                <input type="checkbox" name="is_visible" value="1" <?= (int) ($category['is_visible'] ?? 1) ? 'checked' : '' ?>>
                Visible
            </label>
        </div>
    </div>

    <div class="form-group">
        <label for="image">Category image</label>
        <?php if ($category['image']): ?>
            <img src="<?= e(asset($category['image'])) ?>" alt="" style="width:120px; height:120px; object-fit:cover; border-radius:0.5rem; margin-bottom:0.5rem; display:block;">
        <?php endif; ?>
        <input type="file" id="image" name="image" class="form-control" accept="image/*">
        <small style="color:var(--muted); display:block; margin-top:0.25rem;">Upload a new image to replace the existing one.</small>
    </div>

    <div style="display:flex; gap:0.5rem;">
        <button type="submit" class="btn btn-primary">Update category</button>
        <a href="<?= url('/admin/categories') ?>" class="btn btn-outline">Cancel</a>
    </div>
</form>
