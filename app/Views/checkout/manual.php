<section class="hero" style="padding: 2rem 0;">
    <h1>Manual payment</h1>
    <p>Send the payment and upload your proof.</p>
</section>

<section class="card-grid mt-4 checkout-manual-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
    <div class="glass-card" style="padding:1.5rem;">
        <h2 class="mb-2"><?= e($method['name']) ?></h2>
        <p style="color:var(--muted); font-size:0.9rem; margin:0 0 1rem;">Currency: <?= e($method['currency']) ?> · Type: <?= e($method['type']) ?></p>

        <?php if ($method['type'] === 'crypto'): ?>
            <?php if ($method['network']): ?>
                <p><strong>Network:</strong> <?= e($method['network']) ?></p>
            <?php endif; ?>
            <?php if ($method['wallet_address']): ?>
                <p><strong>Wallet address:</strong><br><code style="word-break:break-all; background:#f5f5f7; padding:0.5rem; border-radius:0.35rem; display:block; margin-top:0.25rem;"><?= e($method['wallet_address']) ?></code></p>
            <?php endif; ?>
            <?php if ($method['qr_image']): ?>
                <img src="<?= e(asset($method['qr_image'])) ?>" alt="QR" style="max-width:180px; border-radius:0.5rem; margin-top:0.5rem;">
            <?php endif; ?>
        <?php else: ?>
            <?php if ($method['bank_name']): ?>
                <p><strong>Bank:</strong> <?= e($method['bank_name']) ?></p>
            <?php endif; ?>
            <?php if ($method['account_name']): ?>
                <p><strong>Account name:</strong> <?= e($method['account_name']) ?></p>
            <?php endif; ?>
            <?php if ($method['account_number']): ?>
                <p><strong>Account number:</strong> <?= e($method['account_number']) ?></p>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($method['instructions']): ?>
            <p style="color:var(--muted); margin-top:1rem;"><?= e($method['instructions']) ?></p>
        <?php endif; ?>

        <div style="margin-top:1.5rem; padding:1rem; background:rgba(0,113,227,0.05); border-radius:0.5rem;">
            <p style="margin:0; font-weight:700;">Order total: <?= e(config('app.currency_symbol')) ?><?= number_format((float) $order['total'], 2) ?></p>
            <p style="margin:0.25rem 0 0; color:var(--muted); font-size:0.85rem;">Order <?= e($order['order_number']) ?></p>
        </div>
    </div>

    <form action="<?= url('/checkout/pay/' . $order['id']) ?>" method="POST" enctype="multipart/form-data" class="glass-card" style="padding:1.5rem;">
        <h3 class="mb-2">Upload proof</h3>
        <?= csrf_field() ?>
        <input type="hidden" name="payment_method_id" value="<?= (int) $method['id'] ?>">

        <div class="form-group">
            <label for="reference">Transaction / transfer reference</label>
            <input type="text" id="reference" name="reference" class="form-control" placeholder="TxID, reference number, etc." required>
        </div>

        <div class="form-group">
            <label for="receipt_image">Receipt / proof of payment</label>
            <input type="file" id="receipt_image" name="receipt_image" class="form-control" accept="image/*" required>
        </div>

        <button type="submit" class="btn btn-primary w-full">Submit proof</button>
        <p class="mt-2" style="color:var(--muted); font-size:0.85rem;">Your order will be reviewed and approved once the payment is verified.</p>
    </form>
</section>
