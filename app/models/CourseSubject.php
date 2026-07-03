<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class CourseSubject
{
    public static function all(array $filters = []): array
    {
        $sql = "SELECT cm.id_curso_materia, cm.estado, cm.created_at,
                       c.id_curso, c.nivel, c.grado, c.paralelo, c.turno,
                       m.id_materia, m.nombre AS materia, m.abreviatura, m.es_submateria, m.es_extra,
                       mp.nombre AS materia_padre
                FROM curso_materia cm
                INNER JOIN cursos c ON c.id_curso = cm.id_curso
                INNER JOIN materias m ON m.id_materia = cm.id_materia
                LEFT JOIN materias mp ON mp.id_materia = m.id_materia_padre
                WHERE 1 = 1";

        $params = [];
        if (!empty($filters['nivel'])) {
            $sql .= ' AND c.nivel = :nivel';
            $params['nivel'] = $filters['nivel'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (m.nombre LIKE :search OR m.abreviatura LIKE :search OR c.paralelo LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY FIELD(c.nivel, 'Inicial', 'Primaria', 'Secundaria'), c.grado, c.paralelo, m.nombre";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM curso_materia WHERE id_curso_materia = ? LIMIT 1');
        $stmt->execute([$id]);
        $assignment = $stmt->fetch();

        return $assignment ?: null;
    }

    public static function create(array $data): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO curso_materia (id_curso, id_materia, estado) VALUES (:id_curso, :id_materia, :estado)'
        );
        $stmt->execute([
            'id_curso' => $data['id_curso'],
            'id_materia' => $data['id_materia'],
            'estado' => $data['estado'],
        ]);
    }

    public static function update(int $id, array $data): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE curso_materia SET id_curso = :id_curso, id_materia = :id_materia, estado = :estado WHERE id_curso_materia = :id_curso_materia'
        );
        $stmt->execute([
            'id_curso' => $data['id_curso'],
            'id_materia' => $data['id_materia'],
            'estado' => $data['estado'],
            'id_curso_materia' => $id,
        ]);
    }

    public static function changeStatus(int $id, int $status): void
    {
        $stmt = Database::connection()->prepare('UPDATE curso_materia SET estado = ? WHERE id_curso_materia = ?');
        $stmt->execute([$status, $id]);
    }
}
