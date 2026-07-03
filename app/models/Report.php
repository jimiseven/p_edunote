<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Report
{
    public static function courseInfo(int $idCurso): ?array
    {
        $stmt = Database::connection()->prepare(
            "SELECT id_curso, nivel, grado, paralelo FROM cursos WHERE id_curso = ? AND estado = 1"
        );
        $stmt->execute([$idCurso]);
        return $stmt->fetch() ?: null;
    }

    public static function activeGestion(): ?array
    {
        $stmt = Database::connection()->query(
            "SELECT id_gestion, nombre, anio FROM gestiones WHERE estado = 'activa' LIMIT 1"
        );
        return $stmt->fetch() ?: null;
    }

    public static function subjects(int $idCurso): array
    {
        $sql = "SELECT m.id_materia, m.nombre, m.abreviatura,
                       m.es_submateria, m.id_materia_padre, m.es_extra
                FROM curso_materia cm
                INNER JOIN materias m ON m.id_materia = cm.id_materia
                WHERE cm.id_curso = ? AND cm.estado = 1 AND m.estado = 1
                ORDER BY m.nombre";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$idCurso]);
        $all = $stmt->fetchAll();

        $individuales = [];
        $padres = [];
        $hijas = [];

        foreach ($all as $m) {
            if ($m['es_submateria']) {
                $hijas[$m['id_materia_padre']][] = $m;
            } elseif ($m['es_extra']) {
                $m['es_extra_flag'] = true;
                $individuales[] = $m;
            } else {
                $padres[$m['id_materia']] = $m;
            }
        }

        // Attach hijas to parents, move childless parents to individuales
        $grupos = [];
        foreach ($padres as $p) {
            if (!empty($hijas[$p['id_materia']])) {
                $p['hijas'] = $hijas[$p['id_materia']];
                $grupos[] = $p;
            } else {
                $individuales[] = $p;
            }
        }

        return [
            'individuales' => $individuales,
            'grupos' => $grupos,
            'todas' => $all,
        ];
    }

    public static function students(int $idCurso, int $idGestion): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT e.id_estudiante, e.nombres, e.apellido_paterno, e.apellido_materno
             FROM estudiantes e
             INNER JOIN matriculas m ON m.id_estudiante = e.id_estudiante
             WHERE m.id_curso = ? AND m.id_gestion = ?
               AND m.estado = 'activo' AND m.deleted_at IS NULL AND e.deleted_at IS NULL
             ORDER BY e.apellido_paterno, e.apellido_materno, e.nombres"
        );
        $stmt->execute([$idCurso, $idGestion]);
        return $stmt->fetchAll();
    }

    public static function gradesByCourse(int $idCurso, int $idGestion): array
    {
        $sql = "SELECT m.id_matricula, e.id_estudiante, c.id_materia, c.id_trimestre, c.nota
                FROM calificaciones c
                INNER JOIN matriculas m ON m.id_matricula = c.id_matricula
                INNER JOIN estudiantes e ON e.id_estudiante = m.id_estudiante
                WHERE m.id_curso = ? AND m.id_gestion = ? AND m.estado = 'activo'
                ORDER BY e.id_estudiante, c.id_materia, c.id_trimestre";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$idCurso, $idGestion]);

        $grades = [];
        foreach ($stmt->fetchAll() as $row) {
            $grades[(int) $row['id_estudiante']][(int) $row['id_materia']][(int) $row['id_trimestre']] = (float) $row['nota'];
        }

        return $grades;
    }
}

