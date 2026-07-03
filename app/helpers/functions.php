<?php

declare(strict_types=1);

function config(string $key, mixed $default = null): mixed
{
    static $config = null;
    $config ??= require BASE_PATH . '/config/app.php';

    return $config[$key] ?? $default;
}

function base_url(string $path = ''): string
{
    $base = rtrim(config('base_url', ''), '/');
    return $base . '/' . ltrim($path, '/');
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $value;
}

function is_authenticated(): bool
{
    return isset($_SESSION['user_id']);
}

function require_auth(): void
{
    if (!is_authenticated()) {
        header('Location: ' . base_url('/login'));
        exit;
    }

    $timeout = (int) config('session_timeout', 1800);
    if (isset($_SESSION['last_activity']) && time() - (int) $_SESSION['last_activity'] > $timeout) {
        $_SESSION = [];
        session_destroy();
        header('Location: ' . base_url('/login'));
        exit;
    }

    $_SESSION['last_activity'] = time();
}

function require_role(string $role): void
{
    require_auth();

    if (($_SESSION['user_role'] ?? '') !== $role) {
        http_response_code(403);
        exit('Acceso no autorizado.');
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('Token CSRF invalido.');
    }
}
