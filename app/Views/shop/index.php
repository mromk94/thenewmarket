<section class="hero" style="padding: 2rem 0;">
    <h1>Shop</h1>
    <p>Discover products from trusted vendors and affiliates.</p>
</section>

<section class="glass-card mt-4" style="padding: 1rem;">
    <form method="GET" action="<?= url('/shop') ?>" style="display:flex; gap:0.75rem; flex-wrap:wrap; align-items:center;">
        <input class="form-control" type="text" name="search" placeholder="Search products..." value="<?= e($search) ?>" style="max-width:260px;">

        <select class="form-control" name="category" style="max-width:180px;">
            <option value="">All categories</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= e($c['slug']) ?>" <?= $currentCategory === $c['slug'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <select class="form-control" name="vendor" style="max-width:180px;">
            <option value="">All vendors</option>
            <?php foreach ($vendors as $v): ?>
                <option value="<?= e($v['id']) ?>" <?= (int) ($currentVendor ?? 0) === (int) $v['id'] ? 'selected' : '' ?>><?= e($v['business_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <select class="form-control" name="availability" style="max-width:150px;">
            <option value="" <?= $availability === '' ? 'selected' : '' ?>>All stock</option>
            <option value="in_stock" <?= $availability === 'in_stock' ? 'selected' : '' ?>>In stock</option>
            <option value="low_stock" <?= $availability === 'low_stock' ? 'selected' : '' ?>>Low stock</option>
            <option value="out_of_stock" <?= $availability === 'out_of_stock' ? 'selected' : '' ?>>Out of stock</option>
        </select>

        <div style="display:flex; gap:0.5rem; align-items:center; flex:1 1 180px; min-width:180px;">
            <input class="form-control" type="number" step="0.01" min="0" name="min_price" placeholder="Min price" value="<?= e($minPrice) ?>" style="max-width:120px;">
            <span style="color:var(--muted);">-</span>
            <input class="form-control" type="number" step="0.01" min="0" name="max_price" placeholder="Max price" value="<?= e($maxPrice) ?>" style="max-width:120px;">
        </div>

        <select class="form-control" name="sort" style="max-width:160px;">
            <option value="featured" <?= $sort === 'featured' ? 'selected' : '' ?>>Featured</option>
            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: low to high</option>
            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: high to low</option>
            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
        </select>

        <button type="submit" class="btn btn-primary">Filter</button>
    </form>
</section>

<section class="card-grid mt-4">
    <?php if (empty($products)): ?>
        <p class="text-center" style="color:var(--muted);">No products found.</p>
    <?php else: ?>
        <?php foreach ($products as $p): ?>
            <div class="glass-card" style="display:flex; flex-direction:column; justify-content:space-between;">
                <div>
                    <div style="width:100%; height:160px; background:rgba(255,255,255,0.05); border-radius:0.5rem; margin-bottom:0.75rem; display:flex; align-items:center; justify-content:center; color:var(--muted); font-size:0.9rem;">
                        <?= $p['thumbnail'] ? '<img src="' . e(asset($p['thumbnail'])) . '" alt="" style="max-height:100%; max-width:100%; border-radius:0.5rem;">' : 'No image' ?>
                    </div>
                    <h3 style="margin:0 0 0.25rem;"><a href="<?= url('/product/' . $p['slug']) ?>"><?= e($p['name']) ?></a></h3>
                    <p style="color:var(--muted); font-size:0.9rem; margin:0 0 0.5rem;"><?= e($p['category_name'] ?? '') ?> · <?= e($p['vendor_name'] ?? 'Marketplace') ?></p>
                    <p style="font-weight:700; font-size:1.1rem;"><?= e(config('app.currency_symbol')) ?><?= number_format((float) $p['price'], 2) ?></p>
                </div>
                <a class="btn btn-primary" href="<?= url('/product/' . $p['slug']) ?>" style="margin-top:0.75rem;">View product</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
