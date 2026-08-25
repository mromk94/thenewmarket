<?php $title = 'My Sales'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>My Sales</h1>
    <p>Orders that include your products and affiliate sales.</p>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <?php if (empty($orders)): ?>
        <p style="color:var(--muted);">No sales yet.</p>
    <?php else: ?>
        <?php foreach ($orders as $o): ?>
            <div class="glass-card mt-2" style="padding:1rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
                    <div>
                        <strong><?= e($o['order_number']) ?></strong>
                        <p style="margin:0; color:var(--muted); font-size:0.85rem;">Placed on <?= e($o['created_at']) ?></p>
                    </div>
                    <div style="font-weight:700;">
                        <?= e(config('app.currency_symbol')) ?><?= number_format($totals[(int) $o['id']] ?? 0, 2) ?>
                    </div>
                </div>

                <table style="width:100%; border-collapse:collapse; margin-top:0.75rem;">
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <?php if ((int) $item['order_id'] === (int) $o['id']): ?>
                                <tr style="border-top:1px solid var(--border);">
                                    <td style="padding:0.5rem 0;">
                                        <a href="<?= url('/product/' . e($item['slug'])) ?>"><?= e($item['product_name']) ?></a>
                                        <p style="margin:0; color:var(--muted); font-size:0.85rem;">Qty: <?= (int) $item['quantity'] ?> · Unit: <?= e(config('app.currency_symbol')) ?><?= number_format((float) $item['unit_price'], 2) ?></p>
                                    </td>
                                    <td style="text-align:right;">
                                        <?= e(config('app.currency_symbol')) ?><?= number_format((float) $item['subtotal'], 2) ?>
                                        <?php if ((float) ($item['affiliate_commission_amount'] ?? 0) > 0): ?>
                                            <p style="margin:0; color:var(--success); font-size:0.8rem;">+<?= e(config('app.currency_symbol')) ?><?= number_format((float) $item['affiliate_commission_amount'], 2) ?> commission</p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
