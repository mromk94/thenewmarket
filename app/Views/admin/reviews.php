<?php $title = 'Reviews'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Reviews</h1>
    <p>Approve or reject customer reviews.</p>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <?php if (empty($reviews)): ?>
        <p style="color:var(--muted);">No pending reviews.</p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                    <th style="padding:0.75rem 0;">Product</th>
                    <th>Customer</th>
                    <th>Rating</th>
                    <th>Title</th>
                    <th>Body</th>
                    <th>Date</th>
                    <th style="text-align:right;"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reviews as $r): ?>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:0.5rem 0;"><a href="<?= url('/product/' . $r['product_slug']) ?>" target="_blank"><?= e($r['product_name']) ?></a></td>
                        <td><?= e($r['email']) ?></td>
                        <td><?= (int) $r['rating'] ?>/5</td>
                        <td><?= e($r['title'] ?: '—') ?></td>
                        <td style="max-width:300px; overflow:hidden; text-overflow:ellipsis;"><?= e($r['body']) ?></td>
                        <td><?= e($r['created_at']) ?></td>
                        <td style="text-align:right;">
                            <form action="<?= url('/admin/reviews/' . $r['id'] . '/approve') ?>" method="POST" style="display:inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline" style="padding:0.3rem 0.6rem;">Approve</button>
                            </form>
                            <form action="<?= url('/admin/reviews/' . $r['id'] . '/reject') ?>" method="POST" style="display:inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline" style="padding:0.3rem 0.6rem; color:#dc2626; border-color:#dc2626;">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
