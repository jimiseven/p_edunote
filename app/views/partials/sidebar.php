<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar p-0">
    <div class="sidebar-wrapper">
        <!-- Header -->
        <div class="sidebar-brand">
            <span class="logo-icon">E</span>
            <span>EDUFILE</span>
        </div>

        <!-- Menu -->
        <div class="sidebar-menu-wrapper">
            <?php if (empty($sections)): ?>
                <div class="text-muted small px-3 py-2">No hay menú disponible</div>
            <?php else: ?>
                <?php foreach ($sections as $key => $section): ?>
                    <div class="sidebar-section-title <?= ($section['open'] ?? false) ? '' : 'collapsed' ?>" data-target="<?= e($key) ?>" data-accordion="toggle">
                        <?= e($section['title']) ?>
                        <span class="accordion-arrow">&#9660;</span>
                    </div>
                    <div class="sidebar-group-list <?= ($section['open'] ?? false) ? 'sidebar-group-open' : '' ?>" id="group-<?= e($key) ?>">
                        <ul class="nav flex-column">
                            <?php foreach ($section['links'] as $link): ?>
                                <li>
                                    <a class="nav-link <?= ($link['active'] ?? false) ? 'active' : '' ?>"
                                       href="<?= e(base_url($link['url'])) ?>">
                                        <span class="feather"><?= \App\Helpers\SidebarHelper::icon($link['icon'] ?? '') ?></span>
                                        <?= e($link['label']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="sidebar-bottom">
            <!-- Theme Toggle -->
            <div class="theme-toggle" id="themeToggle" title="Cambiar tema">
                <span class="theme-toggle-icon" id="themeIcon">🌙</span>
                <div class="theme-toggle-track" id="themeTrack">
                    <div class="theme-toggle-thumb"></div>
                </div>
                <span style="flex:1;">Modo Oscuro</span>
            </div>

            <?php if ($user_name): ?>
                <div class="sidebar-user">
                    <span class="feather"><?= \App\Helpers\SidebarHelper::icon('user') ?></span>
                    <?= e($user_name) ?>
                </div>
            <?php endif; ?>
            <div class="sidebar-logout">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                            <span class="feather"><?= \App\Helpers\SidebarHelper::icon('log-out') ?></span>
                            Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
