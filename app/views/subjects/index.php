<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="main-title mb-1">Gestion de Materias</h1>
        <p class="text-muted mb-0">Administracion de materias, submaterias y materias extra.</p>
    </div>
    <a href="<?= e(base_url('/materias/create')) ?>" class="btn btn-success">Nueva Materia</a>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="<?= e(base_url('/materias')) ?>" class="row g-2">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="Buscar por materia, abreviatura o materia padre" value="<?= e($search) ?>">
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary">Buscar</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Materia</th>
                    <th>Abrev.</th>
                    <th>Tipo</th>
                    <th>Materia Padre</th>
                    <th>Extra</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($subjects)): ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">No hay materias registradas.</td></tr>
                <?php endif; ?>

                <?php foreach ($subjects as $subject): ?>
                    <tr>
                        <td>
                            <strong><?= e($subject['nombre']) ?></strong>
                            <?php if (!empty($subject['descripcion'])): ?>
                                <br><small class="text-muted"><?= e($subject['descripcion']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= e($subject['abreviatura'] ?? '') ?></td>
                        <td>
                            <span class="badge bg-<?= (int) $subject['es_submateria'] === 1 ? 'info text-dark' : 'primary' ?>">
                                <?= (int) $subject['es_submateria'] === 1 ? 'Submateria' : 'Materia' ?>
                            </span>
                        </td>
                        <td><?= e($subject['materia_padre'] ?? '-') ?></td>
                        <td><?= (int) $subject['es_extra'] === 1 ? '<span class="badge bg-warning text-dark">Extra</span>' : '<span class="text-muted">No</span>' ?></td>
                        <td>
                            <span class="badge bg-<?= (int) $subject['estado'] === 1 ? 'success' : 'secondary' ?>">
                                <?= (int) $subject['estado'] === 1 ? 'Activa' : 'Inactiva' ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="<?= e(base_url('/materias/edit?id=' . $subject['id_materia'])) ?>" class="btn btn-sm btn-info text-white">Editar</a>
                            <form method="POST" action="<?= e(base_url('/materias/status')) ?>" class="d-inline" onsubmit="return confirm('¿Cambiar estado de la materia?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id_materia" value="<?= e($subject['id_materia']) ?>">
                                <input type="hidden" name="estado" value="<?= (int) $subject['estado'] === 1 ? 0 : 1 ?>">
                                <button class="btn btn-sm btn-<?= (int) $subject['estado'] === 1 ? 'warning' : 'success' ?>">
                                    <?= (int) $subject['estado'] === 1 ? 'Inactivar' : 'Activar' ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
