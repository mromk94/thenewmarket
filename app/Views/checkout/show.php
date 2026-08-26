<section class="hero" style="padding: 2rem 0;">
    <h1>Checkout</h1>
    <p>Review your order and choose how to pay.</p>
</section>

<form action="<?= url('/checkout') ?>" method="POST" class="card-grid mt-4 checkout-grid" id="checkout-form" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
    <?= csrf_field() ?>

    <section class="glass-card" style="padding: 1.5rem;">
        <h2 class="mb-2">Order summary</h2>
        <table style="width:100%; border-collapse:collapse; margin-bottom:1.5rem;">
            <thead>
                <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                    <th style="padding:0.75rem 0;">Product</th>
                    <th>Qty</th>
                    <th style="text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:0.5rem 0;">
                            <div style="display:flex; gap:0.75rem; align-items:center;">
                                <div style="width:48px; height:48px; background:rgba(255,255,255,0.05); border-radius:0.4rem; display:flex; align-items:center; justify-content:center; font-size:0.75rem; overflow:hidden; flex-shrink:0;">
                                    <?= $item['thumbnail'] ? '<img src="' . e(asset($item['thumbnail'])) . '" alt="" style="width:100%; height:100%; object-fit:cover; border-radius:0.4rem;">' : 'No image' ?>
                                </div>
                                <div>
                                    <?= e($item['name']) ?>
                                    <p style="color:var(--muted); font-size:0.8rem; margin:0;">
                                        <?= e($item['vendor_name'] ?? 'Marketplace') ?>
                                        <?php if (!empty($item['affiliate_vendor_name'])): ?>
                                            · via <?= e($item['affiliate_vendor_name']) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td><?= (int) $item['quantity'] ?></td>
                        <td style="text-align:right;"><?= format_price((float) $item['unit_price'] * (int) $item['quantity']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
            <span style="color:var(--muted);">Subtotal</span>
            <span><?= format_price($summary['subtotal']) ?></span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
            <span style="color:var(--muted);">Shipping</span>
            <span><?= $summary['shipping'] === 0.0 ? 'Free' : format_price($summary['shipping']) ?></span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
            <span style="color:var(--muted);">Tax</span>
            <span><?= format_price($summary['tax']) ?></span>
        </div>
        <?php if ($summary['discount'] > 0): ?>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                <span style="color:var(--muted);">Discount</span>
                <span>-<?= format_price($summary['discount']) ?></span>
            </div>
        <?php endif; ?>
        <div style="display:flex; justify-content:space-between; align-items:center; font-size:1.25rem; font-weight:700; margin-bottom:1.5rem; padding-top:1rem; border-top:1px solid var(--border);">
            <span>Total</span>
            <span><?= format_price($summary['total']) ?></span>
        </div>
    </section>

    <section class="glass-card" style="padding: 1.5rem;">
        <h2 class="mb-2">Delivery address</h2>

        <?php if (empty($addresses)): ?>
            <p style="color:var(--muted); margin-bottom:1rem;">You have no saved shipping address.</p>
            <a href="<?= url('/account/addresses') ?>" class="btn btn-outline">Add address</a>
        <?php else: ?>
            <?php foreach ($addresses as $index => $a): ?>
                <label style="display:block; border:1px solid var(--border); padding:1rem; border-radius:0.75rem; margin-bottom:0.75rem; cursor:pointer;">
                    <input type="radio" name="shipping_address_id" value="<?= (int) $a['id'] ?>" style="margin-right:0.5rem;" <?= $index === 0 ? 'checked' : '' ?>>
                    <strong><?= e($a['first_name']) ?> <?= e($a['last_name']) ?></strong>
                    <p style="color:var(--muted); font-size:0.85rem; margin:0.25rem 0 0;">
                        <?= e($a['address_line_1']) ?>, <?= e($a['city']) ?>, <?= e($a['country']) ?> <?= e($a['zip']) ?>
                    </p>
                </label>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <section class="glass-card" style="padding: 1.5rem;">
        <h2 class="mb-2">Payment</h2>

        <input type="hidden" id="payment_method_id" name="payment_method_id" value="<?= (int) ($paymentMethods[0]['id'] ?? 0) ?>">

        <?php if (!empty($paymentMethods)): ?>
            <?php foreach ($paymentMethods as $index => $m): ?>
                <?php $isFirst = $index === 0; ?>
                <div class="payment-option" data-method="<?= (int) $m['id'] ?>" style="border:<?= $isFirst ? '2px solid var(--primary)' : '1px solid var(--border)' ?>; padding:1rem; border-radius:0.75rem; margin-bottom:0.75rem; cursor:pointer; background:<?= $isFirst ? 'rgba(0,113,227,0.04)' : 'transparent' ?>;" onclick="selectMethod(this, <?= (int) $m['id'] ?>)">
                    <strong><?= e($m['name']) ?></strong>
                    <p style="color:var(--muted); font-size:0.85rem; margin:0.25rem 0 0;"><?= e($m['currency']) ?> · <?= e($m['type']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color:var(--muted);">No payment methods are configured right now.</p>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary w-full" style="margin-top:1rem;" <?= empty($paymentMethods) ? 'disabled' : '' ?>>Complete order</button>
    </section>
</form>

<script>
function selectMethod(el, id) {
    document.querySelectorAll('.payment-option').forEach(o => {
        o.style.border = '1px solid var(--border)';
        o.style.background = 'transparent';
    });
    el.style.border = '2px solid var(--primary)';
    el.style.background = 'rgba(0,113,227,0.04)';
    document.getElementById('payment_method_id').value = id;
}
</script>
