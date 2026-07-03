<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class UserAdmin
{
    public static function all(string $search = ''): array
    {
        $sql = "SELECT u.id_usuario, u.username, u.email, u.estado, u.ultimo_login,
                       r.nombre AS rol_nombre, p.nombres, p.apellidos, p.ci, p.celular
                FROM usuarios u
                INNER JOIN roles r ON r.id_rol = u.id_rol
                LEFT JOIN personal p ON p.id_personal = u.id_personal
                WHERE u.deleted_at IS NULL";

        $params = [];
        if ($search !== '') {
            $sql .= " AND (u.username LIKE :search OR u.email LIKE :search OR p.nombres LIKE :search OR p.apellidos LIKE :search OR p.ci LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY p.apellidos ASC, p.nombres ASC, u.username ASC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $sql = "SELECT u.id_usuario, u.id_personal, u.id_rol, u.username, u.email, u.estado,
                       p.nombres, p.apellidos, p.ci, p.celular, p.cargo, p.especialidad
                FROM usuarios u
                LEFT JOIN personal p ON p.id_personal = u.id_personal
                WHERE u.id_usuario = ? AND u.deleted_at IS NULL
                LIMIT 1";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public static function create(array $data): void
    {
        $conn = Database::connection();
        $conn->beginTransaction();

        try {
            $stmt = $conn->prepare("INSERT INTO personal (nombres, apellidos, ci, celular, email, cargo, especialidad, estado)
                                    VALUES (:nombres, :apellidos, :ci, :celular, :email, :cargo, :especialidad, 1)");
            $stmt->execute([
                'nombres' => $data['nombres'],
                'apellidos' => $data['apellidos'],
                'ci' => $data['ci'],
                'celular' => $data['celular'] ?: null,
                'email' => $data['email'] ?: null,
                'cargo' => $data['cargo'] ?: null,
                'especialidad' => $data['especialidad'] ?: null,
            ]);

            $personalId = (int) $conn->lastInsertId();

            $stmt = $conn->prepare("INSERT INTO usuarios (id_personal, id_rol, username, email, password_hash, estado)
                                    VALUES (:id_personal, :id_rol, :username, :email, :password_hash, 'activo')");
            $stmt->execute([
                'id_personal' => $personalId,
                'id_rol' => $data['id_rol'],
                'username' => $data['username'],
                'email' => $data['email'] ?: null,
                'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            ]);

            $conn->commit();
        } catch (\Throwable $exception) {
            $conn->rollBack();
            throw $exception;
        }
    }

    public static function update(int $id, array $data): void
    {
        $conn = Database::connection();
        $conn->beginTransaction();

        try {
            $user = self::find($id);
            if (!$user) {
                throw new \RuntimeException('Usuario no encontrado.');
            }

            $stmt = $conn->prepare("UPDATE personal
                                    SET nombres = :nombres, apellidos = :apellidos, ci = :ci, celular = :celular,
                                        email = :email, cargo = :cargo, especialidad = :especialidad
                                    WHERE id_personal = :id_personal");
            $stmt->execute([
                'nombres' => $data['nombres'],
                'apellidos' => $data['apellidos'],
                'ci' => $data['ci'],
                'celular' => $data['celular'] ?: null,
                'email' => $data['email'] ?: null,
                'cargo' => $data['cargo'] ?: null,
                'especialidad' => $data['especialidad'] ?: null,
                'id_personal' => $user['id_personal'],
            ]);

            $sql = "UPDATE usuarios SET id_rol = :id_rol, username = :username, email = :email";
            $params = [
                'id_rol' => $data['id_rol'],
                'username' => $data['username'],
                'email' => $data['email'] ?: null,
                'id_usuario' => $id,
            ];

            if (($data['password'] ?? '') !== '') {
                $sql .= ', password_hash = :password_hash';
                $params['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $sql .= ' WHERE id_usuario = :id_usuario';
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            $conn->commit();
        } catch (\Throwable $exception) {
            $conn->rollBack();
            throw $exception;
        }
    }

    public static function changeStatus(int $id, string $status): void
    {
        $stmt = Database::connection()->prepare("UPDATE usuarios SET estado = :estado WHERE id_usuario = :id_usuario");
        $stmt->execute([
            'estado' => $status,
            'id_usuario' => $id,
        ]);
    }
}
