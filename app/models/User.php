<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class User
{
    public static function findActiveByUsername(string $username): ?array
    {
        $sql = "SELECT u.id_usuario, u.username, u.email, u.password_hash, r.nombre AS rol_nombre,
                       p.nombres, p.apellidos
                FROM usuarios u
                INNER JOIN roles r ON r.id_rol = u.id_rol
                LEFT JOIN personal p ON p.id_personal = u.id_personal
                WHERE u.username = :username
                  AND u.estado = 'activo'
                  AND u.deleted_at IS NULL
                LIMIT 1";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public static function registerLogin(int $userId): void
    {
        $conn = Database::connection();
        $conn->prepare('UPDATE usuarios SET ultimo_login = NOW() WHERE id_usuario = ?')->execute([$userId]);
        $conn->prepare(
            'INSERT INTO sesiones_usuario (id_usuario, session_id, ip, user_agent) VALUES (?, ?, ?, ?)'
        )->execute([
            $userId,
            session_id(),
            $_SERVER['REMOTE_ADDR'] ?? null,
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
        ]);
    }
}
