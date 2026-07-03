<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="main-title mb-1">Control de Trimestres</h1>
        <p class="text-muted mb-0">Habilita el trimestre vigente para la carga de notas y ajusta sus fechas.</p>
    </div>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="<?= e(base_url('/trimestres')) ?>" class="row g-2 align-items-end">
            <div class="col-md-10">
                <label class="form-label">Gestion</label>
                <select name="gestion" class="form-select">
                    <?php foreach ($gestiones as $gestion): ?>
                        <option value="<?= e($gestion['id_gestion']) ?>" <?= (int) $gestion['id_gestion'] === (int) $idGestion ? 'selected' : '' ?>>
                            <?= e($gestion['nombre']) ?> - <?= e($gestion['anio']) ?> (<?= e($gestion['estado']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary">Ver</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <?php if (empty($trimestres)): ?>
            <div class="text-center py-4 text-muted">No hay trimestres configurados para esta gestion.</div>
        <?php else: ?>
            <form method="POST" action="<?= e(base_url('/trimestres/update')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="id_gestion" value="<?= e($idGestion) ?>">

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Trimestre</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($trimestres as $trimestre): ?>
                                <tr>
                                    <td>
                                        <strong><?= e($trimestre['nombre']) ?></strong>
                                        <br><small class="text-muted">Numero <?= e($trimestre['numero']) ?></small>
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" name="trimestres[<?= e($trimestre['id_trimestre']) ?>][fecha_inicio]" value="<?= e($trimestre['fecha_inicio'] ?? '') ?>">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" name="trimestres[<?= e($trimestre['id_trimestre']) ?>][fecha_fin]" value="<?= e($trimestre['fecha_fin'] ?? '') ?>">
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= (int) $trimestre['esta_activo'] === 1 ? 'success' : 'secondary' ?>">
                                            <?= (int) $trimestre['esta_activo'] === 1 ? 'Activo para carga' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <?php if ((int) $trimestre['esta_activo'] !== 1): ?>
                                            <button type="submit" form="activate-trimestre-<?= e($trimestre['id_trimestre']) ?>" class="btn btn-sm btn-success" onclick="return confirm('¿Activar este trimestre para carga de notas?')">
                                                Activar
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted">Vigente</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-primary">Guardar Fechas</button>
                </div>
            </form>

            <?php foreach ($trimestres as $trimestre): ?>
                <form id="activate-trimestre-<?= e($trimestre['id_trimestre']) ?>" method="POST" action="<?= e(base_url('/trimestres/activate')) ?>" class="d-none">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_trimestre" value="<?= e($trimestre['id_trimestre']) ?>">
                </form>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
