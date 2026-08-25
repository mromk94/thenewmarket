<section class="hero" style="padding: 2rem 0;">
    <h1>Vendors</h1>
    <p>Discover trusted sellers and their storefronts.</p>
</section>

<section class="card-grid mt-4">
    <?php if (empty($vendors)): ?>
        <p class="text-center" style="color:var(--muted);">No vendors yet.</p>
    <?php else: ?>
        <?php foreach ($vendors as $v): ?>
            <div class="glass-card" style="display:flex; flex-direction:column; justify-content:space-between;">
                <div>
                    <div style="width:80px; height:80px; border-radius:50%; background:rgba(255,255,255,0.08); display:flex; align-items:center; justify-content:center; font-size:1.75rem; margin-bottom:0.75rem;">
                        <?= strtoupper(substr((string) $v['business_name'], 0, 1)) ?>
                    </div>
                    <h3><a href="<?= url('/vendor/' . $v['slug']) ?>"><?= e($v['business_name']) ?></a></h3>
                    <p style="color:var(--muted); font-size:0.9rem;"><?= e($v['description'] ?: 'Curated products and affiliate picks.') ?></p>
                </div>
                <a href="<?= url('/vendor/' . $v['slug']) ?>" class="btn btn-outline" style="margin-top:0.75rem;">Visit store</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
