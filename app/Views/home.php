<?php if (!empty($heroSlides)): ?>
<section class="hero-slider" data-hero-slider style="width:100vw; margin-left:calc(-50vw + 50%); margin-right:calc(-50vw + 50%);">
    <div class="hero-track">
        <?php foreach ($heroSlides as $i => $p): ?>
            <div class="hero-slide <?= $p['thumbnail'] ? '' : 'no-image' ?>">
                <div class="hero-slide-content">
                    <div style="display:flex; gap:0.5rem; margin-bottom:1rem; flex-wrap:wrap; justify-content:<?= $p['thumbnail'] ? 'flex-start' : 'center' ?>;">
                        <?php if ((int) $p['featured']): ?>
                            <span class="badge badge-featured">Featured</span>
                        <?php endif; ?>
                        <?php if ($p['compare_at_price'] && (float) $p['compare_at_price'] > (float) $p['price']): ?>
                            <span class="badge badge-sale">On Sale</span>
                        <?php endif; ?>
                    </div>
                    <h2><?= e($p['name']) ?></h2>
                    <p><?= e($p['short_description'] ?: $p['description'] ?: 'A premium pick from our marketplace.') ?></p>
                    <div class="hero-price">
                        <?= e(config('app.currency_symbol')) ?><?= number_format((float) $p['price'], 2) ?>
                        <?php if ($p['compare_at_price'] && (float) $p['compare_at_price'] > (float) $p['price']): ?>
                            <del><?= e(config('app.currency_symbol')) ?><?= number_format((float) $p['compare_at_price'], 2) ?></del>
                        <?php endif; ?>
                    </div>
                    <a href="<?= url('/product/' . $p['slug']) ?>" class="btn btn-primary btn-lg" style="font-size:1.05rem;">Shop now</a>
                </div>
                <?php if ($p['thumbnail']): ?>
                <div class="hero-image">
                    <img src="<?= e(asset($p['thumbnail'])) ?>" alt="<?= e($p['name']) ?>" loading="<?= $i === 0 ? 'eager' : 'lazy' ?>">
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <button class="hero-arrow prev" type="button" data-hero-prev aria-label="Previous slide">&#10094;</button>
    <button class="hero-arrow next" type="button" data-hero-next aria-label="Next slide">&#10095;</button>

    <div class="hero-dots">
        <?php foreach ($heroSlides as $i => $p): ?>
            <button class="hero-dot <?= $i === 0 ? 'active' : '' ?>" data-hero-dot aria-label="Go to slide <?= $i + 1 ?>"></button>
        <?php endforeach; ?>
    </div>
</section>
<?php else: ?>
<section class="hero">
    <h1><?= e(config('app.name')) ?></h1>
    <p class="hero-subtitle">A premium place to discover curated products from trusted vendors and affiliates.</p>
    <a href="<?= url('/shop') ?>" class="btn btn-primary btn-lg" style="margin-top:1rem;">Browse all products</a>
</section>
<?php endif; ?>

<div class="trust-bar">
    <span>Free shipping over $50</span>
    <span>Secure checkout</span>
    <span>Verified vendors</span>
    <span>Affiliate rewards</span>
</div>

<?php if (!empty($featured)): ?>
<section class="mt-4">
    <div class="section-header">
        <h2 class="section-title">Featured Products</h2>
        <a href="<?= url('/shop') ?>" class="btn btn-outline" style="padding:0.4rem 0.9rem;">View all</a>
    </div>
    <div class="carousel" data-carousel>
        <button class="carousel-nav prev" data-carousel-prev aria-label="Previous">&#10094;</button>
        <div class="carousel-track">
            <?php foreach ($featured as $p): ?>
                <div class="carousel-card glass-card product-card">
                    <div class="badge-wrap">
                        <?php if ((int) $p['featured']): ?>
                            <span class="badge badge-featured">Featured</span>
                        <?php endif; ?>
                        <?php if ($p['compare_at_price'] && (float) $p['compare_at_price'] > (float) $p['price']): ?>
                            <span class="badge badge-sale">On Sale</span>
                        <?php endif; ?>
                    </div>
                    <a href="<?= url('/product/' . $p['slug']) ?>" class="img-preview" style="display:block;">
                        <?= $p['thumbnail'] ? '<img src="' . e(asset($p['thumbnail'])) . '" alt="" loading="lazy">' : '<img class="placeholder-img" src="' . e(asset('images/placeholder-product.svg')) . '" alt="" loading="lazy">' ?>
                    </a>
                    <div class="card-body">
                        <h3><a href="<?= url('/product/' . $p['slug']) ?>"><?= e($p['name']) ?></a></h3>
                        <p style="color:var(--muted); font-size:0.85rem; margin:0 0 0.25rem;"><?= e($p['category_name'] ?? '') ?> · <?= e($p['vendor_name'] ?? 'Marketplace') ?></p>
                        <div class="price-row">
                            <span class="price"><?= e(config('app.currency_symbol')) ?><?= number_format((float) $p['price'], 2) ?></span>
                            <?php if ($p['compare_at_price'] && (float) $p['compare_at_price'] > (float) $p['price']): ?>
                                <span class="compare-price"><?= e(config('app.currency_symbol')) ?><?= number_format((float) $p['compare_at_price'], 2) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-nav next" data-carousel-next aria-label="Next">&#10095;</button>
    </div>
</section>
<?php endif; ?>

<?php
    $categoryIcons = [
        'electronics' => '⚡',
        'smartphones' => '📱',
        'laptops-computers' => '💻',
        'audio' => '🎧',
        'wearables' => '⌚',
        'fashion' => '👕',
        'men' => '👔',
        'women' => '👗',
        'home' => '🏠',
        'furniture' => '🛋️',
        'kitchen' => '🍳',
        'beauty-health' => '💄',
        'sports-outdoors' => '🏃',
        'toys-games' => '🎲',
        'automotive' => '🚗',
    ];
?>

<section class="mt-4">
    <div class="section-header">
        <h2 class="section-title">Shop by Category</h2>
    </div>
    <div class="card-grid category-grid">
        <?php foreach ($categories as $c): ?>
            <?php $icon = $categoryIcons[$c['slug']] ?? '🛍️'; ?>
            <a href="<?= url('/shop?category=' . $c['slug']) ?>" class="glass-card category-card" style="text-align:center;">
                <div class="category-icon"><?= $icon ?></div>
                <h3 style="margin:0.5rem 0 0; font-size:1rem;"><?= e($c['name']) ?></h3>
                <p style="margin:0; color:var(--muted); font-size:0.8rem;"><?= e($c['description'] ?: 'Explore products') ?></p>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<?php if (!empty($justAdded)): ?>
<section class="mt-4 just-added-section">
    <div class="section-header">
        <div>
            <h2 class="section-title" style="font-size:1.25rem;">Just Added</h2>
            <p style="color:var(--muted); margin:0; font-size:0.9rem;">The freshest drops right now</p>
        </div>
        <a href="<?= url('/shop') ?>" class="btn btn-outline" style="padding:0.4rem 0.9rem;">View all</a>
    </div>
    <div class="just-added-grid">
        <?php foreach ($justAdded as $p): ?>
            <a href="<?= url('/product/' . $p['slug']) ?>" class="glass-card just-added-card">
                <div class="img-preview" style="height:140px;">
                    <?= $p['thumbnail'] ? '<img src="' . e(asset($p['thumbnail'])) . '" alt="" loading="lazy">' : '<img class="placeholder-img" src="' . e(asset('images/placeholder-product.svg')) . '" alt="" loading="lazy">' ?>
                    <span class="badge badge-affiliate" style="position:absolute; top:0.6rem; left:0.6rem;">New</span>
                </div>
                <div class="card-body" style="padding:0.75rem 0 0;">
                    <h3 style="font-size:0.95rem; margin:0 0 0.15rem;"><?= e($p['name']) ?></h3>
                    <p style="color:var(--muted); font-size:0.8rem; margin:0 0 0.35rem;"><?= e($p['category_name'] ?? '') ?></p>
                    <span class="price" style="font-size:1rem;"><?= e(config('app.currency_symbol')) ?><?= number_format((float) $p['price'], 2) ?></span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($newest)): ?>
<section class="mt-4">
    <div class="section-header">
        <h2 class="section-title">New Arrivals</h2>
        <a href="<?= url('/shop') ?>" class="btn btn-outline" style="padding:0.4rem 0.9rem;">View all</a>
    </div>
    <div class="carousel" data-carousel>
        <button class="carousel-nav prev" data-carousel-prev aria-label="Previous">&#10094;</button>
        <div class="carousel-track">
            <?php foreach ($newest as $p): ?>
                <div class="carousel-card glass-card product-card">
                    <div class="badge-wrap">
                        <span class="badge badge-affiliate">New</span>
                        <?php if ($p['compare_at_price'] && (float) $p['compare_at_price'] > (float) $p['price']): ?>
                            <span class="badge badge-sale">On Sale</span>
                        <?php endif; ?>
                    </div>
                    <a href="<?= url('/product/' . $p['slug']) ?>" class="img-preview" style="display:block;">
                        <?= $p['thumbnail'] ? '<img src="' . e(asset($p['thumbnail'])) . '" alt="" loading="lazy">' : '<img class="placeholder-img" src="' . e(asset('images/placeholder-product.svg')) . '" alt="" loading="lazy">' ?>
                    </a>
                    <div class="card-body">
                        <h3><a href="<?= url('/product/' . $p['slug']) ?>"><?= e($p['name']) ?></a></h3>
                        <p style="color:var(--muted); font-size:0.85rem; margin:0 0 0.25rem;"><?= e($p['category_name'] ?? '') ?> · <?= e($p['vendor_name'] ?? 'Marketplace') ?></p>
                        <div class="price-row">
                            <span class="price"><?= e(config('app.currency_symbol')) ?><?= number_format((float) $p['price'], 2) ?></span>
                            <?php if ($p['compare_at_price'] && (float) $p['compare_at_price'] > (float) $p['price']): ?>
                                <span class="compare-price"><?= e(config('app.currency_symbol')) ?><?= number_format((float) $p['compare_at_price'], 2) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-nav next" data-carousel-next aria-label="Next">&#10095;</button>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($onSale)): ?>
<section class="mt-4" style="background: linear-gradient(120deg, #fff0f3, #fff); padding: 2.5rem 0; width:100vw; margin-left:calc(-50vw + 50%); margin-right:calc(-50vw + 50%);">
    <div class="section-header" style="max-width:1200px; margin:0 auto 1rem;">
        <h2 class="section-title" style="color:#b91c1c;">On Sale Now</h2>
        <a href="<?= url('/shop') ?>" class="btn btn-outline" style="padding:0.4rem 0.9rem;">Shop deals</a>
    </div>
    <div class="carousel" data-carousel style="max-width:1200px; margin:0 auto;">
        <button class="carousel-nav prev" data-carousel-prev aria-label="Previous">&#10094;</button>
        <div class="carousel-track">
            <?php foreach ($onSale as $p): ?>
                <div class="carousel-card glass-card product-card">
                    <div class="badge-wrap">
                        <span class="badge badge-sale">On Sale</span>
                    </div>
                    <a href="<?= url('/product/' . $p['slug']) ?>" class="img-preview" style="display:block;">
                        <?= $p['thumbnail'] ? '<img src="' . e(asset($p['thumbnail'])) . '" alt="" loading="lazy">' : '<img class="placeholder-img" src="' . e(asset('images/placeholder-product.svg')) . '" alt="" loading="lazy">' ?>
                    </a>
                    <div class="card-body">
                        <h3><a href="<?= url('/product/' . $p['slug']) ?>"><?= e($p['name']) ?></a></h3>
                        <p style="color:var(--muted); font-size:0.85rem; margin:0 0 0.25rem;"><?= e($p['category_name'] ?? '') ?> · <?= e($p['vendor_name'] ?? 'Marketplace') ?></p>
                        <div class="price-row">
                            <span class="price" style="color:#b91c1c;"><?= e(config('app.currency_symbol')) ?><?= number_format((float) $p['price'], 2) ?></span>
                            <span class="compare-price"><?= e(config('app.currency_symbol')) ?><?= number_format((float) $p['compare_at_price'], 2) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-nav next" data-carousel-next aria-label="Next">&#10095;</button>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($trending)): ?>
<section class="mt-4">
    <div class="section-header">
        <h2 class="section-title">Trending Now</h2>
        <a href="<?= url('/shop') ?>" class="btn btn-outline" style="padding:0.4rem 0.9rem;">View all</a>
    </div>
    <div class="carousel" data-carousel>
        <button class="carousel-nav prev" data-carousel-prev aria-label="Previous">&#10094;</button>
        <div class="carousel-track">
            <?php foreach ($trending as $p): ?>
                <div class="carousel-card glass-card product-card">
                    <div class="badge-wrap">
                        <span class="badge badge-featured">Trending</span>
                    </div>
                    <a href="<?= url('/product/' . $p['slug']) ?>" class="img-preview" style="display:block;">
                        <?= $p['thumbnail'] ? '<img src="' . e(asset($p['thumbnail'])) . '" alt="" loading="lazy">' : '<img class="placeholder-img" src="' . e(asset('images/placeholder-product.svg')) . '" alt="" loading="lazy">' ?>
                    </a>
                    <div class="card-body">
                        <h3><a href="<?= url('/product/' . $p['slug']) ?>"><?= e($p['name']) ?></a></h3>
                        <p style="color:var(--muted); font-size:0.85rem; margin:0 0 0.25rem;"><?= e($p['category_name'] ?? '') ?> · <?= e($p['vendor_name'] ?? 'Marketplace') ?></p>
                        <div class="price-row">
                            <span class="price"><?= e(config('app.currency_symbol')) ?><?= number_format((float) $p['price'], 2) ?></span>
                            <?php if ($p['compare_at_price'] && (float) $p['compare_at_price'] > (float) $p['price']): ?>
                                <span class="compare-price"><?= e(config('app.currency_symbol')) ?><?= number_format((float) $p['compare_at_price'], 2) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-nav next" data-carousel-next aria-label="Next">&#10095;</button>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($vendors)): ?>
<section class="mt-4">
    <div class="section-header">
        <h2 class="section-title">Featured Vendors</h2>
    </div>
    <div class="card-grid">
        <?php foreach (array_slice($vendors, 0, 4) as $v): ?>
            <div class="glass-card" style="text-align:center;">
                <div style="width:72px; height:72px; border-radius:50%; background:linear-gradient(135deg, #f0f4ff, #fff); display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin:0 auto 0.75rem; color:var(--primary); box-shadow:0 4px 12px rgba(0,0,0,0.06);">
                    <?= strtoupper(substr((string) $v['business_name'], 0, 1)) ?>
                </div>
                <h3><a href="<?= url('/vendor/' . $v['slug']) ?>"><?= e($v['business_name']) ?></a></h3>
                <p style="color:var(--muted); font-size:0.9rem;"><?= e($v['description'] ?: 'Curated products and affiliate picks.') ?></p>
                <a href="<?= url('/vendor/' . $v['slug']) ?>" class="btn btn-outline" style="margin-top:0.5rem;">Visit store</a>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section class="glass-card mt-4" style="text-align:center; padding: 2.5rem 1rem;">
    <h2 class="section-title" style="margin-bottom:0.5rem;">Why Shop With Us</h2>
    <p style="color:var(--muted); max-width:600px; margin:0 auto 1.5rem;">A marketplace built for discovery, trust, and effortless shopping.</p>
    <div class="card-grid">
        <div class="glass-card" style="text-align:center;">
            <h3>Curated</h3>
            <p style="color:var(--muted);">Every product and vendor is reviewed before going live.</p>
        </div>
        <div class="glass-card" style="text-align:center;">
            <h3>Secure</h3>
            <p style="color:var(--muted);">Protected checkout, verified payments, and encrypted sessions.</p>
        </div>
        <div class="glass-card" style="text-align:center;">
            <h3>Affiliate Rewards</h3>
            <p style="color:var(--muted);">Vendors earn commission by promoting trusted products.</p>
        </div>
    </div>
</section>

<section class="mt-4 newsletter">
    <h2 class="section-title">Join the inner circle</h2>
    <p>Get early access to drops, exclusive deals, and vendor stories.</p>
    <form action="<?= url('/shop') ?>" method="GET">
        <input class="form-control" type="email" name="email" placeholder="Your email" required>
        <button type="submit" class="btn btn-primary">Subscribe</button>
    </form>
    <p style="color:var(--muted); font-size:0.8rem; margin-top:1rem;">No spam. Unsubscribe anytime.</p>
</section>

<section class="glass-card mt-4" style="text-align:center; padding: 2.5rem 1rem;">
    <h2 class="section-title" style="margin-bottom:0.5rem;">Want to Sell?</h2>
    <p style="color:var(--muted); max-width:600px; margin:0 auto 1.5rem;">Create your storefront, list your products, and earn affiliate commission from the marketplace.</p>
    <a href="<?= url('/register') ?>" class="btn btn-primary btn-lg">Become a vendor</a>
</section>
