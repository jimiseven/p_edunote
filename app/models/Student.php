<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Student
{
    public static function all(string $search = ''): array
    {
        $sql = "SELECT e.id_estudiante, e.rude, e.nombres, e.apellido_paterno, e.apellido_materno,
                       e.genero, e.ci, e.fecha_nacimiento, e.estado,
                       c.nivel, c.grado, c.paralelo,
                       r.nombres AS resp_nombres, r.apellido_paterno AS resp_apellido_paterno,
                       r.apellido_materno AS resp_apellido_materno, er.parentesco, r.celular AS resp_celular
                FROM estudiantes e
                LEFT JOIN matriculas m ON m.id_estudiante = e.id_estudiante AND m.estado = 'activo' AND m.deleted_at IS NULL
                LEFT JOIN cursos c ON c.id_curso = m.id_curso
                LEFT JOIN estudiante_responsable er ON er.id_estudiante = e.id_estudiante AND er.es_principal = 1
                LEFT JOIN responsables r ON r.id_responsable = er.id_responsable
                WHERE e.deleted_at IS NULL";

        $params = [];
        if ($search !== '') {
            $sql .= " AND (e.rude LIKE ? OR e.ci LIKE ? OR e.nombres LIKE ? OR e.apellido_paterno LIKE ? OR e.apellido_materno LIKE ? OR r.nombres LIKE ? OR r.ci LIKE ?)";
            $searchVal = '%' . $search . '%';
            $params = [$searchVal, $searchVal, $searchVal, $searchVal, $searchVal, $searchVal, $searchVal];
        }

        $sql .= ' ORDER BY e.apellido_paterno ASC, e.apellido_materno ASC, e.nombres ASC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function create(array $data): void
    {
        $conn = Database::connection();
        $conn->beginTransaction();

        try {
            $stmt = $conn->prepare("INSERT INTO responsables
                (nombres, apellido_paterno, apellido_materno, ci, fecha_nacimiento, parentesco_general, grado_instruccion, idioma_frecuente, ocupacion, celular, direccion)
                VALUES (:nombres, :apellido_paterno, :apellido_materno, :ci, :fecha_nacimiento, :parentesco_general, :grado_instruccion, :idioma_frecuente, :ocupacion, :celular, :direccion)");
            $stmt->execute([
                'nombres' => $data['resp_nombres'],
                'apellido_paterno' => $data['resp_apellido_paterno'],
                'apellido_materno' => $data['resp_apellido_materno'] ?: null,
                'ci' => $data['resp_ci'] ?: null,
                'fecha_nacimiento' => $data['resp_fecha_nacimiento'] ?: null,
                'parentesco_general' => $data['resp_parentesco'],
                'grado_instruccion' => $data['resp_grado_instruccion'] ?: null,
                'idioma_frecuente' => $data['resp_idioma_frecuente'] ?: null,
                'ocupacion' => $data['resp_ocupacion'] ?: null,
                'celular' => $data['resp_celular'] ?: null,
                'direccion' => $data['resp_direccion'] ?: null,
            ]);
            $responsableId = (int) $conn->lastInsertId();

            $stmt = $conn->prepare("INSERT INTO estudiantes
                (rude, nombres, apellido_paterno, apellido_materno, genero, ci, fecha_nacimiento, pais, provincia_departamento, estado)
                VALUES (:rude, :nombres, :apellido_paterno, :apellido_materno, :genero, :ci, :fecha_nacimiento, :pais, :provincia_departamento, 'activo')");
            $stmt->execute([
                'rude' => $data['rude'],
                'nombres' => $data['nombres'],
                'apellido_paterno' => $data['apellido_paterno'],
                'apellido_materno' => $data['apellido_materno'] ?: null,
                'genero' => $data['genero'] ?: null,
                'ci' => $data['ci'] ?: null,
                'fecha_nacimiento' => $data['fecha_nacimiento'] ?: null,
                'pais' => $data['pais'] ?: 'Bolivia',
                'provincia_departamento' => $data['provincia_departamento'] ?: null,
            ]);
            $estudianteId = (int) $conn->lastInsertId();

            $stmt = $conn->prepare("INSERT INTO estudiante_responsable
                (id_estudiante, id_responsable, parentesco, es_principal, vive_con_estudiante, autorizado_recoger)
                VALUES (?, ?, ?, 1, ?, ?)");
            $stmt->execute([
                $estudianteId,
                $responsableId,
                $data['resp_parentesco'],
                $data['resp_vive_con_estudiante'],
                $data['resp_autorizado_recoger'],
            ]);

            self::createMatricula($conn, $estudianteId, (int) $data['id_curso']);
            self::createSecondaryInfo($conn, $estudianteId, $data);

            $conn->commit();
        } catch (\Throwable $exception) {
            $conn->rollBack();
            throw $exception;
        }
    }

    public static function find(int $id): ?array
    {
        $sql = "SELECT e.*, m.id_curso, m.id_matricula,
                       c.nivel, c.grado, c.paralelo,
                       r.id_responsable, r.nombres AS resp_nombres, r.apellido_paterno AS resp_apellido_paterno,
                       r.apellido_materno AS resp_apellido_materno, r.ci AS resp_ci,
                       r.fecha_nacimiento AS resp_fecha_nacimiento, r.grado_instruccion AS resp_grado_instruccion,
                       r.idioma_frecuente AS resp_idioma_frecuente, r.ocupacion AS resp_ocupacion,
                       r.celular AS resp_celular, r.direccion AS resp_direccion,
                       er.parentesco AS resp_parentesco, er.vive_con_estudiante AS resp_vive_con_estudiante,
                       er.autorizado_recoger AS resp_autorizado_recoger
                FROM estudiantes e
                LEFT JOIN matriculas m ON m.id_estudiante = e.id_estudiante AND m.estado = 'activo' AND m.deleted_at IS NULL
                LEFT JOIN cursos c ON c.id_curso = m.id_curso
                LEFT JOIN estudiante_responsable er ON er.id_estudiante = e.id_estudiante AND er.es_principal = 1
                LEFT JOIN responsables r ON r.id_responsable = er.id_responsable
                WHERE e.id_estudiante = ? AND e.deleted_at IS NULL
                LIMIT 1";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$id]);
        $student = $stmt->fetch();

        if (!$student) {
            return null;
        }

        // Fetch secondary info
        $stmt = Database::connection()->prepare("SELECT * FROM estudiante_direccion WHERE id_estudiante = ?");
        $stmt->execute([$id]);
        $student['direccion'] = $stmt->fetch() ?: null;

        $stmt = Database::connection()->prepare("SELECT * FROM estudiante_salud WHERE id_estudiante = ?");
        $stmt->execute([$id]);
        $student['salud'] = $stmt->fetch() ?: null;

        $stmt = Database::connection()->prepare("SELECT * FROM estudiante_idioma_cultura WHERE id_estudiante = ?");
        $stmt->execute([$id]);
        $student['idioma'] = $stmt->fetch() ?: null;

        $stmt = Database::connection()->prepare("SELECT * FROM estudiante_transporte WHERE id_estudiante = ?");
        $stmt->execute([$id]);
        $student['transporte'] = $stmt->fetch() ?: null;

        $stmt = Database::connection()->prepare("SELECT * FROM estudiante_servicios WHERE id_estudiante = ?");
        $stmt->execute([$id]);
        $student['servicios'] = $stmt->fetch() ?: null;

        $stmt = Database::connection()->prepare("SELECT * FROM estudiante_actividad_laboral WHERE id_estudiante = ?");
        $stmt->execute([$id]);
        $student['actividad_laboral'] = $stmt->fetch() ?: null;

        $stmt = Database::connection()->prepare("SELECT * FROM estudiante_dificultades WHERE id_estudiante = ?");
        $stmt->execute([$id]);
        $student['dificultades'] = $stmt->fetch() ?: null;

        $stmt = Database::connection()->prepare("SELECT * FROM estudiante_abandono WHERE id_estudiante = ?");
        $stmt->execute([$id]);
        $student['abandono'] = $stmt->fetch() ?: null;

        return $student;
    }

    public static function update(int $id, array $data): void
    {
        $conn = Database::connection();
        $conn->beginTransaction();

        try {
            $student = self::find($id);
            if (!$student) {
                throw new \RuntimeException('Estudiante no encontrado.');
            }

            $stmt = $conn->prepare("UPDATE estudiantes
                SET rude = :rude, nombres = :nombres, apellido_paterno = :apellido_paterno,
                    apellido_materno = :apellido_materno, genero = :genero, ci = :ci,
                    fecha_nacimiento = :fecha_nacimiento, pais = :pais,
                    provincia_departamento = :provincia_departamento
                WHERE id_estudiante = :id_estudiante");
            $stmt->execute([
                'rude' => $data['rude'],
                'nombres' => $data['nombres'],
                'apellido_paterno' => $data['apellido_paterno'],
                'apellido_materno' => $data['apellido_materno'] ?: null,
                'genero' => $data['genero'] ?: null,
                'ci' => $data['ci'] ?: null,
                'fecha_nacimiento' => $data['fecha_nacimiento'] ?: null,
                'pais' => $data['pais'] ?: 'Bolivia',
                'provincia_departamento' => $data['provincia_departamento'] ?: null,
                'id_estudiante' => $id,
            ]);

            if (!empty($student['id_responsable'])) {
                $stmt = $conn->prepare("UPDATE responsables
                    SET nombres = :nombres, apellido_paterno = :apellido_paterno, apellido_materno = :apellido_materno,
                        ci = :ci, fecha_nacimiento = :fecha_nacimiento, parentesco_general = :parentesco_general,
                        grado_instruccion = :grado_instruccion, idioma_frecuente = :idioma_frecuente,
                        ocupacion = :ocupacion, celular = :celular, direccion = :direccion
                    WHERE id_responsable = :id_responsable");
                $stmt->execute([
                    'nombres' => $data['resp_nombres'],
                    'apellido_paterno' => $data['resp_apellido_paterno'],
                    'apellido_materno' => $data['resp_apellido_materno'] ?: null,
                    'ci' => $data['resp_ci'] ?: null,
                    'fecha_nacimiento' => $data['resp_fecha_nacimiento'] ?: null,
                    'parentesco_general' => $data['resp_parentesco'],
                    'grado_instruccion' => $data['resp_grado_instruccion'] ?: null,
                    'idioma_frecuente' => $data['resp_idioma_frecuente'] ?: null,
                    'ocupacion' => $data['resp_ocupacion'] ?: null,
                    'celular' => $data['resp_celular'] ?: null,
                    'direccion' => $data['resp_direccion'] ?: null,
                    'id_responsable' => $student['id_responsable'],
                ]);

                $stmt = $conn->prepare("UPDATE estudiante_responsable
                    SET parentesco = ?, vive_con_estudiante = ?, autorizado_recoger = ?
                    WHERE id_estudiante = ? AND id_responsable = ?");
                $stmt->execute([$data['resp_parentesco'], $data['resp_vive_con_estudiante'], $data['resp_autorizado_recoger'], $id, $student['id_responsable']]);
            }

            self::updateMatricula($conn, $id, (int) $data['id_curso']);
            $conn->commit();
        } catch (\Throwable $exception) {
            $conn->rollBack();
            throw $exception;
        }
    }

    public static function softDelete(int $id): void
    {
        $stmt = Database::connection()->prepare("UPDATE estudiantes SET deleted_at = NOW(), estado = 'retirado' WHERE id_estudiante = ?");
        $stmt->execute([$id]);
    }

    private static function createMatricula(\PDO $conn, int $estudianteId, int $cursoId): void
    {
        $gestionId = (int) $conn->query("SELECT id_gestion FROM gestiones WHERE estado = 'activa' ORDER BY anio DESC LIMIT 1")->fetchColumn();
        if ($gestionId <= 0 || $cursoId <= 0) {
            return;
        }

        $stmt = $conn->prepare("INSERT INTO matriculas (id_estudiante, id_curso, id_gestion, fecha_matricula, estado)
                                VALUES (?, ?, ?, CURDATE(), 'activo')");
        $stmt->execute([$estudianteId, $cursoId, $gestionId]);
    }

    private static function updateMatricula(\PDO $conn, int $estudianteId, int $cursoId): void
    {
        $gestionId = (int) $conn->query("SELECT id_gestion FROM gestiones WHERE estado = 'activa' ORDER BY anio DESC LIMIT 1")->fetchColumn();
        if ($gestionId <= 0 || $cursoId <= 0) {
            return;
        }

        $stmt = $conn->prepare("SELECT id_matricula FROM matriculas WHERE id_estudiante = ? AND id_gestion = ? AND deleted_at IS NULL LIMIT 1");
        $stmt->execute([$estudianteId, $gestionId]);
        $matriculaId = (int) $stmt->fetchColumn();

        if ($matriculaId > 0) {
            $stmt = $conn->prepare("UPDATE matriculas SET id_curso = ?, estado = 'activo' WHERE id_matricula = ?");
            $stmt->execute([$cursoId, $matriculaId]);
            return;
        }

        $stmt = $conn->prepare("INSERT INTO matriculas (id_estudiante, id_curso, id_gestion, fecha_matricula, estado)
                                VALUES (?, ?, ?, CURDATE(), 'activo')");
        $stmt->execute([$estudianteId, $cursoId, $gestionId]);
    }

    private static function createSecondaryInfo(\PDO $conn, int $estudianteId, array $data): void
    {
        if ($data['dir_departamento'] !== '' || $data['dir_zona'] !== '' || $data['dir_celular'] !== '') {
            $stmt = $conn->prepare("INSERT INTO estudiante_direccion
                (id_estudiante, departamento, provincia, municipio, localidad, comunidad, zona, numero_vivienda, telefono, celular)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$estudianteId, $data['dir_departamento'] ?: null, $data['dir_provincia'] ?: null, $data['dir_municipio'] ?: null, $data['dir_localidad'] ?: null, $data['dir_comunidad'] ?: null, $data['dir_zona'] ?: null, $data['dir_numero_vivienda'] ?: null, $data['dir_telefono'] ?: null, $data['dir_celular'] ?: null]);
        }

        $stmt = $conn->prepare("INSERT INTO estudiante_salud (id_estudiante, tiene_seguro, acceso_posta, acceso_centro_salud, acceso_hospital, observacion)
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$estudianteId, $data['sal_tiene_seguro'], $data['sal_acceso_posta'], $data['sal_acceso_centro_salud'], $data['sal_acceso_hospital'], $data['sal_observacion'] ?: null]);

        if ($data['idi_idioma'] !== '' || $data['idi_cultura'] !== '') {
            $stmt = $conn->prepare("INSERT INTO estudiante_idioma_cultura (id_estudiante, idioma, cultura) VALUES (?, ?, ?)");
            $stmt->execute([$estudianteId, $data['idi_idioma'] ?: null, $data['idi_cultura'] ?: null]);
        }

        if ($data['trans_medio'] !== '' || $data['trans_tiempo_llegada'] !== '') {
            $stmt = $conn->prepare("INSERT INTO estudiante_transporte (id_estudiante, medio, tiempo_llegada) VALUES (?, ?, ?)");
            $stmt->execute([$estudianteId, $data['trans_medio'] ?: null, $data['trans_tiempo_llegada'] ?: null]);
        }

        $stmt = $conn->prepare("INSERT INTO estudiante_servicios
            (id_estudiante, agua_caneria, bano, alcantarillado, internet, energia, recojo_basura, tipo_vivienda)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$estudianteId, $data['serv_agua_caneria'], $data['serv_bano'], $data['serv_alcantarillado'], $data['serv_internet'], $data['serv_energia'], $data['serv_recojo_basura'], $data['serv_tipo_vivienda'] ?: null]);

        if ($data['lab_trabajo'] === 1) {
            $stmt = $conn->prepare("INSERT INTO estudiante_actividad_laboral
                (id_estudiante, trabajo, meses_trabajo, actividad, turno_manana, turno_tarde, turno_noche, frecuencia)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$estudianteId, 1, $data['lab_meses_trabajo'] ?: null, $data['lab_actividad'] ?: null, $data['lab_turno_manana'], $data['lab_turno_tarde'], $data['lab_turno_noche'], $data['lab_frecuencia'] ?: null]);
        }

        if ($data['dif_tiene_dificultad'] === 1) {
            $stmt = $conn->prepare("INSERT INTO estudiante_dificultades
                (id_estudiante, tiene_dificultad, auditiva, visual, intelectual, fisico_motora, psiquica_mental, autista)
                VALUES (?, 1, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$estudianteId, $data['dif_auditiva'], $data['dif_visual'], $data['dif_intelectual'], $data['dif_fisico_motora'], $data['dif_psiquica_mental'], $data['dif_autista']]);
        }

        if ($data['aba_abandono'] === 1) {
            $stmt = $conn->prepare("INSERT INTO estudiante_abandono (id_estudiante, abandono, motivo, observacion) VALUES (?, 1, ?, ?)");
            $stmt->execute([$estudianteId, $data['aba_motivo'] ?: null, $data['aba_observacion'] ?: null]);
        }
    }
}
