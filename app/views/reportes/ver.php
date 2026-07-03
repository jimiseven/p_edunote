<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="main-title mb-1"><?= e($reporte['nombre']) ?></h1>
        <p class="text-muted mb-0">Tipo: <?= e($reporte['tipo_base']) ?> &mdash; Creado: <?= e(date('d/m/Y H:i', strtotime($reporte['created_at'] ?? 'now'))) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= e(base_url('/reportes/constructor?editar=' . $reporte['id_reporte'])) ?>" class="btn btn-warning btn-sm">Editar</a>
        <a href="<?= e(base_url('/reportes')) ?>" class="btn btn-outline-secondary btn-sm">Volver</a>
    </div>
</div>

<?php if (empty($resultados)): ?>
    <div class="alert alert-info">No se encontraron resultados para este reporte.</div>
<?php else: ?>
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between">
            <span><strong><?= count($resultados) ?></strong> registros</span>
        </div>
        <div class="table-responsive" style="max-height: calc(100vh - 200px); overflow: auto;">
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
                                $val = $row[$col] ?? $row['nombre_completo'] ?? '';
                                if ($col === 'fecha_nacimiento' && $val !== '' && $val !== null) {
                                    $val = date('d/m/Y', strtotime((string) $val));
                                }
                                if ($col === 'nombre_completo') $val = $row['nombre_completo'] ?? '';
                                ?>
                                <td><?= e((string) $val) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<script>
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
