<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="main-title mb-1">Centralizador: <?= e($course['nivel'] . ' ' . $course['grado'] . '° ' . $course['paralelo']) ?></h1>
        <p class="text-muted mb-0"><?= e($gestion['nombre'] . ' - ' . $gestion['anio']) ?></p>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
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
        <button onclick="window.print()" class="btn btn-primary btn-sm ms-1">Imprimir</button>
    </div>
</div>

<?php if (empty($students)): ?>
    <div class="alert alert-warning">No hay estudiantes matriculados en este curso.</div>
    <?php return; ?>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="table-responsive" style="max-height: calc(100vh - 200px); overflow: auto;">
        <table class="table table-bordered align-middle mb-0" style="font-size: 0.88rem;">
            <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                <tr>
                    <th style="position: sticky; left: 0; background: #f8f9fa; z-index: 11;">#</th>
                    <th style="position: sticky; left: 40px; background: #f8f9fa; z-index: 11;">Estudiante</th>
                    <?php foreach ($subjects as $sub): ?>
                        <?php if ($esInicial): ?>
                            <th colspan="<?= count($trimestres) ?>"><?= e($sub['nombre']) ?></th>
                        <?php else: ?>
                            <th colspan="4" class="text-center"><?= e($sub['nombre']) ?></th>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if (!$esInicial): ?>
                        <th>P. General</th>
                    <?php endif; ?>
                </tr>
                <?php if ($esInicial): ?>
                    <tr>
                        <th style="position: sticky; left: 0; background: #e9ecef; z-index: 11;"></th>
                        <th style="position: sticky; left: 40px; background: #e9ecef; z-index: 11;"></th>
                        <?php foreach ($subjects as $sub): ?>
                            <?php foreach ($trimestres as $t): ?>
                                <th class="text-center" style="font-size: 0.78rem; font-weight: 400;"><?= e('T' . $t['numero']) ?></th>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php else: ?>
                    <tr>
                        <th style="position: sticky; left: 0; background: #e9ecef; z-index: 11;"></th>
                        <th style="position: sticky; left: 40px; background: #e9ecef; z-index: 11;"></th>
                        <?php foreach ($subjects as $sub): ?>
                            <th class="text-center" style="font-size: 0.78rem; font-weight: 400;">T1</th>
                            <th class="text-center" style="font-size: 0.78rem; font-weight: 400;">T2</th>
                            <th class="text-center" style="font-size: 0.78rem; font-weight: 400;">T3</th>
                            <th class="text-center" style="font-size: 0.78rem; font-weight: 400;">P</th>
                        <?php endforeach; ?>
                        <th></th>
                    </tr>
                <?php endif; ?>
            </thead>
            <tbody>
                <?php $contador = 1; ?>
                <?php foreach ($students as $student): ?>
                    <?php
                    $idMat = (int) $student['id_matricula'];
                    ?>
                    <tr>
                        <td style="position: sticky; left: 0; background: #fff; z-index: 1;"><?= $contador++ ?></td>
                        <td style="position: sticky; left: 40px; background: #fff; z-index: 1;">
                            <?= e(strtoupper(trim($student['apellido_paterno'] . ' ' . $student['apellido_materno'] . ', ' . $student['nombres']))) ?>
                        </td>
                        <?php if ($esInicial): ?>
                            <?php foreach ($subjects as $sub): ?>
                                <?php foreach ($trimestres as $t): ?>
                                    <td class="text-center" style="white-space: pre-wrap; max-width: 150px; font-size: 0.82rem;">
                                        <?= e($grades[$idMat][$sub['id_materia']][$t['id_trimestre']] ?? '') ?>
                                    </td>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php
                            $sumaPromedios = 0;
                            $contPromedios = 0;
                            ?>
                            <?php foreach ($subjects as $sub): ?>
                                <?php
                                $n1 = $grades[$idMat][$sub['id_materia']][1] ?? '';
                                $n2 = $grades[$idMat][$sub['id_materia']][2] ?? '';
                                $n3 = $grades[$idMat][$sub['id_materia']][3] ?? '';
                                $notasValidas = array_filter([$n1, $n2, $n3], fn($v) => is_numeric($v));
                                $promedio = count($notasValidas) > 0 ? number_format(array_sum($notasValidas) / count($notasValidas), 2) : '';
                                if ($promedio !== '' && !$sub['es_extra'] && !$sub['es_submateria']) {
                                    $sumaPromedios += (float) $promedio;
                                    $contPromedios++;
                                }
                                ?>
                                <td class="text-center <?= (is_numeric($n1) && $n1 < 51) ? 'text-danger fw-bold' : '' ?>"><?= e($n1) ?></td>
                                <td class="text-center <?= (is_numeric($n2) && $n2 < 51) ? 'text-danger fw-bold' : '' ?>"><?= e($n2) ?></td>
                                <td class="text-center <?= (is_numeric($n3) && $n3 < 51) ? 'text-danger fw-bold' : '' ?>"><?= e($n3) ?></td>
                                <td class="text-center bg-light fw-medium <?= (is_numeric($promedio) && $promedio < 51) ? 'text-danger' : '' ?>"><?= e($promedio) ?></td>
                            <?php endforeach; ?>
                            <td class="text-center fw-bold">
                                <?= $contPromedios > 0 ? number_format($sumaPromedios / $contPromedios, 2) : '--' ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
