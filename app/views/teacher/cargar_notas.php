<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="main-title mb-1"><?= e($assignment['materia']) ?></h1>
        <p class="text-muted mb-0">
            <?= e($assignment['nivel'] . ' ' . $assignment['grado'] . '° ' . $assignment['paralelo']) ?>
            &mdash; <?= e($assignment['gestion_nombre'] . ' ' . $assignment['anio']) ?>
        </p>
    </div>
    <a href="<?= e(base_url('/docente/dashboard')) ?>" class="btn btn-outline-secondary">Volver</a>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!$esInicial): ?>
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalExcel">
        Cargar desde Excel
    </button>

    <div class="modal fade" id="modalExcel" tabindex="-1" aria-labelledby="modalExcelLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalExcelLabel">Cargar Notas desde Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?= e(base_url('/docente/notas/store')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_asignacion" value="<?= e($assignment['id_asignacion']) ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Seleccione el Trimestre:</label>
                            <select name="bimestre_excel" class="form-select mb-3">
                                <?php foreach ($trimestres as $t): ?>
                                    <option value="<?= e($t['id_trimestre']) ?>" <?= $t['esta_activo'] ? '' : 'disabled' ?>>
                                        <?= e($t['nombre']) ?><?= $t['esta_activo'] ? '' : ' (no habilitado)' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label class="form-label">Pegue aquí la columna de notas:</label>
                            <textarea name="datos_excel" class="form-control font-monospace" rows="6" placeholder="Pegue aquí SOLO la columna de notas desde Excel"></textarea>
                            <div class="form-text">Una nota por línea, en el mismo orden que aparecen los estudiantes en la tabla.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="guardar_excel" class="btn btn-primary">Cargar Notas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="table-container card shadow-sm">
    <form method="POST" action="<?= e(base_url('/docente/notas/store')) ?>" id="notasForm">
        <?= csrf_field() ?>
        <input type="hidden" name="id_asignacion" value="<?= e($assignment['id_asignacion']) ?>">

        <div class="table-responsive" style="max-height: 70vh; overflow: auto;">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th style="position: sticky; left: 0; background: #f8f9fa; z-index: 11;">#</th>
                        <th style="position: sticky; left: 40px; background: #f8f9fa; z-index: 11;">Estudiante</th>
                        <?php foreach ($trimestres as $t): ?>
                            <th class="text-center <?= $t['esta_activo'] ? 'bg-primary text-white' : 'bg-secondary text-white' ?>">
                                <?= e($t['nombre']) ?>
                                <?php if (!$t['esta_activo']): ?>
                                    <span class="badge bg-light text-dark ms-1">No habilitado</span>
                                <?php endif; ?>
                            </th>
                        <?php endforeach; ?>
                        <?php if (!$esInicial): ?>
                            <th>Promedio</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $contador = 1; ?>
                    <?php foreach ($students as $student): ?>
                        <?php
                        $notas = [];
                        foreach ($trimestres as $t) {
                            $notas[$t['id_trimestre']] = $existingGrades[$student['id_matricula']][$t['id_trimestre']] ?? '';
                        }
                        ?>
                        <tr>
                            <td style="position: sticky; left: 0; background: #fff; z-index: 1;"><?= $contador++ ?></td>
                            <td style="position: sticky; left: 40px; background: #fff; z-index: 1;">
                                <strong><?= e(trim($student['apellido_paterno'] . ' ' . $student['apellido_materno'] . ' ' . $student['nombres'])) ?></strong>
                            </td>
                            <?php foreach ($trimestres as $t): ?>
                                <td class="text-center">
                                    <?php if ($esInicial): ?>
                                        <textarea name="notas[<?= e($student['id_matricula']) ?>][<?= e($t['id_trimestre']) ?>]"
                                                  class="form-control form-control-sm font-monospace"
                                                  style="min-width: 140px; height: 60px; resize: vertical;"
                                                  <?= $t['esta_activo'] ? '' : 'readonly disabled' ?>
                                                  placeholder="<?= $t['esta_activo'] ? 'Comentario...' : 'No habilitado' ?>"><?= e($notas[$t['id_trimestre']]) ?></textarea>
                                    <?php else: ?>
                                        <input type="number"
                                               name="notas[<?= e($student['id_matricula']) ?>][<?= e($t['id_trimestre']) ?>]"
                                               class="form-control form-control-sm text-center nota-input"
                                               style="width: 80px;"
                                               value="<?= e($notas[$t['id_trimestre']]) ?>"
                                               step="0.01" min="0" max="100"
                                               <?= $t['esta_activo'] ? '' : 'readonly disabled' ?>
                                               oninput="highlightLowGrades(this)">
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                            <?php if (!$esInicial): ?>
                                <td class="text-center fw-bold promedio">
                                    <?php
                                    $valores = array_filter(array_map(fn($t) => is_numeric($notas[$t['id_trimestre']]) ? (float) $notas[$t['id_trimestre']] : null, $trimestres), fn($v) => $v !== null);
                                    echo count($valores) > 0 ? number_format(array_sum($valores) / count($valores), 2) : '--';
                                    ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between p-3 border-top">
            <a href="<?= e(base_url('/docente/dashboard')) ?>" class="btn btn-secondary">Volver</a>
            <button type="submit" class="btn btn-primary">Guardar Notas</button>
        </div>
    </form>
</div>

<script>
function highlightLowGrades(input) {
    input.style.color = input.value && parseFloat(input.value) < 51 ? '#dc3545' : '';
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.nota-input').forEach(function(input) {
        if (input.value && parseFloat(input.value) < 51) {
            input.style.color = '#dc3545';
        }
    });
});
</script>
