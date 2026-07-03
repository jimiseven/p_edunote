<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class SubjectAdmin
{
    public static function all(string $search = ''): array
    {
        $sql = "SELECT m.id_materia, m.nombre, m.abreviatura, m.descripcion, m.es_submateria,
                       m.id_materia_padre, p.nombre AS materia_padre, m.es_extra, m.estado
                FROM materias m
                LEFT JOIN materias p ON p.id_materia = m.id_materia_padre
                WHERE 1 = 1";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (m.nombre LIKE :search OR m.abreviatura LIKE :search OR p.nombre LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY COALESCE(p.nombre, m.nombre), m.es_submateria, m.nombre';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function parents(?int $excludeId = null): array
    {
        $sql = 'SELECT id_materia, nombre FROM materias WHERE estado = 1 AND es_submateria = 0';
        $params = [];

        if ($excludeId !== null) {
            $sql .= ' AND id_materia <> :exclude_id';
            $params['exclude_id'] = $excludeId;
        }

        $sql .= ' ORDER BY nombre';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM materias WHERE id_materia = ? LIMIT 1');
        $stmt->execute([$id]);
        $subject = $stmt->fetch();

        return $subject ?: null;
    }

    public static function create(array $data): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO materias (nombre, abreviatura, descripcion, es_submateria, id_materia_padre, es_extra, estado)
             VALUES (:nombre, :abreviatura, :descripcion, :es_submateria, :id_materia_padre, :es_extra, :estado)'
        );
        $stmt->execute(self::params($data));
    }

    public static function update(int $id, array $data): void
    {
        $params = self::params($data);
        $params['id_materia'] = $id;

        $stmt = Database::connection()->prepare(
            'UPDATE materias
             SET nombre = :nombre, abreviatura = :abreviatura, descripcion = :descripcion,
                 es_submateria = :es_submateria, id_materia_padre = :id_materia_padre,
                 es_extra = :es_extra, estado = :estado
             WHERE id_materia = :id_materia'
        );
        $stmt->execute($params);
    }

    public static function changeStatus(int $id, int $status): void
    {
        $stmt = Database::connection()->prepare('UPDATE materias SET estado = ? WHERE id_materia = ?');
        $stmt->execute([$status, $id]);
    }

    private static function params(array $data): array
    {
        return [
            'nombre' => $data['nombre'],
            'abreviatura' => $data['abreviatura'] ?: null,
            'descripcion' => $data['descripcion'] ?: null,
            'es_submateria' => $data['es_submateria'],
            'id_materia_padre' => $data['es_submateria'] === 1 ? $data['id_materia_padre'] : null,
            'es_extra' => $data['es_extra'],
            'estado' => $data['estado'],
        ];
    }
}
