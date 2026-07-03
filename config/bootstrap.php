<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

// Composer autoload (for dompdf)
$composerAutoload = BASE_PATH . '/vendor/autoload.php';
if (is_file($composerAutoload)) {
    require $composerAutoload;
}

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

require BASE_PATH . '/app/helpers/functions.php';

session_name('EDUNOTE_P_SESSION');
session_start();
