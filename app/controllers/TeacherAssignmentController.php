<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\TeacherAssignment;
use PDOException;

class TeacherAssignmentController extends Controller
{
    public function index(): void
    {
        require_any_role(['Administrador', 'Secretaria']);

        $filters = [
            'nivel' => trim($_GET['nivel'] ?? ''),
            'search' => trim($_GET['search'] ?? ''),
        ];

        if (!in_array($filters['nivel'], ['', 'Inicial', 'Primaria', 'Secundaria'], true)) {
            $filters['nivel'] = '';
        }

        $this->view('teacher_assignments/index', [
            'title' => 'Asignacion de Docentes',
            'assignments' => TeacherAssignment::all($filters),
            'filters' => $filters,
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function create(): void
    {
        require_any_role(['Administrador', 'Secretaria']);
        $this->form('teacher_assignments/create', 'Nueva Asignacion Docente');
    }

    public function store(): void
    {
        require_any_role(['Administrador', 'Secretaria']);
        verify_csrf();

        $data = $this->validatedData();
        if (is_string($data)) {
            flash('error', $data);
            $this->redirect('/docentes-asignaciones/create');
        }

        try {
            TeacherAssignment::create($data);
            flash('success', 'Docente asignado correctamente.');
            $this->redirect('/docentes-asignaciones');
        } catch (PDOException $exception) {
            flash('error', 'No se pudo asignar. Verifique que el docente no tenga ya esa materia y curso en la gestion.');
            $this->redirect('/docentes-asignaciones/create');
        }
    }

    public function edit(): void
    {
        require_any_role(['Administrador', 'Secretaria']);

        $id = (int) ($_GET['id'] ?? 0);
        $assignment = TeacherAssignment::find($id);
        if (!$assignment) {
            flash('error', 'Asignacion no encontrada.');
            $this->redirect('/docentes-asignaciones');
        }

        $this->form('teacher_assignments/edit', 'Editar Asignacion Docente', $assignment);
    }

    public function update(): void
    {
        require_any_role(['Administrador', 'Secretaria']);
        verify_csrf();

        $id = (int) ($_POST['id_asignacion'] ?? 0);
        $data = $this->validatedData();
        if (is_string($data)) {
            flash('error', $data);
            $this->redirect('/docentes-asignaciones/edit?id=' . $id);
        }

        try {
            TeacherAssignment::update($id, $data);
            flash('success', 'Asignacion actualizada correctamente.');
            $this->redirect('/docentes-asignaciones');
        } catch (PDOException $exception) {
            flash('error', 'No se pudo actualizar. Verifique duplicados.');
            $this->redirect('/docentes-asignaciones/edit?id=' . $id);
        }
    }

    public function status(): void
    {
        require_any_role(['Administrador', 'Secretaria']);
        verify_csrf();

        $id = (int) ($_POST['id_asignacion'] ?? 0);
        $status = ($_POST['estado'] ?? '') === 'activo' ? 'activo' : 'inactivo';
        TeacherAssignment::changeStatus($id, $status);

        flash('success', 'Estado de asignacion actualizado.');
        $this->redirect('/docentes-asignaciones');
    }

    private function form(string $view, string $title, ?array $assignment = null): void
    {
        $this->view($view, [
            'title' => $title,
            'assignment' => $assignment,
            'teachers' => TeacherAssignment::teachers(),
            'courseSubjects' => TeacherAssignment::courseSubjects(),
            'gestiones' => TeacherAssignment::activeGestiones(),
            'error' => flash('error'),
        ]);
    }

    private function validatedData(): array|string
    {
        $data = [
            'id_personal' => (int) ($_POST['id_personal'] ?? 0),
            'id_curso_materia' => (int) ($_POST['id_curso_materia'] ?? 0),
            'id_gestion' => (int) ($_POST['id_gestion'] ?? 0),
            'estado' => ($_POST['estado'] ?? 'activo') === 'activo' ? 'activo' : 'inactivo',
            'estado_carga' => ($_POST['estado_carga'] ?? 'FALTA') === 'CARGADO' ? 'CARGADO' : 'FALTA',
        ];

        if ($data['id_personal'] <= 0) {
            return 'Seleccione un docente.';
        }

        if ($data['id_curso_materia'] <= 0) {
            return 'Seleccione una materia asignada a curso.';
        }

        if ($data['id_gestion'] <= 0) {
            return 'Seleccione una gestion.';
        }

        return $data;
    }
}
