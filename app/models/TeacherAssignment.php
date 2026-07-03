<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class TeacherAssignment
{
    public static function all(array $filters = []): array
    {
        $sql = "SELECT da.id_asignacion, da.estado, da.estado_carga, da.created_at,
                       p.nombres, p.apellidos, p.ci,
                       c.nivel, c.grado, c.paralelo,
                       m.nombre AS materia, m.abreviatura,
                       g.nombre AS gestion, g.anio
                FROM docente_asignaciones da
                INNER JOIN personal p ON p.id_personal = da.id_personal
                INNER JOIN curso_materia cm ON cm.id_curso_materia = da.id_curso_materia
                INNER JOIN cursos c ON c.id_curso = cm.id_curso
                INNER JOIN materias m ON m.id_materia = cm.id_materia
                INNER JOIN gestiones g ON g.id_gestion = da.id_gestion
                WHERE 1 = 1";

        $params = [];
        if (!empty($filters['nivel'])) {
            $sql .= ' AND c.nivel = :nivel';
            $params['nivel'] = $filters['nivel'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (p.nombres LIKE :search_nombres
                       OR p.apellidos LIKE :search_apellidos
                       OR CONCAT(p.nombres, ' ', p.apellidos) LIKE :search_nombre_completo
                       OR CONCAT(p.apellidos, ' ', p.nombres) LIKE :search_apellido_completo
                       OR p.ci LIKE :search_ci
                       OR m.nombre LIKE :search_materia
                       OR m.abreviatura LIKE :search_abreviatura)";
            $searchVal = '%' . $filters['search'] . '%';
            $params['search_nombres'] = $searchVal;
            $params['search_apellidos'] = $searchVal;
            $params['search_nombre_completo'] = $searchVal;
            $params['search_apellido_completo'] = $searchVal;
            $params['search_ci'] = $searchVal;
            $params['search_materia'] = $searchVal;
            $params['search_abreviatura'] = $searchVal;
        }

        $sql .= " ORDER BY FIELD(c.nivel, 'Inicial', 'Primaria', 'Secundaria'), c.grado, c.paralelo, m.nombre, p.apellidos";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM docente_asignaciones WHERE id_asignacion = ? LIMIT 1');
        $stmt->execute([$id]);
        $assignment = $stmt->fetch();

        return $assignment ?: null;
    }

    public static function teachers(): array
    {
        $sql = "SELECT DISTINCT p.id_personal, p.nombres, p.apellidos, p.ci, p.especialidad
                FROM personal p
                INNER JOIN usuarios u ON u.id_personal = p.id_personal
                INNER JOIN roles r ON r.id_rol = u.id_rol
                WHERE r.nombre = 'Docente'
                  AND p.estado = 1
                  AND p.deleted_at IS NULL
                  AND u.estado = 'activo'
                  AND u.deleted_at IS NULL
                ORDER BY p.apellidos, p.nombres";

        return Database::connection()->query($sql)->fetchAll();
    }

    public static function courseSubjects(): array
    {
        $sql = "SELECT cm.id_curso_materia,
                       CONCAT(c.nivel, ' ', c.grado, '° ', c.paralelo, ' - ', m.nombre) AS nombre
                FROM curso_materia cm
                INNER JOIN cursos c ON c.id_curso = cm.id_curso
                INNER JOIN materias m ON m.id_materia = cm.id_materia
                WHERE cm.estado = 1 AND c.estado = 1 AND m.estado = 1
                ORDER BY FIELD(c.nivel, 'Inicial', 'Primaria', 'Secundaria'), c.grado, c.paralelo, m.nombre";

        return Database::connection()->query($sql)->fetchAll();
    }

    public static function activeGestiones(): array
    {
        return Database::connection()
            ->query("SELECT id_gestion, nombre, anio FROM gestiones WHERE estado IN ('activa','planificada') ORDER BY anio DESC")
            ->fetchAll();
    }

    public static function create(array $data): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO docente_asignaciones (id_personal, id_curso_materia, id_gestion, estado, estado_carga)
             VALUES (:id_personal, :id_curso_materia, :id_gestion, :estado, :estado_carga)'
        );
        $stmt->execute($data);
    }

    public static function update(int $id, array $data): void
    {
        $data['id_asignacion'] = $id;
        $stmt = Database::connection()->prepare(
            'UPDATE docente_asignaciones
             SET id_personal = :id_personal, id_curso_materia = :id_curso_materia,
                 id_gestion = :id_gestion, estado = :estado, estado_carga = :estado_carga
             WHERE id_asignacion = :id_asignacion'
        );
        $stmt->execute($data);
    }

    public static function changeStatus(int $id, string $status): void
    {
        $stmt = Database::connection()->prepare('UPDATE docente_asignaciones SET estado = ? WHERE id_asignacion = ?');
        $stmt->execute([$status, $id]);
    }
}
