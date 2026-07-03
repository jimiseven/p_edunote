<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CourseView;

class CourseViewController extends Controller
{
    public function show(): void
    {
        require_any_role(['Administrador', 'Secretaria', 'Director']);

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

    public function excel(): void
    {
        require_any_role(['Administrador', 'Secretaria', 'Director']);

        $idCurso = (int) ($_GET['curso'] ?? 0);
        if ($idCurso <= 0) exit('Curso no valido.');

        $course = CourseView::info($idCurso);
        if (!$course) exit('Curso no encontrado.');

        $gestion = CourseView::activeGestion();
        if (!$gestion) exit('No hay gestion activa.');

        $esInicial = $course['nivel'] === 'Inicial';
        $gestionId = (int) $gestion['id_gestion'];

        $subjects = CourseView::subjects($idCurso);
        $students = CourseView::students($idCurso, $gestionId);
        $grades = CourseView::grades($idCurso, $gestionId, $esInicial);
        $trimestres = CourseView::trimestres($gestionId);

        // --- BUILD EXCEL ---
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Centralizador');

        $col = 1; $row = 1;
        $c = fn($col, $row) => \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row;

        // Title row
        $sheet->setCellValue($c(1, $row), 'CENTRALIZADOR: ' . $course['nivel'] . ' ' . $course['grado'] . '° ' . $course['paralelo']);
        $sheet->mergeCells($c(1, $row) . ':' . $c(3, $row));
        $sheet->getStyle($c(1, $row))->getFont()->setBold(true)->setSize(14);
        $row++;

        $sheet->setCellValue($c(1, $row), $gestion['nombre'] . ' - ' . $gestion['anio']);
        $sheet->mergeCells($c(1, $row) . ':' . $c(3, $row));
        $row += 2;

        // Header row 1
        $startCol = 1;
        $sheet->setCellValue($c($startCol, $row), '#');
        $sheet->setCellValue($c($startCol + 1, $row), 'ESTUDIANTE');

        $currentCol = $startCol + 2;
        if ($esInicial) {
            foreach ($subjects as $sub) {
                $mergeEnd = $currentCol + count($trimestres) - 1;
                $sheet->setCellValue($c($currentCol, $row), $sub['nombre']);
                $sheet->mergeCells($c($currentCol, $row) . ':' . $c($mergeEnd, $row));
                $currentCol = $mergeEnd + 1;
            }
        } else {
            foreach ($subjects as $sub) {
                $sheet->setCellValue($c($currentCol, $row), $sub['nombre']);
                $sheet->mergeCells($c($currentCol, $row) . ':' . $c($currentCol + 3, $row));
                $currentCol += 4;
            }
            $sheet->setCellValue($c($currentCol, $row), 'P. GENERAL');
        }
        $sheet->getStyle($c($startCol, $row) . ':' . $c($currentCol, $row))->getFont()->setBold(true);
        $sheet->getStyle($c($startCol, $row) . ':' . $c($currentCol, $row + 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $row++;

        // Header row 2
        $sheet->setCellValue($c($startCol, $row), '');
        $sheet->setCellValue($c($startCol + 1, $row), '');
        $currentCol = $startCol + 2;
        if ($esInicial) {
            foreach ($subjects as $sub) {
                foreach ($trimestres as $t) {
                    $sheet->setCellValue($c($currentCol++, $row), 'T' . $t['numero']);
                }
            }
        } else {
            foreach ($subjects as $sub) {
                $sheet->setCellValue($c($currentCol++, $row), 'T1');
                $sheet->setCellValue($c($currentCol++, $row), 'T2');
                $sheet->setCellValue($c($currentCol++, $row), 'T3');
                $sheet->setCellValue($c($currentCol++, $row), 'P');
            }
            $sheet->setCellValue($c($currentCol, $row), '');
        }
        $sheet->getStyle($c($startCol, $row) . ':' . $c($currentCol, $row))->getFont()->setBold(true);
        $row++;

        // Data rows
        $contador = 1;
        foreach ($students as $student) {
            $idMat = (int) $student['id_matricula'];
            $nombre = strtoupper(trim($student['apellido_paterno'] . ' ' . $student['apellido_materno'] . ', ' . $student['nombres']));

            $sheet->setCellValue($c($startCol, $row), $contador++);
            $sheet->setCellValue($c($startCol + 1, $row), $nombre);

            $currentCol = $startCol + 2;

            if ($esInicial) {
                foreach ($subjects as $sub) {
                    foreach ($trimestres as $t) {
                        $val = $grades[$idMat][$sub['id_materia']][$t['id_trimestre']] ?? '';
                        $sheet->setCellValue($c($currentCol++, $row), $val);
                    }
                }
            } else {
                $sumaPromedios = 0;
                $contPromedios = 0;

                foreach ($subjects as $sub) {
                    $n1 = $grades[$idMat][$sub['id_materia']][1] ?? '';
                    $n2 = $grades[$idMat][$sub['id_materia']][2] ?? '';
                    $n3 = $grades[$idMat][$sub['id_materia']][3] ?? '';

                    $sheet->setCellValue($c($currentCol++, $row), $n1);
                    $sheet->setCellValue($c($currentCol++, $row), $n2);
                    $sheet->setCellValue($c($currentCol++, $row), $n3);

                    $notasValidas = array_filter([$n1, $n2, $n3], fn($v) => is_numeric($v));
                    $promedio = count($notasValidas) > 0 ? round(array_sum($notasValidas) / count($notasValidas), 2) : '';
                    $sheet->setCellValue($c($currentCol++, $row), $promedio);

                    if ($promedio !== '' && !$sub['es_extra'] && !$sub['es_submateria']) {
                        $sumaPromedios += (float) $promedio;
                        $contPromedios++;
                    }
                }

                $promGeneral = $contPromedios > 0 ? round($sumaPromedios / $contPromedios, 2) : '--';
                $sheet->setCellValue($c($currentCol, $row), $promGeneral);
            }

            $row++;
        }

        // Auto-width columns
        $lastCol = $currentCol;
        for ($i = 1; $i <= $lastCol; $i++) {
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
        }

        // Output
        $filename = 'Centralizador_' . str_replace(' ', '_', $course['nivel'] . '_' . $course['grado'] . '_' . $course['paralelo']) . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
