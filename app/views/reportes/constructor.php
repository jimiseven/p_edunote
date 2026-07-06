<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="main-title mb-1"><?= $editId > 0 ? 'Editar Reporte' : 'Nuevo Reporte' ?></h1>
        <p class="text-muted mb-0">Filtra datos, elige columnas y genera reportes académicos dinámicos.</p>
    </div>
    <a href="<?= e(base_url('/reportes')) ?>" class="btn btn-outline-secondary">← Volver</a>
</div>

<?= $mensaje ?? '' ?>

<form method="POST" action="" id="reportForm">
    <input type="hidden" name="tipo_base" value="<?= e($tipoBase) ?>">
    <?php if ($editId > 0): ?>
        <input type="hidden" name="id_reporte_editar" value="<?= e($editId) ?>">
    <?php endif; ?>

    <!-- TOP BAR: Generate + Save -->
    <div class="card shadow-sm mb-3 border-success">
        <div class="card-body py-2 d-flex flex-wrap align-items-center gap-2">
            <div class="flex-grow-1 d-flex flex-wrap gap-2 align-items-center">
                <input type="text" name="nombre_reporte" class="form-control form-control-sm" style="width:200px"
                       value="<?= e($reporte['nombre'] ?? '') ?>" placeholder="Nombre del reporte">
                <input type="text" name="descripcion_reporte" class="form-control form-control-sm" style="width:250px"
                       value="<?= e($reporte['descripcion'] ?? '') ?>" placeholder="Descripción (opcional)">
            </div>
            <div class="d-flex gap-1">
                <button type="submit" name="accion" value="generar" class="btn btn-info text-white btn-sm px-3">
                    ▶ Generar
                </button>
                <button type="submit" name="accion" value="guardar" class="btn btn-success btn-sm px-3">
                    💾 <?= $editId > 0 ? 'Actualizar' : 'Guardar' ?>
                </button>
            </div>
        </div>
    </div>

    <!-- MAIN GRID -->
    <div class="row g-3">
        <!-- LEFT: FILTERS -->
        <div class="col-lg-4">
            <div class="accordion" id="filtersAccordion">
                <!-- Basic Filters -->
                <div class="accordion-item border-0 mb-2 shadow-sm">
                    <h2 class="accordion-header">
                        <button class="accordion-button fw-bold py-2" type="button" data-bs-toggle="collapse" data-bs-target="#basicFilters">
                            📋 Datos del Estudiante
                        </button>
                    </h2>
                    <div id="basicFilters" class="accordion-collapse collapse show">
                        <div class="accordion-body p-2 bg-light" style="max-height:350px; overflow-y:auto;">
                            <div class="row g-1">
                                <?php $basicFilters = ['nivel','grado','paralelo','genero','edad_min','edad_max','pais','con_ci','con_rude','tiene_dificultad']; ?>
                                <?php foreach ($basicFilters as $key): ?>
                                    <?php $field = $filterFields[$key] ?? null; if (!$field) continue; ?>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold mb-0"><?= e($field['label']) ?></label>
                                        <?php if ($field['type'] === 'multi'): ?>
                                            <?php $selected = (is_array($filtros[$key] ?? null)) ? $filtros[$key] : (($filtros[$key] ?? '') !== '' ? [$filtros[$key]] : []); ?>
                                            <div class="d-flex flex-wrap gap-1 border rounded p-1 bg-white" style="max-height:80px; overflow-y:auto;">
                                                <?php foreach ($field['options'] as $opt): ?>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox" name="filtros[<?= e($key) ?>][]" value="<?= e((string)$opt) ?>" id="f_<?= e($key) ?>_<?= e($opt) ?>" <?= in_array((string)$opt, $selected, true) ? 'checked' : '' ?>>
                                                        <label class="form-check-label small" for="f_<?= e($key) ?>_<?= e($opt) ?>"><?= e((string)$opt) ?></label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php elseif ($field['type'] === 'select'): ?>
                                            <select name="filtros[<?= e($key) ?>]" class="form-select form-select-sm">
                                                <option value="todos">Todos</option>
                                                <?php foreach ($field['options'] as $k => $v): $val = is_int($k) ? $v : $k; ?>
                                                    <option value="<?= e($val) ?>" <?= ((string)($filtros[$key] ?? '') === (string)$val) ? 'selected' : '' ?>><?= e($v) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php elseif ($field['type'] === 'number'): ?>
                                            <input type="number" name="filtros[<?= e($key) ?>]" class="form-control form-control-sm" value="<?= e($filtros[$key] ?? '') ?>" min="0" max="150">
                                        <?php elseif ($field['type'] === 'text'): ?>
                                            <input type="text" name="filtros[<?= e($key) ?>]" class="form-control form-control-sm" value="<?= e($filtros[$key] ?? '') ?>">
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Filters -->
                <div class="accordion-item border-0 mb-2 shadow-sm">
                    <h2 class="accordion-header">
                        <button class="accordion-button fw-bold py-2" type="button" data-bs-toggle="collapse" data-bs-target="#acadFilters">
                            🎓 Rendimiento Académico
                        </button>
                    </h2>
                    <div id="acadFilters" class="accordion-collapse collapse">
                        <div class="accordion-body p-2 bg-light">
                            <div class="row g-1">
                                <?php $acadFilters = ['trimestre','nota_minima','estado_academico','materia_especifica','filtro_materias']; ?>
                                <?php foreach ($acadFilters as $key): ?>
                                    <?php $field = $filterFields[$key] ?? null; if (!$field) continue; ?>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold mb-0"><?= e($field['label']) ?></label>
                                        <?php if ($field['type'] === 'select'): ?>
                                            <select name="filtros[<?= e($key) ?>]" class="form-select form-select-sm">
                                                <option value="todos">Todos</option>
                                                <?php foreach ($field['options'] as $k => $v): $val = is_int($k) ? $v : $k; ?>
                                                    <option value="<?= e($val) ?>" <?= ((string)($filtros[$key] ?? '') === (string)$val) ? 'selected' : '' ?>><?= e($v) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php elseif ($field['type'] === 'number'): ?>
                                            <input type="number" name="filtros[<?= e($key) ?>]" class="form-control form-control-sm" value="<?= e($filtros[$key] ?? ($field['default'] ?? 51)) ?>" min="0" max="100">
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: COLUMNS + RESULTS -->
        <div class="col-lg-8">
            <!-- Columnas del Estudiante -->
            <div class="card shadow-sm mb-2">
                <div class="card-header fw-bold py-1 d-flex justify-content-between align-items-center bg-primary text-white bg-opacity-75">
                    <span>📌 Datos Personales</span>
                    <small class="text-white-50">Click para seleccionar</small>
                </div>
                <div class="card-body py-1 px-2" style="max-height:140px; overflow-y:auto;">
                    <div class="row g-1">
                        <?php $basicCols = ['id_estudiante','rude','nombres','apellido_paterno','apellido_materno','nombre_completo','genero','ci','fecha_nacimiento','edad','pais','provincia_departamento','nivel','grado','paralelo','tiene_dificultad','tipo_dificultad']; ?>
                        <?php foreach ($basicCols as $ck): ?>
                            <?php $label = $columnAliases[$ck] ?? null; if (!$label) continue; $selected = in_array($ck, $columnas); ?>
                            <div class="col-4 col-md-3 col-xl-2">
                                <div class="form-check column-item border rounded p-1 m-0 <?= $selected ? 'bg-primary text-white' : 'bg-white' ?>"
                                     onclick="toggleColumn(this, '<?= e($ck) ?>')" style="cursor:pointer;">
                                    <input class="form-check-input d-none" type="checkbox" name="columnas[]" value="<?= e($ck) ?>" id="col_<?= e($ck) ?>" <?= $selected ? 'checked' : '' ?>>
                                    <label class="form-check-label small mb-0 <?= $selected ? 'text-white' : '' ?>" for="col_<?= e($ck) ?>" style="cursor:pointer; font-size:0.72rem;"><?= e($label) ?></label>
                                    <input type="hidden" name="columnas_orden[<?= e($ck) ?>]" class="col-order" value="<?= $selected ? array_search($ck, $columnas) + 1 : 999 ?>">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Columnas Académicas -->
            <div class="card shadow-sm mb-2">
                <div class="card-header fw-bold py-1 d-flex justify-content-between align-items-center bg-info text-white">
                    <span>📊 Rendimiento</span>
                    <small class="text-white-50">Promedios, materias, detalle</small>
                </div>
                <div class="card-body py-1 px-2">
                    <div class="row g-1">
                        <?php $acadCols = ['promedio_t1','promedio_t2','promedio_t3','promedio_general','materias_aprobadas','materias_reprobadas','total_materias','detalle_materias','estado_final']; ?>
                        <?php foreach ($acadCols as $ck): ?>
                            <?php $label = $columnAliases[$ck] ?? null; if (!$label) continue; $selected = in_array($ck, $columnas); ?>
                            <div class="col-4 col-md-3 col-xl-2">
                                <div class="form-check column-item border rounded p-1 m-0 <?= $selected ? 'bg-info text-white' : 'bg-white' ?>"
                                     onclick="toggleColumn(this, '<?= e($ck) ?>')" style="cursor:pointer;">
                                    <input class="form-check-input d-none" type="checkbox" name="columnas[]" value="<?= e($ck) ?>" id="col_<?= e($ck) ?>" <?= $selected ? 'checked' : '' ?>>
                                    <label class="form-check-label small mb-0 <?= $selected ? 'text-white' : '' ?>" for="col_<?= e($ck) ?>" style="cursor:pointer; font-size:0.72rem;"><?= e($label) ?></label>
                                    <input type="hidden" name="columnas_orden[<?= e($ck) ?>]" class="col-order" value="<?= $selected ? array_search($ck, $columnas) + 1 : 999 ?>">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- RESULTS -->
            <?php if ($resultados !== null): ?>
                <div class="card shadow-sm">
                    <div class="card-header fw-bold d-flex justify-content-between align-items-center py-1">
                        <span>📄 Resultados (<?= $resultadosCount ?> registros)</span>
                        <small class="text-muted">Click en columna para ordenar</small>
                    </div>
                    <?php if ($resultadosCount > 0): ?>
                        <div class="table-responsive" style="max-height:55vh; overflow:auto;">
                            <table class="table table-striped table-sm mb-0" id="resultsTable">
                                <thead class="table-dark" style="position:sticky; top:0; z-index:5;">
                                    <tr>
                                        <th style="width:32px">#</th>
                                        <?php foreach ($columnas as $col): ?>
                                            <th class="sortable" style="cursor:pointer; white-space:nowrap;"><?= e($columnAliases[$col] ?? $col) ?> <span class="sort-icon ms-1 opacity-50">↕</span></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; foreach ($resultados as $row): ?>
                                        <tr>
                                            <td class="text-muted"><?= $i++ ?></td>
                                            <?php foreach ($columnas as $col): ?>
                                                <?php
                                                $val = $row[$col] ?? '';
                                                if ($col === 'fecha_nacimiento' && $val !== '' && $val !== null) {
                                                    $val = date('d/m/Y', strtotime((string)$val));
                                                }
                                                ?>
                                                <td style="font-size:0.82rem;"><?= e((string)$val) ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="p-3 text-muted">No se encontraron resultados con los filtros seleccionados.</div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<style>
.column-item { transition: all 0.12s; user-select: none; }
.column-item:hover { box-shadow: 0 1px 4px rgba(0,0,0,0.15); }
.accordion-button:not(.collapsed) { background: #e9ecef; color: #1f2937; }
.accordion-button:focus { box-shadow: none; }
#resultsTable td { max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
</style>

<script>
function toggleColumn(el, key) {
    const cb = el.querySelector('input[type="checkbox"]');
    const order = el.querySelector('.col-order');
    cb.checked = !cb.checked;
    if (cb.checked) {
        el.classList.remove('bg-white');
        if (el.closest('.card-header.bg-info')?.closest('.card')) {
            el.classList.add('bg-info', 'text-white');
        } else {
            el.classList.add('bg-primary', 'text-white');
        }
        el.querySelector('label').classList.add('text-white');
        const orders = [...document.querySelectorAll('.col-order')].map(i => parseInt(i.value)).filter(v => v < 999);
        order.value = orders.length > 0 ? Math.max(...orders) + 1 : 1;
    } else {
        el.classList.add('bg-white');
        el.classList.remove('bg-primary', 'bg-info', 'text-white');
        el.querySelector('label').classList.remove('text-white');
        order.value = 999;
    }
}
</script>
