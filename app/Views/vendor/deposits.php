<?php $title = 'Add Funds'; ?>

<section class="vendor-header">
    <div class="vendor-header-info">
        <div>
            <h1>Add Funds</h1>
            <p>Top up your wallet. Requests are reviewed by an admin.</p>
        </div>
    </div>
    <a href="<?= url('/vendor/wallet') ?>" class="btn btn-outline">Wallet</a>
</section>

<section class="vendor-balance glass-card">
    <div class="vendor-balance-info">
        <p class="vendor-balance-label">Current wallet balance</p>
        <h2 class="vendor-balance-amount"><?= e(config('app.currency_symbol')) ?><?= number_format($balance, 2) ?></h2>
    </div>
</section>

<section class="glass-card mt-4" style="padding:1.5rem;">
    <h3 class="vendor-recent-title" style="margin-bottom:1rem;">Choose a method</h3>
    <?php if (empty($methods)): ?>
        <p style="color:var(--muted);">No top-up methods are available right now.</p>
    <?php else: ?>
        <div class="vendor-method-grid">
            <?php foreach ($methods as $m): ?>
                <div class="vendor-method-card glass-card">
                    <div class="vendor-method-head">
                        <span class="vendor-method-name"><?= e($m['name']) ?></span>
                        <span class="vendor-method-type"><?= e(ucfirst($m['type'])) ?></span>
                    </div>
                    <p class="vendor-method-currency"><?= e($m['currency']) ?></p>
                    <p class="vendor-method-instructions"><?= e($m['instructions'] ?: 'Select this method to see transfer details.') ?></p>
                    <button type="button" class="btn btn-primary" style="width:100%;" data-open-deposit="<?= (int) $m['id'] ?>">Use this method</button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="glass-card mt-4" style="padding:1.5rem;">
    <h3 class="vendor-recent-title" style="margin-bottom:1rem;">Your top-up requests</h3>
    <?php if (empty($deposits)): ?>
        <p style="color:var(--muted);">You have not submitted any top-up requests yet.</p>
    <?php else: ?>
        <div class="vendor-tx-list">
            <?php foreach ($deposits as $d): ?>
                <div class="vendor-tx-item">
                    <div class="vendor-tx-main">
                        <p class="vendor-tx-type"><?= e($d['method_name']) ?></p>
                        <p class="vendor-tx-desc">Ref: <?= e($d['reference'] ?: '—') ?> · <?= e($d['created_at']) ?></p>
                    </div>
                    <div class="vendor-tx-side">
                        <p class="vendor-tx-amount" style="font-weight:700;">+<?= e(config('app.currency_symbol')) ?><?= number_format((float) $d['amount'], 2) ?> <?= e($d['currency']) ?></p>
                        <span class="badge" style="text-transform:capitalize; background:<?= $d['status'] === 'approved' ? 'var(--success)' : ($d['status'] === 'rejected' ? '#dc2626' : '#f59e0b') ?>; color:#fff;"><?= e($d['status']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<div id="deposit-modal" class="modal">
    <div class="glass-card modal-card">
        <button type="button" class="modal-close" data-close-deposit aria-label="Close">×</button>
        <h2 id="modal-title" class="mb-2" style="padding-right:2rem;">Top up</h2>

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
                <button type="submit" class="btn btn-primary" style="flex:1;">Submit request</button>
                <button type="button" class="btn btn-outline" data-close-deposit>Cancel</button>
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
        if (method.wallet_address) details += '<div style="margin:0 0 0.5rem;"><strong>Wallet address:</strong><br><code style="word-break:break-all; background:var(--surface); padding:0.5rem; border-radius:0.35rem; display:block;">' + method.wallet_address + '</code></div>';
        if (method.qr_image) details += '<img src="/assets/' + method.qr_image + '" alt="QR" style="max-width:160px; border-radius:0.5rem;">';
    } else {
        if (method.bank_name) details += '<p style="margin:0 0 0.5rem;"><strong>Bank:</strong> ' + method.bank_name + '</p>';
        if (method.account_name) details += '<p style="margin:0 0 0.5rem;"><strong>Account name:</strong> ' + method.account_name + '</p>';
        if (method.account_number) details += '<p style="margin:0 0 0.5rem;"><strong>Account number:</strong> ' + method.account_number + '</p>';
    }

    if (method.instructions) details += '<p style="margin:0.75rem 0 0; color:var(--muted); font-size:0.9rem;">' + method.instructions + '</p>';

    document.getElementById('modal-details').innerHTML = details;
    document.getElementById('deposit-modal').classList.add('is-open');
    document.body.style.overflow = 'hidden';
}

function closeDepositModal() {
    document.getElementById('deposit-modal').classList.remove('is-open');
    document.body.style.overflow = '';
}

document.querySelectorAll('[data-open-deposit]').forEach(btn => {
    btn.addEventListener('click', function () {
        openDepositModal(parseInt(this.dataset.openDeposit));
    });
});

document.querySelectorAll('[data-close-deposit]').forEach(btn => {
    btn.addEventListener('click', closeDepositModal);
});

document.getElementById('deposit-modal').addEventListener('click', function (e) {
    if (e.target === this) closeDepositModal();
});
</script>
