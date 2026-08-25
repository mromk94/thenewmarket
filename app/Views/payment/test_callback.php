<section class="hero" style="padding: 2rem 0;">
    <h1>Test payment</h1>
    <p>Confirm the test payment for order #<?= (int) $orderId ?>.</p>
</section>

<section class="glass-card max-w-md" style="padding: 1.5rem;">
    <p class="text-center" style="font-size:1.25rem; font-weight:700; margin-bottom:1.5rem;">
        Reference: <?= e($reference) ?>
    </p>

    <form action="<?= url('/payment/test/pay') ?>" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="reference" value="<?= e($reference) ?>">
        <input type="hidden" name="order_id" value="<?= (int) $orderId ?>">
        <button type="submit" class="btn btn-primary w-full">Mark as paid</button>
    </form>

    <p class="text-center mt-4" style="color:var(--muted);">This simulates a successful payment.</p>
</section>
