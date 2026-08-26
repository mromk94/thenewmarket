<section class="hero" style="padding: 2rem 0;">
    <h1>My orders</h1>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <?php if (empty($orders)): ?>
        <p class="text-center" style="color:var(--muted);">You have not placed any orders yet.</p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                    <th>Order</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:0.5rem 0;"><?= e($o['order_number']) ?></td>
                        <td><?= format_price((float) $o['total']) ?></td>
                        <td><?= e($o['status']) ?></td>
                        <td><?= e($o['created_at']) ?></td>
                        <td><a href="<?= url('/account/orders/' . $o['id']) ?>" class="btn btn-outline" style="padding:0.35rem 0.7rem;">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
