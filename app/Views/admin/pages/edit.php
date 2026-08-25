<?php $title = 'Edit ' . $page['title']; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Edit page</h1>
    <p><code><?= e($page['slug']) ?></code></p>
</section>

<section class="glass-card mt-4" style="padding:1.5rem;">
    <form action="<?= url('/admin/pages/' . $page['id']) ?>" method="POST">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" class="form-control" value="<?= e($page['title']) ?>" required>
        </div>

        <div class="form-group">
            <label for="meta_description">Meta description</label>
            <input type="text" id="meta_description" name="meta_description" class="form-control" value="<?= e($page['meta_description'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="content">HTML content</label>
            <textarea id="content" name="content" class="form-control" rows="12" required><?= e($page['content']) ?></textarea>
        </div>

        <label style="display:flex; align-items:center; gap:0.5rem; margin:1rem 0;">
            <input type="checkbox" name="is_active" value="1" <?= (int) $page['is_active'] ? 'checked' : '' ?>>
            Active
        </label>

        <div style="display:flex; gap:0.5rem;">
            <button type="submit" class="btn btn-primary">Save page</button>
            <a href="<?= url('/admin/pages') ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</section>
