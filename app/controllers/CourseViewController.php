<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CourseView;

class CourseViewController extends Controller
{
    public function show(): void
    {
        require_auth();

        $idCurso = (int) ($_GET['curso'] ?? 0);
        if ($idCurso <= 0) {
            flash('error', 'Curso no valido.');
            $this->redirect('/dashboard');
        }

        $gestion = CourseView::activeGestion();
        if (!$gestion) {
            flash('error', 'No hay una gestion activa.');
            $this->redirect('/dashboard');
        }

        $course = CourseView::info($idCurso);
        if (!$course) {
            flash('error', 'Curso no encontrado.');
            $this->redirect('/dashboard');
        }

        $esInicial = $course['nivel'] === 'Inicial';
        $gestionId = (int) $gestion['id_gestion'];

        $subjects = CourseView::subjects($idCurso);
        $students = CourseView::students($idCurso, $gestionId);
        $grades = CourseView::grades($idCurso, $gestionId, $esInicial);
        $trimestres = CourseView::trimestres($gestionId);
        $allCourses = CourseView::allCourses();

        // Build prev/next navigation
        $prevCurso = null;
        $nextCurso = null;
        foreach ($allCourses as $i => $c) {
            if ((int) $c['id_curso'] === $idCurso) {
                if ($i > 0) $prevCurso = $allCourses[$i - 1];
                if ($i < count($allCourses) - 1) $nextCurso = $allCourses[$i + 1];
                break;
            }
        }

        $this->view('curso/ver', [
            'title' => 'Centralizador - ' . $course['nivel'] . ' ' . $course['grado'] . '° ' . $course['paralelo'],
            'course' => $course,
            'gestion' => $gestion,
            'subjects' => $subjects,
            'students' => $students,
            'grades' => $grades,
            'trimestres' => $trimestres,
            'esInicial' => $esInicial,
            'prevCurso' => $prevCurso,
            'nextCurso' => $nextCurso,
        ]);
    }
}
