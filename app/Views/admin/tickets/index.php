<?php $title = 'Support Tickets'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Support Tickets</h1>
    <p><?= number_format($openCount) ?> open ticket<?= $openCount === 1 ? '' : 's' ?>.</p>
</section>

<section class="glass-card" style="padding:1.5rem;">
    <form method="GET" action="<?= url('/admin/tickets') ?>" style="display:flex; gap:0.75rem; flex-wrap:wrap; align-items:center; margin-bottom:1rem;">
        <select name="status" class="form-control" onchange="this.form.submit()" style="max-width:180px;">
            <option value="">All statuses</option>
            <option value="open" <?= $status === 'open' ? 'selected' : '' ?>>Open</option>
            <option value="in_progress" <?= $status === 'in_progress' ? 'selected' : '' ?>>In progress</option>
            <option value="resolved" <?= $status === 'resolved' ? 'selected' : '' ?>>Resolved</option>
            <option value="closed" <?= $status === 'closed' ? 'selected' : '' ?>>Closed</option>
        </select>
        <select name="priority" class="form-control" onchange="this.form.submit()" style="max-width:180px;">
            <option value="">All priorities</option>
            <option value="low" <?= $priority === 'low' ? 'selected' : '' ?>>Low</option>
            <option value="medium" <?= $priority === 'medium' ? 'selected' : '' ?>>Medium</option>
            <option value="high" <?= $priority === 'high' ? 'selected' : '' ?>>High</option>
        </select>
        <noscript><button type="submit" class="btn btn-outline">Filter</button></noscript>
    </form>

    <?php if (empty($tickets)): ?>
        <p style="color:var(--muted);">No tickets found.</p>
    <?php else: ?>
        <div class="ticket-list">
            <?php foreach ($tickets as $t): ?>
                <a href="<?= url('/admin/tickets/' . $t['id']) ?>" class="ticket-row">
                    <div class="ticket-info">
                        <p class="ticket-subject"><?= e($t['subject']) ?></p>
                        <p class="ticket-meta"><?= e($t['vendor_name'] ?? 'Marketplace') ?> · <?= e(ucfirst($t['category'] ?: 'general')) ?> · <?= e($t['created_at']) ?></p>
                    </div>
                    <div class="ticket-badges">
                        <span class="ticket-status" data-status="<?= e($t['status']) ?>"><?= e(ucfirst(str_replace('_', ' ', $t['status']))) ?></span>
                        <span class="ticket-priority" data-priority="<?= e($t['priority']) ?>"><?= e(ucfirst($t['priority'])) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
