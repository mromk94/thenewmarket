<section class="hero" style="padding: 3rem 0;">
    <h1>Forgot password?</h1>
    <p>Enter your email and we will send you a reset link.</p>
</section>

<section class="glass-card max-w-md" style="padding: 1.5rem;">
    <form action="<?= url('/forgot-password') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input class="form-control" type="email" id="email" name="email" value="<?= e(old('email')) ?>" required>
        </div>

        <button type="submit" class="btn btn-primary w-full">Send reset link</button>
    </form>

    <p class="text-center" style="color:var(--muted); margin-top:1rem; font-size:0.9rem;">
        <a href="<?= url('/login') ?>">Back to login</a>
    </p>
</section>
