<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class AcademicPeriod
{
    public static function allGestiones(): array
    {
        $stmt = Database::connection()->query(
            "SELECT id_gestion, anio, nombre, fecha_inicio, fecha_fin, estado
             FROM gestiones
             ORDER BY anio DESC"
        );

        return $stmt->fetchAll();
    }

    public static function trimestresByGestion(int $idGestion): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT id_trimestre, id_gestion, numero, nombre, fecha_inicio, fecha_fin, esta_activo
             FROM trimestres
             WHERE id_gestion = ?
             ORDER BY numero"
        );
        $stmt->execute([$idGestion]);

        return $stmt->fetchAll();
    }

    public static function gestionExists(int $idGestion): bool
    {
        $stmt = Database::connection()->prepare('SELECT COUNT(*) FROM gestiones WHERE id_gestion = ?');
        $stmt->execute([$idGestion]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public static function trimestreGestion(int $idTrimestre): ?int
    {
        $stmt = Database::connection()->prepare('SELECT id_gestion FROM trimestres WHERE id_trimestre = ? LIMIT 1');
        $stmt->execute([$idTrimestre]);
        $idGestion = $stmt->fetchColumn();

        return $idGestion === false ? null : (int) $idGestion;
    }

    public static function updateDates(int $idGestion, array $trimestres): void
    {
        $conn = Database::connection();
        $stmt = $conn->prepare(
            'UPDATE trimestres
             SET fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin
             WHERE id_trimestre = :id_trimestre AND id_gestion = :id_gestion'
        );

        foreach ($trimestres as $idTrimestre => $data) {
            $stmt->execute([
                'fecha_inicio' => $data['fecha_inicio'] ?: null,
                'fecha_fin' => $data['fecha_fin'] ?: null,
                'id_trimestre' => (int) $idTrimestre,
                'id_gestion' => $idGestion,
            ]);
        }
    }

    public static function setActiveStatus(int $idTrimestre, bool $active): void
    {
        $stmt = Database::connection()->prepare('UPDATE trimestres SET esta_activo = ? WHERE id_trimestre = ?');
        $stmt->execute([$active ? 1 : 0, $idTrimestre]);
    }
}
