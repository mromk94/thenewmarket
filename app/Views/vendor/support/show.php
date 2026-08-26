<?php $title = 'Ticket #' . $ticket['id']; ?>

<section class="vendor-header">
    <div class="vendor-header-info">
        <div>
            <h1><?= e($ticket['subject']) ?></h1>
            <p><?= e(ucfirst($ticket['category'])) ?> · opened <?= e($ticket['created_at']) ?></p>
        </div>
    </div>
    <a href="<?= url('/vendor/support') ?>" class="btn btn-outline">All tickets</a>
</section>

<section class="glass-card" style="padding:1.5rem; margin-top:1.5rem;">
    <div class="ticket-badges" style="margin-bottom:1rem;">
        <span class="ticket-status" data-status="<?= e($ticket['status']) ?>"><?= e(ucfirst(str_replace('_', ' ', $ticket['status']))) ?></span>
        <span class="ticket-priority" data-priority="<?= e($ticket['priority']) ?>"><?= e(ucfirst($ticket['priority'])) ?></span>
    </div>
    <p style="white-space:pre-wrap; color:var(--text); line-height:1.6;"><?= e($ticket['message']) ?></p>
</section>

<section class="glass-card mt-4" style="padding:1.5rem;">
    <h3 class="vendor-recent-title" style="margin-bottom:1rem;">Conversation</h3>
    <?php if (empty($replies)): ?>
        <p style="color:var(--muted);">No replies yet. The admin team will respond shortly.</p>
    <?php else: ?>
        <div class="ticket-conversation">
            <?php foreach ($replies as $r): ?>
                <div class="ticket-message <?= $r['is_admin'] ? 'ticket-message-admin' : 'ticket-message-user' ?>">
                    <div class="ticket-message-head">
                        <strong><?= $r['is_admin'] ? 'Admin' : 'You' ?></strong>
                        <span class="ticket-message-time"><?= e($r['created_at']) ?></span>
                    </div>
                    <p style="white-space:pre-wrap; margin:0.5rem 0 0; line-height:1.6;"><?= e($r['message']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (in_array($ticket['status'], ['open', 'in_progress'], true)): ?>
        <form action="<?= url('/vendor/support/' . $ticket['id']) ?>" method="POST" style="margin-top:1.5rem;">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="message">Add a reply</label>
                <textarea id="message" name="message" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Send reply</button>
        </form>
    <?php endif; ?>
</section>
