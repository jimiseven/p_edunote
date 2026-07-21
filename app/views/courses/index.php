<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="main-title mb-1">Gestion de Cursos</h1>
        <p class="text-muted mb-0">Administracion de niveles, grados, paralelos y turnos.</p>
    </div>
    <a href="<?= e(base_url('/cursos/create')) ?>" class="btn btn-success">
        <i class="feather-plus me-1"></i>Nuevo Curso
    </a>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= e($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= e($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card shadow-sm mb-3" style="border: 0; border-radius: 12px;">
    <div class="card-body">
        <form method="GET" action="<?= e(base_url('/cursos')) ?>" class="row g-2">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="Buscar por nivel, grado, paralelo o turno" value="<?= e($search) ?>">
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary">Buscar</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm" style="border: 0; border-radius: 12px; overflow: hidden;">
    <div class="table-scrollable-wrapper">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Nivel</th>
                    <th>Grado</th>
                    <th>Paralelo</th>
                    <th>Turno</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($courses)): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">No hay cursos registrados.</td></tr>
                <?php endif; ?>

                <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?= e($course['nivel']) ?></td>
                        <td><?= e($course['grado']) ?>°</td>
                        <td><?= e($course['paralelo']) ?></td>
                        <td><?= e($course['turno'] ?? 'Sin turno') ?></td>
                        <td>
                            <span class="badge bg-<?= (int) $course['estado'] === 1 ? 'success' : 'secondary' ?>">
                                <?= (int) $course['estado'] === 1 ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="<?= e(base_url('/cursos/edit?id=' . $course['id_curso'])) ?>" class="btn btn-sm btn-info text-white">Editar</a>
                            <form method="POST" action="<?= e(base_url('/cursos/status')) ?>" class="d-inline" onsubmit="return confirm('¿Cambiar estado del curso?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id_curso" value="<?= e($course['id_curso']) ?>">
                                <input type="hidden" name="estado" value="<?= (int) $course['estado'] === 1 ? 0 : 1 ?>">
                                <button class="btn btn-sm btn-<?= (int) $course['estado'] === 1 ? 'warning' : 'success' ?>">
                                    <?= (int) $course['estado'] === 1 ? 'Inactivar' : 'Activar' ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
