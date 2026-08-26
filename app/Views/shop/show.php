<?php
$price = (float) $product['price'];
$salePrice = !empty($product['sale_price']) && (float) $product['sale_price'] > 0 ? (float) $product['sale_price'] : null;
$compareAt = !empty($product['compare_at_price']) && (float) $product['compare_at_price'] > 0 ? (float) $product['compare_at_price'] : null;
$displayPrice = ($salePrice && $salePrice < $price) ? $salePrice : $price;
$originalPrice = ($salePrice && $salePrice < $price) ? $price : (($compareAt && $compareAt > $displayPrice) ? $compareAt : null);

$stockStatus = 'In stock';
$stockClass = 'success';
if ((int) $product['stock_qty'] <= 0) {
    $stockStatus = 'Out of stock';
    $stockClass = 'danger';
} elseif ((int) $product['stock_qty'] <= 5) {
    $stockStatus = 'Low stock';
    $stockClass = 'warning';
}
?>

<section class="glass-card mt-4 product-detail" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:2.5rem; padding:2rem;">
    <div class="product-gallery">
        <div class="product-main" style="width:100%; height:clamp(280px,40vw,420px); background:rgba(255,255,255,0.05); border-radius:0.75rem; display:flex; align-items:center; justify-content:center; color:var(--muted); overflow:hidden;">
            <?php if ($thumbnail): ?>
                <img id="product-main-image" src="<?= e(asset($thumbnail)) ?>" alt="<?= e($product['name']) ?>" style="max-height:100%; max-width:100%; object-fit:contain; border-radius:0.75rem;">
            <?php else: ?>
                No image
            <?php endif; ?>
        </div>

        <?php if (count($images) > 1): ?>
            <div class="product-thumbs" style="display:flex; gap:0.5rem; margin-top:1rem; flex-wrap:nowrap; overflow-x:auto;">
                <?php foreach ($images as $img): ?>
                    <?php if (empty($img['file_path'])) continue; ?>
                    <button type="button" class="product-thumb" data-src="<?= e(asset($img['file_path'])) ?>" style="width:72px; height:72px; flex-shrink:0; padding:0; border:2px solid transparent; border-radius:0.5rem; overflow:hidden; background:transparent; cursor:pointer;">
                        <img src="<?= e(asset($img['file_path'])) ?>" alt="" style="width:100%; height:100%; object-fit:cover; border-radius:0.4rem;">
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="product-info" style="display:flex; flex-direction:column; justify-content:center;">
        <p class="product-category" style="color:var(--muted); margin:0 0 0.5rem; font-size:0.95rem; text-transform:uppercase; letter-spacing:0.04em;"><?= e($product['category_name'] ?? '') ?></p>
        <h1 class="product-title" style="margin:0 0 1rem; font-size:clamp(1.75rem,4vw,2.5rem); line-height:1.1;"><?= e($product['name']) ?></h1>

        <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1rem; flex-wrap:wrap;">
            <span class="stars" style="--rating: <?= (float) $reviewStats['average'] ?>;" aria-label="<?= number_format((float) $reviewStats['average'], 1) ?> stars"></span>
            <span style="font-weight:600;"><?= number_format((float) $reviewStats['average'], 1) ?></span>
            <span style="color:var(--muted); font-size:0.9rem;">· <?= (int) $reviewStats['count'] ?> review<?= (int) $reviewStats['count'] === 1 ? '' : 's' ?></span>
        </div>

        <div class="product-price" style="font-size:clamp(1.75rem,4vw,2.25rem); font-weight:700; margin:0.5rem 0;">
            <?= e(config('app.currency_symbol')) ?><?= number_format($displayPrice, 2) ?>
            <?php if ($originalPrice): ?>
                <del style="color:var(--muted); font-size:1.15rem; font-weight:500; margin-left:0.5rem;"><?= e(config('app.currency_symbol')) ?><?= number_format($originalPrice, 2) ?></del>
            <?php endif; ?>
        </div>

        <div style="display:flex; align-items:center; gap:0.5rem; margin:0.75rem 0; color:var(--muted); font-size:0.85rem;">
            <span>We accept</span>
            <svg width="32" height="20" viewBox="0 0 48 30" fill="none" aria-label="Visa" style="vertical-align:middle;">
                <rect width="48" height="30" rx="3" fill="#1A1F71"/>
                <path d="M20.6 21.5l3.6-14h-5.5l-5.8 14h7.7z" fill="#F7B600" opacity="0.9"/>
                <path d="M32.5 7.5c-1.5 0-2.6.3-3.4 1.1l-.3.3c.8-.6 1.8-.8 2.7-.8 1.7 0 2.7.9 2.7 2.3 0 .3 0 .5-.1.8l-2.1 8.2h4.2l2.7-10.5c-1.3-1.5-3.4-1.4-6.4-1.4z" fill="#fff"/>
            </svg>
            <svg width="32" height="20" viewBox="0 0 48 30" fill="none" aria-label="Mastercard" style="vertical-align:middle;">
                <rect width="48" height="30" rx="3" fill="#F2F2F2"/>
                <circle cx="18" cy="15" r="9" fill="#EB001B" opacity="0.9"/>
                <circle cx="30" cy="15" r="9" fill="#F79E1B" opacity="0.9"/>
            </svg>
            <svg width="32" height="20" viewBox="0 0 48 30" fill="none" aria-label="American Express" style="vertical-align:middle;">
                <rect width="48" height="30" rx="3" fill="#006FCF"/>
                <text x="24" y="19" text-anchor="middle" font-size="9" fill="#fff" font-weight="bold" font-family="Arial, sans-serif">AMEX</text>
            </svg>
        </div>

        <p class="product-description" style="color:var(--muted); line-height:1.7; margin:1rem 0;"><?= e($product['description']) ?></p>

        <div class="product-meta" style="display:flex; flex-wrap:wrap; gap:1rem; align-items:center; margin:0.5rem 0 1rem; font-size:0.9rem; color:var(--muted);">
            <span>Sold by <strong style="color:var(--text);"><?= e($product['vendor_name'] ?? 'The New Age Marketplace') ?></strong></span>
            <?php if (!empty($product['sku'])): ?>
                <span>SKU <strong style="color:var(--text);"><?= e($product['sku']) ?></strong></span>
            <?php endif; ?>
            <span class="product-stock product-stock-<?= $stockClass ?>"> <?= $stockStatus ?></span>
        </div>

        <?php if (!empty($affiliateVendorName)): ?>
            <p style="margin:0 0 1rem; font-size:0.9rem;">
                Referred by <a href="<?= url('/vendor/' . $vendorSlug) ?>"><?= e($affiliateVendorName) ?></a>
            </p>
        <?php endif; ?>

        <?php if ((int) $product['stock_qty'] > 0): ?>
            <form action="<?= url('/cart/add') ?>" method="POST" data-ajax-cart="add" style="margin-top:0.5rem;">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                <input type="hidden" name="affiliate_vendor_id" value="<?= (int) ($affiliateVendorId ?? 0) ?>">
                <input type="hidden" name="return" value="<?= url('/product/' . $product['slug'] . (empty($vendorSlug) ? '' : '?vendor=' . $vendorSlug)) ?>">
                <div style="display:flex; gap:0.75rem; align-items:center; flex-wrap:wrap;">
                    <label for="qty" style="color:var(--muted); font-size:0.9rem;">Quantity</label>
                    <input class="form-control" type="number" id="qty" name="quantity" value="1" min="1" max="<?= (int) $product['stock_qty'] ?>" style="max-width:80px;">
                    <button type="submit" class="btn btn-primary btn-lg" style="flex:1; min-width:180px;">Add to cart</button>
                </div>
            </form>
        <?php else: ?>
            <button type="button" class="btn btn-outline btn-lg" disabled style="margin-top:0.5rem; width:100%;">Out of stock</button>
        <?php endif; ?>
    </div>
</section>

<?php if ($canReview): ?>
<section class="glass-card mt-4" style="padding:1.5rem;">
    <h2 class="section-title" style="font-size:1.25rem; margin-bottom:1rem;">Write a review</h2>
    <form action="<?= url('/product/' . $product['slug'] . '/review') ?>" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="order_id" value="<?= (int) $reviewOrderId ?>">
        <div class="form-group">
            <label for="rating">Rating</label>
            <select id="rating" name="rating" class="form-control" style="max-width:120px;" required>
                <option value="5">5 - Excellent</option>
                <option value="4">4 - Good</option>
                <option value="3">3 - Average</option>
                <option value="2">2 - Poor</option>
                <option value="1">1 - Terrible</option>
            </select>
        </div>
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" class="form-control" placeholder="Summarize your experience">
        </div>
        <div class="form-group">
            <label for="body">Review</label>
            <textarea id="body" name="body" class="form-control" rows="3" required placeholder="What did you like or dislike?"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit review</button>
    </form>
</section>
<?php endif; ?>

<section class="glass-card mt-4" style="padding:1.5rem;">
    <h2 class="section-title" style="font-size:1.25rem; margin-bottom:1rem;">Customer reviews</h2>
    <?php if (empty($reviews)): ?>
        <p style="color:var(--muted);">No reviews yet.</p>
    <?php else: ?>
        <?php foreach ($reviews as $r): ?>
            <div style="border-bottom:1px solid var(--border); padding:1rem 0;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:0.5rem;">
                    <div>
                        <strong><?= e($r['first_name'] ?? $r['email']) ?> <?= e($r['last_name'] ?? '') ?></strong>
                        <?php if ($r['is_verified_purchase']): ?><span class="badge" style="background:var(--success); color:#fff; margin-left:0.5rem;">Verified</span><?php endif; ?>
                    </div>
                    <span style="color:var(--muted); font-size:0.85rem;"><?= e($r['created_at']) ?></span>
                </div>
                <?php $rating = min(5, max(0, (int) ($r['rating'] ?? 0))); ?>
                <div style="color:#f59e0b; margin:0.25rem 0;"><?= str_repeat('★', $rating) ?><?= str_repeat('☆', 5 - $rating) ?></div>
                <?php if ($r['title']): ?><h4 style="margin:0.25rem 0; font-size:1rem;"><?= e($r['title']) ?></h4><?php endif; ?>
                <p style="margin:0; color:var(--text);"><?= e($r['body']) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php if (!empty($related)): ?>
<section class="mt-4" style="padding:1.5rem 0;">
    <div class="section-header" style="margin-bottom:1rem;">
        <h2 class="section-title" style="font-size:1.5rem;">Others also viewed</h2>
    </div>
    <div class="card-grid" style="gap:1.5rem;">
        <?php foreach ($related as $p): ?>
            <div class="glass-card product-card" style="display:flex; flex-direction:column; justify-content:space-between;">
                <div>
                    <a href="<?= url('/product/' . $p['slug']) ?>" style="display:block;">
                        <div class="img-preview" style="height:180px; border-radius:0.75rem; overflow:hidden;">
                            <?= $p['thumbnail'] ? '<img src="' . e(asset($p['thumbnail'])) . '" alt="" style="width:100%; height:100%; object-fit:cover; border-radius:0.5rem;">' : 'No image' ?>
                        </div>
                    </a>
                    <h3 style="margin:0.75rem 0 0.25rem; font-size:1rem;"><a href="<?= url('/product/' . $p['slug']) ?>"><?= e($p['name']) ?></a></h3>
                    <p style="color:var(--muted); font-size:0.85rem; margin:0 0 0.5rem;"><?= e($p['category_name'] ?? '') ?> · <?= e($p['vendor_name'] ?? 'Marketplace') ?></p>
                    <p style="font-weight:700; font-size:1.1rem;"><?= e(config('app.currency_symbol')) ?><?= number_format((float) $p['price'], 2) ?></p>
                </div>
                <a class="btn btn-outline" href="<?= url('/product/' . $p['slug']) ?>" style="margin-top:0.75rem;">View product</a>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($hot)): ?>
<section class="mt-4" style="padding:1.5rem 0;">
    <div class="section-header" style="margin-bottom:1rem;">
        <h2 class="section-title" style="font-size:1.5rem;">Hot items right now</h2>
    </div>
    <div class="card-grid" style="gap:1.5rem;">
        <?php foreach ($hot as $p): ?>
            <div class="glass-card product-card" style="display:flex; flex-direction:column; justify-content:space-between;">
                <div>
                    <a href="<?= url('/product/' . $p['slug']) ?>" style="display:block;">
                        <div class="img-preview" style="height:180px; border-radius:0.75rem; overflow:hidden;">
                            <?= $p['thumbnail'] ? '<img src="' . e(asset($p['thumbnail'])) . '" alt="" style="width:100%; height:100%; object-fit:cover; border-radius:0.5rem;">' : 'No image' ?>
                        </div>
                    </a>
                    <h3 style="margin:0.75rem 0 0.25rem; font-size:1rem;"><a href="<?= url('/product/' . $p['slug']) ?>"><?= e($p['name']) ?></a></h3>
                    <p style="color:var(--muted); font-size:0.85rem; margin:0 0 0.5rem;"><?= e($p['category_name'] ?? '') ?> · <?= e($p['vendor_name'] ?? 'Marketplace') ?></p>
                    <p style="font-weight:700; font-size:1.1rem;"><?= e(config('app.currency_symbol')) ?><?= number_format((float) $p['price'], 2) ?></p>
                </div>
                <a class="btn btn-outline" href="<?= url('/product/' . $p['slug']) ?>" style="margin-top:0.75rem;">View product</a>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
