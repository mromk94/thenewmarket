<?php

declare(strict_types=1);

$basePath = dirname(__DIR__);
$envPath = $basePath . '/.env';
$lockPath = $basePath . '/install.lock';
$title = 'Install The New Age Marketplace';

function e(?string $text): string {
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

function envExists(): bool {
    global $envPath;
    return is_file($envPath) && filesize($envPath) > 0;
}

function isInstalled(): bool {
    global $lockPath, $basePath;
    if (is_file($lockPath)) {
        return true;
    }
    if (!envExists()) {
        return false;
    }
    $env = parseEnv();
    if (empty($env['DB_DATABASE']) || empty($env['DB_HOST'])) {
        return false;
    }
    try {
        $pdo = new PDO(
            "mysql:host={$env['DB_HOST']};port={$env['DB_PORT']};dbname={$env['DB_DATABASE']};charset=utf8mb4",
            $env['DB_USERNAME'],
            $env['DB_PASSWORD'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $stmt = $pdo->query("SELECT id FROM users LIMIT 1");
        return (bool) $stmt->fetch();
    } catch (Throwable $e) {
        return false;
    }
}

function parseEnv(): array {
    global $envPath;
    $values = [];
    if (!is_file($envPath)) {
        return $values;
    }
    foreach (file($envPath) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $values[$key] = trim($value, '"\'');
    }
    return $values;
}

function writeEnv(array $data): void {
    global $envPath;
    $lines = [
        'APP_NAME="' . $data['app_name'] . '"',
        'APP_ENV=production',
        'APP_URL=' . $data['app_url'],
        'APP_DEBUG=false',
        'APP_TIMEZONE=' . $data['app_timezone'],
        'APP_CURRENCY=' . $data['app_currency'],
        'APP_CURRENCY_SYMBOL="' . $data['app_currency_symbol'] . '"',
        '',
        'DB_HOST=' . $data['db_host'],
        'DB_PORT=' . $data['db_port'],
        'DB_DATABASE=' . $data['db_database'],
        'DB_USERNAME=' . $data['db_username'],
        'DB_PASSWORD="' . $data['db_password'] . '"',
        '',
        'MAIL_MAILER=' . $data['mail_mailer'],
        'MAIL_HOST=' . $data['mail_host'],
        'MAIL_PORT=' . $data['mail_port'],
        'MAIL_USERNAME=' . $data['mail_username'],
        'MAIL_PASSWORD="' . $data['mail_password'] . '"',
        'MAIL_ENCRYPTION=' . $data['mail_encryption'],
        'MAIL_FROM_ADDRESS=' . $data['mail_from_address'],
        'MAIL_FROM_NAME="' . $data['app_name'] . '"',
        '',
        'PAYMENT_PROVIDER=test',
        '',
        'SECURITY_SESSION_LIFETIME=120',
        'SECURITY_LOGIN_ATTEMPTS=5',
        'SECURITY_LOGIN_DECAY=900',
    ];
    file_put_contents($envPath, implode("\n", $lines) . "\n");
}

function testDb(array $data): ?string {
    try {
        $pdo = new PDO(
            "mysql:host={$data['db_host']};port={$data['db_port']};charset=utf8mb4",
            $data['db_username'],
            $data['db_password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$data['db_database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        return null;
    } catch (Throwable $e) {
        return $e->getMessage();
    }
}

function pdo(array $data): PDO {
    return new PDO(
        "mysql:host={$data['db_host']};port={$data['db_port']};dbname={$data['db_database']};charset=utf8mb4",
        $data['db_username'],
        $data['db_password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
}

function runMigrations(PDO $pdo): void {
    global $basePath;
    $files = glob($basePath . '/database/migrations/*.sql');
    usort($files, 'strnatcmp');
    foreach ($files as $file) {
        $sql = file_get_contents($file);
        if (!$sql) {
            continue;
        }
        $pdo->exec($sql);
    }
}

function createAdmin(PDO $pdo, array $data): void {
    $hash = password_hash($data['admin_password'], PASSWORD_DEFAULT);
    $role = $pdo->prepare("SELECT id FROM roles WHERE name = 'admin' LIMIT 1");
    $role->execute();
    $roleId = (int) $role->fetchColumn();

    $stmt = $pdo->prepare("INSERT INTO users (role_id, email, password_hash, status, email_verified_at, created_at) VALUES (?, ?, ?, 'active', NOW(), NOW())");
    $stmt->execute([$roleId, $data['admin_email'], $hash]);
    $userId = (int) $pdo->lastInsertId();

    $profile = $pdo->prepare("INSERT INTO user_profiles (user_id, first_name, last_name, phone) VALUES (?, 'Admin', 'User', '')");
    $profile->execute([$userId]);

    $wallet = $pdo->prepare("INSERT INTO wallets (user_id, balance, currency) VALUES (?, 0.0000, ?)");
    $wallet->execute([$userId, $data['app_currency']]);
}

function seedSettings(PDO $pdo, array $data): void {
    $groups = [
        ['general', 'site_name', $data['app_name']],
        ['general', 'currency', $data['app_currency']],
        ['general', 'currency_symbol', $data['app_currency_symbol']],
        ['general', 'timezone', $data['app_timezone']],
        ['general', 'contact_email', $data['admin_email']],
        ['branding', 'site_name', $data['app_name']],
        ['branding', 'short_site_name', $data['app_name']],
        ['branding', 'tagline', 'A premium marketplace for curated products and affiliates.'],
        ['branding', 'description', 'Discover curated products from trusted vendors and affiliates.'],
        ['branding', 'logo_url', $data['app_url'] . '/assets/images/logo.svg'],
        ['branding', 'favicon_url', $data['app_url'] . '/assets/images/favicon.svg'],
        ['branding', 'icon_url', $data['app_url'] . '/assets/images/icon.svg'],
        ['branding', 'sender_name', $data['app_name']],
        ['branding', 'sender_email', $data['mail_from_address']],
        ['branding', 'support_email', $data['admin_email']],
        ['mail', 'mailer', $data['mail_mailer']],
        ['mail', 'host', $data['mail_host']],
        ['mail', 'port', $data['mail_port']],
        ['mail', 'username', $data['mail_username']],
        ['mail', 'password', $data['mail_password']],
        ['mail', 'encryption', $data['mail_encryption']],
        ['mail', 'from_address', $data['mail_from_address']],
        ['mail', 'from_name', $data['app_name']],
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO settings (setting_group, setting_key, setting_value) VALUES (?, ?, ?)");
    foreach ($groups as $g) {
        $stmt->execute($g);
    }
}

$step = $_POST['step'] ?? '1';
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === '2') {
    $data = [
        'app_name' => trim($_POST['app_name'] ?? 'The New Age Marketplace'),
        'app_url' => rtrim(trim($_POST['app_url'] ?? ''), '/'),
        'app_timezone' => trim($_POST['app_timezone'] ?? 'UTC'),
        'app_currency' => trim($_POST['app_currency'] ?? 'USD'),
        'app_currency_symbol' => trim($_POST['app_currency_symbol'] ?? '$'),
        'db_host' => trim($_POST['db_host'] ?? 'localhost'),
        'db_port' => trim($_POST['db_port'] ?? '3306'),
        'db_database' => trim($_POST['db_database'] ?? 'thenewage'),
        'db_username' => trim($_POST['db_username'] ?? ''),
        'db_password' => $_POST['db_password'] ?? '',
        'admin_email' => trim($_POST['admin_email'] ?? ''),
        'admin_password' => $_POST['admin_password'] ?? '',
        'mail_mailer' => trim($_POST['mail_mailer'] ?? 'log'),
        'mail_host' => trim($_POST['mail_host'] ?? ''),
        'mail_port' => trim($_POST['mail_port'] ?? '587'),
        'mail_username' => trim($_POST['mail_username'] ?? ''),
        'mail_password' => $_POST['mail_password'] ?? '',
        'mail_encryption' => trim($_POST['mail_encryption'] ?? 'tls'),
        'mail_from_address' => trim($_POST['mail_from_address'] ?? 'noreply@example.com'),
    ];

    if (empty($data['app_name']) || empty($data['app_url']) || empty($data['db_database']) || empty($data['db_username']) || empty($data['admin_email']) || empty($data['admin_password'])) {
        $errors[] = 'Please fill in all required fields.';
    } elseif (!filter_var($data['admin_email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid admin email.';
    } else {
        $dbError = testDb($data);
        if ($dbError) {
            $errors[] = 'Database connection failed: ' . $dbError;
        } else {
            try {
                writeEnv($data);
                $pdo = pdo($data);
                runMigrations($pdo);
                createAdmin($pdo, $data);
                seedSettings($pdo, $data);
                file_put_contents($lockPath, date('Y-m-d H:i:s'));
                $success = true;
            } catch (Throwable $e) {
                $errors[] = 'Installation failed: ' . $e->getMessage();
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; min-height: 100vh; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; background: #f5f5f7; color: #1d1d1f; }
        .wrap { max-width: 720px; margin: 0 auto; padding: 3rem 1.5rem; }
        .card { background: rgba(255,255,255,0.95); border: 1px solid rgba(0,0,0,0.08); border-radius: 1.25rem; box-shadow: 0 16px 48px rgba(0,0,0,0.08); padding: 2rem; }
        h1 { font-size: 1.75rem; margin: 0 0 0.5rem; }
        p { color: #6e6e73; line-height: 1.5; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; font-size: 0.9rem; color: #6e6e73; margin-bottom: 0.35rem; }
        input, select { width: 100%; padding: 0.75rem 0.9rem; border: 1px solid rgba(0,0,0,0.12); border-radius: 0.65rem; font-size: 1rem; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .btn { display: inline-block; padding: 0.85rem 1.5rem; border: none; border-radius: 0.65rem; background: #0071e3; color: #fff; font-weight: 600; cursor: pointer; text-decoration: none; }
        .btn:hover { background: #005bb5; }
        .alert { padding: 0.85rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem; }
        .alert-danger { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        code { background: #f5f5f7; padding: 0.2rem 0.4rem; border-radius: 0.35rem; font-size: 0.9rem; }
        .steps { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; }
        .step { flex: 1; height: 4px; border-radius: 2px; background: #e5e7eb; }
        .step.active { background: #0071e3; }
        .muted { color: #6e6e73; font-size: 0.9rem; }
        @media (max-width: 640px) { .grid-2 { grid-template-columns: 1fr; } .wrap { padding: 1.5rem 1rem; } .card { padding: 1.5rem; } }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <?php if (isInstalled() && !$success): ?>
                <h1>Already installed</h1>
                <p class="muted">This marketplace has already been configured. Remove <code>install.lock</code> to run the installer again.</p>
                <p style="margin-top:1.5rem;"><a href="/" class="btn">Go to site</a></p>
            <?php elseif ($success): ?>
                <div class="alert alert-success">Installation complete.</div>
                <h1>You're ready to go</h1>
                <p>Your marketplace is installed and the admin account has been created.</p>
                <ul class="muted">
                    <li>Admin login: <code><?= e($data['admin_email'] ?? '') ?></code></li>
                    <li>Login at: <a href="/login">/login</a></li>
                    <li>Admin dashboard: <a href="/admin">/admin</a></li>
                </ul>
                <p style="margin-top:1.5rem;"><a href="/" class="btn">Visit marketplace</a></p>
            <?php else: ?>
                <h1><?= e($title) ?></h1>
                <p class="muted">Configure your database, admin account and branding to get started on cPanel.</p>
                <div class="steps">
                    <div class="step active"></div>
                </div>
                <?php foreach ($errors as $err): ?>
                    <div class="alert alert-danger"><?= e($err) ?></div>
                <?php endforeach; ?>
                <form method="POST">
                    <input type="hidden" name="step" value="2">
                    <h3 style="margin-bottom:0.5rem;">Site</h3>
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Site name</label>
                            <input type="text" name="app_name" value="The New Age Marketplace" required>
                        </div>
                        <div class="form-group">
                            <label>Site URL</label>
                            <input type="url" name="app_url" placeholder="https://yourdomain.com" required>
                        </div>
                        <div class="form-group">
                            <label>Currency</label>
                            <input type="text" name="app_currency" value="USD" required>
                        </div>
                        <div class="form-group">
                            <label>Currency symbol</label>
                            <input type="text" name="app_currency_symbol" value="$" required>
                        </div>
                        <div class="form-group">
                            <label>Timezone</label>
                            <input type="text" name="app_timezone" value="UTC" required>
                        </div>
                    </div>

                    <h3 style="margin-bottom:0.5rem;">Database</h3>
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Host</label>
                            <input type="text" name="db_host" value="localhost" required>
                        </div>
                        <div class="form-group">
                            <label>Port</label>
                            <input type="text" name="db_port" value="3306" required>
                        </div>
                        <div class="form-group">
                            <label>Database name</label>
                            <input type="text" name="db_database" value="thenewage" required>
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="db_username" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="db_password" required>
                        </div>
                    </div>

                    <h3 style="margin-bottom:0.5rem;">Admin account</h3>
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="admin_email" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="admin_password" minlength="8" required>
                        </div>
                    </div>

                    <h3 style="margin-bottom:0.5rem;">SMTP (optional)</h3>
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Mailer</label>
                            <select name="mail_mailer">
                                <option value="log" selected>Log only</option>
                                <option value="smtp">SMTP</option>
                                <option value="mail">PHP mail()</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>SMTP host</label>
                            <input type="text" name="mail_host" placeholder="smtp.example.com">
                        </div>
                        <div class="form-group">
                            <label>Port</label>
                            <input type="text" name="mail_port" value="587">
                        </div>
                        <div class="form-group">
                            <label>Encryption</label>
                            <select name="mail_encryption">
                                <option value="tls" selected>TLS</option>
                                <option value="ssl">SSL</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>SMTP username</label>
                            <input type="text" name="mail_username">
                        </div>
                        <div class="form-group">
                            <label>SMTP password</label>
                            <input type="password" name="mail_password">
                        </div>
                        <div class="form-group">
                            <label>From address</label>
                            <input type="email" name="mail_from_address" value="noreply@example.com">
                        </div>
                    </div>

                    <button type="submit" class="btn">Install marketplace</button>
                </form>
                <p class="muted" style="margin-top:1.5rem;">After installation, delete or rename <code>public/install.php</code> for security. <code>install.lock</code> already blocks reruns.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
