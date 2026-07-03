<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="main-title mb-1">Materias por Curso</h1>
        <p class="text-muted mb-0">Asignacion de materias a niveles, grados y paralelos.</p>
    </div>
    <a href="<?= e(base_url('/cursos-materias/create')) ?>" class="btn btn-success">Nueva Asignacion</a>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="<?= e(base_url('/cursos-materias')) ?>" class="row g-2">
            <div class="col-md-4">
                <select name="nivel" class="form-select">
                    <option value="">Todos los niveles</option>
                    <?php foreach (['Inicial', 'Primaria', 'Secundaria'] as $nivel): ?>
                        <option value="<?= e($nivel) ?>" <?= ($filters['nivel'] ?? '') === $nivel ? 'selected' : '' ?>><?= e($nivel) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Buscar por materia, abreviatura o paralelo" value="<?= e($filters['search'] ?? '') ?>">
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Curso</th>
                    <th>Materia</th>
                    <th>Tipo</th>
                    <th>Turno</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($assignments)): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">No hay materias asignadas a cursos.</td></tr>
                <?php endif; ?>

                <?php foreach ($assignments as $assignment): ?>
                    <tr>
                        <td><?= e($assignment['nivel'] . ' ' . $assignment['grado'] . '° ' . $assignment['paralelo']) ?></td>
                        <td>
                            <strong><?= e($assignment['materia']) ?></strong>
                            <?php if (!empty($assignment['abreviatura'])): ?>
                                <small class="text-muted">(<?= e($assignment['abreviatura']) ?>)</small>
                            <?php endif; ?>
                            <?php if (!empty($assignment['materia_padre'])): ?>
                                <br><small class="text-muted">Padre: <?= e($assignment['materia_padre']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= (int) $assignment['es_submateria'] === 1 ? 'info text-dark' : 'primary' ?>">
                                <?= (int) $assignment['es_submateria'] === 1 ? 'Submateria' : 'Materia' ?>
                            </span>
                            <?php if ((int) $assignment['es_extra'] === 1): ?>
                                <span class="badge bg-warning text-dark">Extra</span>
                            <?php endif; ?>
                        </td>
                        <td><?= e($assignment['turno'] ?? '-') ?></td>
                        <td>
                            <span class="badge bg-<?= (int) $assignment['estado'] === 1 ? 'success' : 'secondary' ?>">
                                <?= (int) $assignment['estado'] === 1 ? 'Activa' : 'Inactiva' ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="<?= e(base_url('/cursos-materias/edit?id=' . $assignment['id_curso_materia'])) ?>" class="btn btn-sm btn-info text-white">Editar</a>
                            <form method="POST" action="<?= e(base_url('/cursos-materias/status')) ?>" class="d-inline" onsubmit="return confirm('¿Cambiar estado de la asignacion?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id_curso_materia" value="<?= e($assignment['id_curso_materia']) ?>">
                                <input type="hidden" name="estado" value="<?= (int) $assignment['estado'] === 1 ? 0 : 1 ?>">
                                <button class="btn btn-sm btn-<?= (int) $assignment['estado'] === 1 ? 'warning' : 'success' ?>">
                                    <?= (int) $assignment['estado'] === 1 ? 'Inactivar' : 'Activar' ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
