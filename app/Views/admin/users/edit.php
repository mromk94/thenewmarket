<?php $title = 'Edit User'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Edit User</h1>
    <p><?= e($user['email']) ?></p>
</section>

<form action="<?= url('/admin/users/' . $user['id']) ?>" method="POST" class="glass-card mt-4" style="padding:1.5rem;">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" class="form-control" value="<?= e($user['email']) ?>" required>
    </div>

    <div class="form-group">
        <label for="role_id">Role</label>
        <select id="role_id" name="role_id" class="form-control" required>
            <?php foreach ($roles as $r): ?>
                <option value="<?= (int) $r['id'] ?>" <?= (int) $r['id'] === (int) $user['role_id'] ? 'selected' : '' ?>><?= e($r['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="status">Status</label>
        <select id="status" name="status" class="form-control" required>
            <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="pending" <?= $user['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="suspended" <?= $user['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
            <option value="banned" <?= $user['status'] === 'banned' ? 'selected' : '' ?>>Banned</option>
        </select>
    </div>

    <div class="form-group">
        <label for="password">New password (leave blank to keep current)</label>
        <input type="password" id="password" name="password" class="form-control">
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">Save user</button>
        <a href="<?= url('/admin/users') ?>" class="btn btn-outline">Cancel</a>
    </div>
</form>
