<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class CourseView
{
    public static function info(int $idCurso): ?array
    {
        $stmt = Database::connection()->prepare(
            "SELECT c.id_curso, c.nivel, c.grado, c.paralelo, c.turno
             FROM cursos c
             WHERE c.id_curso = ? AND c.estado = 1"
        );
        $stmt->execute([$idCurso]);
        $info = $stmt->fetch();

        return $info ?: null;
    }

    public static function activeGestion(): ?array
    {
        $stmt = Database::connection()->query(
            "SELECT id_gestion, nombre, anio
             FROM gestiones
             WHERE estado = 'activa'
             LIMIT 1"
        );
        $gestion = $stmt->fetch();

        return $gestion ?: null;
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

        // Separate into padres, extras, hijas
        $padres = [];
        $extras = [];
        $hijas = [];

        foreach ($all as $m) {
            if ($m['es_extra']) {
                $extras[] = $m;
            } elseif (!$m['es_submateria']) {
                $m['hijas'] = [];
                $padres[$m['id_materia']] = $m;
            } else {
                $hijas[] = $m;
            }
        }

        // Attach hijas to their padres
        foreach ($hijas as $h) {
            if (isset($padres[$h['id_materia_padre']])) {
                $padres[$h['id_materia_padre']]['hijas'][] = $h;
            }
        }

        // Final order: padres simples -> extras -> padres con hijas -> their hijas
        $padresSimples = [];
        $padresConHijas = [];
        foreach ($padres as $p) {
            if (empty($p['hijas'])) {
                $padresSimples[] = $p;
            } else {
                $padresConHijas[] = $p;
            }
        }

        $result = array_merge($padresSimples, $extras, $padresConHijas);
        foreach ($padresConHijas as $p) {
            $result = array_merge($result, $p['hijas']);
        }

        return $result;
    }

    public static function students(int $idCurso, int $idGestion): array
    {
        $sql = "SELECT e.id_estudiante, e.nombres, e.apellido_paterno, e.apellido_materno,
                       m.id_matricula
                FROM estudiantes e
                INNER JOIN matriculas m ON m.id_estudiante = e.id_estudiante
                WHERE m.id_curso = ?
                  AND m.id_gestion = ?
                  AND m.estado = 'activo'
                  AND m.deleted_at IS NULL
                  AND e.deleted_at IS NULL
                ORDER BY e.apellido_paterno, e.apellido_materno, e.nombres";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$idCurso, $idGestion]);

        return $stmt->fetchAll();
    }

    public static function grades(int $idCurso, int $idGestion, bool $esInicial): array
    {
        $field = $esInicial ? 'c.comentario' : 'c.nota';
        $sql = "SELECT m.id_matricula, c.id_materia, c.id_trimestre, $field AS valor
                FROM calificaciones c
                INNER JOIN matriculas m ON m.id_matricula = c.id_matricula
                WHERE m.id_curso = ?
                  AND m.id_gestion = ?
                ORDER BY m.id_matricula, c.id_materia, c.id_trimestre";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$idCurso, $idGestion]);

        $grades = [];
        foreach ($stmt->fetchAll() as $row) {
            $grades[(int) $row['id_matricula']][(int) $row['id_materia']][(int) $row['id_trimestre']] = $row['valor'];
        }

        return $grades;
    }

    public static function trimestres(int $idGestion): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT id_trimestre, numero, nombre, esta_activo
             FROM trimestres
             WHERE id_gestion = ?
             ORDER BY numero"
        );
        $stmt->execute([$idGestion]);
        return $stmt->fetchAll();
    }

    public static function allCourses(): array
    {
        $stmt = Database::connection()->query(
            "SELECT id_curso, nivel, grado, paralelo
             FROM cursos
             WHERE estado = 1
             ORDER BY FIELD(nivel, 'Inicial', 'Primaria', 'Secundaria'), grado, paralelo"
        );
        return $stmt->fetchAll();
    }
}
