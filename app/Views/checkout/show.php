<section class="hero" style="padding: 2rem 0;">
    <h1>Checkout</h1>
    <p>Review your order and choose how to pay.</p>
</section>

<form action="<?= url('/checkout') ?>" method="POST" class="card-grid mt-4 checkout-grid" id="checkout-form" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
    <?= csrf_field() ?>
    <input type="hidden" id="payment_method_id" name="payment_method_id" value="0">

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
                        <td style="text-align:right;"><?= e(config('app.currency_symbol')) ?><?= number_format((float) $item['unit_price'] * (int) $item['quantity'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
            <span style="color:var(--muted);">Subtotal</span>
            <span><?= e(config('app.currency_symbol')) ?><?= number_format($summary['subtotal'], 2) ?></span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
            <span style="color:var(--muted);">Shipping</span>
            <span><?= $summary['shipping'] === 0.0 ? 'Free' : e(config('app.currency_symbol')) . number_format($summary['shipping'], 2) ?></span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
            <span style="color:var(--muted);">Tax</span>
            <span><?= e(config('app.currency_symbol')) ?><?= number_format($summary['tax'], 2) ?></span>
        </div>
        <?php if ($summary['discount'] > 0): ?>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                <span style="color:var(--muted);">Discount</span>
                <span>-<?= e(config('app.currency_symbol')) ?><?= number_format($summary['discount'], 2) ?></span>
            </div>
        <?php endif; ?>
        <div style="display:flex; justify-content:space-between; align-items:center; font-size:1.25rem; font-weight:700; margin-bottom:1.5rem; padding-top:1rem; border-top:1px solid var(--border);">
            <span>Total</span>
            <span><?= e(config('app.currency_symbol')) ?><?= number_format($summary['total'], 2) ?></span>
        </div>
    </section>

    <section class="glass-card" style="padding: 1.5rem;">
        <h2 class="mb-2">Payment</h2>

        <div class="payment-option active" data-method="0" style="border:2px solid var(--primary); padding:1rem; border-radius:0.75rem; margin-bottom:0.75rem; cursor:pointer; background:rgba(0,113,227,0.04);" onclick="selectMethod(this, 0)">
            <strong>Test payment gateway</strong>
            <p style="color:var(--muted); font-size:0.85rem; margin:0.25rem 0 0;">Simulate an instant card payment.</p>
        </div>

        <?php if (!empty($paymentMethods)): ?>
            <p style="color:var(--muted); font-size:0.85rem; margin:1rem 0 0.5rem;">Or pay manually:</p>
            <?php foreach ($paymentMethods as $m): ?>
                <div class="payment-option" data-method="<?= (int) $m['id'] ?>" style="border:1px solid var(--border); padding:1rem; border-radius:0.75rem; margin-bottom:0.75rem; cursor:pointer;" onclick="selectMethod(this, <?= (int) $m['id'] ?>)">
                    <strong><?= e($m['name']) ?></strong>
                    <p style="color:var(--muted); font-size:0.85rem; margin:0.25rem 0 0;"><?= e($m['currency']) ?> · <?= e($m['type']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary w-full" style="margin-top:1rem;">Complete order</button>
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
