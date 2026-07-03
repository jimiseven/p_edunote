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
    <?php
        $sidebar = \App\Helpers\SidebarHelper::data();
        $sections = $sidebar['sections'];
        $user_name = $sidebar['user_name'];
    ?>
    <div class="container-fluid">
        <div class="row min-vh-100">
            <?= view_partial('partials/sidebar', ['sections' => $sections, 'user_name' => $user_name]) ?>

            <main class="col-md-9 col-lg-10 main-content">
                <?= $content ?>
            </main>
        </div>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:10px;">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Cerrar sesión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Realmente desea cerrar sesión?
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a href="<?= e(base_url('/logout')) ?>" class="btn btn-primary">Confirmar</a>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= e(base_url('/assets/js/bootstrap.bundle.min.js')) ?>"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var titles = document.querySelectorAll('#sidebarMenu .sidebar-section-title[data-accordion="toggle"]');
        titles.forEach(function(title) {
            var group = document.getElementById('group-' + title.dataset.target);
            if (!group || !group.classList.contains('sidebar-group-list')) return;

            title.addEventListener('click', function() {
                var isOpen = group.classList.contains('sidebar-group-open');

                // Cerrar todos los grupos
                document.querySelectorAll('#sidebarMenu .sidebar-group-list').forEach(function(ul) {
                    ul.classList.remove('sidebar-group-open');
                });
                document.querySelectorAll('#sidebarMenu .sidebar-section-title[data-accordion="toggle"]').forEach(function(t) {
                    t.classList.add('collapsed');
                });

                // Abrir solo el clickeado si estaba cerrado
                if (!isOpen) {
                    group.classList.add('sidebar-group-open');
                    title.classList.remove('collapsed');
                }
            });
        });
    });
    </script>
</body>
</html>
