<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Grade;

class GradeController extends Controller
{
    public function index(): void
    {
        require_role('Docente');

        $idAsignacion = (int) ($_GET['asignacion'] ?? 0);
        if ($idAsignacion <= 0) {
            flash('error', 'Asignacion no valida.');
            $this->redirect('/docente/dashboard');
        }

        $assignment = Grade::assignmentInfo($idAsignacion, (int) $_SESSION['user_id']);
        if (!$assignment) {
            flash('error', 'Asignacion no encontrada.');
            $this->redirect('/docente/dashboard');
        }

        $esInicial = $assignment['nivel'] === 'Inicial';
        $gestionId = (int) $assignment['id_gestion'];
        $cursoId = (int) $assignment['id_curso'];
        $materiaId = (int) $assignment['id_materia'];

        $trimestres = Grade::trimestres($gestionId);
        $activeTrimestreIds = Grade::activeTrimestreIds($gestionId);
        $students = Grade::enrolledStudents($cursoId, $gestionId);
        $existingGrades = Grade::existingGrades($materiaId, $gestionId, $esInicial);

        $this->view('teacher/cargar_notas', [
            'title' => 'Cargar Notas - ' . $assignment['materia'],
            'assignment' => $assignment,
            'trimestres' => $trimestres,
            'activeTrimestreIds' => $activeTrimestreIds,
            'students' => $students,
            'existingGrades' => $existingGrades,
            'esInicial' => $esInicial,
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function store(): void
    {
        require_role('Docente');
        verify_csrf();

        $idAsignacion = (int) ($_POST['id_asignacion'] ?? 0);
        $assignment = Grade::assignmentInfo($idAsignacion, (int) $_SESSION['user_id']);

        if (!$assignment) {
            flash('error', 'Asignacion no encontrada.');
            $this->redirect('/docente/dashboard');
        }

        $esInicial = $assignment['nivel'] === 'Inicial';
        $idMateria = (int) $assignment['id_materia'];
        $idGestion = (int) $assignment['id_gestion'];

        try {
            $gradesData = [];

            if (isset($_POST['guardar_excel'])) {
                $idTrimestreExcel = (int) ($_POST['bimestre_excel'] ?? 0);
                $activeTrimestres = Grade::activeTrimestreIds($idGestion);
                if (!in_array($idTrimestreExcel, $activeTrimestres, true)) {
                    throw new \RuntimeException('El trimestre seleccionado no esta habilitado para carga de notas.');
                }

                $datosExcel = explode("\n", trim($_POST['datos_excel'] ?? ''));
                $students = Grade::enrolledStudents((int) $assignment['id_curso'], $idGestion);

                if (count($datosExcel) !== count($students)) {
                    throw new \RuntimeException(
                        'La cantidad de ' . ($esInicial ? 'comentarios' : 'notas') .
                        ' no coincide con el numero de estudiantes (' . count($students) . ' esperados, ' . count($datosExcel) . ' recibidos).'
                    );
                }

                foreach ($students as $index => $student) {
                    $gradesData[(int) $student['id_matricula']][$idTrimestreExcel] = trim($datosExcel[$index]);
                }
            } else {
                $gradesData = $_POST['notas'] ?? [];
            }

            Grade::saveGrades(
                $idAsignacion,
                $idMateria,
                $idGestion,
                $esInicial,
                $gradesData
            );

            flash('success', 'Notas cargadas correctamente.');
        } catch (\Throwable $e) {
            flash('error', 'Error al guardar las notas: ' . $e->getMessage());
        }

        $this->redirect('/docente/notas?asignacion=' . $idAsignacion);
    }
}
