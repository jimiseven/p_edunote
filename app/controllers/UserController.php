<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Role;
use App\Models\UserAdmin;
use PDOException;

class UserController extends Controller
{
    public function index(): void
    {
        require_role('Administrador');

        $search = trim($_GET['search'] ?? '');
        $this->view('users/index', [
            'title' => 'Gestion de Usuarios',
            'users' => UserAdmin::all($search),
            'search' => $search,
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function create(): void
    {
        require_role('Administrador');

        $this->view('users/create', [
            'title' => 'Nuevo Usuario',
            'roles' => Role::allActive(),
            'error' => flash('error'),
        ]);
    }

    public function store(): void
    {
        require_role('Administrador');
        verify_csrf();

        $data = $this->validatedData(true);
        if (is_string($data)) {
            flash('error', $data);
            $this->redirect('/usuarios/create');
        }

        try {
            UserAdmin::create($data);
            flash('success', 'Usuario creado correctamente.');
            $this->redirect('/usuarios');
        } catch (PDOException $exception) {
            flash('error', 'No se pudo crear el usuario. Verifique CI, usuario o email duplicado.');
            $this->redirect('/usuarios/create');
        }
    }

    public function edit(): void
    {
        require_role('Administrador');

        $id = (int) ($_GET['id'] ?? 0);
        $user = UserAdmin::find($id);

        if (!$user) {
            flash('error', 'Usuario no encontrado.');
            $this->redirect('/usuarios');
        }

        $this->view('users/edit', [
            'title' => 'Editar Usuario',
            'user' => $user,
            'roles' => Role::allActive(),
            'error' => flash('error'),
        ]);
    }

    public function update(): void
    {
        require_role('Administrador');
        verify_csrf();

        $id = (int) ($_POST['id_usuario'] ?? 0);
        $data = $this->validatedData(false);
        if (is_string($data)) {
            flash('error', $data);
            $this->redirect('/usuarios/edit?id=' . $id);
        }

        try {
            UserAdmin::update($id, $data);
            flash('success', 'Usuario actualizado correctamente.');
            $this->redirect('/usuarios');
        } catch (PDOException $exception) {
            flash('error', 'No se pudo actualizar el usuario. Verifique CI, usuario o email duplicado.');
            $this->redirect('/usuarios/edit?id=' . $id);
        }
    }

    public function toggleStatus(): void
    {
        require_role('Administrador');
        verify_csrf();

        $id = (int) ($_POST['id_usuario'] ?? 0);
        $status = ($_POST['estado'] ?? '') === 'activo' ? 'activo' : 'inactivo';

        if ($id === (int) ($_SESSION['user_id'] ?? 0) && $status !== 'activo') {
            flash('error', 'No puede inactivar su propio usuario.');
            $this->redirect('/usuarios');
        }

        UserAdmin::changeStatus($id, $status);
        flash('success', 'Estado actualizado correctamente.');
        $this->redirect('/usuarios');
    }

    private function validatedData(bool $requirePassword): array|string
    {
        $data = [
            'nombres' => trim($_POST['nombres'] ?? ''),
            'apellidos' => trim($_POST['apellidos'] ?? ''),
            'ci' => trim($_POST['ci'] ?? ''),
            'celular' => trim($_POST['celular'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'cargo' => trim($_POST['cargo'] ?? ''),
            'especialidad' => trim($_POST['especialidad'] ?? ''),
            'id_rol' => (int) ($_POST['id_rol'] ?? 0),
            'username' => trim($_POST['username'] ?? ''),
            'password' => trim($_POST['password'] ?? ''),
        ];

        if ($data['nombres'] === '' || $data['apellidos'] === '' || $data['ci'] === '' || $data['username'] === '' || $data['id_rol'] <= 0) {
            return 'Complete nombres, apellidos, CI, usuario y rol.';
        }

        if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return 'Ingrese un email valido.';
        }

        if ($requirePassword && strlen($data['password']) < 6) {
            return 'La contrasena debe tener al menos 6 caracteres.';
        }

        if (!$requirePassword && $data['password'] !== '' && strlen($data['password']) < 6) {
            return 'La nueva contrasena debe tener al menos 6 caracteres.';
        }

        return $data;
    }
}
