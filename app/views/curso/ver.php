<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h1 class="main-title mb-1">Centralizador: <?= e($course['nivel'] . ' ' . $course['grado'] . '° ' . $course['paralelo']) ?></h1>
        <p class="mb-0 text-secondary-emphasis small"><?= e($gestion['nombre'] . ' - ' . $gestion['anio']) ?></p>
    </div>
</div>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div class="d-flex flex-wrap align-items-center gap-2">
        <?php if ($prevCurso): ?>
            <a href="<?= e(base_url('/ver-curso?curso=' . $prevCurso['id_curso'])) ?>" class="btn btn-outline-secondary btn-sm">&larr; <?= e($prevCurso['nivel'] . ' ' . $prevCurso['grado'] . '° ' . $prevCurso['paralelo']) ?></a>
        <?php else: ?>
            <button class="btn btn-outline-secondary btn-sm" disabled>&larr; Anterior</button>
        <?php endif; ?>
        <?php if ($nextCurso): ?>
            <a href="<?= e(base_url('/ver-curso?curso=' . $nextCurso['id_curso'])) ?>" class="btn btn-outline-secondary btn-sm ms-1"><?= e($nextCurso['nivel'] . ' ' . $nextCurso['grado'] . '° ' . $nextCurso['paralelo']) ?> &rarr;</a>
        <?php else: ?>
            <button class="btn btn-outline-secondary btn-sm ms-1" disabled>Siguiente &rarr;</button>
        <?php endif; ?>
    </div>
    <div>
        <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">Volver</a>
        <a href="<?= e(base_url('/ver-curso/excel?curso=' . $course['id_curso'])) ?>" class="btn btn-success btn-sm ms-1">📥 Excel</a>
    </div>
</div>

<?php if (empty($students)): ?>
    <div class="alert alert-warning">No hay estudiantes matriculados en este curso.</div>
    <?php return; ?>
<?php endif; ?>

<?php if (!$esInicial): ?>
    <?php
    $totalSubjects = count(array_filter($subjects, fn($s) => !$s['es_extra'] && !$s['es_submateria']));
    $aprobados = 0; $reprobados = 0;
    foreach ($students as $st) {
        $idMat = (int) $st['id_matricula'];
        $s = 0; $c = 0;
        foreach ($subjects as $sub) {
            $n = array_filter([
                $grades[$idMat][$sub['id_materia']][1] ?? '',
                $grades[$idMat][$sub['id_materia']][2] ?? '',
                $grades[$idMat][$sub['id_materia']][3] ?? ''
            ], fn($v) => is_numeric($v));
            $p = count($n) > 0 ? array_sum($n) / count($n) : 0;
            if ($p > 0 && !$sub['es_extra'] && !$sub['es_submateria']) { $s += $p; $c++; }
        }
        $pg = $c > 0 ? $s / $c : 0;
        if ($pg >= 51) $aprobados++; elseif ($pg > 0) $reprobados++;
    }
    ?>
<div class="d-flex flex-wrap align-items-center gap-3 mb-3 small">
    <span class="fw-semibold" style="color:var(--bs-emphasis-color);">📚 <?= $totalSubjects ?> Materias</span>
    <span class="fw-semibold" style="color:var(--bs-emphasis-color);">👥 <?= count($students) ?> Alumnos</span>
    <span class="fw-semibold" style="color:var(--bs-success-text);">✅ <?= $aprobados ?> Aprobados</span>
    <span class="fw-semibold" style="color:var(--bs-danger-text);">❌ <?= $reprobados ?> Reprobados</span>
    <span class="fw-semibold" style="color:var(--bs-primary-text);">📊 <?= round(($aprobados / max(count($students),1)) * 100, 1) ?>% Aprobación</span>
</div>
<?php endif; ?>

<div class="card shadow-sm border-0 rounded-3" style="background:var(--bs-body-bg);">
    <div class="table-responsive" style="max-height:calc(100vh - 260px);overflow:auto;" id="ct">
        <table class="table table-sm align-middle mb-0" style="font-size:0.82rem;min-width:600px;">
            <thead>
                <tr>
                    <th style="width:36px;min-width:36px;max-width:36px;" class="text-center text-white">#</th>
                    <th style="min-width:180px;" class="text-white">Estudiante</th>
                    <?php foreach ($subjects as $sub): ?>
                        <th colspan="<?= $esInicial ? count($trimestres) : 4 ?>" class="text-center text-white" style="background:var(--table-header-bg);min-width:<?= $esInicial ? count($trimestres)*45 : 180 ?>px;"><?= e($sub['nombre']) ?></th>
                    <?php endforeach; ?>
                    <?php if (!$esInicial): ?>
                        <th class="text-center text-white" style="background:var(--table-header-bg);min-width:55px;">P. Gral</th>
                    <?php endif; ?>
                </tr>
                <tr>
                    <th style="width:36px;min-width:36px;max-width:36px;"></th>
                    <th style="min-width:180px;"></th>
                    <?php if ($esInicial): ?>
                        <?php foreach ($subjects as $sub): ?>
                            <?php foreach ($trimestres as $t): ?>
                                <th class="text-center text-secondary fw-normal" style="font-size:0.73rem;background:var(--table-subheader-bg);">T<?= e($t['numero']) ?></th>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php foreach ($subjects as $sub): ?>
                            <th class="text-center text-secondary fw-normal" style="font-size:0.73rem;background:var(--table-subheader-bg);">T1</th>
                            <th class="text-center text-secondary fw-normal" style="font-size:0.73rem;background:var(--table-subheader-bg);">T2</th>
                            <th class="text-center text-secondary fw-normal" style="font-size:0.73rem;background:var(--table-subheader-bg);">T3</th>
                            <th class="text-center fw-semibold text-secondary" style="font-size:0.73rem;background:var(--bs-warning-bg-subtle);">Prom</th>
                        <?php endforeach; ?>
                        <th style="background:var(--table-subheader-bg);"></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($students as $st): $idMat = (int) $st['id_matricula']; ?>
                <tr>
                    <td class="text-center text-secondary fw-semibold" style="width:36px;min-width:36px;max-width:36px;"><?= $i++ ?></td>
                    <td style="min-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        <?= e(strtoupper(trim($st['apellido_paterno'] . ' ' . $st['apellido_materno'] . ', ' . $st['nombres']))) ?>
                    </td>
                    <?php if ($esInicial): ?>
                        <?php foreach ($subjects as $sub): ?>
                            <?php foreach ($trimestres as $t): ?>
                                <?php $v = $grades[$idMat][$sub['id_materia']][$t['id_trimestre']] ?? ''; ?>
                                <td class="text-center">
                                    <?php if (is_numeric($v)): ?>
                                        <span class="badge rounded-pill <?= $v >= 51 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> px-2 fw-medium"><?= (int)$v == $v ? (int)$v : $v ?></span>
                                    <?php else: ?>
                                        <span class="text-muted"><?= e($v) ?: '--' ?></span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php
                        $sumProm = 0; $cntProm = 0;
                        foreach ($subjects as $sub):
                            $nn = [];
                            for ($t=1;$t<=3;$t++) $nn[$t] = $grades[$idMat][$sub['id_materia']][$t] ?? '';
                            $valid = array_filter($nn, fn($v) => is_numeric($v));
                            $prom = count($valid) > 0 ? number_format(array_sum($valid) / count($valid), 2) : '';
                            if ($prom !== '' && !$sub['es_extra'] && !$sub['es_submateria']) {
                                $sumProm += (float)$prom; $cntProm++;
                            }
                        ?>
                            <td class="text-center"><?= is_numeric($nn[1]) ? '<span class="badge rounded-pill '.($nn[1]>=51?'bg-success-subtle text-success':'bg-danger-subtle text-danger').' px-2 fw-medium">'.((int)$nn[1]==$nn[1]?(int)$nn[1]:$nn[1]).'</span>' : '<span class="text-muted">--</span>' ?></td>
                            <td class="text-center"><?= is_numeric($nn[2]) ? '<span class="badge rounded-pill '.($nn[2]>=51?'bg-success-subtle text-success':'bg-danger-subtle text-danger').' px-2 fw-medium">'.((int)$nn[2]==$nn[2]?(int)$nn[2]:$nn[2]).'</span>' : '<span class="text-muted">--</span>' ?></td>
                            <td class="text-center"><?= is_numeric($nn[3]) ? '<span class="badge rounded-pill '.($nn[3]>=51?'bg-success-subtle text-success':'bg-danger-subtle text-danger').' px-2 fw-medium">'.((int)$nn[3]==$nn[3]?(int)$nn[3]:$nn[3]).'</span>' : '<span class="text-muted">--</span>' ?></td>
                            <td class="text-center fw-semibold" style="background:var(--bs-warning-bg-subtle);">
                                <?php if (is_numeric($prom)): ?>
                                    <span class="badge rounded-pill <?= $prom >= 51 ? 'bg-success text-white' : 'bg-danger text-white' ?> px-2 fw-bold"><?= $prom ?></span>
                                <?php else: ?>
                                    <span class="text-muted">--</span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                        <td class="text-center fw-bold">
                            <?php $promGral = $cntProm > 0 ? number_format($sumProm / $cntProm, 2) : '--'; ?>
                            <?php if (is_numeric($promGral)): ?>
                                <span class="badge rounded-pill px-2 py-1 fw-bold <?= $promGral >= 51 ? 'bg-success text-white' : 'bg-danger text-white' ?>"><?= $promGral ?></span>
                            <?php else: ?>--<?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
#ct td, #ct th { border:1px solid var(--bs-border-color); white-space:nowrap; }

/* Columna # fija */
#ct td:first-child, #ct th:first-child {
    position: sticky;
    left: 0;
    width: 36px;
    min-width: 36px;
    max-width: 36px;
    z-index: 50;
}

/* Columna Estudiante fija — bloquea el texto que pasa detrás */
#ct td:nth-child(2), #ct th:nth-child(2) {
    position: sticky;
    left: 36px;
    min-width: 180px;
    z-index: 60;
    border-right: 4px solid var(--bs-border-color);
    box-shadow: 6px 0 10px rgba(0,0,0,0.15);
}

/* Header rows fijos verticalmente */
#ct thead tr:nth-child(1) {
    position: sticky;
    top: 0;
    z-index: 70;
}
#ct thead tr:nth-child(1) th { background: var(--table-header-bg); }
#ct thead tr:nth-child(2) {
    position: sticky;
    top: 35px;
    z-index: 65;
}
#ct thead tr:nth-child(2) th { background: var(--table-subheader-bg); }

/* Las celdas sticky del header deben tapar las demás celdas */
#ct thead tr:nth-child(1) th:first-child, #ct thead tr:nth-child(1) th:nth-child(2) { z-index: 80; }
#ct thead tr:nth-child(2) th:first-child, #ct thead tr:nth-child(2) th:nth-child(2) { z-index: 75; }

/* Fondos cuerpo */
#ct tbody td:nth-child(1), #ct tbody td:nth-child(2) { background: var(--table-cell-bg); }

#ct tbody tr:hover td { background-color: var(--table-hover) !important; }
#ct tbody tr:hover td:nth-child(1), #ct tbody tr:hover td:nth-child(2) { background-color: var(--table-hover) !important; }
</style>
