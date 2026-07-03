<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="main-title mb-1"><?= $editId > 0 ? 'Editar Reporte' : 'Nuevo Reporte' ?></h1>
        <p class="text-muted mb-0">Selecciona filtros y columnas para generar un reporte.</p>
    </div>
    <a href="<?= e(base_url('/reportes')) ?>" class="btn btn-outline-secondary">Volver</a>
</div>

<?= $mensaje ?? '' ?>

<form method="POST" action="" id="reportForm">
    <input type="hidden" name="tipo_base" value="<?= e($tipoBase) ?>">
    <?php if ($editId > 0): ?>
        <input type="hidden" name="id_reporte_editar" value="<?= e($editId) ?>">
    <?php endif; ?>

    <div class="row g-3">
        <!-- FILTERS -->
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">Filtros</div>
                <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
                    <?php foreach ($filterFields as $key => $field): ?>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold"><?= e($field['label']) ?></label>
                            <?php if ($field['type'] === 'multi'): ?>
                                <?php
                                $selected = (is_array($filtros[$key] ?? null)) ? $filtros[$key] : (($filtros[$key] ?? '') !== '' ? [$filtros[$key]] : []);
                                ?>
                                <div class="border rounded p-2" style="max-height: 120px; overflow-y: auto;">
                                    <?php foreach ($field['options'] as $opt): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   name="filtros[<?= e($key) ?>][]"
                                                   value="<?= e((string) $opt) ?>"
                                                   id="f_<?= e($key) ?>_<?= e($opt) ?>"
                                                   <?= in_array((string) $opt, $selected, true) ? 'checked' : '' ?>>
                                            <label class="form-check-label small" for="f_<?= e($key) ?>_<?= e($opt) ?>"><?= e((string) $opt) ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php elseif ($field['type'] === 'select'): ?>
                                <select name="filtros[<?= e($key) ?>]" class="form-select form-select-sm">
                                    <option value="todos">Todos</option>
                                    <?php foreach ($field['options'] as $k => $v): ?>
                                        <?php $val = is_int($k) ? $v : $k; ?>
                                        <option value="<?= e($val) ?>" <?= ((string)($filtros[$key] ?? '') === (string) $val) ? 'selected' : '' ?>><?= e($v) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php elseif ($field['type'] === 'number'): ?>
                                <input type="number" name="filtros[<?= e($key) ?>]" class="form-control form-control-sm"
                                       value="<?= e($filtros[$key] ?? '') ?>" min="0" max="150">
                            <?php elseif ($field['type'] === 'text'): ?>
                                <input type="text" name="filtros[<?= e($key) ?>]" class="form-control form-control-sm"
                                       value="<?= e($filtros[$key] ?? '') ?>">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- COLUMNS + ACTIONS -->
        <div class="col-md-7">
            <div class="card shadow-sm mb-3">
                <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                    <span>Columnas</span>
                    <small class="text-muted">Arrastra para ordenar / click para seleccionar</small>
                </div>
                <div class="card-body">
                    <div class="row g-2" id="columnasContainer">
                        <?php foreach ($columnAliases as $key => $label): ?>
                            <?php $selected = in_array($key, $columnas); ?>
                            <div class="col-6 col-md-4 col-xl-3">
                                <div class="form-check column-item border rounded p-2 <?= $selected ? 'bg-primary text-white' : 'bg-light' ?>"
                                     onclick="toggleColumn(this, '<?= e($key) ?>')">
                                    <input class="form-check-input d-none" type="checkbox"
                                           name="columnas[]" value="<?= e($key) ?>"
                                           id="col_<?= e($key) ?>"
                                           <?= $selected ? 'checked' : '' ?>>
                                    <label class="form-check-label small <?= $selected ? 'text-white' : '' ?>" for="col_<?= e($key) ?>" style="cursor: pointer;">
                                        <?= e($label) ?>
                                    </label>
                                    <input type="hidden" name="columnas_orden[<?= e($key) ?>]"
                                           class="col-order"
                                           value="<?= $selected ? array_search($key, $columnas) + 1 : 999 ?>">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- SAVE SECTION -->
            <div class="card shadow-sm mb-3 border-success">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nombre del Reporte</label>
                            <input type="text" name="nombre_reporte" class="form-control form-control-sm"
                                   value="<?= e($reporte['nombre'] ?? '') ?>" placeholder="Ej: Listado de estudiantes Primaria">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Descripción (opcional)</label>
                            <input type="text" name="descripcion_reporte" class="form-control form-control-sm"
                                   value="<?= e($reporte['descripcion'] ?? '') ?>" placeholder="Breve descripción">
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" name="accion" value="generar" class="btn btn-info text-white btn-sm px-4">
                            <i class="bi bi-play"></i> Generar
                        </button>
                        <button type="submit" name="accion" value="guardar" class="btn btn-success btn-sm px-4">
                            <i class="bi bi-save"></i> <?= $editId > 0 ? 'Actualizar' : 'Guardar' ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- RESULTS -->
            <?php if ($resultados !== null): ?>
                <div class="card shadow-sm">
                    <div class="card-header fw-bold d-flex justify-content-between">
                        <span>Resultados (<?= $resultadosCount ?> registros)</span>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($resultadosCount > 0): ?>
                            <div class="table-responsive" style="max-height: 50vh; overflow: auto;">
                                <table class="table table-striped table-sm mb-0" id="resultsTable">
                                    <thead class="table-dark" style="position: sticky; top: 0; z-index: 5;">
                                        <tr>
                                            <th>#</th>
                                            <?php foreach ($columnas as $col): ?>
                                                <th class="sortable" style="cursor: pointer;"><?= e($columnAliases[$col] ?? $col) ?> <span class="sort-icon ms-1 opacity-50">↕</span></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; foreach ($resultados as $row): ?>
                                            <tr>
                                                <td><?= $i++ ?></td>
                                                <?php foreach ($columnas as $col): ?>
                                                    <?php
                                                    $val = $row[$col] ?? '';
                                                    if ($col === 'fecha_nacimiento' && $val !== '' && $val !== null) {
                                                        $val = date('d/m/Y', strtotime((string) $val));
                                                    }
                                                    ?>
                                                    <td><?= e((string) $val) ?></td>
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
                </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<script>
function toggleColumn(el, key) {
    const cb = el.querySelector('input[type="checkbox"]');
    const order = el.querySelector('.col-order');
    cb.checked = !cb.checked;
    
    if (cb.checked) {
        el.classList.remove('bg-light');
        el.classList.add('bg-primary', 'text-white');
        el.querySelector('label').classList.add('text-white');
        // Assign order based on highest existing
        const orders = [...document.querySelectorAll('.col-order')].map(i => parseInt(i.value)).filter(v => v < 999);
        order.value = orders.length > 0 ? Math.max(...orders) + 1 : 1;
    } else {
        el.classList.add('bg-light');
        el.classList.remove('bg-primary', 'text-white');
        el.querySelector('label').classList.remove('text-white');
        order.value = 999;
    }
}

// Column sorting
document.addEventListener('click', function(e) {
    const th = e.target.closest('.sortable');
    if (!th) return;
    
    const table = th.closest('table');
    if (!table) return;
    
    const tbody = table.querySelector('tbody');
    if (!tbody) return;
    
    const colIdx = [...th.parentElement.children].indexOf(th);
    const isAsc = th.dataset.sort !== 'asc';
    th.dataset.sort = isAsc ? 'asc' : 'desc';
    
    // Update icons
    th.querySelectorAll('.sort-icon').forEach(el => el.remove());
    th.insertAdjacentHTML('beforeend', `<span class="sort-icon ms-1">${isAsc ? '▲' : '▼'}</span>`);
    
    const rows = [...tbody.querySelectorAll('tr')];
    rows.sort((a, b) => {
        const va = (a.children[colIdx]?.textContent || '').trim();
        const vb = (b.children[colIdx]?.textContent || '').trim();
        const na = parseFloat(va), nb = parseFloat(vb);
        if (!isNaN(na) && !isNaN(nb)) return isAsc ? na - nb : nb - na;
        return isAsc ? va.localeCompare(vb) : vb.localeCompare(va);
    });
    rows.forEach(r => tbody.appendChild(r));
});
</script>
