<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class ReportBuilder
{
    private static array $columnAliases = [
        'id_estudiante' => 'ID Estudiante',
        'rude' => 'RUDE',
        'nombres' => 'Nombres',
        'apellido_paterno' => 'Apellido Paterno',
        'apellido_materno' => 'Apellido Materno',
        'nombre_completo' => 'Nombre Completo',
        'genero' => 'Género',
        'ci' => 'Carnet de Identidad',
        'fecha_nacimiento' => 'Fecha de Nacimiento',
        'edad' => 'Edad',
        'pais' => 'País',
        'provincia_departamento' => 'Provincia/Departamento',
        'nivel' => 'Nivel',
        'grado' => 'Curso',
        'paralelo' => 'Paralelo',
        'tiene_dificultad' => 'Tiene Dificultad',
        'tipo_dificultad' => 'Tipo de Dificultad',
    ];

    private static array $filterableFields = [
        'nivel' => ['type' => 'multi', 'label' => 'Nivel', 'options' => ['Inicial', 'Primaria', 'Secundaria']],
        'grado' => ['type' => 'multi', 'label' => 'Curso/Grado', 'options' => []], // dynamic
        'paralelo' => ['type' => 'multi', 'label' => 'Paralelo', 'options' => []], // dynamic
        'genero' => ['type' => 'select', 'label' => 'Género', 'options' => ['Masculino', 'Femenino']],
        'edad_min' => ['type' => 'number', 'label' => 'Edad Mínima'],
        'edad_max' => ['type' => 'number', 'label' => 'Edad Máxima'],
        'pais' => ['type' => 'text', 'label' => 'País'],
        'con_ci' => ['type' => 'select', 'label' => 'Carnet Identidad', 'options' => ['todos' => 'Todos', 'con' => 'Con carnet', 'sin' => 'Sin carnet']],
        'con_rude' => ['type' => 'select', 'label' => 'RUDE', 'options' => ['todos' => 'Todos', 'con' => 'Con RUDE', 'sin' => 'Sin RUDE']],
        'tiene_dificultad' => ['type' => 'select', 'label' => 'Dificultades', 'options' => ['todos' => 'Todos', '1' => 'Con dificultades', '0' => 'Sin dificultades']],
    ];

    public static function getColumnAliases(): array
    {
        return self::$columnAliases;
    }

    public static function getFilterableFields(): array
    {
        $fields = self::$filterableFields;

        // Fill dynamic options
        $conn = Database::connection();
        $fields['grado']['options'] = $conn->query("SELECT DISTINCT grado FROM cursos WHERE estado = 1 ORDER BY grado")->fetchAll(\PDO::FETCH_COLUMN);
        $fields['paralelo']['options'] = $conn->query("SELECT DISTINCT paralelo FROM cursos WHERE estado = 1 ORDER BY paralelo")->fetchAll(\PDO::FETCH_COLUMN);

        return $fields;
    }

    public static function all(): array
    {
        $stmt = Database::connection()->query(
            "SELECT rg.id_reporte, rg.nombre, rg.tipo_base, rg.created_at,
                    u.username AS creador
             FROM reportes_guardados rg
             LEFT JOIN usuarios u ON u.id_usuario = rg.id_usuario
             WHERE rg.deleted_at IS NULL
             ORDER BY rg.created_at DESC"
        );
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $conn = Database::connection();
        $stmt = $conn->prepare(
            "SELECT rg.*, u.username AS creador
             FROM reportes_guardados rg
             LEFT JOIN usuarios u ON u.id_usuario = rg.id_usuario
             WHERE rg.id_reporte = ? AND rg.deleted_at IS NULL"
        );
        $stmt->execute([$id]);
        $reporte = $stmt->fetch();

        if (!$reporte) return null;

        // Get columns
        $stmt = $conn->prepare(
            "SELECT campo, alias_mostrar, orden
             FROM reportes_columnas
             WHERE id_reporte = ?
             ORDER BY orden"
        );
        $stmt->execute([$id]);
        $reporte['columnas'] = $stmt->fetchAll();

        // Get filters
        $stmt = $conn->prepare(
            "SELECT campo, operador, valor1, valor2
             FROM reportes_filtros
             WHERE id_reporte = ?"
        );
        $stmt->execute([$id]);
        $reporte['filtros_db'] = $stmt->fetchAll();

        // Convert filters to associative
        $filtros = [];
        foreach ($reporte['filtros_db'] as $f) {
            $filtros[$f['campo']] = $f['operador'] === 'in' ? explode(',', $f['valor1']) : $f['valor1'];
        }
        $reporte['filtros'] = $filtros;

        return $reporte;
    }

    public static function buildSql(array $filters, array $columns, string $tipoBase): array
    {
        $conn = Database::connection();
        $selects = [];
        $joins = [];
        $where = [];
        $params = [];

        // Always need gestion filter - get active gestion
        $gestionId = (int) $conn->query("SELECT id_gestion FROM gestiones WHERE estado = 'activa' LIMIT 1")->fetchColumn();

        // Base FROM
        $sql = "SELECT ";

        if (empty($columns)) {
            $selects[] = "e.nombres, e.apellido_paterno, e.apellido_materno";
        } else {
            foreach ($columns as $col) {
                switch ($col) {
                    case 'id_estudiante':
                        $selects[] = "e.id_estudiante"; break;
                    case 'rude':
                        $selects[] = "e.rude"; break;
                    case 'nombres':
                        $selects[] = "e.nombres"; break;
                    case 'apellido_paterno':
                        $selects[] = "e.apellido_paterno"; break;
                    case 'apellido_materno':
                        $selects[] = "e.apellido_materno"; break;
                    case 'nombre_completo':
                        $selects[] = "CONCAT(COALESCE(e.apellido_paterno,''), ' ', COALESCE(e.apellido_materno,''), ' ', e.nombres) AS nombre_completo"; break;
                    case 'genero':
                        $selects[] = "e.genero"; break;
                    case 'ci':
                        $selects[] = "e.ci"; break;
                    case 'fecha_nacimiento':
                        $selects[] = "e.fecha_nacimiento"; break;
                    case 'edad':
                        $selects[] = "TIMESTAMPDIFF(YEAR, e.fecha_nacimiento, CURDATE()) AS edad"; break;
                    case 'pais':
                        $selects[] = "e.pais"; break;
                    case 'provincia_departamento':
                        $selects[] = "e.provincia_departamento"; break;
                    case 'nivel':
                        $selects[] = "c.nivel"; $joins['cursos'] = true; break;
                    case 'grado':
                        $selects[] = "c.grado"; $joins['cursos'] = true; break;
                    case 'paralelo':
                        $selects[] = "c.paralelo"; $joins['cursos'] = true; break;
                    case 'tiene_dificultad':
                        $selects[] = "COALESCE(ed.tiene_dificultad, 0) AS tiene_dificultad";
                        $joins['dificultades'] = true; break;
                    case 'tipo_dificultad':
                        $selects[] = "CONCAT_WS(', ',
                            CASE WHEN ed.auditiva != 'ninguna' THEN CONCAT('auditiva:', ed.auditiva) END,
                            CASE WHEN ed.visual != 'ninguna' THEN CONCAT('visual:', ed.visual) END,
                            CASE WHEN ed.intelectual != 'ninguna' THEN CONCAT('intelectual:', ed.intelectual) END,
                            CASE WHEN ed.fisico_motora != 'ninguna' THEN CONCAT('fisico_motora:', ed.fisico_motora) END,
                            CASE WHEN ed.psiquica_mental != 'ninguna' THEN CONCAT('psiquica_mental:', ed.psiquica_mental) END,
                            CASE WHEN ed.autista != 'ninguna' THEN CONCAT('autista:', ed.autista) END
                        ) AS tipo_dificultad";
                        $joins['dificultades'] = true; break;
                }
            }
        }

        $sql .= implode(", ", $selects);
        $sql .= " FROM estudiantes e";
        $sql .= " INNER JOIN matriculas m ON m.id_estudiante = e.id_estudiante AND m.estado = 'activo' AND m.deleted_at IS NULL";
        $where[] = "m.id_gestion = ?";
        $params[] = $gestionId;

        $hasCursoFilter = false;
        if (!empty($filters)) {
            foreach ($filters as $campo => $valor) {
                if ($valor === '' || $valor === 'todos') continue;
                if (is_array($valor) && empty($valor)) continue;

                switch ($campo) {
                    case 'nivel':
                        $joins['cursos'] = true;
                        if (is_array($valor)) {
                            $ph = implode(',', array_fill(0, count($valor), '?'));
                            $where[] = "c.nivel IN ($ph)";
                            $params = array_merge($params, $valor);
                        } else {
                            $where[] = "c.nivel = ?";
                            $params[] = $valor;
                        }
                        break;
                    case 'grado':
                        $joins['cursos'] = true;
                        if (is_array($valor)) {
                            $ph = implode(',', array_fill(0, count($valor), '?'));
                            $where[] = "c.grado IN ($ph)";
                            $params = array_merge($params, $valor);
                        } else {
                            $where[] = "c.grado = ?";
                            $params[] = $valor;
                        }
                        break;
                    case 'paralelo':
                        $joins['cursos'] = true;
                        if (is_array($valor)) {
                            $ph = implode(',', array_fill(0, count($valor), '?'));
                            $where[] = "c.paralelo IN ($ph)";
                            $params = array_merge($params, $valor);
                        } else {
                            $where[] = "c.paralelo = ?";
                            $params[] = $valor;
                        }
                        break;
                    case 'genero':
                        $where[] = "e.genero = ?";
                        $params[] = $valor;
                        break;
                    case 'edad_min':
                        if (is_numeric($valor)) {
                            $where[] = "TIMESTAMPDIFF(YEAR, e.fecha_nacimiento, CURDATE()) >= ?";
                            $params[] = (int) $valor;
                        }
                        break;
                    case 'edad_max':
                        if (is_numeric($valor)) {
                            $where[] = "TIMESTAMPDIFF(YEAR, e.fecha_nacimiento, CURDATE()) <= ?";
                            $params[] = (int) $valor;
                        }
                        break;
                    case 'pais':
                        $where[] = "e.pais = ?";
                        $params[] = $valor;
                        break;
                    case 'con_ci':
                        if ($valor === 'con') $where[] = "e.ci IS NOT NULL AND e.ci != ''";
                        elseif ($valor === 'sin') $where[] = "(e.ci IS NULL OR e.ci = '')";
                        break;
                    case 'con_rude':
                        if ($valor === 'con') $where[] = "e.rude IS NOT NULL AND e.rude != ''";
                        elseif ($valor === 'sin') $where[] = "(e.rude IS NULL OR e.rude = '')";
                        break;
                    case 'tiene_dificultad':
                        $joins['dificultades'] = true;
                        if ($valor === '1') $where[] = "ed.tiene_dificultad = 1";
                        elseif ($valor === '0') $where[] = "(ed.tiene_dificultad = 0 OR ed.tiene_dificultad IS NULL)";
                        break;
                }
            }
        }

        // Add JOINs
        if ($joins['cursos'] ?? false) {
            $sql .= " INNER JOIN cursos c ON c.id_curso = m.id_curso AND c.estado = 1";
        }
        if ($joins['dificultades'] ?? false) {
            $sql .= " LEFT JOIN estudiante_dificultades ed ON ed.id_estudiante = e.id_estudiante";
        }

        $sql .= " WHERE e.deleted_at IS NULL AND " . implode(" AND ", $where);
        $sql .= " ORDER BY e.apellido_paterno, e.apellido_materno, e.nombres";

        return ['sql' => $sql, 'params' => $params];
    }

    public static function executeQuery(string $sql, array $params): array
    {
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function save(string $nombre, string $tipoBase, ?string $descripcion, array $filters, array $columns, array $columnOrder, int $editId = 0): array
    {
        $conn = Database::connection();
        $userId = (int) ($_SESSION['user_id'] ?? 0);

        try {
            $conn->beginTransaction();

            if ($editId > 0) {
                $stmt = $conn->prepare("UPDATE reportes_guardados SET nombre = ?, updated_at = NOW() WHERE id_reporte = ? AND id_usuario = ?");
                $stmt->execute([$nombre, $editId, $userId]);
                $idReporte = $editId;

                $conn->prepare("DELETE FROM reportes_filtros WHERE id_reporte = ?")->execute([$editId]);
                $conn->prepare("DELETE FROM reportes_columnas WHERE id_reporte = ?")->execute([$editId]);
            } else {
                $stmt = $conn->prepare("INSERT INTO reportes_guardados (nombre, descripcion, tipo_base, id_usuario) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nombre, $descripcion, $tipoBase, $userId]);
                $idReporte = (int) $conn->lastInsertId();
            }

            // Save filters
            if (!empty($filters)) {
                $stmtF = $conn->prepare("INSERT INTO reportes_filtros (id_reporte, campo, operador, valor1) VALUES (?, ?, ?, ?)");
                foreach ($filters as $campo => $valor) {
                    if ($valor === '' || $valor === 'todos') continue;
                    if (is_array($valor) && empty($valor)) continue;
                    if (is_array($valor)) {
                        $stmtF->execute([$idReporte, $campo, 'in', implode(',', $valor)]);
                    } else {
                        $stmtF->execute([$idReporte, $campo, '=', $valor]);
                    }
                }
            }

            // Save columns
            if (!empty($columns)) {
                $stmtC = $conn->prepare("INSERT INTO reportes_columnas (id_reporte, campo, alias_mostrar, orden) VALUES (?, ?, ?, ?)");
                foreach ($columns as $col) {
                    $orden = $columnOrder[$col] ?? 1;
                    $alias = self::$columnAliases[$col] ?? $col;
                    $stmtC->execute([$idReporte, $col, $alias, (int) $orden]);
                }
            }

            $conn->commit();
            return ['success' => true, 'id_reporte' => $idReporte];
        } catch (\Throwable $e) {
            $conn->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public static function delete(int $id): void
    {
        $conn = Database::connection();
        $conn->beginTransaction();
        try {
            $conn->prepare("DELETE FROM reportes_filtros WHERE id_reporte = ?")->execute([$id]);
            $conn->prepare("DELETE FROM reportes_columnas WHERE id_reporte = ?")->execute([$id]);
            $conn->prepare("UPDATE reportes_guardados SET deleted_at = NOW() WHERE id_reporte = ?")->execute([$id]);
            $conn->commit();
        } catch (\Throwable $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public static function getNiveles(): array
    {
        $stmt = Database::connection()->query("SELECT DISTINCT nivel FROM cursos WHERE estado = 1 ORDER BY FIELD(nivel, 'Inicial', 'Primaria', 'Secundaria')");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public static function getCursos(): array
    {
        $stmt = Database::connection()->query("SELECT DISTINCT grado FROM cursos WHERE estado = 1 ORDER BY grado");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public static function getParalelos(): array
    {
        $stmt = Database::connection()->query("SELECT DISTINCT paralelo FROM cursos WHERE estado = 1 ORDER BY paralelo");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
}
