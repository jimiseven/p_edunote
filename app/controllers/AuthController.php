<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (is_authenticated()) {
            $this->redirect('/dashboard');
        }

        $this->view('auth/login', [
            'title' => 'Iniciar Sesion',
            'error' => flash('error'),
        ], 'auth');
    }

    public function login(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            flash('error', 'Complete usuario y contrasena.');
            $this->redirect('/login');
        }

        $user = User::findActiveByUsername($username);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            flash('error', 'Usuario o contrasena incorrectos.');
            $this->redirect('/login');
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id_usuario'];
        $_SESSION['user_name'] = trim(($user['nombres'] ?? '') . ' ' . ($user['apellidos'] ?? '')) ?: $user['username'];
        $_SESSION['user_role'] = $user['rol_nombre'];
        $_SESSION['last_activity'] = time();

        User::registerLogin((int) $user['id_usuario']);

        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
        header('Location: ' . base_url('/login'));
        exit;
    }
}
