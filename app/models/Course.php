<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Course
{
    public static function allActive(): array
    {
        $sql = "SELECT id_curso, nivel, grado, paralelo,
                       CONCAT(nivel, ' ', grado, '° ', paralelo) AS nombre
                FROM cursos
                WHERE estado = 1
                ORDER BY FIELD(nivel, 'Inicial', 'Primaria', 'Secundaria'), grado, paralelo";

        return Database::connection()->query($sql)->fetchAll();
    }
}
