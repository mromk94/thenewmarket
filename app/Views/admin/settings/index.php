<?php $title = ucfirst($group) . ' Settings'; ?>

<section class="hero" style="padding: 2rem 0;">
    <h1>Admin Settings</h1>
    <p>Manage <?= e($group) ?> configuration.</p>
</section>

<div class="glass-card mt-4">
    <nav class="admin-tabs" style="display:flex; flex-wrap:wrap; gap:0.5rem; margin-bottom:1.5rem; border-bottom:1px solid var(--border); padding-bottom:1rem;">
        <?php foreach ($groups as $g): ?>
            <a href="<?= url('/admin/settings/' . $g) ?>" class="btn <?= $g === $group ? 'btn-primary' : 'btn-outline' ?>" style="padding:0.4rem 0.9rem; font-size:0.9rem;"><?= ucfirst($g) ?></a>
        <?php endforeach; ?>
    </nav>

    <form action="<?= url('/admin/settings') ?>" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="group" value="<?= e($group) ?>">

        <?php if ($group === 'general'): ?>
            <div class="form-group">
                <label for="s_site_name">Site name</label>
                <input type="text" id="s_site_name" name="s_site_name" class="form-control" value="<?= e($values['site_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_currency">Currency</label>
                <input type="text" id="s_currency" name="s_currency" class="form-control" value="<?= e($values['currency'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_currency_symbol">Currency symbol</label>
                <input type="text" id="s_currency_symbol" name="s_currency_symbol" class="form-control" value="<?= e($values['currency_symbol'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_timezone">Timezone</label>
                <input type="text" id="s_timezone" name="s_timezone" class="form-control" value="<?= e($values['timezone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_contact_email">Contact email</label>
                <input type="email" id="s_contact_email" name="s_contact_email" class="form-control" value="<?= e($values['contact_email'] ?? '') ?>">
            </div>
        <?php elseif ($group === 'branding'): ?>
            <div class="form-group">
                <label for="s_tagline">Tagline</label>
                <input type="text" id="s_tagline" name="s_tagline" class="form-control" value="<?= e($values['tagline'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_description">Site description</label>
                <textarea id="s_description" name="s_description" class="form-control" rows="3"><?= e($values['description'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label for="s_logo_url">Logo URL</label>
                <input type="text" id="s_logo_url" name="s_logo_url" class="form-control" value="<?= e($values['logo_url'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_favicon_url">Favicon URL</label>
                <input type="text" id="s_favicon_url" name="s_favicon_url" class="form-control" value="<?= e($values['favicon_url'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_icon_url">Icon / Apple touch URL</label>
                <input type="text" id="s_icon_url" name="s_icon_url" class="form-control" value="<?= e($values['icon_url'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_sender_name">Sender name</label>
                <input type="text" id="s_sender_name" name="s_sender_name" class="form-control" value="<?= e($values['sender_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_sender_email">Sender email</label>
                <input type="email" id="s_sender_email" name="s_sender_email" class="form-control" value="<?= e($values['sender_email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_support_email">Support email</label>
                <input type="email" id="s_support_email" name="s_support_email" class="form-control" value="<?= e($values['support_email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_phone">Phone</label>
                <input type="text" id="s_phone" name="s_phone" class="form-control" value="<?= e($values['phone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_address">Address</label>
                <textarea id="s_address" name="s_address" class="form-control" rows="2"><?= e($values['address'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label for="s_social_facebook">Facebook</label>
                <input type="url" id="s_social_facebook" name="s_social_facebook" class="form-control" value="<?= e($values['social_facebook'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_social_instagram">Instagram</label>
                <input type="url" id="s_social_instagram" name="s_social_instagram" class="form-control" value="<?= e($values['social_instagram'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_social_twitter">Twitter / X</label>
                <input type="url" id="s_social_twitter" name="s_social_twitter" class="form-control" value="<?= e($values['social_twitter'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_social_linkedin">LinkedIn</label>
                <input type="url" id="s_social_linkedin" name="s_social_linkedin" class="form-control" value="<?= e($values['social_linkedin'] ?? '') ?>">
            </div>
        <?php elseif ($group === 'mail'): ?>
            <div class="form-group">
                <label for="s_mailer">Mailer</label>
                <select id="s_mailer" name="s_mailer" class="form-control">
                    <option value="log" <?= ($values['mailer'] ?? '') === 'log' ? 'selected' : '' ?>>Log (local)</option>
                    <option value="smtp" <?= ($values['mailer'] ?? '') === 'smtp' ? 'selected' : '' ?>>SMTP</option>
                    <option value="mail" <?= ($values['mailer'] ?? '') === 'mail' ? 'selected' : '' ?>>PHP mail()</option>
                </select>
            </div>
            <div class="form-group">
                <label for="s_host">SMTP host</label>
                <input type="text" id="s_host" name="s_host" class="form-control" value="<?= e($values['host'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_port">SMTP port</label>
                <input type="number" id="s_port" name="s_port" class="form-control" value="<?= e($values['port'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_username">SMTP username</label>
                <input type="text" id="s_username" name="s_username" class="form-control" value="<?= e($values['username'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_password">SMTP password</label>
                <input type="password" id="s_password" name="s_password" class="form-control" value="<?= e($values['password'] ?? '') ?>" placeholder="Leave blank to keep current">
            </div>
            <div class="form-group">
                <label for="s_encryption">Encryption</label>
                <select id="s_encryption" name="s_encryption" class="form-control">
                    <option value="tls" <?= ($values['encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS</option>
                    <option value="ssl" <?= ($values['encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                </select>
            </div>
            <div class="form-group">
                <label for="s_from_address">From address</label>
                <input type="email" id="s_from_address" name="s_from_address" class="form-control" value="<?= e($values['from_address'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_from_name">From name</label>
                <input type="text" id="s_from_name" name="s_from_name" class="form-control" value="<?= e($values['from_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_reply_to">Reply-to</label>
                <input type="email" id="s_reply_to" name="s_reply_to" class="form-control" value="<?= e($values['reply_to'] ?? '') ?>">
            </div>
        <?php elseif ($group === 'security'): ?>
            <div class="form-group">
                <label for="s_session_lifetime">Session lifetime (minutes)</label>
                <input type="number" id="s_session_lifetime" name="s_session_lifetime" class="form-control" value="<?= e($values['session_lifetime'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_login_attempts">Login attempts before lockout</label>
                <input type="number" id="s_login_attempts" name="s_login_attempts" class="form-control" value="<?= e($values['login_attempts'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_login_decay">Lockout duration (seconds)</label>
                <input type="number" id="s_login_decay" name="s_login_decay" class="form-control" value="<?= e($values['login_decay'] ?? '') ?>">
            </div>
        <?php elseif ($group === 'seo'): ?>
            <div class="form-group">
                <label for="s_default_title">Default title</label>
                <input type="text" id="s_default_title" name="s_default_title" class="form-control" value="<?= e($values['default_title'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_default_description">Default description</label>
                <textarea id="s_default_description" name="s_default_description" class="form-control" rows="3"><?= e($values['default_description'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label for="s_canonical_url">Canonical URL</label>
                <input type="url" id="s_canonical_url" name="s_canonical_url" class="form-control" value="<?= e($values['canonical_url'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="s_og_image">Open Graph image URL</label>
                <input type="url" id="s_og_image" name="s_og_image" class="form-control" value="<?= e($values['og_image'] ?? '') ?>">
            </div>
        <?php else: ?>
            <p style="color:var(--muted);">No fields defined for this group yet.</p>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary">Save <?= ucfirst($group) ?> settings</button>
    </form>

    <?php if ($group === 'mail'): ?>
    <div class="glass-card mt-4">
        <h3>Send test email</h3>
        <form action="<?= url('/admin/settings/mail/test') ?>" method="POST" style="display:flex; gap:0.5rem; flex-wrap:wrap; align-items:flex-end;">
            <?= csrf_field() ?>
            <div style="flex:1; min-width:220px;">
                <label for="test_to">Recipient</label>
                <input type="email" id="test_to" name="to" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-outline">Send test email</button>
        </form>
    </div>
    <?php endif; ?>
</div>
