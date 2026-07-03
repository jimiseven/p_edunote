<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CourseAdmin;
use PDOException;

class CourseController extends Controller
{
    public function index(): void
    {
        require_any_role(['Administrador', 'Secretaria']);

        $search = trim($_GET['search'] ?? '');
        $this->view('courses/index', [
            'title' => 'Gestion de Cursos',
            'courses' => CourseAdmin::all($search),
            'search' => $search,
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function create(): void
    {
        require_any_role(['Administrador', 'Secretaria']);

        $this->view('courses/create', [
            'title' => 'Nuevo Curso',
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
            $this->redirect('/cursos/create');
        }

        try {
            CourseAdmin::create($data);
            flash('success', 'Curso creado correctamente.');
            $this->redirect('/cursos');
        } catch (PDOException $exception) {
            flash('error', 'No se pudo crear el curso. Verifique que no exista el mismo nivel, grado y paralelo.');
            $this->redirect('/cursos/create');
        }
    }

    public function edit(): void
    {
        require_any_role(['Administrador', 'Secretaria']);

        $id = (int) ($_GET['id'] ?? 0);
        $course = CourseAdmin::find($id);
        if (!$course) {
            flash('error', 'Curso no encontrado.');
            $this->redirect('/cursos');
        }

        $this->view('courses/edit', [
            'title' => 'Editar Curso',
            'course' => $course,
            'error' => flash('error'),
        ]);
    }

    public function update(): void
    {
        require_any_role(['Administrador', 'Secretaria']);
        verify_csrf();

        $id = (int) ($_POST['id_curso'] ?? 0);
        $data = $this->validatedData();
        if (is_string($data)) {
            flash('error', $data);
            $this->redirect('/cursos/edit?id=' . $id);
        }

        try {
            CourseAdmin::update($id, $data);
            flash('success', 'Curso actualizado correctamente.');
            $this->redirect('/cursos');
        } catch (PDOException $exception) {
            flash('error', 'No se pudo actualizar el curso. Verifique datos duplicados.');
            $this->redirect('/cursos/edit?id=' . $id);
        }
    }

    public function status(): void
    {
        require_any_role(['Administrador', 'Secretaria']);
        verify_csrf();

        $id = (int) ($_POST['id_curso'] ?? 0);
        $status = (int) ($_POST['estado'] ?? 0) === 1 ? 1 : 0;
        CourseAdmin::changeStatus($id, $status);

        flash('success', 'Estado del curso actualizado.');
        $this->redirect('/cursos');
    }

    private function validatedData(): array|string
    {
        $data = [
            'nivel' => trim($_POST['nivel'] ?? ''),
            'grado' => (int) ($_POST['grado'] ?? 0),
            'paralelo' => strtoupper(trim($_POST['paralelo'] ?? '')),
            'turno' => trim($_POST['turno'] ?? ''),
            'estado' => (int) ($_POST['estado'] ?? 1) === 1 ? 1 : 0,
        ];

        if (!in_array($data['nivel'], ['Inicial', 'Primaria', 'Secundaria'], true)) {
            return 'Seleccione un nivel valido.';
        }

        if ($data['grado'] < 1 || $data['grado'] > 6) {
            return 'El grado debe estar entre 1 y 6.';
        }

        if ($data['nivel'] === 'Inicial' && $data['grado'] > 2) {
            return 'Inicial solo permite grados 1 y 2.';
        }

        if ($data['paralelo'] === '' || strlen($data['paralelo']) > 5) {
            return 'Ingrese un paralelo valido.';
        }

        if ($data['turno'] !== '' && !in_array($data['turno'], ['manana', 'tarde', 'noche'], true)) {
            return 'Seleccione un turno valido.';
        }

        return $data;
    }
}
