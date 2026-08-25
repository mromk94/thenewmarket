<?php

declare(strict_types=1);

$basePath = dirname(__DIR__);
$envPath = $basePath . '/.env';

function parseEnv(string $path): array {
    $values = [];
    if (!is_file($path)) {
        return $values;
    }
    foreach (file($path) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $values[$key] = trim($value, '"\'');
    }
    return $values;
}

function check(string $label, bool $ok, string $detail = ''): void {
    $status = $ok ? 'OK' : 'FAIL';
    $color = $ok ? 'green' : 'red';
    echo "<p><strong style=\"color:$color\">$status</strong> — $label";
    if ($detail) {
        echo " <em>($detail)</em>";
    }
    echo "</p>";
}

$env = parseEnv($envPath);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Check</title>
</head>
<body style="font-family: sans-serif; padding: 2rem;">
    <h1>Site Health Check</h1>
    <pre>PHP <?= phpversion() ?></pre>

    <?php
    check('PHP version >= 8.1', PHP_VERSION_ID >= 80100);
    check('.env file exists', is_file($envPath));
    check('PDO MySQL extension loaded', extension_loaded('pdo_mysql'));
    check('Autoloader exists', is_file($basePath . '/vendor/autoload.php'));
    check('Storage logs writable', is_writable($basePath . '/storage/logs') || !is_dir($basePath . '/storage/logs'));

    if (!is_file($envPath)) {
        exit;
    }

    $host = $env['DB_HOST'] ?? '127.0.0.1';
    $port = (int) ($env['DB_PORT'] ?? 3306);
    $db = $env['DB_DATABASE'] ?? '';
    $user = $env['DB_USERNAME'] ?? '';
    $pass = $env['DB_PASSWORD'] ?? '';

    try {
        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
            $user,
            $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        check('Database connection', true, "$host:$port / $db");
    } catch (Throwable $e) {
        check('Database connection', false, $e->getMessage());
        exit;
    }

    try {
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        check('Tables found', count($tables) > 0, implode(', ', $tables));
    } catch (Throwable $e) {
        check('Show tables', false, $e->getMessage());
    }

    try {
        $settings = $pdo->query("SHOW COLUMNS FROM settings")->fetchAll(PDO::FETCH_COLUMN);
        check('settings table', in_array('setting_group', $settings) && in_array('setting_key', $settings) && in_array('setting_value', $settings), implode(', ', $settings));
    } catch (Throwable $e) {
        check('settings table', false, $e->getMessage());
    }

    try {
        $roles = $pdo->query("SELECT name FROM roles")->fetchAll(PDO::FETCH_COLUMN);
        check('roles seeded', count($roles) > 0, implode(', ', $roles));
    } catch (Throwable $e) {
        check('roles', false, $e->getMessage());
    }

    try {
        $users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        check('Users', (int) $users > 0, (string) $users . ' user(s)');
    } catch (Throwable $e) {
        check('Users', false, $e->getMessage());
    }

    try {
        $settingsRows = $pdo->query("SELECT COUNT(*) FROM settings")->fetchColumn();
        check('Settings rows', true, (string) $settingsRows . ' row(s)');
    } catch (Throwable $e) {
        check('Settings rows', false, $e->getMessage());
    }
    ?>

    <h2>Last logged errors</h2>
    <?php
    $logFile = $basePath . '/storage/logs/app.log';
    if (is_file($logFile) && is_readable($logFile)) {
        $lines = array_filter(explode("\n", file_get_contents($logFile)));
        $last = array_slice($lines, -10);
        echo '<pre>' . implode("\n", $last) . '</pre>';
    } else {
        echo '<p>No log file found or not readable.</p>';
    }
    ?>
</body>
</html>
