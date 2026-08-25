<?php $title = 'My Addresses'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>My Addresses</h1>
    <p>Manage your billing and shipping addresses.</p>
</section>

<div class="card-grid" style="grid-template-columns: 1fr 1fr;">
    <div class="glass-card" style="padding:1.5rem;">
        <h2 class="mb-2">Shipping addresses</h2>
        <?php if (empty($shipping)): ?>
            <p style="color:var(--muted);">No shipping addresses saved.</p>
        <?php else: ?>
            <?php foreach ($shipping as $a): ?>
                <div style="border-bottom:1px solid var(--border); padding:0.75rem 0;">
                    <strong><?= e($a['label'] ?: 'Address') ?></strong>
                    <?php if ((int) $a['is_default']): ?> <span class="badge" style="background:var(--primary); color:#fff;">Default</span><?php endif; ?>
                    <p style="margin:0.25rem 0 0; color:var(--muted); font-size:0.9rem;">
                        <?= e($a['first_name'] . ' ' . $a['last_name']) ?><br>
                        <?= e($a['address_line_1']) ?><br>
                        <?= e($a['city']) ?>, <?= e($a['state']) ?> <?= e($a['zip']) ?><br>
                        <?= e($a['country']) ?>
                    </p>
                    <div style="display:flex; gap:0.5rem; margin-top:0.5rem;">
                        <?php if (!(int) $a['is_default']): ?>
                            <form action="<?= url('/account/addresses/' . $a['id'] . '/default') ?>" method="POST" style="display:inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline" style="padding:0.25rem 0.5rem; font-size:0.85rem;">Set default</button>
                            </form>
                        <?php endif; ?>
                        <form action="<?= url('/account/addresses/' . $a['id'] . '/delete') ?>" method="POST" style="display:inline;" onsubmit="return confirm('Delete this address?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline" style="padding:0.25rem 0.5rem; font-size:0.85rem; color:#dc2626; border-color:#dc2626;">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="glass-card" style="padding:1.5rem;">
        <h2 class="mb-2">Billing addresses</h2>
        <?php if (empty($billing)): ?>
            <p style="color:var(--muted);">No billing addresses saved.</p>
        <?php else: ?>
            <?php foreach ($billing as $a): ?>
                <div style="border-bottom:1px solid var(--border); padding:0.75rem 0;">
                    <strong><?= e($a['label'] ?: 'Address') ?></strong>
                    <?php if ((int) $a['is_default']): ?> <span class="badge" style="background:var(--primary); color:#fff;">Default</span><?php endif; ?>
                    <p style="margin:0.25rem 0 0; color:var(--muted); font-size:0.9rem;">
                        <?= e($a['first_name'] . ' ' . $a['last_name']) ?><br>
                        <?= e($a['address_line_1']) ?><br>
                        <?= e($a['city']) ?>, <?= e($a['state']) ?> <?= e($a['zip']) ?><br>
                        <?= e($a['country']) ?>
                    </p>
                    <div style="display:flex; gap:0.5rem; margin-top:0.5rem;">
                        <?php if (!(int) $a['is_default']): ?>
                            <form action="<?= url('/account/addresses/' . $a['id'] . '/default') ?>" method="POST" style="display:inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline" style="padding:0.25rem 0.5rem; font-size:0.85rem;">Set default</button>
                            </form>
                        <?php endif; ?>
                        <form action="<?= url('/account/addresses/' . $a['id'] . '/delete') ?>" method="POST" style="display:inline;" onsubmit="return confirm('Delete this address?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline" style="padding:0.25rem 0.5rem; font-size:0.85rem; color:#dc2626; border-color:#dc2626;">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<section class="glass-card mt-4" style="padding:1.5rem;">
    <h2 class="mb-2">Add address</h2>
    <form action="<?= url('/account/addresses') ?>" method="POST">
        <?= csrf_field() ?>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:1rem;">
            <div class="form-group" style="margin-bottom:0;">
                <label for="type">Type</label>
                <select id="type" name="type" class="form-control">
                    <option value="shipping">Shipping</option>
                    <option value="billing">Billing</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label for="label">Label (e.g. Home, Office)</label>
                <input type="text" id="label" name="label" class="form-control">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label for="first_name">First name</label>
                <input type="text" id="first_name" name="first_name" class="form-control" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label for="last_name">Last name</label>
                <input type="text" id="last_name" name="last_name" class="form-control" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" class="form-control">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label for="address_line_1">Address line 1</label>
                <input type="text" id="address_line_1" name="address_line_1" class="form-control" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label for="address_line_2">Address line 2</label>
                <input type="text" id="address_line_2" name="address_line_2" class="form-control">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label for="city">City</label>
                <input type="text" id="city" name="city" class="form-control" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label for="state">State / Province</label>
                <input type="text" id="state" name="state" class="form-control">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label for="country">Country</label>
                <input type="text" id="country" name="country" class="form-control" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label for="zip">ZIP / Postal</label>
                <input type="text" id="zip" name="zip" class="form-control" required>
            </div>
        </div>
        <label style="display:flex; align-items:center; gap:0.5rem; margin-top:1rem;">
            <input type="checkbox" name="is_default" value="1">
            Set as default for this type
        </label>
        <button type="submit" class="btn btn-primary mt-2">Add address</button>
    </form>
</section>
