<section class="glass-card mt-4" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; padding: 2rem;">
    <div>
        <div style="width:100%; height:360px; background:rgba(255,255,255,0.05); border-radius:0.75rem; display:flex; align-items:center; justify-content:center; color:var(--muted);">
            <?= $thumbnail ? '<img src="' . e(asset($thumbnail)) . '" alt="" style="max-height:100%; max-width:100%; border-radius:0.75rem;">' : 'No image' ?>
        </div>
    </div>

    <div style="display:flex; flex-direction:column; justify-content:center;">
        <p style="color:var(--muted);"><?= e($product['category_name'] ?? '') ?></p>
        <h1 style="margin:0 0 1rem;"><?= e($product['name']) ?></h1>
        <p style="color:var(--muted);"><?= e($product['description']) ?></p>

        <p style="font-size:1.75rem; font-weight:700; margin:1rem 0;">
            <?= e(config('app.currency_symbol')) ?><?= number_format((float) $product['price'], 2) ?>
        </p>

        <p style="color:var(--muted); font-size:0.9rem;">
            Sold by <?= e($product['vendor_name'] ?? 'The New Age Marketplace') ?>
            <?php if (!empty($affiliateVendorName)): ?>
                · Referred by <a href="<?= url('/vendor/' . $vendorSlug) ?>"><?= e($affiliateVendorName) ?></a>
            <?php endif; ?>
        </p>

        <?php if (count($images) > 1): ?>
            <div style="display:flex; gap:0.5rem; margin:1rem 0;">
                <?php foreach ($images as $img): ?>
                    <div style="width:60px; height:60px; background:rgba(255,255,255,0.05); border-radius:0.4rem; overflow:hidden; flex-shrink:0;">
                        <?= !empty($img['file_path']) ? '<img src="' . e(asset($img['file_path'])) . '" alt="" style="width:100%; height:100%; object-fit:cover; border-radius:0.4rem;">' : '<span style="color:var(--muted); font-size:0.6rem;">No image</span>' ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div style="margin:1rem 0;">
            <span style="font-size:1.25rem; font-weight:700;"><?= number_format((float) $reviewStats['average'], 1) ?></span>
            <span class="stars" style="--rating: <?= (float) $reviewStats['average'] ?>;" aria-label="<?= number_format((float) $reviewStats['average'], 1) ?> stars"></span>
            <span style="color:var(--muted); font-size:0.9rem;">· <?= (int) $reviewStats['count'] ?> review<?= (int) $reviewStats['count'] === 1 ? '' : 's' ?></span>
        </div>

        <form action="<?= url('/cart/add') ?>" method="POST" data-ajax-cart="add" style="margin-top:1.5rem;">
            <?= csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
            <input type="hidden" name="affiliate_vendor_id" value="<?= (int) ($affiliateVendorId ?? 0) ?>">
            <input type="hidden" name="return" value="<?= url('/product/' . $product['slug'] . (empty($vendorSlug) ? '' : '?vendor=' . $vendorSlug)) ?>">
            <label for="qty" style="color:var(--muted);">Quantity</label>
            <div style="display:flex; gap:0.75rem; align-items:center;">
                <input class="form-control" type="number" id="qty" name="quantity" value="1" min="1" max="<?= (int) $product['stock_qty'] ?>" style="max-width:80px;">
                <button type="submit" class="btn btn-primary">Add to cart</button>
            </div>
        </form>
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
                <div style="color:#f59e0b; margin:0.25rem 0;"><?= str_repeat('★', (int) $r['rating']) ?><?= str_repeat('☆', 5 - (int) $r['rating']) ?></div>
                <?php if ($r['title']): ?><h4 style="margin:0.25rem 0; font-size:1rem;"><?= e($r['title']) ?></h4><?php endif; ?>
                <p style="margin:0; color:var(--text);"><?= e($r['body']) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
