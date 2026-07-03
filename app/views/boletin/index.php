<div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <a href="<?= e(base_url('/dashboard/' . strtolower($course['nivel']))) ?>" class="btn btn-outline-secondary">
        &larr; Atras
    </a>
    <h2 class="main-title mb-0">Boletin: <?= e($course['nivel'] . ' ' . $course['grado'] . '° ' . $course['paralelo']) ?></h2>
    <div class="d-flex gap-2">
        <a href="<?= e(base_url('/boletin/pdf?id_curso=' . $course['id_curso'] . '&vista=' . $vista . '&trimestre=' . $trimestre)) ?>" class="btn btn-danger" target="_blank">
            PDF
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="bi bi-printer"></i> Imprimir
        </button>
    </div>
</div>

<div class="d-flex gap-3 mb-3 no-print">
    <form method="GET" action="">
        <input type="hidden" name="id_curso" value="<?= e($course['id_curso']) ?>">
        <div class="input-group input-group-sm">
            <span class="input-group-text">Vista</span>
            <select name="vista" class="form-select" onchange="this.form.submit()">
                <option value="trimestral" <?= $vista === 'trimestral' ? 'selected' : '' ?>>Trimestral</option>
                <option value="anual" <?= $vista === 'anual' ? 'selected' : '' ?>>Anual</option>
            </select>
        </div>
    </form>
    <?php if ($vista === 'trimestral'): ?>
        <form method="GET" action="">
            <input type="hidden" name="id_curso" value="<?= e($course['id_curso']) ?>">
            <input type="hidden" name="vista" value="trimestral">
            <div class="input-group input-group-sm">
                <span class="input-group-text">Trimestre</span>
                <select name="trimestre" class="form-select" onchange="this.form.submit()">
                    <option value="1" <?= $trimestre === 1 ? 'selected' : '' ?>>Primer Trimestre</option>
                    <option value="2" <?= $trimestre === 2 ? 'selected' : '' ?>>Segundo Trimestre</option>
                    <option value="3" <?= $trimestre === 3 ? 'selected' : '' ?>>Tercer Trimestre</option>
                </select>
            </div>
        </form>
    <?php endif; ?>
    <div class="d-flex align-items-center">
        <span class="badge bg-info fs-6">
            <?= $vista === 'trimestral' ? 'Trimestre ' . $trimestre : 'Vista Anual' ?>
        </span>
    </div>
</div>

<?php if (empty($subjects['todas'])): ?>
    <div class="alert alert-warning">No hay materias asignadas a este curso.</div>
    <?php return; ?>
<?php elseif (empty($students)): ?>
    <div class="alert alert-warning">No hay estudiantes matriculados en este curso.</div>
    <?php return; ?>
<?php endif; ?>

<style>
    .boletin-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    .boletin-table th, .boletin-table td { border: 1px solid #dee2e6; padding: 5px 4px; text-align: center; vertical-align: middle; }
    .boletin-table thead th { position: sticky; top: 0; z-index: 10; }
    .boletin-table .num-cell, .boletin-table .name-cell { position: sticky; background: #fff; z-index: 5; }
    .boletin-table .num-cell { left: 0; z-index: 6; min-width: 30px; }
    .boletin-table .name-cell { left: 30px; z-index: 6; min-width: 220px; text-align: left; font-weight: 500; }
    .boletin-table thead .num-cell, .boletin-table thead .name-cell { z-index: 15; background: #e9ecef !important; }
    .header-principal { background: #2f75b5; color: #fff; font-weight: bold; }
    .header-grupo { background: #d9e1f2; font-weight: bold; color: #305496; }
    .header-sub { background: #e7edf7; font-weight: 600; font-size: 0.78rem; }
    .promedio-cell { background: #ffedea; color: #d72c16; font-weight: bold; }
    .tr-striped td { background-color: #f8f9fa; }
    .tr-striped .num-cell, .tr-striped .name-cell { background-color: #f8f9fa; }
    @media print { .no-print { display: none !important; } }
</style>

<div class="table-responsive" style="max-height: calc(100vh - 150px); overflow: auto;">
    <table class="boletin-table">
        <thead>
            <tr>
                <th rowspan="2" class="num-cell">#</th>
                <th rowspan="2" class="name-cell">Estudiante</th>
                <?php if (!empty($subjects['individuales'])): ?>
                    <th colspan="<?= count($subjects['individuales']) ?>" class="header-principal"></th>
                <?php endif; ?>
                <?php foreach ($subjects['grupos'] as $g): ?>
                    <th colspan="<?= count($g['hijas']) ?>" class="header-grupo"><?= e($g['nombre']) ?></th>
                <?php endforeach; ?>
                <th rowspan="2" class="promedio-cell">PROM. GRAL</th>
            </tr>
            <tr>
                <?php foreach ($subjects['individuales'] as $m): ?>
                    <th class="header-sub"><?= e($m['abreviatura'] ?? strtoupper(substr($m['nombre'], 0, 5))) ?></th>
                <?php endforeach; ?>
                <?php foreach ($subjects['grupos'] as $g): ?>
                    <?php foreach ($g['hijas'] as $h): ?>
                        <th class="header-sub"><?= e($h['abreviatura'] ?? strtoupper(substr($h['nombre'], 0, 5))) ?></th>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php $contador = 1; ?>
            <?php foreach ($students as $est): ?>
                <?php $idEst = (int) $est['id_estudiante']; ?>
                <tr class="<?= $contador % 2 === 0 ? 'tr-striped' : '' ?>">
                    <td class="num-cell"><?= $contador++ ?></td>
                    <td class="name-cell"><?= e(strtoupper(trim($est['apellido_paterno'] . ' ' . $est['apellido_materno'] . ', ' . $est['nombres']))) ?></td>
                    <?php foreach ($subjects['individuales'] as $m): ?>
                        <td>
                            <?php if ($vista === 'trimestral'): ?>
                                <?= e($grades[$idEst][$m['id_materia']][$trimestre] ?? '-') ?>
                            <?php else: ?>
                                <?php
                                $s = 0; $c = 0;
                                for ($t = 1; $t <= 3; $t++) {
                                    if (isset($grades[$idEst][$m['id_materia']][$t])) { $s += $grades[$idEst][$m['id_materia']][$t]; $c++; }
                                }
                                echo $c > 0 ? number_format($s / $c, 1) : '-';
                                ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    <?php foreach ($subjects['grupos'] as $g): ?>
                        <?php foreach ($g['hijas'] as $h): ?>
                            <td>
                                <?php if ($vista === 'trimestral'): ?>
                                    <?= e($grades[$idEst][$h['id_materia']][$trimestre] ?? '-') ?>
                                <?php else: ?>
                                    <?php
                                    $s = 0; $c = 0;
                                    for ($t = 1; $t <= 3; $t++) {
                                        if (isset($grades[$idEst][$h['id_materia']][$t])) { $s += $grades[$idEst][$h['id_materia']][$t]; $c++; }
                                    }
                                    echo $c > 0 ? number_format($s / $c, 1) : '-';
                                    ?>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    <td class="promedio-cell">
                        <?= e($vista === 'trimestral' ? ($promediosTrim[$idEst] ?? '-') : ($promedios[$idEst] ?? '-')) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
