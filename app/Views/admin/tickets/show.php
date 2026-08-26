<?php $title = 'Ticket #' . $ticket['id']; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1><?= e($ticket['subject']) ?></h1>
    <p><?= e($ticket['vendor_name'] ?? 'Marketplace') ?> · <?= e($ticket['user_email']) ?> · opened <?= e($ticket['created_at']) ?></p>
</section>

<section class="glass-card" style="padding:1.5rem; margin-top:1rem;">
    <div class="ticket-badges" style="margin-bottom:1rem;">
        <span class="ticket-status" data-status="<?= e($ticket['status']) ?>"><?= e(ucfirst(str_replace('_', ' ', $ticket['status']))) ?></span>
        <span class="ticket-priority" data-priority="<?= e($ticket['priority']) ?>"><?= e(ucfirst($ticket['priority'])) ?></span>
    </div>
    <p style="white-space:pre-wrap; color:var(--text); line-height:1.6;"><?= e($ticket['message']) ?></p>
</section>

<section class="glass-card mt-4" style="padding:1.5rem;">
    <h3 class="vendor-recent-title" style="margin-bottom:1rem;">Conversation</h3>
    <?php if (empty($replies)): ?>
        <p style="color:var(--muted);">No replies yet.</p>
    <?php else: ?>
        <div class="ticket-conversation">
            <?php foreach ($replies as $r): ?>
                <div class="ticket-message <?= $r['is_admin'] ? 'ticket-message-admin' : 'ticket-message-user' ?>">
                    <div class="ticket-message-head">
                        <strong><?= $r['is_admin'] ? 'Admin' : 'Vendor' ?></strong>
                        <span class="ticket-message-time"><?= e($r['created_at']) ?></span>
                    </div>
                    <p style="white-space:pre-wrap; margin:0.5rem 0 0; line-height:1.6;"><?= e($r['message']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="glass-card mt-4" style="padding:1.5rem;">
    <h3 class="vendor-recent-title" style="margin-bottom:1rem;">Reply & update</h3>
    <form action="<?= url('/admin/tickets/' . $ticket['id'] . '/reply') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="message">Reply</label>
            <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
        </div>
        <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
            <div class="form-group" style="flex:1; min-width:140px;">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="open" <?= $ticket['status'] === 'open' ? 'selected' : '' ?>>Open</option>
                    <option value="in_progress" <?= $ticket['status'] === 'in_progress' ? 'selected' : '' ?>>In progress</option>
                    <option value="resolved" <?= $ticket['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                    <option value="closed" <?= $ticket['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                </select>
            </div>
            <div class="form-group" style="flex:1; min-width:140px;">
                <label for="priority">Priority</label>
                <select id="priority" name="priority" class="form-control">
                    <option value="low" <?= $ticket['priority'] === 'low' ? 'selected' : '' ?>>Low</option>
                    <option value="medium" <?= $ticket['priority'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="high" <?= $ticket['priority'] === 'high' ? 'selected' : '' ?>>High</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;">Send reply</button>
    </form>
</section>
