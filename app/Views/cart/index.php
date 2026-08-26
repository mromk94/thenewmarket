<section class="cart-modal" style="padding: 2rem 0;">
    <div class="glass-card" style="max-width:900px; margin:0 auto; padding:1.5rem; box-shadow:var(--shadow), 0 0 40px rgba(0,0,0,0.1); border:1px solid var(--border);">
        <h1 style="margin-bottom:0.5rem; font-size:1.5rem;">Your cart</h1>
        <p style="color:var(--muted); margin-bottom:1.25rem;"><?= count($items) ?> item<?= count($items) === 1 ? '' : 's' ?></p>

        <?php if (empty($items)): ?>
            <div style="text-align:center; padding:2rem;">
                <p style="color:var(--muted);">Your cart is empty.</p>
                <a href="<?= url('/shop') ?>" class="btn btn-primary" style="margin-top:1rem;">Continue shopping</a>
            </div>
        <?php else: ?>
            <div style="overflow-x:auto;">
                <table style="width:100%; min-width:620px; border-collapse:collapse;">
                    <thead>
                        <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                            <th style="padding:0.75rem 0;">Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th style="text-align:right;">Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr style="border-bottom:1px solid var(--border);">
                                <td style="padding:0.75rem 0;">
                                    <div style="display:flex; gap:0.75rem; align-items:center;">
                                        <div style="width:64px; height:64px; background:rgba(255,255,255,0.05); border-radius:0.4rem; display:flex; align-items:center; justify-content:center; color:var(--muted); font-size:0.75rem; overflow:hidden; flex-shrink:0;">
                                            <?= $item['thumbnail'] ? '<img src="' . e(asset($item['thumbnail'])) . '" alt="" style="width:100%; height:100%; object-fit:cover; border-radius:0.4rem;">' : 'No image' ?>
                                        </div>
                                        <div>
                                            <a href="<?= url('/product/' . $item['slug']) ?>"><?= e($item['name']) ?></a>
                                            <p style="color:var(--muted); font-size:0.85rem; margin:0.25rem 0 0;">
                                                <?= e($item['vendor_name'] ?? 'Marketplace') ?>
                                                <?php if (!empty($item['affiliate_vendor_name'])): ?>
                                                    · via <?= e($item['affiliate_vendor_name']) ?>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td><?= format_price((float) $item['unit_price']) ?></td>
                                <td>
                                    <form action="<?= url('/cart/' . $item['cart_item_id'] . '/update') ?>" method="POST" data-ajax-cart="update" style="display:inline-flex; gap:0.5rem; align-items:center;">
                                        <?= csrf_field() ?>
                                        <input type="number" name="quantity" value="<?= (int) $item['quantity'] ?>" min="0" style="width:60px; padding:0.4rem; background:transparent; border:1px solid var(--border); border-radius:0.35rem; color:var(--text);">
                                        <button type="submit" class="btn btn-outline" style="padding:0.4rem 0.8rem;">Update</button>
                                    </form>
                                </td>
                                <td style="text-align:right;"><?= format_price((float) $item['unit_price'] * (int) $item['quantity']) ?></td>
                                <td style="text-align:right;">
                                    <form action="<?= url('/cart/' . $item['cart_item_id'] . '/remove') ?>" method="POST" data-ajax-cart="remove" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline" style="padding:0.4rem 0.8rem; color:#ef4444; border-color:#ef4444;">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div style="display:flex; gap:0.5rem; align-items:stretch; flex-wrap:wrap; margin-top:1.5rem;">
                <form action="<?= url('/cart/coupon') ?>" method="POST" style="display:flex; gap:0.5rem; flex-wrap:wrap; flex:1;">
                    <?= csrf_field() ?>
                    <input type="text" name="coupon_code" class="form-control" placeholder="Coupon code" value="<?= e($summary['coupon_code'] ?? '') ?>" style="min-width:140px; flex:1;">
                    <button type="submit" class="btn btn-outline">Apply</button>
                </form>
                <?php if (!empty($summary['coupon_code'])): ?>
                    <form action="<?= url('/cart/coupon/remove') ?>" method="POST" style="display:inline;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline" style="color:#dc2626; border-color:#dc2626;">Remove coupon</button>
                    </form>
                <?php endif; ?>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:0.5rem; margin-top:1.5rem;">
                <span style="color:var(--muted);">Subtotal</span>
                <span style="text-align:right;"><?= format_price($summary['subtotal']) ?></span>

                <span style="color:var(--muted);">Shipping</span>
                <span style="text-align:right;"><?= $summary['shipping'] === 0.0 ? 'Free' : format_price($summary['shipping']) ?></span>

                <span style="color:var(--muted);">Tax</span>
                <span style="text-align:right;"><?= format_price($summary['tax']) ?></span>

                <?php if ($summary['discount'] > 0): ?>
                    <span style="color:var(--muted);">Discount</span>
                    <span style="text-align:right; color:var(--success);">-<?= format_price($summary['discount']) ?></span>
                <?php endif; ?>

                <span style="font-size:1.25rem; font-weight:700; padding-top:0.75rem; border-top:1px solid var(--border);">Total</span>
                <span style="font-size:1.25rem; font-weight:700; text-align:right; padding-top:0.75rem; border-top:1px solid var(--border);" data-cart-total="true"><?= format_price($summary['total']) ?></span>
            </div>

            <div style="text-align:right; margin-top:1.5rem;">
                <a href="<?= url('/shop') ?>" class="btn btn-outline" style="margin-right:0.5rem;">Keep shopping</a>
                <?php if (\App\Core\Session::has('user_id')): ?>
                    <a href="<?= url('/checkout') ?>" class="btn btn-primary">Continue to checkout</a>
                <?php else: ?>
                    <a href="<?= url('/checkout') ?>" class="btn btn-primary">Sign in to checkout</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
