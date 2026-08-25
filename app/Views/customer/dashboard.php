<section class="hero">
    <h1>Welcome back, <?= e(($user['first_name'] ?? $user['email']) ?? '') ?></h1>
    <p>This is your customer dashboard. Orders, wallet, and saved items will appear here.</p>
</section>

<div class="card-grid">
    <div class="glass-card">
        <h3>My Orders</h3>
        <p>Track and view your order history.</p>
        <a href="<?= url('/account/orders') ?>" class="btn btn-outline">View orders</a>
    </div>
    <div class="glass-card">
        <h3>My Wallet</h3>
        <p>Check your store credit and transaction history.</p>
        <a href="<?= url('/account/wallet') ?>" class="btn btn-outline">View wallet</a>
    </div>
    <div class="glass-card">
        <h3>Notifications</h3>
        <p>Your messages and alerts.</p>
        <a href="<?= url('/account/notifications') ?>" class="btn btn-outline">View notifications</a>
    </div>
    <div class="glass-card">
        <h3>Refunds</h3>
        <p>Request a refund for a paid order.</p>
        <a href="<?= url('/account/refunds') ?>" class="btn btn-outline">Refund requests</a>
    </div>
    <div class="glass-card">
        <h3>Settings</h3>
        <p>Manage your addresses and profile.</p>
        <a href="<?= url('/account/profile') ?>" class="btn btn-outline">Edit profile</a>
    </div>
</div>
