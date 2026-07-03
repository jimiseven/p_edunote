<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Grade
{
    public static function assignmentInfo(int $idAsignacion, ?int $idUsuario = null): ?array
    {
        $sql = "SELECT da.id_asignacion, da.id_gestion, da.id_curso_materia, da.estado_carga,
                       da.id_personal,
                       cm.id_curso, cm.id_materia,
                       c.nivel, c.grado, c.paralelo, c.turno,
                       m.nombre AS materia, m.abreviatura,
                       g.nombre AS gestion_nombre, g.anio
                FROM docente_asignaciones da
                INNER JOIN personal p ON p.id_personal = da.id_personal
                INNER JOIN curso_materia cm ON cm.id_curso_materia = da.id_curso_materia
                INNER JOIN cursos c ON c.id_curso = cm.id_curso
                INNER JOIN materias m ON m.id_materia = cm.id_materia
                INNER JOIN gestiones g ON g.id_gestion = da.id_gestion
                WHERE da.id_asignacion = ? AND da.estado = 'activo'";

        $params = [$idAsignacion];

        if ($idUsuario !== null) {
            $sql .= ' AND EXISTS (SELECT 1 FROM usuarios u WHERE u.id_usuario = ? AND u.id_personal = p.id_personal)';
            $params[] = $idUsuario;
        }

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        $info = $stmt->fetch();

        return $info ?: null;
    }

    public static function trimestres(int $idGestion): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT id_trimestre, numero, nombre, esta_activo
             FROM trimestres
             WHERE id_gestion = ?
             ORDER BY numero"
        );
        $stmt->execute([$idGestion]);
        return $stmt->fetchAll();
    }

    public static function activeTrimestreIds(int $idGestion): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT id_trimestre FROM trimestres WHERE id_gestion = ? AND esta_activo = 1"
        );
        $stmt->execute([$idGestion]);

        return array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    public static function enrolledStudents(int $idCurso, int $idGestion): array
    {
        $sql = "SELECT e.id_estudiante, e.nombres, e.apellido_paterno, e.apellido_materno, e.genero,
                       m.id_matricula
                FROM estudiantes e
                INNER JOIN matriculas m ON m.id_estudiante = e.id_estudiante
                WHERE m.id_curso = ?
                  AND m.id_gestion = ?
                  AND m.estado = 'activo'
                  AND m.deleted_at IS NULL
                  AND e.deleted_at IS NULL
                ORDER BY e.apellido_paterno, e.apellido_materno, e.nombres";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$idCurso, $idGestion]);
        return $stmt->fetchAll();
    }

    public static function existingGrades(int $idMateria, int $idGestion, bool $esInicial): array
    {
        $field = $esInicial ? 'c.comentario' : 'c.nota';
        $sql = "SELECT c.id_matricula, c.id_trimestre, $field AS valor
                FROM calificaciones c
                INNER JOIN matriculas m ON m.id_matricula = c.id_matricula
                WHERE c.id_materia = ?
                  AND m.id_gestion = ?";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$idMateria, $idGestion]);

        $grades = [];
        foreach ($stmt->fetchAll() as $row) {
            $grades[(int) $row['id_matricula']][(int) $row['id_trimestre']] = $row['valor'];
        }

        return $grades;
    }

    public static function saveGrades(int $idAsignacion, int $idMateria, int $idGestion, bool $esInicial, array $gradesData): void
    {
        $conn = Database::connection();
        $conn->beginTransaction();

        try {
            $activeTrimestres = self::activeTrimestreIds($idGestion);
            if (empty($activeTrimestres)) {
                throw new \RuntimeException('No hay trimestres activos para carga de notas.');
            }

            $validMatriculas = self::assignmentMatriculaIds($idAsignacion, $idGestion);
            if (empty($validMatriculas)) {
                throw new \RuntimeException('No hay estudiantes inscritos para esta asignacion.');
            }

            $upsertNota = $conn->prepare(
                "INSERT INTO calificaciones (id_matricula, id_materia, id_trimestre, id_asignacion, nota, estado)
                 VALUES (?, ?, ?, ?, ?, 'borrador')
                 ON DUPLICATE KEY UPDATE nota = VALUES(nota), updated_at = NOW()"
            );

            $upsertNotaZero = $conn->prepare(
                "INSERT INTO calificaciones (id_matricula, id_materia, id_trimestre, id_asignacion, nota, estado)
                 VALUES (?, ?, ?, ?, 0, 'borrador')
                 ON DUPLICATE KEY UPDATE nota = VALUES(nota), updated_at = NOW()"
            );

            $upsertComentario = $conn->prepare(
                "INSERT INTO calificaciones (id_matricula, id_materia, id_trimestre, id_asignacion, comentario, estado)
                 VALUES (?, ?, ?, ?, ?, 'borrador')
                 ON DUPLICATE KEY UPDATE comentario = VALUES(comentario), updated_at = NOW()"
            );

            $deleteStmt = $conn->prepare(
                "DELETE FROM calificaciones WHERE id_matricula = ? AND id_materia = ? AND id_trimestre = ?"
            );

            foreach ($gradesData as $idMatricula => $trimestres) {
                $idMatricula = (int) $idMatricula;
                if (!in_array($idMatricula, $validMatriculas, true)) {
                    throw new \RuntimeException('La matricula enviada no pertenece a esta asignacion.');
                }

                foreach ($trimestres as $idTrimestre => $valor) {
                    $idTrimestre = (int) $idTrimestre;
                    if (!in_array($idTrimestre, $activeTrimestres, true)) {
                        throw new \RuntimeException('No se puede guardar en un trimestre inactivo.');
                    }

                    $valor = trim((string) $valor);

                    if ($esInicial) {
                        if ($valor === '') {
                            $deleteStmt->execute([$idMatricula, $idMateria, (int) $idTrimestre]);
                            continue;
                        }
                        $upsertComentario->execute([$idMatricula, $idMateria, $idTrimestre, $idAsignacion, $valor]);
                    } else {
                        if ($valor === '') {
                            $deleteStmt->execute([$idMatricula, $idMateria, $idTrimestre]);
                            continue;
                        }

                        $normalized = str_replace(',', '.', $valor);
                        if (!is_numeric($normalized)) {
                            throw new \RuntimeException('Nota invalida: ' . $valor);
                        }

                        $notaValor = (float) $normalized;
                        if ($notaValor < 0 || $notaValor > 100) {
                            throw new \RuntimeException('La nota debe estar entre 0 y 100.');
                        }

                        if ($notaValor === 0.0) {
                            $upsertNotaZero->execute([$idMatricula, $idMateria, $idTrimestre, $idAsignacion]);
                        } else {
                            $upsertNota->execute([$idMatricula, $idMateria, $idTrimestre, $idAsignacion, $notaValor]);
                        }
                    }
                }
            }

            $conn->prepare("UPDATE docente_asignaciones SET estado_carga = 'CARGADO', updated_at = NOW() WHERE id_asignacion = ?")
                 ->execute([$idAsignacion]);

            $conn->commit();
        } catch (\Throwable $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    private static function assignmentMatriculaIds(int $idAsignacion, int $idGestion): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT m.id_matricula
             FROM docente_asignaciones da
             INNER JOIN curso_materia cm ON cm.id_curso_materia = da.id_curso_materia
             INNER JOIN matriculas m ON m.id_curso = cm.id_curso AND m.id_gestion = da.id_gestion
             INNER JOIN estudiantes e ON e.id_estudiante = m.id_estudiante
             WHERE da.id_asignacion = ?
               AND da.id_gestion = ?
               AND da.estado = 'activo'
               AND m.estado = 'activo'
               AND m.deleted_at IS NULL
               AND e.deleted_at IS NULL"
        );
        $stmt->execute([$idAsignacion, $idGestion]);

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }
}
