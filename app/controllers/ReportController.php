<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Report;

class ReportController extends Controller
{
    public function boletin(): void
    {
        require_auth();

        $idCurso = (int) ($_GET['id_curso'] ?? 0);
        if ($idCurso <= 0) {
            flash('error', 'Curso no valido.');
            $this->redirect('/dashboard');
        }

        $course = Report::courseInfo($idCurso);
        if (!$course) {
            flash('error', 'Curso no encontrado.');
            $this->redirect('/dashboard');
        }

        $gestion = Report::activeGestion();
        if (!$gestion) {
            flash('error', 'No hay gestion activa.');
            $this->redirect('/dashboard');
        }

        $vista = $_GET['vista'] ?? 'trimestral';
        $trimestre = (int) ($_GET['trimestre'] ?? 1);
        $gestionId = (int) $gestion['id_gestion'];

        $subjects = Report::subjects($idCurso);
        $students = Report::students($idCurso, $gestionId);
        $grades = Report::gradesByCourse($idCurso, $gestionId);

        // Compute averages
        $materiasParaProm = [];
        foreach ($subjects['todas'] as $m) {
            if (!$m['es_extra'] && !$m['es_submateria']) {
                $materiasParaProm[] = $m;
            }
        }
        // Also include hijas for averaging
        foreach ($subjects['grupos'] as $g) {
            foreach ($g['hijas'] as $h) {
                $materiasParaProm[] = $h;
            }
        }

        $promedios = [];
        $promediosTrim = [];
        foreach ($students as $est) {
            $idEst = (int) $est['id_estudiante'];

            // Annual average
            $total = 0;
            $cnt = 0;
            foreach ($materiasParaProm as $mat) {
                $suma = 0;
                $cntNotas = 0;
                for ($t = 1; $t <= 3; $t++) {
                    if (isset($grades[$idEst][$mat['id_materia']][$t])) {
                        $suma += $grades[$idEst][$mat['id_materia']][$t];
                        $cntNotas++;
                    }
                }
                if ($cntNotas > 0) {
                    $total += $suma / $cntNotas;
                    $cnt++;
                }
            }
            $promedios[$idEst] = $cnt > 0 ? number_format($total / $cnt, 2) : '-';

            // Trimestral average
            $totalTrim = 0;
            $cntTrim = 0;
            foreach ($materiasParaProm as $mat) {
                if (isset($grades[$idEst][$mat['id_materia']][$trimestre])) {
                    $totalTrim += $grades[$idEst][$mat['id_materia']][$trimestre];
                    $cntTrim++;
                }
            }
            $promediosTrim[$idEst] = $cntTrim > 0 ? number_format($totalTrim / $cntTrim, 2) : '-';
        }

        $this->view('boletin/index', [
            'title' => 'Boletin - ' . $course['nivel'] . ' ' . $course['grado'] . '° ' . $course['paralelo'],
            'course' => $course,
            'gestion' => $gestion,
            'subjects' => $subjects,
            'students' => $students,
            'grades' => $grades,
            'vista' => $vista,
            'trimestre' => $trimestre,
            'promedios' => $promedios,
            'promediosTrim' => $promediosTrim,
        ]);
    }
}
