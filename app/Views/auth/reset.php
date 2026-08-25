<section class="hero" style="padding: 3rem 0;">
    <h1>Reset password</h1>
    <p>Choose a new password for your account.</p>
</section>

<section class="glass-card max-w-md" style="padding: 1.5rem;">
    <form action="<?= url('/reset-password') ?>" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= e($token) ?>">
        <input type="hidden" name="email" value="<?= e($email) ?>">

        <div class="mb-3">
            <label for="password" class="form-label">New password</label>
            <input class="form-control" type="password" id="password" name="password" minlength="8" required>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm new password</label>
            <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" minlength="8" required>
        </div>

        <button type="submit" class="btn btn-primary w-full">Reset password</button>
    </form>

    <p class="text-center" style="color:var(--muted); margin-top:1rem; font-size:0.9rem;">
        <a href="<?= url('/login') ?>">Back to login</a>
    </p>
</section>
