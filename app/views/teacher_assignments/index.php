<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="main-title mb-1">Asignacion de Docentes</h1>
        <p class="text-muted mb-0">Relaciona docentes con cursos, materias y gestion escolar.</p>
    </div>
    <a href="<?= e(base_url('/docentes-asignaciones/create')) ?>" class="btn btn-success">Nueva Asignacion</a>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="<?= e(base_url('/docentes-asignaciones')) ?>" class="row g-2">
            <div class="col-md-4">
                <select name="nivel" class="form-select">
                    <option value="">Todos los niveles</option>
                    <?php foreach (['Inicial', 'Primaria', 'Secundaria'] as $nivel): ?>
                        <option value="<?= e($nivel) ?>" <?= ($filters['nivel'] ?? '') === $nivel ? 'selected' : '' ?>><?= e($nivel) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Buscar docente, CI o materia" value="<?= e($filters['search'] ?? '') ?>">
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
                    <th>Docente</th>
                    <th>Curso</th>
                    <th>Materia</th>
                    <th>Gestion</th>
                    <th>Carga</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($assignments)): ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">No hay docentes asignados.</td></tr>
                <?php endif; ?>

                <?php foreach ($assignments as $assignment): ?>
                    <tr>
                        <td><strong><?= e(trim($assignment['apellidos'] . ', ' . $assignment['nombres'])) ?></strong><br><small class="text-muted">CI: <?= e($assignment['ci']) ?></small></td>
                        <td><?= e($assignment['nivel'] . ' ' . $assignment['grado'] . '° ' . $assignment['paralelo']) ?></td>
                        <td><?= e($assignment['materia']) ?><?= !empty($assignment['abreviatura']) ? ' (' . e($assignment['abreviatura']) . ')' : '' ?></td>
                        <td><?= e($assignment['gestion'] . ' - ' . $assignment['anio']) ?></td>
                        <td><span class="badge bg-<?= $assignment['estado_carga'] === 'CARGADO' ? 'success' : 'secondary' ?>"><?= e($assignment['estado_carga']) ?></span></td>
                        <td><span class="badge bg-<?= $assignment['estado'] === 'activo' ? 'success' : 'secondary' ?>"><?= e($assignment['estado']) ?></span></td>
                        <td class="text-end">
                            <a href="<?= e(base_url('/docentes-asignaciones/edit?id=' . $assignment['id_asignacion'])) ?>" class="btn btn-sm btn-info text-white">Editar</a>
                            <form method="POST" action="<?= e(base_url('/docentes-asignaciones/status')) ?>" class="d-inline" onsubmit="return confirm('¿Cambiar estado de asignacion?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id_asignacion" value="<?= e($assignment['id_asignacion']) ?>">
                                <input type="hidden" name="estado" value="<?= $assignment['estado'] === 'activo' ? 'inactivo' : 'activo' ?>">
                                <button class="btn btn-sm btn-<?= $assignment['estado'] === 'activo' ? 'warning' : 'success' ?>">
                                    <?= $assignment['estado'] === 'activo' ? 'Inactivar' : 'Activar' ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
