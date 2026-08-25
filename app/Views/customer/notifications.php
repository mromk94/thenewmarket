<?php $title = 'Notifications'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Notifications</h1>
    <p>You have <?= (int) $unreadCount ?> unread.</p>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <?php if (empty($notifications)): ?>
        <p style="color:var(--muted);">No notifications yet.</p>
    <?php else: ?>
        <?php foreach ($notifications as $n): ?>
            <div style="border-bottom:1px solid var(--border); padding:0.75rem 0; <?= (int) $n['is_read'] ? '' : 'background:rgba(0,113,227,0.04);' ?>">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; flex-wrap:wrap;">
                    <div>
                        <strong><?= e($n['title']) ?></strong>
                        <?php if (!(int) $n['is_read']): ?>
                            <span class="badge" style="background:#0071e3; color:#fff; margin-left:0.5rem;">New</span>
                        <?php endif; ?>
                        <p style="margin:0.25rem 0; color:var(--muted); font-size:0.9rem;"><?= e($n['message']) ?></p>
                        <p style="margin:0; color:var(--muted); font-size:0.8rem;"><?= e($n['created_at']) ?></p>
                    </div>
                    <?php if (!(int) $n['is_read']): ?>
                        <form action="<?= url('/account/notifications/' . $n['id'] . '/read') ?>" method="POST" style="display:inline;">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline" style="padding:0.3rem 0.6rem;">Mark as read</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
