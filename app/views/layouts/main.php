<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? config('name')) ?></title>
    <link rel="stylesheet" href="<?= e(base_url('/assets/css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_url('/assets/css/app.css')) ?>">
</head>
<body>
    <div class="container-fluid">
        <div class="row min-vh-100">
            <aside class="col-md-3 col-lg-2 sidebar p-0">
                <div class="sidebar-brand"><span class="logo-icon">E</span><span>EDUFILE</span></div>
                <nav class="px-3">
                    <div class="sidebar-section-title">PANEL</div>
                    <a class="nav-link active" href="<?= e(base_url('/dashboard')) ?>">Dashboard</a>
                    <?php if (($_SESSION['user_role'] ?? '') === 'Administrador'): ?>
                        <div class="sidebar-section-title">ADMINISTRACION</div>
                        <a class="nav-link" href="<?= e(base_url('/usuarios')) ?>">Usuarios</a>
                    <?php endif; ?>
                    <div class="sidebar-section-title">SISTEMA</div>
                    <a class="nav-link" href="<?= e(base_url('/logout')) ?>">Cerrar sesion</a>
                </nav>
                <div class="sidebar-user">Usuario: <?= e($_SESSION['user_name'] ?? '') ?></div>
            </aside>
            <main class="col-md-9 col-lg-10 main-content">
                <?= $content ?>
            </main>
        </div>
    </div>
    <script src="<?= e(base_url('/assets/js/bootstrap.bundle.min.js')) ?>"></script>
</body>
</html>
