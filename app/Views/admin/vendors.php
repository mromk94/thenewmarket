<section class="hero" style="padding: 2rem 0;">
    <h1>Vendors</h1>
    <p>Manage vendor applications and accounts.</p>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                <th style="padding:0.75rem 0;">Business</th>
                <th>Contact</th>
                <th>Status</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vendors as $v): ?>
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:0.5rem 0;">
                        <strong><?= e($v['business_name']) ?></strong>
                        <p style="color:var(--muted); font-size:0.85rem; margin:0;"><?= e($v['slug']) ?></p>
                    </td>
                    <td><?= e($v['email']) ?></td>
                    <td><?= e($v['status']) ?></td>
                    <td style="text-align:right;">
                        <div style="display:flex; gap:0.4rem; justify-content:flex-end; flex-wrap:wrap; align-items:center;">
                            <a href="<?= url('/admin/vendors/' . $v['id'] . '/edit') ?>" class="btn btn-outline" style="padding:0.3rem 0.6rem;">Edit</a>
                            <?php if ($v['status'] !== 'approved'): ?>
                                <form action="<?= url('/admin/vendors/' . $v['id'] . '/update') ?>" method="POST" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-outline" style="padding:0.3rem 0.6rem;">Approve</button>
                                </form>
                            <?php else: ?>
                                <form action="<?= url('/admin/vendors/' . $v['id'] . '/update') ?>" method="POST" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="suspend">
                                    <input type="text" name="rejection_reason" class="form-control" placeholder="Reason" style="max-width:140px; padding:0.35rem 0.5rem; font-size:0.85rem; display:inline-block; width:auto;">
                                    <button type="submit" class="btn btn-outline" style="padding:0.3rem 0.6rem;">Suspend</button>
                                </form>
                            <?php endif; ?>
                            <form action="<?= url('/admin/vendors/' . $v['id'] . '/update') ?>" method="POST" style="display:inline;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="reject">
                                <input type="text" name="rejection_reason" class="form-control" placeholder="Reason" style="max-width:140px; padding:0.35rem 0.5rem; font-size:0.85rem; display:inline-block; width:auto;">
                                <button type="submit" class="btn btn-outline" style="padding:0.3rem 0.6rem; color:#ef4444; border-color:#ef4444;">Reject</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
