<?php $title = 'Email Templates'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Email Templates</h1>
    <p>Edit the content and subjects of transactional emails.</p>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                <th style="padding:0.75rem 0;">Key</th>
                <th>Subject</th>
                <th>Active</th>
                <th style="text-align:right;"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($templates as $t): ?>
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:0.5rem 0;"><code><?= e($t['key']) ?></code></td>
                    <td><?= e($t['subject']) ?></td>
                    <td><?= (int) $t['is_active'] ? 'Yes' : 'No' ?></td>
                    <td style="text-align:right;">
                        <a href="<?= url('/admin/email-templates/' . $t['id'] . '/edit') ?>" class="btn btn-outline" style="padding:0.3rem 0.6rem;">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
