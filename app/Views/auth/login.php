<div class="glass-card max-w-md">
    <h1 class="text-center mb-2">Sign in</h1>
    <form action="<?= url('/login') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="email">Email</label>
            <input class="form-control" type="email" id="email" name="email" value="<?= e((string) old('email')) ?>" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input class="form-control" type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary w-full">Sign in</button>
    </form>

    <p class="text-center mt-4">Do not have an account? <a href="<?= url('/register') ?>">Register</a></p>
    <p class="text-center" style="font-size:0.9rem;"><a href="<?= url('/forgot-password') ?>">Forgot password?</a></p>
</div>
