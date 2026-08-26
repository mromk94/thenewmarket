<?php $title = 'Currencies'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Currencies</h1>
    <p>Manage currencies and exchange rates against the default.</p>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <h2 class="mb-2">Add currency</h2>
    <form action="<?= url('/admin/currencies') ?>" method="POST" style="display:grid; gap:1rem;">
        <?= csrf_field() ?>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap:1rem;">
            <div class="form-group" style="margin-bottom:0;">
                <label for="code">Code</label>
                <input type="text" id="code" name="code" class="form-control" placeholder="EUR" maxlength="3" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Euro" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label for="symbol">Symbol</label>
                <input type="text" id="symbol" name="symbol" class="form-control" placeholder="€" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label for="exchange_rate">Rate vs default</label>
                <input type="number" step="0.00000001" id="exchange_rate" name="exchange_rate" class="form-control" value="1" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label for="is_active">Active</label>
                <select id="is_active" name="is_active" class="form-control">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary" style="width:fit-content;">Add currency</button>
    </form>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <h2 class="mb-2">Existing currencies</h2>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                    <th>Code</th>
                    <th>Name</th>
                    <th>Symbol</th>
                    <th>Rate</th>
                    <th>Active</th>
                    <th>Default</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($currencies as $c): ?>
                    <tr style="border-bottom:1px solid var(--border);">
                        <form action="<?= url('/admin/currencies/' . $c['id'] . '/update') ?>" method="POST" id="currency-<?= (int) $c['id'] ?>">
                            <?= csrf_field() ?>
                            <td style="padding:0.5rem 0;"><input type="text" name="code" value="<?= e($c['code']) ?>" class="form-control" style="min-width:70px;" maxlength="3"></td>
                            <td><input type="text" name="name" value="<?= e($c['name']) ?>" class="form-control" style="min-width:120px;"></td>
                            <td><input type="text" name="symbol" value="<?= e($c['symbol']) ?>" class="form-control" style="min-width:60px;"></td>
                            <td><input type="number" step="0.00000001" name="exchange_rate" value="<?= e($c['exchange_rate']) ?>" class="form-control" style="min-width:120px;"></td>
                            <td>
                                <select name="is_active" class="form-control">
                                    <option value="1" <?= (int) $c['is_active'] ? 'selected' : '' ?>>Yes</option>
                                    <option value="0" <?= !(int) $c['is_active'] ? 'selected' : '' ?>>No</option>
                                </select>
                            </td>
                            <td>
                                <?php if ((int) $c['id'] !== (int) $defaultId): ?>
                                    <a href="#" onclick="document.getElementById('default-<?= (int) $c['id'] ?>').submit(); return false;" class="btn btn-outline" style="padding:0.35rem 0.7rem;">Set default</a>
                                <?php else: ?>
                                    <span class="btn btn-primary" style="padding:0.35rem 0.7rem;">Default</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display:flex; gap:0.35rem; flex-wrap:wrap;">
                                    <button type="submit" class="btn btn-outline" style="padding:0.35rem 0.7rem;">Save</button>
                                    <a href="#" onclick="if(confirm('Delete this currency?')) document.getElementById('delete-<?= (int) $c['id'] ?>').submit(); return false;" class="btn btn-outline" style="padding:0.35rem 0.7rem; color:#dc2626; border-color:#dc2626;">Delete</a>
                                </div>
                            </td>
                        </form>
                        <form action="<?= url('/admin/currencies/' . $c['id'] . '/default') ?>" method="POST" id="default-<?= (int) $c['id'] ?>" style="display:none;"><?= csrf_field() ?></form>
                        <form action="<?= url('/admin/currencies/' . $c['id'] . '/delete') ?>" method="POST" id="delete-<?= (int) $c['id'] ?>" style="display:none;"><?= csrf_field() ?></form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
