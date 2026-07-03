<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\SubjectAdmin;
use PDOException;

class SubjectController extends Controller
{
    public function index(): void
    {
        require_any_role(['Administrador', 'Secretaria']);

        $search = trim($_GET['search'] ?? '');
        $this->view('subjects/index', [
            'title' => 'Gestion de Materias',
            'subjects' => SubjectAdmin::all($search),
            'search' => $search,
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function create(): void
    {
        require_any_role(['Administrador', 'Secretaria']);

        $this->view('subjects/create', [
            'title' => 'Nueva Materia',
            'parents' => SubjectAdmin::parents(),
            'error' => flash('error'),
        ]);
    }

    public function store(): void
    {
        require_any_role(['Administrador', 'Secretaria']);
        verify_csrf();

        $data = $this->validatedData();
        if (is_string($data)) {
            flash('error', $data);
            $this->redirect('/materias/create');
        }

        try {
            SubjectAdmin::create($data);
            flash('success', 'Materia creada correctamente.');
            $this->redirect('/materias');
        } catch (PDOException $exception) {
            flash('error', 'No se pudo crear la materia.');
            $this->redirect('/materias/create');
        }
    }

    public function edit(): void
    {
        require_any_role(['Administrador', 'Secretaria']);

        $id = (int) ($_GET['id'] ?? 0);
        $subject = SubjectAdmin::find($id);
        if (!$subject) {
            flash('error', 'Materia no encontrada.');
            $this->redirect('/materias');
        }

        $this->view('subjects/edit', [
            'title' => 'Editar Materia',
            'subject' => $subject,
            'parents' => SubjectAdmin::parents($id),
            'error' => flash('error'),
        ]);
    }

    public function update(): void
    {
        require_any_role(['Administrador', 'Secretaria']);
        verify_csrf();

        $id = (int) ($_POST['id_materia'] ?? 0);
        $data = $this->validatedData();
        if (is_string($data)) {
            flash('error', $data);
            $this->redirect('/materias/edit?id=' . $id);
        }

        try {
            SubjectAdmin::update($id, $data);
            flash('success', 'Materia actualizada correctamente.');
            $this->redirect('/materias');
        } catch (PDOException $exception) {
            flash('error', 'No se pudo actualizar la materia.');
            $this->redirect('/materias/edit?id=' . $id);
        }
    }

    public function status(): void
    {
        require_any_role(['Administrador', 'Secretaria']);
        verify_csrf();

        $id = (int) ($_POST['id_materia'] ?? 0);
        $status = (int) ($_POST['estado'] ?? 0) === 1 ? 1 : 0;
        SubjectAdmin::changeStatus($id, $status);

        flash('success', 'Estado de la materia actualizado.');
        $this->redirect('/materias');
    }

    private function validatedData(): array|string
    {
        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'abreviatura' => strtoupper(trim($_POST['abreviatura'] ?? '')),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'es_submateria' => isset($_POST['es_submateria']) ? 1 : 0,
            'id_materia_padre' => (int) ($_POST['id_materia_padre'] ?? 0),
            'es_extra' => isset($_POST['es_extra']) ? 1 : 0,
            'estado' => (int) ($_POST['estado'] ?? 1) === 1 ? 1 : 0,
        ];

        if ($data['nombre'] === '') {
            return 'Ingrese el nombre de la materia.';
        }

        if (strlen($data['nombre']) > 150) {
            return 'El nombre de la materia es demasiado largo.';
        }

        if ($data['es_submateria'] === 1 && $data['id_materia_padre'] <= 0) {
            return 'Seleccione una materia padre para la submateria.';
        }

        return $data;
    }
}
