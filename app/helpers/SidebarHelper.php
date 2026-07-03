<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * SidebarHelper
 *
 * Build the sidebar menu structure, detect the active link and determine
 * which accordion section should be opened.
 *
 * Logic replicated from p_edufile's includes/sidebar.php but adapted to MVC:
 * - role based menu sections
 * - link active when the current URI contains/equals the link path
 * - accordion auto-opens the section that contains the active link
 */
class SidebarHelper
{
    /**
     * Get the full sidebar data for the current user.
     *
     * @return array{sections: array, user: array, open: string|null}
     */
    public static function data(): array
    {
        $role = $_SESSION['user_role'] ?? null;
        $current = self::currentPath();

        $sections = match ($role) {
            'Administrador' => self::adminMenu(),
            'Docente' => self::teacherMenu(),
            default => [],
        };

        // Mark active links
        foreach ($sections as $key => &$section) {
            foreach ($section['links'] as $idx => &$link) {
                $link['active'] = self::isActive($link['url'], $current, $link['force_active'] ?? null);
            }
            unset($link);

            // Section should be open if it contains an active link
            $section['open'] = self::hasActive($section['links']);
        }
        unset($section);

        $openSection = null;
        foreach ($sections as $key => $section) {
            if ($section['open']) {
                $openSection = $key;
                break;
            }
        }

        // If nothing is active, open the first section by default
        if (!$openSection && !empty($sections)) {
            $firstKey = array_key_first($sections);
            $sections[$firstKey]['open'] = true;
            $openSection = $firstKey;
        }

        return [
            'sections' => $sections,
            'user_name' => $_SESSION['user_name'] ?? null,
            'open_section' => $openSection,
        ];
    }

    /**
     * Menu structure for the Administrator role.
     */
    private static function adminMenu(): array
    {
        return [
            'clases' => [
                'title' => 'CLASES Y CURSOS',
                'links' => [
                    ['url' => '/dashboard/inicial', 'label' => 'Inicial', 'icon' => 'user'],
                    ['url' => '/dashboard/primaria', 'label' => 'Primaria', 'icon' => 'book'],
                    ['url' => '/dashboard/secundaria', 'label' => 'Secundaria', 'icon' => 'layers'],
                ],
            ],
            'control' => [
                'title' => 'PANEL DE CONTROL',
                'links' => [
                    ['url' => '/estudiantes', 'label' => 'Estudiantes', 'icon' => 'users'],
                    ['url' => '/dashboard', 'label' => 'Dashboard', 'icon' => 'monitor'],
                ],
            ],
            'academico' => [
                'title' => 'ACADEMICO',
                'links' => [
                    ['url' => '/cursos', 'label' => 'Cursos', 'icon' => 'book'],
                    ['url' => '/materias', 'label' => 'Materias', 'icon' => 'calendar'],
                    ['url' => '/cursos-materias', 'label' => 'Materias por Curso', 'icon' => 'layers'],
                    ['url' => '/docentes-asignaciones', 'label' => 'Asignar Docentes', 'icon' => 'users'],
                ],
            ],
            'admin' => [
                'title' => 'ADMINISTRACION',
                'links' => [
                    ['url' => '/usuarios', 'label' => 'Usuarios', 'icon' => 'user'],
                ],
            ],
            'reportes' => [
                'title' => 'REPORTES',
                'links' => [
                    ['url' => '/reportes', 'label' => 'Reportes', 'icon' => 'file-text'],
                ],
            ],
        ];
    }

    /**
     * Menu structure for the Teacher role.
     */
    private static function teacherMenu(): array
    {
        return [
            'cursos' => [
                'title' => 'MIS CURSOS',
                'links' => [
                    ['url' => '/docente/dashboard', 'label' => 'Ver Cursos', 'icon' => 'book-open'],
                ],
            ],
        ];
    }

    /**
     * Normalized current request path (without base_url prefix).
     */
    public static function currentPath(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        if ($base !== '' && $base !== '/' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }

        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : rtrim($path, '/');
    }

    /**
     * Determine if a link is active.
     *
     * Uses exact route matching to avoid marking parent links as active when
     * the user is on a child page (e.g. /dashboard should not be active on
     * /dashboard/primaria). This is the correct equivalent of p_edufile's
     * active() function for a route-based MVC app.
     */
    private static function isActive(string $url, string $current, ?string $force = null): bool
    {
        // p_edufile style: force active from session
        if ($force !== null && isset($_SESSION['force_active']) && $_SESSION['force_active'] === $force) {
            return true;
        }

        return rtrim($url, '/') === rtrim($current, '/');
    }

    /**
     * Check if any link in the section is active.
     */
    private static function hasActive(array $links): bool
    {
        foreach ($links as $link) {
            if ($link['active'] ?? false) {
                return true;
            }
        }
        return false;
    }

    /**
     * SVG icons (Feather-like, inline).
     */
    public static function icon(string $name): string
    {
        return match ($name) {
            'user' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>',
            'book' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>',
            'layers' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>',
            'users' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
            'monitor' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>',
            'calendar' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
            'book-open' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>',
            'info' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>',
            'file-text' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>',
            'log-out' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>',
            default => '',
        };
    }
}
