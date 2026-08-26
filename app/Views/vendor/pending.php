<?php $title = 'Vendor Application Pending'; ?>

<section class="hero" style="padding-top: 4rem; min-height: 70vh; display:flex; align-items:center; justify-content:center;">
    <div class="glass-card" style="max-width: 520px; width: 100%; text-align:center; padding: 2.5rem; position:relative; overflow:hidden;">
        <div style="position:absolute; top:0; left:0; right:0; height: 6px; background: linear-gradient(90deg, var(--primary), var(--accent));"></div>

        <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; border-radius: 50%; background: rgba(0,113,227,0.1); display:flex; align-items:center; justify-content:center;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 40px; height: 40px; color: var(--primary);" aria-hidden="true">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
        </div>

        <h1 style="font-size: 1.75rem; margin-bottom: 0.75rem;">Application under review</h1>
        <p style="color: var(--muted); margin-bottom: 1.5rem;">
            Thanks for applying to sell with <strong><?= e(config('app.name')) ?></strong>.
            Your vendor profile is being reviewed and we'll notify you as soon as it's approved.
        </p>

        <div class="glass-card" style="padding: 1rem; margin-bottom: 1.5rem; text-align:left; background: rgba(255,255,255,0.03);">
            <p style="margin:0; font-size: 0.95rem;">
                <strong>Status:</strong> <span style="color: #f59e0b;">Pending approval</span>
            </p>
            <p style="margin: 0.5rem 0 0; font-size: 0.85rem; color: var(--muted);">
                While you wait, you can continue exploring the marketplace and shopping as a customer.
            </p>
        </div>

        <div style="display:flex; gap: 0.75rem; justify-content:center; flex-wrap:wrap;">
            <a href="<?= url('/shop') ?>" class="btn btn-primary" style="min-width: 180px;">Back to shopping</a>
            <a href="<?= url('/account') ?>" class="btn btn-outline" style="min-width: 140px;">My account</a>
        </div>
    </div>
</section>
