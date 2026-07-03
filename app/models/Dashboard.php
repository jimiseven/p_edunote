<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Dashboard
{
    public static function stats(): array
    {
        $conn = Database::connection();

        return [
            'estudiantes' => (int) $conn->query('SELECT COUNT(*) FROM estudiantes WHERE deleted_at IS NULL')->fetchColumn(),
            'personal' => (int) $conn->query('SELECT COUNT(*) FROM personal WHERE deleted_at IS NULL')->fetchColumn(),
            'cursos' => (int) $conn->query('SELECT COUNT(*) FROM cursos WHERE estado = 1')->fetchColumn(),
            'usuarios' => (int) $conn->query("SELECT COUNT(*) FROM usuarios WHERE estado = 'activo' AND deleted_at IS NULL")->fetchColumn(),
        ];
    }

    public static function coursesByLevel(string $nivel): array
    {
        $conn = Database::connection();

        $sql = "SELECT c.id_curso, c.grado, c.paralelo,
                       COUNT(e.id_estudiante) AS total_estudiantes,
                       SUM(CASE WHEN e.genero = 'Masculino' THEN 1 ELSE 0 END) AS hombres,
                       SUM(CASE WHEN e.genero = 'Femenino' THEN 1 ELSE 0 END) AS mujeres
                FROM cursos c
                LEFT JOIN matriculas m ON m.id_curso = c.id_curso
                    AND m.estado = 'activo'
                    AND m.deleted_at IS NULL
                LEFT JOIN estudiantes e ON e.id_estudiante = m.id_estudiante
                    AND e.deleted_at IS NULL
                WHERE c.nivel = :nivel AND c.estado = 1
                GROUP BY c.id_curso, c.grado, c.paralelo
                ORDER BY c.grado, c.paralelo";

        $stmt = $conn->prepare($sql);
        $stmt->execute(['nivel' => $nivel]);

        return $stmt->fetchAll();
    }
}
