<?php $title = 'Notifications'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Notifications</h1>
    <p>Send messages to customers or vendors.</p>
</section>

<section class="card-grid mt-4">
    <form action="<?= url('/admin/notifications') ?>" method="POST" class="glass-card" style="padding:1.5rem;">
        <h3 class="mb-2">Send notification</h3>
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" class="form-control" rows="3" required></textarea>
        </div>

        <div class="form-group">
            <label for="type">Type</label>
            <select id="type" name="type" class="form-control">
                <option value="info">Info</option>
                <option value="success">Success</option>
                <option value="warning">Warning</option>
            </select>
        </div>

        <div class="form-group">
            <label for="user_id">Recipient</label>
            <select id="user_id" name="user_id" class="form-control">
                <option value="0">-- Select one --</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?= (int) $u['id'] ?>"><?= e($u['email']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <label style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1rem;">
            <input type="checkbox" name="send_all" value="1">
            Send to all users
        </label>

        <button type="submit" class="btn btn-primary">Send</button>
    </form>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <h2 class="mb-2">Recent notifications</h2>
    <?php if (empty($recent)): ?>
        <p style="color:var(--muted);">No notifications sent yet.</p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                    <th style="padding:0.75rem 0;">Recipient</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Read</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent as $n): ?>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:0.5rem 0;"><?= e($n['email']) ?></td>
                        <td><?= e($n['title']) ?></td>
                        <td><?= e($n['type']) ?></td>
                        <td><?= (int) $n['is_read'] ? 'Yes' : 'No' ?></td>
                        <td><?= e($n['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
