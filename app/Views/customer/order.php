<section class="hero" style="padding: 2rem 0;">
    <h1>Order <?= e($order['order_number']) ?></h1>
    <p>
        Order status: <span style="text-transform:capitalize;"><?= e($order['status']) ?></span> ·
        Payment: <span style="text-transform:capitalize;"><?= e($order['payment_status']) ?></span>
    </p>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem; overflow-x: auto;">
    <table style="width:100%; min-width:560px; border-collapse:collapse; margin-bottom:1.5rem;">
        <thead>
            <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                <th>Product</th>
                <th>Qty</th>
                <th style="text-align:right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $i): ?>
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:0.5rem 0;"><?= e($i['product_name']) ?></td>
                    <td><?= (int) $i['quantity'] ?></td>
                    <td style="text-align:right;"><?= e(config('app.currency_symbol')) ?><?= number_format((float) $i['subtotal'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="display:flex; justify-content:space-between; font-size:1.25rem; font-weight:700; margin-bottom:1.5rem;">
        <span>Total</span>
        <span><?= e(config('app.currency_symbol')) ?><?= number_format((float) $order['total'], 2) ?></span>
    </div>

    <?php
        $proof = \App\Models\PaymentProof::forOrder((int) $order['id']);
    ?>
    <?php if ($order['payment_status'] === 'pending' && $proof): ?>
        <div style="padding:1rem; background:rgba(245,158,11,0.08); border:1px solid #f59e0b; border-radius:0.5rem;">
            <strong>Payment proof submitted</strong>
            <p style="margin:0.25rem 0 0; font-size:0.9rem;">Method: <?= e($proof['method_name']) ?> · Status: <?= e($proof['status']) ?></p>
            <?php if ($proof['receipt_image']): ?>
                <a href="<?= e(asset($proof['receipt_image'])) ?>" target="_blank" class="btn btn-outline" style="margin-top:0.75rem;">View receipt</a>
            <?php endif; ?>
        </div>
    <?php elseif ($order['payment_status'] === 'pending'): ?>
        <div style="padding:1rem; background:rgba(0,113,227,0.08); border:1px solid var(--primary); border-radius:0.5rem;">
            <strong>Pending payment</strong>
            <p style="margin:0.25rem 0 0; font-size:0.9rem;">Choose a payment method at checkout to complete this order.</p>
        </div>
    <?php endif; ?>
</section>
