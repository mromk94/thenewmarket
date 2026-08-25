<?php $title = 'My Profile'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>My Profile</h1>
    <p>Update your details and password.</p>
</section>

<div class="card-grid" style="grid-template-columns: 1fr 1fr;">
    <form action="<?= url('/account/profile') ?>" method="POST" class="glass-card" style="padding:1.5rem;">
        <h2 class="mb-2">Profile details</h2>
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="first_name">First name</label>
            <input type="text" id="first_name" name="first_name" class="form-control" value="<?= e($user['first_name'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="last_name">Last name</label>
            <input type="text" id="last_name" name="last_name" class="form-control" value="<?= e($user['last_name'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="tel" id="phone" name="phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update profile</button>
    </form>

    <form action="<?= url('/account/change-password') ?>" method="POST" class="glass-card" style="padding:1.5rem;">
        <h2 class="mb-2">Change password</h2>
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="current_password">Current password</label>
            <input type="password" id="current_password" name="current_password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="new_password">New password</label>
            <input type="password" id="new_password" name="new_password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm new password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Change password</button>
    </form>
</div>
