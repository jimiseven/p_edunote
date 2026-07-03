<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 12mm 10mm; size: letter landscape; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 8.5pt; color: #222; }
    h1 { font-size: 14pt; text-align: center; margin: 0 0 4px; color: #1a3a6b; }
    .subtitle { text-align: center; font-size: 9pt; color: #555; margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; font-size: 7.5pt; }
    th, td { border: 1px solid #333; padding: 2px 3px; text-align: center; vertical-align: middle; }
    th { background: #2f75b5; color: #fff; font-weight: bold; font-size: 7pt; }
    .name-cell { text-align: left; font-weight: bold; min-width: 160px; }
    .promedio-cell { background: #ffedea; color: #d72c16; font-weight: bold; }
    .header-grupo { background: #4472c4; }
    .header-sub { background: #d9e1f2; color: #1a3a6b; font-size: 6.5pt; }
    .tr-even td { background-color: #f5f7fa; }
    .tr-even .name-cell { background-color: #f5f7fa; }
    .footer { margin-top: 8px; font-size: 7pt; text-align: right; color: #888; }
    .page-break { page-break-after: always; }
</style>
</head>
<body>

<h1>UNIDAD EDUCATIVA</h1>
<div class="subtitle">
    <strong>BOLETÍN DE NOTAS</strong> &mdash;
    <?= e($course['nivel']) ?> &mdash;
    <?= e($course['grado']) ?>° &mdash;
    <?= e($course['paralelo']) ?> &mdash;
    Gestión <?= e($gestion['anio']) ?>
    &middot; <?= $vista === 'trimestral' ? 'Trimestre ' . $trimestre : 'Vista Anual' ?>
</div>

<table>
    <thead>
        <tr>
            <th style="width:22px">#</th>
            <th class="name-cell">ESTUDIANTE</th>
            <?php foreach ($subjects['individuales'] as $m): ?>
                <th class="header-sub"><?= e($m['abreviatura'] ?? strtoupper(substr($m['nombre'], 0, 6))) ?></th>
            <?php endforeach; ?>
            <?php foreach ($subjects['grupos'] as $g): ?>
                <?php foreach ($g['hijas'] as $h): ?>
                    <th class="header-sub"><?= e($h['abreviatura'] ?? strtoupper(substr($h['nombre'], 0, 6))) ?></th>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <th class="promedio-cell" style="min-width:28px">PROM</th>
        </tr>
    </thead>
    <tbody>
        <?php $contador = 1; ?>
        <?php foreach ($students as $est): ?>
            <?php $idEst = (int) $est['id_estudiante']; ?>
            <tr class="<?= $contador % 2 === 0 ? 'tr-even' : '' ?>">
                <td><?= $contador++ ?></td>
                <td class="name-cell"><?= e(strtoupper(trim($est['apellido_paterno'] . ' ' . $est['apellido_materno'] . ', ' . $est['nombres']))) ?></td>
                <?php foreach ($subjects['individuales'] as $m): ?>
                    <td>
                        <?php if ($vista === 'trimestral'): ?>
                            <?= e((string) ($grades[$idEst][$m['id_materia']][$trimestre] ?? '-')) ?>
                        <?php else: ?>
                            <?php $s = 0; $c = 0;
                            for ($t = 1; $t <= 3; $t++) {
                                if (isset($grades[$idEst][$m['id_materia']][$t])) { $s += $grades[$idEst][$m['id_materia']][$t]; $c++; }
                            }
                            echo $c > 0 ? number_format($s / $c, 1) : '-'; ?>
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>
                <?php foreach ($subjects['grupos'] as $g): ?>
                    <?php foreach ($g['hijas'] as $h): ?>
                        <td>
                            <?php if ($vista === 'trimestral'): ?>
                                <?= e((string) ($grades[$idEst][$h['id_materia']][$trimestre] ?? '-')) ?>
                            <?php else: ?>
                                <?php $s = 0; $c = 0;
                                for ($t = 1; $t <= 3; $t++) {
                                    if (isset($grades[$idEst][$h['id_materia']][$t])) { $s += $grades[$idEst][$h['id_materia']][$t]; $c++; }
                                }
                                echo $c > 0 ? number_format($s / $c, 1) : '-'; ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                <td class="promedio-cell"><?= e($vista === 'trimestral' ? ($promediosTrim[$idEst] ?? '-') : ($promedios[$idEst] ?? '-')) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="footer">Generado el <?= date('d/m/Y H:i') ?></div>
</body>
</html>
