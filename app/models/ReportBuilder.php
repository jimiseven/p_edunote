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
        // Academic columns
        'promedio_t1' => 'Promedio Trimestre 1',
        'promedio_t2' => 'Promedio Trimestre 2',
        'promedio_t3' => 'Promedio Trimestre 3',
        'promedio_general' => 'Promedio General',
        'materias_aprobadas' => 'Materias Aprobadas',
        'materias_reprobadas' => 'Materias Reprobadas',
        'total_materias' => 'Total Materias',
        'estado_final' => 'Estado Final',
        'detalle_materias' => 'Detalle por Materia',
    ];

    private static array $filterableFields = [
        'nivel' => ['type' => 'multi', 'label' => 'Nivel', 'options' => ['Inicial', 'Primaria', 'Secundaria']],
        'grado' => ['type' => 'multi', 'label' => 'Curso/Grado', 'options' => []],
        'paralelo' => ['type' => 'multi', 'label' => 'Paralelo', 'options' => []],
        'genero' => ['type' => 'select', 'label' => 'Género', 'options' => ['Masculino', 'Femenino']],
        'edad_min' => ['type' => 'number', 'label' => 'Edad Mínima'],
        'edad_max' => ['type' => 'number', 'label' => 'Edad Máxima'],
        'pais' => ['type' => 'text', 'label' => 'País'],
        'con_ci' => ['type' => 'select', 'label' => 'Carnet Identidad', 'options' => ['todos' => 'Todos', 'con' => 'Con carnet', 'sin' => 'Sin carnet']],
        'con_rude' => ['type' => 'select', 'label' => 'RUDE', 'options' => ['todos' => 'Todos', 'con' => 'Con RUDE', 'sin' => 'Sin RUDE']],
        'tiene_dificultad' => ['type' => 'select', 'label' => 'Dificultades', 'options' => ['todos' => 'Todos', '1' => 'Con dificultades', '0' => 'Sin dificultades']],
        // Academic filters
        'trimestre' => ['type' => 'select', 'label' => 'Trimestre', 'options' => [
            'todos' => 'Todos',
            '1' => 'Primer Trimestre',
            '2' => 'Segundo Trimestre',
            '3' => 'Tercer Trimestre',
        ]],
        'nota_minima' => ['type' => 'number', 'label' => 'Nota Mínima Aprobación', 'default' => 51],
        'estado_academico' => ['type' => 'select', 'label' => 'Estado Académico', 'options' => [
            'todos' => 'Todos',
            'aprobado' => 'Aprobados',
            'reprobado' => 'Reprobados',
            'critico' => 'Más críticos (nota < 36)',
            'destacado' => 'Destacados (nota >= 80)',
        ]],
        'materia_especifica' => ['type' => 'select', 'label' => 'Materia Específica', 'options' => []], // dynamic
        'filtro_materias' => ['type' => 'select', 'label' => 'Detalle de Materias', 'options' => [
            'todas' => 'Todas',
            'aprobadas' => 'Solo aprobadas',
            'reprobadas' => 'Solo reprobadas',
        ]],
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
        $fields['materia_especifica']['options'] = $conn->query("SELECT id_materia, nombre FROM materias WHERE estado = 1 ORDER BY nombre")->fetchAll(\PDO::FETCH_KEY_PAIR);
        // Convert to string keys for form
        $strOptions = [];
        foreach ($fields['materia_especifica']['options'] as $k => $v) {
            $strOptions[(string)$k] = $v;
        }
        $fields['materia_especifica']['options'] = $strOptions;

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
        $groupBy = [];
        $having = [];

        $gestionId = (int) $conn->query("SELECT id_gestion FROM gestiones WHERE estado = 'activa' LIMIT 1")->fetchColumn();

        // Detect if we need academic data
        $needsAcademics = false;
        $academicCols = ['promedio_t1', 'promedio_t2', 'promedio_t3', 'promedio_general', 'materias_aprobadas', 'materias_reprobadas', 'total_materias', 'estado_final'];
        foreach ($columns as $col) {
            if (in_array($col, $academicCols)) { $needsAcademics = true; break; }
        }
        $academicFilters = ['trimestre', 'nota_minima', 'estado_academico', 'materia_especifica', 'filtro_materias'];
        foreach ($academicFilters as $af) {
            if (isset($filters[$af]) && $filters[$af] !== '' && $filters[$af] !== 'todos') { $needsAcademics = true; break; }
            if (isset($filters[$af]) && is_array($filters[$af]) && !empty($filters[$af])) { $needsAcademics = true; break; }
        }

        $notaMinima = (int) ($filters['nota_minima'] ?? 51);
        $trimestre = $filters['trimestre'] ?? 'todos';

        if (empty($columns)) {
            $selects[] = "e.nombres, e.apellido_paterno, e.apellido_materno";
        } else {
            foreach ($columns as $col) {
                switch ($col) {
                    case 'id_estudiante': $selects[] = "e.id_estudiante"; break;
                    case 'rude': $selects[] = "e.rude"; break;
                    case 'nombres': $selects[] = "e.nombres"; break;
                    case 'apellido_paterno': $selects[] = "e.apellido_paterno"; break;
                    case 'apellido_materno': $selects[] = "e.apellido_materno"; break;
                    case 'nombre_completo':
                        $selects[] = "CONCAT(COALESCE(e.apellido_paterno,''), ' ', COALESCE(e.apellido_materno,''), ' ', e.nombres) AS nombre_completo"; break;
                    case 'genero': $selects[] = "e.genero"; break;
                    case 'ci': $selects[] = "e.ci"; break;
                    case 'fecha_nacimiento': $selects[] = "e.fecha_nacimiento"; break;
                    case 'edad': $selects[] = "TIMESTAMPDIFF(YEAR, e.fecha_nacimiento, CURDATE()) AS edad"; break;
                    case 'pais': $selects[] = "e.pais"; break;
                    case 'provincia_departamento': $selects[] = "e.provincia_departamento"; break;
                    case 'nivel': $selects[] = "c.nivel"; $joins['cursos'] = true; $groupBy['c'] = true; break;
                    case 'grado': $selects[] = "c.grado"; $joins['cursos'] = true; $groupBy['c'] = true; break;
                    case 'paralelo': $selects[] = "c.paralelo"; $joins['cursos'] = true; $groupBy['c'] = true; break;
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
                    // Academic columns
                    case 'promedio_t1':
                        $selects[] = "ROUND(AVG(CASE WHEN c_trim.id_trimestre = 1 THEN c_trim.nota END), 2) AS promedio_t1";
                        $joins['calificaciones'] = true; $groupBy['e'] = true; break;
                    case 'promedio_t2':
                        $selects[] = "ROUND(AVG(CASE WHEN c_trim.id_trimestre = 2 THEN c_trim.nota END), 2) AS promedio_t2";
                        $joins['calificaciones'] = true; $groupBy['e'] = true; break;
                    case 'promedio_t3':
                        $selects[] = "ROUND(AVG(CASE WHEN c_trim.id_trimestre = 3 THEN c_trim.nota END), 2) AS promedio_t3";
                        $joins['calificaciones'] = true; $groupBy['e'] = true; break;
                    case 'promedio_general':
                        $selects[] = "ROUND(AVG(c_trim.nota), 2) AS promedio_general";
                        $joins['calificaciones'] = true; $groupBy['e'] = true; break;
                    case 'materias_aprobadas':
                        $selects[] = "COUNT(DISTINCT CASE WHEN AVG_calc.prom_materia >= $notaMinima THEN AVG_calc.id_materia END) AS materias_aprobadas";
                        $selects[] = "COUNT(DISTINCT CASE WHEN AVG_calc.prom_materia < $notaMinima THEN AVG_calc.id_materia END) AS materias_reprobadas";
                        $selects[] = "COUNT(DISTINCT AVG_calc.id_materia) AS total_materias";
                        $joins['calificaciones_prom'] = true; $groupBy['e'] = true; break;
                    case 'materias_reprobadas':
                        if (!in_array('materias_aprobadas', $columns)) {
                            $selects[] = "COUNT(DISTINCT CASE WHEN AVG_calc.prom_materia < $notaMinima THEN AVG_calc.id_materia END) AS materias_reprobadas";
                            $joins['calificaciones_prom'] = true; $groupBy['e'] = true;
                        }
                        break;
                    case 'total_materias':
                        if (!in_array('materias_aprobadas', $columns)) {
                            $selects[] = "COUNT(DISTINCT AVG_calc.id_materia) AS total_materias";
                            $joins['calificaciones_prom'] = true; $groupBy['e'] = true;
                        }
                        break;
                    case 'estado_final':
                        $selects[] = "CASE WHEN MIN(prom_materia) >= $notaMinima THEN 'APROVADO' ELSE 'REPROBADO' END AS estado_final";
                        $joins['calificaciones_prom'] = true; $groupBy['e'] = true; break;
                    case 'detalle_materias':
                        $filtroMat = $filters['filtro_materias'] ?? 'todas';
                        if ($filtroMat === 'aprobadas') {
                            $selects[] = "GROUP_CONCAT(CONCAT(m_det.nombre, ': ', ROUND(AVG_calc.prom_materia, 1)) ORDER BY m_det.nombre SEPARATOR ' | ') AS detalle_materias";
                        } elseif ($filtroMat === 'reprobadas') {
                            $selects[] = "GROUP_CONCAT(CONCAT(m_det.nombre, ': ', ROUND(AVG_calc.prom_materia, 1)) ORDER BY m_det.nombre SEPARATOR ' | ') AS detalle_materias";
                        } else {
                            $selects[] = "GROUP_CONCAT(CONCAT(m_det.nombre, ': ', ROUND(AVG_calc.prom_materia, 1),
                                CASE WHEN AVG_calc.prom_materia >= $notaMinima THEN ' (A)' ELSE ' (R)' END
                            ) ORDER BY m_det.nombre SEPARATOR ' | ') AS detalle_materias";
                        }
                        $joins['calificaciones_prom'] = true;
                        $joins['materias_det'] = true;
                        $groupBy['e'] = true; break;
                }
            }
        }

        // Base query
        $sql = "SELECT " . implode(", ", $selects) . " FROM estudiantes e";
        $sql .= " INNER JOIN matriculas m ON m.id_estudiante = e.id_estudiante AND m.estado = 'activo' AND m.deleted_at IS NULL";
        $where[] = "m.id_gestion = ?";
        $params[] = $gestionId;

        // Apply filters (may set joins flags)
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
                        } else { $where[] = "c.nivel = ?"; $params[] = $valor; }
                        break;
                    case 'grado':
                        $joins['cursos'] = true;
                        if (is_array($valor)) {
                            $ph = implode(',', array_fill(0, count($valor), '?'));
                            $where[] = "c.grado IN ($ph)";
                            $params = array_merge($params, $valor);
                        } else { $where[] = "c.grado = ?"; $params[] = $valor; }
                        break;
                    case 'paralelo':
                        $joins['cursos'] = true;
                        if (is_array($valor)) {
                            $ph = implode(',', array_fill(0, count($valor), '?'));
                            $where[] = "c.paralelo IN ($ph)";
                            $params = array_merge($params, $valor);
                        } else { $where[] = "c.paralelo = ?"; $params[] = $valor; }
                        break;
                    case 'genero': $where[] = "e.genero = ?"; $params[] = $valor; break;
                    case 'edad_min':
                        if (is_numeric($valor)) { $where[] = "TIMESTAMPDIFF(YEAR, e.fecha_nacimiento, CURDATE()) >= ?"; $params[] = (int)$valor; }
                        break;
                    case 'edad_max':
                        if (is_numeric($valor)) { $where[] = "TIMESTAMPDIFF(YEAR, e.fecha_nacimiento, CURDATE()) <= ?"; $params[] = (int)$valor; }
                        break;
                    case 'pais': $where[] = "e.pais = ?"; $params[] = $valor; break;
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
                    case 'estado_academico':
                        if (in_array($valor, ['aprobado', 'reprobado', 'critico', 'destacado'])) {
                            $joins['calificaciones_prom'] = true;
                            if ($valor === 'critico') {
                                $having[] = "MIN(AVG_calc.prom_materia) < 36";
                            } elseif ($valor === 'destacado') {
                                $having[] = "MIN(AVG_calc.prom_materia) >= 80";
                            } elseif ($valor === 'aprobado') {
                                $having[] = "MIN(AVG_calc.prom_materia) >= $notaMinima";
                            } else {
                                $having[] = "MIN(AVG_calc.prom_materia) < $notaMinima";
                            }
                        }
                        break;
                    case 'materia_especifica':
                        if (is_numeric($valor)) {
                            $joins['calificaciones_prom'] = true;
                            $where[] = "AVG_calc.id_materia = ?";
                            $params[] = (int)$valor;
                        }
                        break;
                    case 'filtro_materias':
                        $joins['calificaciones_prom'] = true;
                        break;
                }
            }
        }

        // JOINs
        // Academic joins (after filters may set them)
        // Group joins: when both AVG_calc and c_trim exist, manage duplication
        if ($joins['calificaciones'] ?? false) {
            $sql .= " INNER JOIN calificaciones c_trim ON c_trim.id_matricula = m.id_matricula AND c_trim.nota IS NOT NULL";
            // Ensure AVG_calc uses the same matricula-materia scope to avoid row multiplication
            if (!($joins['calificaciones_prom'] ?? false)) {
                $sql .= " AND 1=1";
            }
        }
        if ($joins['calificaciones_prom'] ?? false) {
            $trimWhere = "";
            $filtroMatWhere = "";
            if ($trimestre !== 'todos' && $trimestre !== '') {
                $trimWhere = " AND c2.id_trimestre = " . (int)$trimestre;
            }
            $filtroMat = $filters['filtro_materias'] ?? 'todas';
            if ($filtroMat === 'aprobadas') {
                $filtroMatWhere = " GROUP BY c2.id_matricula, c2.id_materia HAVING AVG(c2.nota) >= $notaMinima";
            } elseif ($filtroMat === 'reprobadas') {
                $filtroMatWhere = " GROUP BY c2.id_matricula, c2.id_materia HAVING AVG(c2.nota) < $notaMinima";
            } else {
                $filtroMatWhere = " GROUP BY c2.id_matricula, c2.id_materia";
            }
            $sql .= " INNER JOIN (
                SELECT c2.id_matricula, c2.id_materia, AVG(c2.nota) AS prom_materia
                FROM calificaciones c2
                WHERE c2.nota IS NOT NULL$trimWhere
                $filtroMatWhere
            ) AVG_calc ON AVG_calc.id_matricula = m.id_matricula";
        }
        if ($joins['cursos'] ?? false) {
            $sql .= " INNER JOIN cursos c ON c.id_curso = m.id_curso AND c.estado = 1";
        }
        if ($joins['dificultades'] ?? false) {
            $sql .= " LEFT JOIN estudiante_dificultades ed ON ed.id_estudiante = e.id_estudiante";
        }
        if ($joins['materias_det'] ?? false) {
            $sql .= " INNER JOIN materias m_det ON m_det.id_materia = AVG_calc.id_materia";
        }

        $sql .= " WHERE e.deleted_at IS NULL AND " . implode(" AND ", $where);

        // GROUP BY
        if (!empty($groupBy)) {
            $sql .= " GROUP BY e.id_estudiante";
        }

        $innerOrder = " ORDER BY e.apellido_paterno, e.apellido_materno, e.nombres";

        // For academic filters that need HAVING on derived columns
        if (!empty($having)) {
            $minPromSelect = "MIN(AVG_calc.prom_materia) AS __min_prom";
            // Strip any trailing ORDER BY from $sql (it may not have one yet)
            $innerBase = "SELECT " . $minPromSelect . ", " . substr($sql, 7);

            $havingSql = " HAVING " . implode(" AND ", $having);
            $havingSql = str_replace("MIN(AVG_calc.prom_materia)", "__min_prom", $havingSql);

            $innerSql = $innerBase . $havingSql . $innerOrder;
            $sql = "SELECT t.* FROM ($innerSql) AS t";
        } else {
            $sql .= $innerOrder;
        }

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
