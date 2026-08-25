<?php $title = 'Add Funds'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Add Funds</h1>
    <p>Top up your vendor wallet manually. Requests are reviewed by an admin.</p>
</section>

<section class="card-grid mt-4">
    <div class="glass-card" style="padding:1.5rem;">
        <h3 style="margin:0;"><?= e(config('app.currency_symbol')) ?><?= number_format($balance, 2) ?></h3>
        <p style="color:var(--muted); margin:0.25rem 0 0;">Current wallet balance</p>
    </div>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <h2 class="mb-2">Choose a method</h2>
    <?php if (empty($methods)): ?>
        <p style="color:var(--muted);">No top-up methods are available right now.</p>
    <?php else: ?>
        <div class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
            <?php foreach ($methods as $m): ?>
                <div class="glass-card" style="padding:1rem;">
                    <h4><?= e($m['name']) ?></h4>
                    <p style="color:var(--muted); font-size:0.9rem;"><?= e($m['currency']) ?> · <?= e(ucfirst($m['type'])) ?></p>
                    <p style="color:var(--muted); font-size:0.85rem;"><?= e($m['instructions'] ?: 'Select this method to see transfer details.') ?></p>
                    <button type="button" class="btn btn-primary" style="width:100%;" onclick="openDepositModal(<?= (int) $m['id'] ?>)">Use this method</button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="glass-card mt-4" style="padding: 1.5rem;">
    <h2 class="mb-2">Your top-up requests</h2>
    <?php if (empty($deposits)): ?>
        <p style="color:var(--muted);">You have not submitted any top-up requests yet.</p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; color:var(--muted); border-bottom:1px solid var(--border);">
                    <th style="padding:0.75rem 0;">Method</th>
                    <th>Amount</th>
                    <th>Reference</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($deposits as $d): ?>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:0.5rem 0;"><?= e($d['method_name']) ?></td>
                        <td><?= e(config('app.currency_symbol')) ?><?= number_format((float) $d['amount'], 2) ?> <?= e($d['currency']) ?></td>
                        <td><?= e($d['reference'] ?: '—') ?></td>
                        <td><span class="badge" style="text-transform:capitalize; background:<?= $d['status'] === 'approved' ? 'var(--success)' : ($d['status'] === 'rejected' ? '#dc2626' : '#f59e0b') ?>; color:#fff;"><?= e($d['status']) ?></span></td>
                        <td><?= e($d['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<div id="deposit-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center; padding:1rem;">
    <div class="glass-card" style="width:100%; max-width:520px; max-height:90vh; overflow:auto; padding:1.5rem; position:relative;">
        <button type="button" onclick="closeDepositModal()" style="position:absolute; top:1rem; right:1rem; background:transparent; border:none; font-size:1.25rem; cursor:pointer; color:var(--muted);">×</button>
        <h2 id="modal-title" class="mb-2">Top up</h2>

        <div id="modal-details" style="margin-bottom:1rem;"></div>

        <form action="<?= url('/vendor/deposits') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" id="modal-method-id" name="deposit_method_id" value="">

            <div class="form-group">
                <label for="amount">Amount to add</label>
                <input type="number" step="0.01" min="0.01" id="amount" name="amount" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="reference">Transaction reference / memo</label>
                <input type="text" id="reference" name="reference" class="form-control" placeholder="TxID, transfer note, etc.">
            </div>

            <div class="form-group">
                <label for="receipt_image">Receipt / proof of payment</label>
                <input type="file" id="receipt_image" name="receipt_image" class="form-control" accept="image/*">
            </div>

            <p style="color:var(--muted); font-size:0.85rem;">Your request will be reviewed before the funds are added to your wallet.</p>

            <div style="display:flex; gap:0.5rem; margin-top:1rem;">
                <button type="submit" class="btn btn-primary">Submit request</button>
                <button type="button" class="btn btn-outline" onclick="closeDepositModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
const methods = <?= json_encode($methods, JSON_HEX_TAG | JSON_HEX_AMP) ?>;

function openDepositModal(methodId) {
    const method = methods.find(m => parseInt(m.id) === methodId);
    if (!method) return;

    document.getElementById('modal-title').textContent = 'Top up with ' + method.name;
    document.getElementById('modal-method-id').value = method.id;

    let details = '<p style="margin:0 0 0.5rem; color:var(--muted);">Currency: ' + (method.currency || '—') + '</p>';

    if (method.type === 'crypto') {
        if (method.network) details += '<p style="margin:0 0 0.5rem;"><strong>Network:</strong> ' + method.network + '</p>';
        if (method.wallet_address) details += '<div style="margin:0 0 0.5rem;"><strong>Wallet address:</strong><br><code style="word-break:break-all; background:#f3f4f6; padding:0.5rem; border-radius:0.35rem; display:block;">' + method.wallet_address + '</code></div>';
        if (method.qr_image) details += '<img src="/assets/' + method.qr_image + '" alt="QR" style="max-width:160px; border-radius:0.5rem;">';
    } else {
        if (method.bank_name) details += '<p style="margin:0 0 0.5rem;"><strong>Bank:</strong> ' + method.bank_name + '</p>';
        if (method.account_name) details += '<p style="margin:0 0 0.5rem;"><strong>Account name:</strong> ' + method.account_name + '</p>';
        if (method.account_number) details += '<p style="margin:0 0 0.5rem;"><strong>Account number:</strong> ' + method.account_number + '</p>';
    }

    if (method.instructions) details += '<p style="margin:0.75rem 0 0; color:var(--muted); font-size:0.9rem;">' + method.instructions + '</p>';

    document.getElementById('modal-details').innerHTML = details;
    document.getElementById('deposit-modal').style.display = 'flex';
}

function closeDepositModal() {
    document.getElementById('deposit-modal').style.display = 'none';
}
</script>
