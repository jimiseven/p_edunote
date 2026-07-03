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
}
