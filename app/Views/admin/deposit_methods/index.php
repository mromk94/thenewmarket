<?php $title = 'Deposit Methods'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Deposit Methods</h1>
    <p>Configure how vendors can add funds to their wallet.</p>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <h2 style="margin:0;">Methods</h2>
        <a href="<?= url('/admin/deposit-methods/create') ?>" class="btn btn-primary">Add method</a>
    </div>

    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                <th style="padding:0.75rem 0;">Type</th>
                <th>Name</th>
                <th>Currency</th>
                <th>Active</th>
                <th style="text-align:right;"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($methods as $m): ?>
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:0.5rem 0;"><?= e(ucfirst($m['type'])) ?></td>
                    <td><?= e($m['name']) ?></td>
                    <td><?= e($m['currency']) ?></td>
                    <td><?= (int) $m['is_active'] ? 'Yes' : 'No' ?></td>
                    <td style="text-align:right;">
                        <a href="<?= url('/admin/deposit-methods/' . $m['id'] . '/edit') ?>" class="btn btn-outline" style="padding:0.3rem 0.6rem;">Edit</a>
                        <form action="<?= url('/admin/deposit-methods/' . $m['id'] . '/delete') ?>" method="POST" style="display:inline;" onsubmit="return confirm('Delete this method?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline" style="padding:0.3rem 0.6rem; color:#dc2626; border-color:#dc2626;">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
