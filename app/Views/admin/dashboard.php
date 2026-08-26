<section class="hero" style="padding: 2rem 0;">
    <h1>Admin Dashboard</h1>
    <p>Overview of the marketplace.</p>
    <a href="<?= url('/admin/settings/general') ?>" class="btn btn-outline mt-2">Settings</a>
</section>

<section class="card-grid mt-4">
    <div class="glass-card">
        <h3><?= number_format($stats['orders']) ?></h3>
        <p style="color:var(--muted);">Orders</p>
    </div>
    <div class="glass-card">
        <h3><?= number_format($stats['customers']) ?></h3>
        <p style="color:var(--muted);">Customers</p>
    </div>
    <div class="glass-card">
        <h3><?= number_format($stats['vendors']) ?></h3>
        <p style="color:var(--muted);">Approved Vendors</p>
    </div>
    <div class="glass-card">
        <h3><?= number_format($stats['products']) ?></h3>
        <p style="color:var(--muted);">Products</p>
    </div>
    <div class="glass-card">
        <h3><?= e(config('app.currency_symbol')) ?><?= number_format($stats['total_revenue'], 2) ?></h3>
        <p style="color:var(--muted);">Revenue</p>
    </div>
    <div class="glass-card">
        <h3><?= number_format($stats['pending_vendors'] + $stats['pending_products']) ?></h3>
        <p style="color:var(--muted);">Pending Approvals</p>
    </div>
    <div class="glass-card">
        <h3><?= number_format($stats['open_tickets']) ?></h3>
        <p style="color:var(--muted);">Open Tickets</p>
    </div>
</section>

<section class="mt-4">
    <h2 class="mb-2">Quick links</h2>
    <div class="card-grid">
        <div class="glass-card">
            <h3>Products</h3>
            <p style="color:var(--muted);">Post and manage products.</p>
            <a href="<?= url('/admin/products') ?>" class="btn btn-outline">View products</a>
        </div>
        <div class="glass-card">
            <h3>Categories</h3>
            <p style="color:var(--muted);">Manage product categories.</p>
            <a href="<?= url('/admin/categories') ?>" class="btn btn-outline">View categories</a>
        </div>
        <div class="glass-card">
            <h3>Users</h3>
            <p style="color:var(--muted);">View and edit accounts.</p>
            <a href="<?= url('/admin/users') ?>" class="btn btn-outline">View users</a>
        </div>
        <div class="glass-card">
            <h3>Vendors</h3>
            <p style="color:var(--muted);">Approve and edit vendors.</p>
            <a href="<?= url('/admin/vendors') ?>" class="btn btn-outline">View vendors</a>
        </div>
        <div class="glass-card">
            <h3>Deposits</h3>
            <p style="color:var(--muted);">Vendor top-up methods and requests.</p>
            <a href="<?= url('/admin/deposit-methods') ?>" class="btn btn-outline">Manage deposits</a>
        </div>
        <div class="glass-card">
            <h3>Withdrawals</h3>
            <p style="color:var(--muted);">Approve vendor payouts.</p>
            <a href="<?= url('/admin/withdrawals') ?>" class="btn btn-outline">View withdrawals</a>
        </div>
        <div class="glass-card">
            <h3>Payment Methods</h3>
            <p style="color:var(--muted);">Manual crypto/bank checkout options.</p>
            <a href="<?= url('/admin/payment-methods') ?>" class="btn btn-outline">Manage methods</a>
        </div>
        <div class="glass-card">
            <h3>Currencies</h3>
            <p style="color:var(--muted);">Set exchange rates for multi-currency display.</p>
            <a href="<?= url('/admin/currencies') ?>" class="btn btn-outline">Manage currencies</a>
        </div>
        <div class="glass-card">
            <h3>Payment Proofs</h3>
            <p style="color:var(--muted);">Review customer payment uploads.</p>
            <a href="<?= url('/admin/payment-proofs') ?>" class="btn btn-outline">View proofs</a>
        </div>
        <div class="glass-card">
            <h3>Reviews</h3>
            <p style="color:var(--muted);">Approve customer product reviews.</p>
            <a href="<?= url('/admin/reviews') ?>" class="btn btn-outline">View reviews</a>
        </div>
        <div class="glass-card">
            <h3>Coupons</h3>
            <p style="color:var(--muted);">Create and manage discount codes.</p>
            <a href="<?= url('/admin/coupons') ?>" class="btn btn-outline">View coupons</a>
        </div>
        <div class="glass-card">
            <h3>Email Templates</h3>
            <p style="color:var(--muted);">Edit transactional email content.</p>
            <a href="<?= url('/admin/email-templates') ?>" class="btn btn-outline">Edit templates</a>
        </div>
        <div class="glass-card">
            <h3>Content Pages</h3>
            <p style="color:var(--muted);">Edit about, contact, terms and privacy.</p>
            <a href="<?= url('/admin/pages') ?>" class="btn btn-outline">Edit pages</a>
        </div>
        <div class="glass-card">
            <h3>Notifications</h3>
            <p style="color:var(--muted);">Broadcast in-app messages.</p>
            <a href="<?= url('/admin/notifications') ?>" class="btn btn-outline">Send notification</a>
        </div>
        <div class="glass-card">
            <h3>Support Tickets</h3>
            <p style="color:var(--muted);">Vendor requests and replies.</p>
            <a href="<?= url('/admin/tickets') ?>" class="btn btn-outline">View tickets</a>
        </div>
        <div class="glass-card">
            <h3>Delivery</h3>
            <p style="color:var(--muted);">Update order delivery status and tracking.</p>
            <a href="<?= url('/admin/delivery') ?>" class="btn btn-outline">Manage delivery</a>
        </div>
        <div class="glass-card">
            <h3>Refunds</h3>
            <p style="color:var(--muted);">Review customer refund requests.</p>
            <a href="<?= url('/admin/refunds') ?>" class="btn btn-outline">View refunds</a>
        </div>
    </div>
</section>

<section class="mt-4">
    <h2 class="mb-2">Pending Vendors</h2>
    <?php if (empty($pendingVendors)): ?>
        <p style="color:var(--muted);">No pending vendors.</p>
    <?php else: ?>
        <div class="card-grid">
            <?php foreach ($pendingVendors as $v): ?>
                <div class="glass-card">
                    <h3><?= e($v['business_name']) ?></h3>
                    <p style="color:var(--muted); font-size:0.9rem;"><?= e($v['email']) ?></p>
                    <div style="display:flex; gap:0.5rem; margin-top:0.75rem;">
                        <form action="<?= url('/admin/vendors/' . $v['id'] . '/update') ?>" method="POST" style="display:inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn btn-primary" style="padding:0.35rem 0.7rem;">Approve</button>
                        </form>
                        <form action="<?= url('/admin/vendors/' . $v['id'] . '/update') ?>" method="POST" style="display:inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="btn btn-outline" style="padding:0.35rem 0.7rem; color:#ef4444; border-color:#ef4444;">Reject</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="mt-4">
    <h2 class="mb-2">Pending Products</h2>
    <?php if (empty($pendingProducts)): ?>
        <p style="color:var(--muted);">No pending products.</p>
    <?php else: ?>
        <div class="card-grid">
            <?php foreach ($pendingProducts as $p): ?>
                <div class="glass-card">
                    <h3><?= e($p['name']) ?></h3>
                    <p style="color:var(--muted); font-size:0.9rem;"><?= e($p['vendor_name'] ?? 'Marketplace') ?> · <?= e($p['owner_email']) ?></p>
                    <div style="display:flex; gap:0.5rem; margin-top:0.75rem;">
                        <form action="<?= url('/admin/products/' . $p['id'] . '/update') ?>" method="POST" style="display:inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="publish">
                            <button type="submit" class="btn btn-primary" style="padding:0.35rem 0.7rem;">Publish</button>
                        </form>
                        <form action="<?= url('/admin/products/' . $p['id'] . '/update') ?>" method="POST" style="display:inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="btn btn-outline" style="padding:0.35rem 0.7rem; color:#ef4444; border-color:#ef4444;">Reject</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
