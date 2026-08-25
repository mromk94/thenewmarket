<?php $title = 'Edit Email Template'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Edit email template</h1>
    <p><code><?= e($template['key']) ?></code></p>
</section>

<section class="glass-card mt-4" style="padding:1.5rem;">
    <form action="<?= url('/admin/email-templates/' . $template['id']) ?>" method="POST">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" class="form-control" value="<?= e($template['subject']) ?>" required>
        </div>

        <div class="form-group">
            <label for="body">HTML body</label>
            <textarea id="body" name="body" class="form-control" rows="12" required><?= e($template['body']) ?></textarea>
            <small style="color:var(--muted); display:block; margin-top:0.25rem;">Use {{variable_name}} placeholders exactly as shown in the default template.</small>
        </div>

        <label style="display:flex; align-items:center; gap:0.5rem; margin:1rem 0;">
            <input type="checkbox" name="is_active" value="1" <?= (int) $template['is_active'] ? 'checked' : '' ?>>
            Active
        </label>

        <div style="display:flex; gap:0.5rem;">
            <button type="submit" class="btn btn-primary">Save template</button>
            <a href="<?= url('/admin/email-templates') ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</section>
