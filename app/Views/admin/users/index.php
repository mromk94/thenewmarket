<?php $title = 'Users'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Users</h1>
    <p>Manage customer and vendor accounts.</p>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                <th style="padding:0.75rem 0;">Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th style="text-align:right;"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:0.5rem 0;">
                        <strong><?= e(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?></strong>
                        <p style="color:var(--muted); font-size:0.85rem; margin:0;">ID #<?= (int) $u['id'] ?></p>
                    </td>
                    <td><?= e($u['email']) ?></td>
                    <td><?= e($u['role_name']) ?></td>
                    <td><?= e($u['status']) ?></td>
                    <td style="text-align:right;">
                        <a href="<?= url('/admin/users/' . $u['id'] . '/edit') ?>" class="btn btn-outline" style="padding:0.3rem 0.6rem;">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
