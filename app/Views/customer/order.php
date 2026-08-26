<section class="hero" style="padding: 2rem 0;">
    <h1>Order <?= e($order['order_number']) ?></h1>
    <p>
        Order status: <span style="text-transform:capitalize;"><?= e($order['status']) ?></span> ·
        Payment: <span style="text-transform:capitalize;"><?= e($order['payment_status']) ?></span>
    </p>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <table style="width:100%; border-collapse:collapse; margin-bottom:1.5rem;">
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
                    <td style="padding:0.5rem 0; word-break:break-word;"><?= e($i['product_name']) ?></td>
                    <td><?= (int) $i['quantity'] ?></td>
                    <td style="text-align:right;"><?= format_price((float) $i['subtotal']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:0.5rem; margin-bottom:1.5rem;">
        <span style="color:var(--muted);">Subtotal</span>
        <span style="text-align:right;"><?= format_price((float) $order['subtotal']) ?></span>
        <span style="color:var(--muted);">Delivery</span>
        <span style="text-align:right;"><?= format_price((float) $order['shipping']) ?></span>
        <?php if (!empty($order['tax']) && (float) $order['tax'] > 0): ?>
            <span style="color:var(--muted);">Tax</span>
            <span style="text-align:right;"><?= format_price((float) $order['tax']) ?></span>
        <?php endif; ?>
        <span style="font-size:1.25rem; font-weight:700; padding-top:0.75rem; border-top:1px solid var(--border);">Total</span>
        <span style="font-size:1.25rem; font-weight:700; text-align:right; padding-top:0.75rem; border-top:1px solid var(--border);"><?= format_price((float) $order['total']) ?></span>
    </div>

    <div style="margin-bottom:1.5rem;">
        <h3 style="font-size:1rem; margin-bottom:0.5rem;">Delivery</h3>
        <p style="margin:0.25rem 0 0; color:var(--muted); font-size:0.9rem;">
            Status: <span style="text-transform:capitalize;"><?= e($order['delivery_status'] ?? 'pending') ?></span>
            <?php if (!empty($order['delivery_stage'])): ?>
                · Stage: <?= e($order['delivery_stage']) ?>
            <?php endif; ?>
            <?php if (!empty($order['tracking_number'])): ?>
                · Tracking: <strong><?= e($order['tracking_number']) ?></strong>
            <?php endif; ?>
        </p>
    </div>

    <?php if (!empty($order['address_line_1'])): ?>
        <div style="margin-bottom:1.5rem;">
            <h3 style="font-size:1rem; margin-bottom:0.5rem;">Shipping address</h3>
            <p style="margin:0; color:var(--muted); font-size:0.9rem;">
                <?= e($order['first_name'] . ' ' . $order['last_name']) ?><br>
                <?= e($order['address_line_1']) ?><br>
                <?php if (!empty($order['address_line_2'])): ?>
                    <?= e($order['address_line_2']) ?><br>
                <?php endif; ?>
                <?= e($order['city']) ?>, <?= e($order['state'] ?? '') ?> <?= e($order['zip']) ?><br>
                <?= e($order['country']) ?>
            </p>
        </div>
    <?php endif; ?>

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
