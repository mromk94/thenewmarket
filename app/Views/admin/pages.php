<?php $title = 'Content Pages'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Content Pages</h1>
    <p>Edit about, contact, terms, privacy and other pages.</p>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                <th style="padding:0.75rem 0;">Title</th>
                <th>Slug</th>
                <th>Active</th>
                <th style="text-align:right;"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pages as $p): ?>
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:0.5rem 0;"><?= e($p['title']) ?></td>
                    <td><code><?= e($p['slug']) ?></code></td>
                    <td><?= (int) $p['is_active'] ? 'Yes' : 'No' ?></td>
                    <td style="text-align:right;">
                        <a href="<?= url('/admin/pages/' . $p['id'] . '/edit') ?>" class="btn btn-outline" style="padding:0.3rem 0.6rem;">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
