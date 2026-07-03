<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="main-title mb-1">Lista de Reportes</h1>
        <p class="text-muted mb-0">Reportes guardados en el sistema.</p>
    </div>
    <a href="<?= e(base_url('/reportes/constructor?tipo=info_estudiantil')) ?>" class="btn btn-success">
        + Reporte Nuevo
    </a>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<div class="card shadow-sm">
    <?php if (empty($reportes)): ?>
        <div class="card-body text-center py-5">
            <p class="text-muted mb-0">No hay reportes guardados. Crea tu primer reporte usando el botón "Reporte Nuevo".</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Creado por</th>
                        <th>Fecha</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $n = 1; foreach ($reportes as $r): ?>
                        <tr>
                            <td><?= $n++ ?></td>
                            <td><strong><?= e($r['nombre']) ?></strong></td>
                            <td><span class="badge bg-info"><?= e($r['tipo_base']) ?></span></td>
                            <td><?= e($r['creador'] ?? '-') ?></td>
                            <td><?= e(date('d/m/Y H:i', strtotime($r['created_at']))) ?></td>
                            <td class="text-end">
                                <a href="<?= e(base_url('/reportes/ver?id=' . $r['id_reporte'])) ?>" class="btn btn-sm btn-info text-white">Ver</a>
                                <a href="<?= e(base_url('/reportes/constructor?editar=' . $r['id_reporte'])) ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="<?= e(base_url('/reportes/eliminar?id=' . $r['id_reporte'])) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este reporte?')">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
