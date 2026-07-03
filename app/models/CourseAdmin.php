<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class CourseAdmin
{
    public static function all(string $search = ''): array
    {
        $sql = "SELECT id_curso, nivel, grado, paralelo, turno, estado, created_at
                FROM cursos
                WHERE 1 = 1";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (nivel LIKE :search OR grado LIKE :search OR paralelo LIKE :search OR turno LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY FIELD(nivel, 'Inicial', 'Primaria', 'Secundaria'), grado, paralelo";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM cursos WHERE id_curso = ? LIMIT 1');
        $stmt->execute([$id]);
        $course = $stmt->fetch();

        return $course ?: null;
    }

    public static function create(array $data): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO cursos (nivel, grado, paralelo, turno, estado) VALUES (:nivel, :grado, :paralelo, :turno, :estado)'
        );
        $stmt->execute([
            'nivel' => $data['nivel'],
            'grado' => $data['grado'],
            'paralelo' => $data['paralelo'],
            'turno' => $data['turno'] ?: null,
            'estado' => $data['estado'],
        ]);
    }

    public static function update(int $id, array $data): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE cursos SET nivel = :nivel, grado = :grado, paralelo = :paralelo, turno = :turno, estado = :estado WHERE id_curso = :id_curso'
        );
        $stmt->execute([
            'nivel' => $data['nivel'],
            'grado' => $data['grado'],
            'paralelo' => $data['paralelo'],
            'turno' => $data['turno'] ?: null,
            'estado' => $data['estado'],
            'id_curso' => $id,
        ]);
    }

    public static function changeStatus(int $id, int $status): void
    {
        $stmt = Database::connection()->prepare('UPDATE cursos SET estado = ? WHERE id_curso = ?');
        $stmt->execute([$status, $id]);
    }
}
