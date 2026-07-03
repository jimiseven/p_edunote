<?php
/**
 * Regenera notas para Primaria/Secundaria con distribución realista:
 * - 15% reprobados (< 51)
 * - 10% críticos (< 36)
 * - 15% destacados (>= 80)
 * - 60% normal (51-79)
 */
require __DIR__ . '/../config/bootstrap.php';
use App\Core\Database;

$conn = Database::connection();

// Get all calificaciones with nota not null, excluding Inicial
$rows = $conn->query("
    SELECT c.id_calificacion, c.id_matricula, c.id_materia, c.id_trimestre,
           c2.nivel, m.id_estudiante
    FROM calificaciones c
    JOIN matriculas m ON m.id_matricula = c.id_matricula
    JOIN cursos c2 ON c2.id_curso = m.id_curso
    WHERE c.nota IS NOT NULL AND c2.nivel IN ('Primaria', 'Secundaria')
    ORDER BY c.id_calificacion
")->fetchAll();

echo "Total a regenerar: " . count($rows) . "\n";

$conn->beginTransaction();
$stmt = $conn->prepare("UPDATE calificaciones SET nota = ? WHERE id_calificacion = ?");
$count = 0;

foreach ($rows as $row) {
    $id = (int)$row['id_calificacion'];
    $idEst = (int)$row['id_estudiante'];
    $trim = (int)$row['id_trimestre'];
    $materia = (int)$row['id_materia'];
    
    // Deterministic "base" per student (seed)
    $seed = $idEst * 13 + $materia * 7;
    srand($seed + $trim * 31);
    
    $rand = rand(1, 100);
    
    if ($rand <= 15) {
        // Reprobado (10-50)
        $nota = rand(10, 50);
    } elseif ($rand <= 25) {
        // Critico (10-35)
        $nota = rand(10, 35);
    } elseif ($rand <= 40) {
        // Destacado (80-100)
        $nota = rand(80, 100);
    } else {
        // Normal (51-79)
        $nota = rand(51, 79);
    }
    
    $stmt->execute([$nota, $id]);
    $count++;
    
    if ($count % 2000 === 0) echo "  $count actualizados...\n";
}

$conn->commit();
echo "Listo! $count notas actualizadas.\n";

// Verify distribution
$stats = $conn->query("
    SELECT
        SUM(CASE WHEN c.nota < 51 THEN 1 ELSE 0 END) as reprobados,
        SUM(CASE WHEN c.nota < 36 THEN 1 ELSE 0 END) as criticos,
        SUM(CASE WHEN c.nota >= 80 THEN 1 ELSE 0 END) as destacados,
        COUNT(*) as total
    FROM calificaciones c
    JOIN matriculas m ON m.id_matricula = c.id_matricula
    JOIN cursos c2 ON c2.id_curso = m.id_curso
    WHERE c.nota IS NOT NULL AND c2.nivel IN ('Primaria', 'Secundaria')
")->fetch();

echo "Distribución:\n";
echo "  Reprobados (< 51): {$stats['reprobados']} (" . round($stats['reprobados']*100/$stats['total'], 1) . "%)\n";
echo "  Críticos (< 36): {$stats['criticos']} (" . round($stats['criticos']*100/$stats['total'], 1) . "%)\n";
echo "  Destacados (>= 80): {$stats['destacados']} (" . round($stats['destacados']*100/$stats['total'], 1) . "%)\n";
echo "  Total: {$stats['total']}\n";
