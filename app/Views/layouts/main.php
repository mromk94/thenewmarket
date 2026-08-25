<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? setting('branding', 'site_name', config('app.name'))) ?></title>
    <meta name="description" content="<?= e($description ?? setting('branding', 'description', '')) ?>">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <script>window.currencySymbol = '<?= e(config('app.currency_symbol')) ?>';</script>
    <link rel="icon" type="image/svg+xml" href="<?= e(setting('branding', 'favicon_url', asset('images/favicon.svg'))) ?>">
    <link rel="apple-touch-icon" href="<?= e(setting('branding', 'icon_url', asset('images/icon.svg'))) ?>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body>
    <?php include BASE_PATH . '/app/Views/partials/nav.php'; ?>

    <main class="container">
        <?= $content ?>
    </main>

    <?php $error = App\Core\Session::getFlash('error'); ?>
    <?php if ($error): ?>
        <div class="toast toast-danger" role="alert"><?= e((string) $error) ?></div>
    <?php endif; ?>

    <?php $success = App\Core\Session::getFlash('success'); ?>
    <?php if ($success): ?>
        <div class="toast toast-success" role="alert"><?= e((string) $success) ?></div>
    <?php endif; ?>

    <?php include BASE_PATH . '/app/Views/partials/cart-float.php'; ?>
    <?php include BASE_PATH . '/app/Views/partials/footer.php'; ?>
    <script src="<?= asset('js/app.js') ?>"></script>
    <script src="<?= asset('js/cart.js') ?>"></script>
</body>
</html>
