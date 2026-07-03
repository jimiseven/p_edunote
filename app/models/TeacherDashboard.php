<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class TeacherDashboard
{
    public static function assignedCourses(int $userId): array
    {
        $sql = "SELECT da.id_asignacion, da.estado_carga, da.estado,
                       cm.id_curso_materia,
                       c.nivel, c.grado, c.paralelo, c.turno,
                       m.nombre AS materia, m.abreviatura,
                       g.nombre AS gestion, g.anio,
                       COUNT(DISTINCT mat.id_matricula) AS total_estudiantes
                FROM usuarios u
                INNER JOIN personal p ON p.id_personal = u.id_personal
                INNER JOIN docente_asignaciones da ON da.id_personal = p.id_personal
                INNER JOIN curso_materia cm ON cm.id_curso_materia = da.id_curso_materia
                INNER JOIN cursos c ON c.id_curso = cm.id_curso
                INNER JOIN materias m ON m.id_materia = cm.id_materia
                INNER JOIN gestiones g ON g.id_gestion = da.id_gestion
                LEFT JOIN matriculas mat ON mat.id_curso = c.id_curso
                    AND mat.id_gestion = g.id_gestion
                    AND mat.estado = 'activo'
                    AND mat.deleted_at IS NULL
                WHERE u.id_usuario = :user_id
                  AND da.estado = 'activo'
                  AND cm.estado = 1
                  AND c.estado = 1
                  AND m.estado = 1
                GROUP BY da.id_asignacion, cm.id_curso_materia, c.id_curso, m.id_materia, g.id_gestion
                ORDER BY FIELD(c.nivel, 'Inicial', 'Primaria', 'Secundaria'), c.grado, c.paralelo, m.nombre";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }
}
