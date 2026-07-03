<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Role
{
    public static function allActive(): array
    {
        return Database::connection()
            ->query('SELECT id_rol, nombre FROM roles WHERE estado = 1 ORDER BY nombre')
            ->fetchAll();
    }
}
