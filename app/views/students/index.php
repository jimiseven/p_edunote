<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="main-title mb-1">Listado de Estudiantes</h1>
        <p class="text-muted mb-0">Gestion de estudiantes, responsables y matriculas.</p>
    </div>
    <a href="<?= e(base_url('/estudiantes/create')) ?>" class="btn btn-success">Nuevo Estudiante</a>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="<?= e(base_url('/estudiantes')) ?>" class="row g-2">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="Buscar por CI, RUDE, estudiante o responsable" value="<?= e($search) ?>">
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
                    <th>Ap. Paterno</th>
                    <th>Ap. Materno</th>
                    <th>Nombres</th>
                    <th>CI</th>
                    <th>RUDE</th>
                    <th>Curso</th>
                    <th>Responsable</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                    <tr><td colspan="9" class="text-center py-4 text-muted">No hay estudiantes registrados.</td></tr>
                <?php endif; ?>

                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= e($student['apellido_paterno']) ?></td>
                        <td><?= e($student['apellido_materno']) ?></td>
                        <td><?= e($student['nombres']) ?></td>
                        <td><?= e($student['ci']) ?></td>
                        <td><?= e($student['rude']) ?></td>
                        <td>
                            <?php if (!empty($student['nivel'])): ?>
                                <?= e($student['nivel'] . ' ' . $student['grado'] . '° ' . $student['paralelo']) ?>
                            <?php else: ?>
                                <span class="text-muted">Sin matricula</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($student['resp_nombres'])): ?>
                                <strong><?= e(trim($student['resp_nombres'] . ' ' . $student['resp_apellido_paterno'] . ' ' . $student['resp_apellido_materno'])) ?></strong><br>
                                <span class="badge bg-info text-dark"><?= e($student['parentesco']) ?></span>
                                <?php if (!empty($student['resp_celular'])): ?>
                                    <small class="text-muted"> <?= e($student['resp_celular']) ?></small>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">Sin responsable</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge bg-success"><?= e($student['estado']) ?></span></td>
                        <td class="text-end">
                            <a href="<?= e(base_url('/estudiantes/show?id=' . $student['id_estudiante'])) ?>" class="btn btn-sm btn-secondary">Ver</a>
                            <a href="<?= e(base_url('/estudiantes/edit?id=' . $student['id_estudiante'])) ?>" class="btn btn-sm btn-info text-white">Editar</a>
                            <form method="POST" action="<?= e(base_url('/estudiantes/delete')) ?>" class="d-inline" onsubmit="return confirm('¿Retirar este estudiante?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id_estudiante" value="<?= e($student['id_estudiante']) ?>">
                                <button class="btn btn-sm btn-warning">Retirar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
