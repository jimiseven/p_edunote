<?php
/**
 * Genera notas de prueba para todos los estudiantes
 * Inserta directamente en la BD.
 * Uso: php bds/generar_notas.php
 */

require __DIR__ . '/../config/bootstrap.php';

use App\Core\Database;

$conn = Database::connection();

echo "Obteniendo datos...\n";

// Obtener matriculas con curso
$stmt = $conn->query("
    SELECT m.id_matricula, m.id_estudiante, m.id_curso, c.nivel
    FROM matriculas m
    INNER JOIN cursos c ON c.id_curso = m.id_curso
    WHERE m.estado = 'activo' AND m.deleted_at IS NULL
    ORDER BY m.id_matricula
");
$matriculas = $stmt->fetchAll();

// Obtener materias por curso
$stmt = $conn->query("
    SELECT cm.id_curso, cm.id_materia
    FROM curso_materia cm
    WHERE cm.estado = 1
    ORDER BY cm.id_curso, cm.id_materia
");
$materiasPorCurso = [];
foreach ($stmt->fetchAll() as $row) {
    $materiasPorCurso[(int)$row['id_curso']][] = (int)$row['id_materia'];
}

// Mapa: (curso_materia id via curso_materia) -> id_asignacion
$stmt = $conn->query("
    SELECT cm.id_curso_materia, cm.id_curso, cm.id_materia, da.id_asignacion
    FROM curso_materia cm
    INNER JOIN docente_asignaciones da ON da.id_curso_materia = cm.id_curso_materia AND da.estado = 'activo'
    WHERE cm.estado = 1
");
$asigMap = [];
foreach ($stmt->fetchAll() as $row) {
    $key = (int)$row['id_curso'] . '_' . (int)$row['id_materia'];
    $asigMap[$key] = (int)$row['id_asignacion'];
}

$comentariosInicial = [
    'Logra las actividades propuestas',
    'En proceso de desarrollo',
    'Requiere apoyo en actividades',
    'Participa activamente en clases',
    'Muestra interés en aprender',
    'Se relaciona bien con sus compañeros',
    'Desarrolla habilidades progresivamente',
    'Cumple con las tareas asignadas',
    'Necesita reforzar hábitos',
    'Avanza según su ritmo de aprendizaje',
];

$trimestres = [1, 2, 3];
$insertSQL = "INSERT INTO calificaciones (id_matricula, id_materia, id_trimestre, id_asignacion, nota, comentario, estado) VALUES ";
$batch = [];
$batchSize = 500;
$total = 0;

echo "Generando notas...\n";
$conn->beginTransaction();

foreach ($matriculas as $mat) {
    $idMat = (int)$mat['id_matricula'];
    $idCurso = (int)$mat['id_curso'];
    $nivel = $mat['nivel'];

    $materias = $materiasPorCurso[$idCurso] ?? [];
    if (empty($materias)) continue;

    foreach ($materias as $idMatId) {
        $key = $idCurso . '_' . $idMatId;
        $idAsig = $asigMap[$key] ?? null;
        $idAsigVal = $idAsig ?: 'NULL';

        foreach ($trimestres as $trim) {
            if ($nivel === 'Inicial') {
                $comentario = $comentariosInicial[($total) % count($comentariosInicial)];
                $batch[] = "($idMat, $idMatId, $trim, " . ($idAsigVal === 'NULL' ? 'NULL' : $idAsigVal) . ", NULL, " . $conn->quote($comentario) . ", 'registrado')";
            } else {
                $base = 65 + ($idMat % 20) - 10;
                $variacion = (($total * 7 + $trim * 13) % 21) - 10;
                $nota = max(10, min(100, $base + $variacion));
                $batch[] = "($idMat, $idMatId, $trim, " . ($idAsigVal === 'NULL' ? 'NULL' : $idAsigVal) . ", $nota, NULL, 'registrado')";
            }
            $total++;

            if (count($batch) >= $batchSize) {
                $conn->exec($insertSQL . implode(', ', $batch));
                $batch = [];
                echo "  $total insertados...\n";
            }
        }
    }
}

// Restantes
if (!empty($batch)) {
    $conn->exec($insertSQL . implode(', ', $batch));
    echo "  $total insertados...\n";
}

$conn->commit();
echo "Listo! $total calificaciones insertadas.\n";
