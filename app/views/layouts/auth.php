<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? config('name')) ?></title>
    <link rel="stylesheet" href="<?= e(base_url('/assets/css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_url('/assets/css/app.css')) ?>">
</head>
<body class="login-body">
    <?= $content ?>
    <script src="<?= e(base_url('/assets/js/bootstrap.bundle.min.js')) ?>"></script>
</body>
</html>
