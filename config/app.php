<?php

declare(strict_types=1);

return [
    'name' => 'EduNote P',
    'base_url' => (function () {
        // Dynamically detect the base path from SCRIPT_NAME.
        // Works on any environment: local XAMPP, cPanel with public/ as doc root,
        // or cPanel with the project in a subfolder.
        $base = dirname($_SERVER['SCRIPT_NAME']);
        return $base === '/' || $base === '\\' ? '' : rtrim(str_replace('\\', '/', $base), '/');
    })(),
    'session_timeout' => 1800,
];
