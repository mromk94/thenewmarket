<?php $title = 'Support'; ?>

<section class="vendor-header">
    <div class="vendor-header-info">
        <div>
            <h1>Support</h1>
            <p>Request help from the admin team.</p>
        </div>
    </div>
    <a href="<?= url('/vendor/dashboard') ?>" class="btn btn-outline">Dashboard</a>
</section>

<section class="glass-card" style="padding:1.5rem; margin-top:1.5rem;">
    <h3 class="vendor-recent-title" style="margin-bottom:1rem;">Open a new ticket</h3>
    <form action="<?= url('/vendor/support') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" class="form-control" required value="<?= e(Session::old('subject')) ?>">
        </div>
        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category" class="form-control">
                <option value="general">General</option>
                <option value="payments">Payments / Wallet</option>
                <option value="products">Products / Listings</option>
                <option value="account">Account / Verification</option>
                <option value="technical">Technical issue</option>
            </select>
        </div>
        <div class="form-group">
            <label for="priority">Priority</label>
            <select id="priority" name="priority" class="form-control">
                <option value="low">Low</option>
                <option value="medium" selected>Medium</option>
                <option value="high">High</option>
            </select>
        </div>
        <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" class="form-control" rows="5" required><?= e(Session::old('message')) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;">Submit ticket</button>
    </form>
</section>

<section class="glass-card mt-4" style="padding:1.5rem;">
    <h3 class="vendor-recent-title" style="margin-bottom:1rem;">Your tickets</h3>
    <?php if (empty($tickets)): ?>
        <p style="color:var(--muted);">You have not opened any support tickets yet.</p>
    <?php else: ?>
        <div class="ticket-list">
            <?php foreach ($tickets as $t): ?>
                <a href="<?= url('/vendor/support/' . $t['id']) ?>" class="ticket-row">
                    <div class="ticket-info">
                        <p class="ticket-subject"><?= e($t['subject']) ?></p>
                        <p class="ticket-meta"><?= e(ucfirst($t['category'])) ?> · <?= e($t['created_at']) ?></p>
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
