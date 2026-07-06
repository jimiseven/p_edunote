<!DOCTYPE html>
<html lang="es" id="htmlRoot">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? config('name')) ?></title>
    <link rel="stylesheet" href="<?= e(base_url('/assets/css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_url('/assets/css/app.css')) ?>">
    <script>
    (function() {
        const key = 'edunote-theme';
        const stored = localStorage.getItem(key);
        const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = stored || (systemDark ? 'dark' : 'light');
        document.documentElement.setAttribute('data-bs-theme', theme);
    })();
    </script>
</head>
<body>
    <?php
        $sidebar = \App\Helpers\SidebarHelper::data();
        $sections = $sidebar['sections'];
        $user_name = $sidebar['user_name'];
    ?>
    <div class="app-layout">
        <div class="sidebar-scope" x-data="sidebarScope()" x-init="init()">
            <?= view_partial('partials/sidebar', ['sections' => $sections, 'user_name' => $user_name]) ?>
        </div>

        <main class="main-content">
            <?= $content ?>
        </main>
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
    function sidebarScope() {
        const themeKey = 'edunote-theme';
        const sidebarKey = 'edunote-sidebar';

        return {
            theme: document.documentElement.getAttribute('data-bs-theme') || 'light',
            collapsed: localStorage.getItem(sidebarKey) === 'true',

            init() {
                // Asegurar que el body/html reflejen el estado inicial del sidebar
                if (this.collapsed) {
                    document.documentElement.classList.add('sidebar-collapsed');
                } else {
                    document.documentElement.classList.remove('sidebar-collapsed');
                }
            },

            toggleTheme() {
                this.theme = this.theme === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-bs-theme', this.theme);
                localStorage.setItem(themeKey, this.theme);
            },

            toggle() {
                this.collapsed = !this.collapsed;
                document.documentElement.classList.toggle('sidebar-collapsed', this.collapsed);
                localStorage.setItem(sidebarKey, this.collapsed);
            }
        };
    }
    </script>
    <script defer src="<?= e(base_url('/assets/js/alpine.min.js')) ?>"></script>
    <script>
    // Sidebar accordion (vanilla, lightweight)
    document.addEventListener('DOMContentLoaded', function() {
        var titles = document.querySelectorAll('#sidebarMenu .sidebar-section-title[data-accordion="toggle"]');
        titles.forEach(function(title) {
            var group = document.getElementById('group-' + title.dataset.target);
            if (!group || !group.classList.contains('sidebar-group-list')) return;

            title.addEventListener('click', function() {
                var isOpen = group.classList.contains('sidebar-group-open');

                document.querySelectorAll('#sidebarMenu .sidebar-group-list').forEach(function(ul) {
                    ul.classList.remove('sidebar-group-open');
                });
                document.querySelectorAll('#sidebarMenu .sidebar-section-title[data-accordion="toggle"]').forEach(function(t) {
                    t.classList.add('collapsed');
                });

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
