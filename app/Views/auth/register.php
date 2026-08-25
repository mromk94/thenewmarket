<div class="glass-card max-w-md">
    <h1 class="text-center mb-2">Create account</h1>
    <form action="<?= url('/register') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="first_name">First name</label>
            <input class="form-control" type="text" id="first_name" name="first_name" value="<?= e((string) old('first_name')) ?>">
        </div>

        <div class="form-group">
            <label for="last_name">Last name</label>
            <input class="form-control" type="text" id="last_name" name="last_name" value="<?= e((string) old('last_name')) ?>">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input class="form-control" type="email" id="email" name="email" value="<?= e((string) old('email')) ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input class="form-control" type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="role">Account type</label>
            <select class="form-control" id="role" name="role" onchange="toggleBusinessName(this)">
                <option value="customer" <?= old('role') === 'customer' ? 'selected' : '' ?>>Customer</option>
                <option value="vendor" <?= old('role') === 'vendor' ? 'selected' : '' ?>>Vendor</option>
            </select>
        </div>

        <div class="form-group" id="business-group" style="display:<?= old('role') === 'vendor' ? 'block' : 'none' ?>;">
            <label for="business_name">Business name</label>
            <input class="form-control" type="text" id="business_name" name="business_name" value="<?= e((string) old('business_name')) ?>">
        </div>

        <button type="submit" class="btn btn-primary w-full">Register</button>
    </form>

    <p class="text-center mt-4">Already have an account? <a href="<?= url('/login') ?>">Sign in</a></p>
</div>

<script>
function toggleBusinessName(select) {
    const group = document.getElementById('business-group');
    group.style.display = select.value === 'vendor' ? 'block' : 'none';
}
</script>
