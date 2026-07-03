<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Course;
use App\Models\CourseSubject;
use App\Models\SubjectAdmin;
use PDOException;

class CourseSubjectController extends Controller
{
    public function index(): void
    {
        require_role('Administrador');

        $filters = [
            'nivel' => trim($_GET['nivel'] ?? ''),
            'search' => trim($_GET['search'] ?? ''),
        ];

        $this->view('course_subjects/index', [
            'title' => 'Materias por Curso',
            'assignments' => CourseSubject::all($filters),
            'filters' => $filters,
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function create(): void
    {
        require_role('Administrador');

        $this->view('course_subjects/create', [
            'title' => 'Asignar Materia a Curso',
            'courses' => Course::allActive(),
            'subjects' => SubjectAdmin::all(),
            'error' => flash('error'),
        ]);
    }

    public function store(): void
    {
        require_role('Administrador');
        verify_csrf();

        $data = $this->validatedData();
        if (is_string($data)) {
            flash('error', $data);
            $this->redirect('/cursos-materias/create');
        }

        try {
            CourseSubject::create($data);
            flash('success', 'Materia asignada al curso correctamente.');
            $this->redirect('/cursos-materias');
        } catch (PDOException $exception) {
            flash('error', 'No se pudo asignar. Verifique que la materia no este ya asignada a ese curso.');
            $this->redirect('/cursos-materias/create');
        }
    }

    public function edit(): void
    {
        require_role('Administrador');

        $id = (int) ($_GET['id'] ?? 0);
        $assignment = CourseSubject::find($id);
        if (!$assignment) {
            flash('error', 'Asignacion no encontrada.');
            $this->redirect('/cursos-materias');
        }

        $this->view('course_subjects/edit', [
            'title' => 'Editar Asignacion',
            'assignment' => $assignment,
            'courses' => Course::allActive(),
            'subjects' => SubjectAdmin::all(),
            'error' => flash('error'),
        ]);
    }

    public function update(): void
    {
        require_role('Administrador');
        verify_csrf();

        $id = (int) ($_POST['id_curso_materia'] ?? 0);
        $data = $this->validatedData();
        if (is_string($data)) {
            flash('error', $data);
            $this->redirect('/cursos-materias/edit?id=' . $id);
        }

        try {
            CourseSubject::update($id, $data);
            flash('success', 'Asignacion actualizada correctamente.');
            $this->redirect('/cursos-materias');
        } catch (PDOException $exception) {
            flash('error', 'No se pudo actualizar. Verifique duplicados.');
            $this->redirect('/cursos-materias/edit?id=' . $id);
        }
    }

    public function status(): void
    {
        require_role('Administrador');
        verify_csrf();

        $id = (int) ($_POST['id_curso_materia'] ?? 0);
        $status = (int) ($_POST['estado'] ?? 0) === 1 ? 1 : 0;
        CourseSubject::changeStatus($id, $status);

        flash('success', 'Estado de la asignacion actualizado.');
        $this->redirect('/cursos-materias');
    }

    private function validatedData(): array|string
    {
        $data = [
            'id_curso' => (int) ($_POST['id_curso'] ?? 0),
            'id_materia' => (int) ($_POST['id_materia'] ?? 0),
            'estado' => (int) ($_POST['estado'] ?? 1) === 1 ? 1 : 0,
        ];

        if ($data['id_curso'] <= 0) {
            return 'Seleccione un curso.';
        }

        if ($data['id_materia'] <= 0) {
            return 'Seleccione una materia.';
        }

        return $data;
    }
}
