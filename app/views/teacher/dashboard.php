<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="main-title mb-1">Mis Cursos</h1>
        <p class="text-muted mb-0">Cursos y materias asignadas al docente.</p>
    </div>
    <span class="badge bg-secondary fs-6"><?= e($_SESSION['user_name'] ?? '') ?></span>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nivel</th>
                    <th>Curso</th>
                    <th>Materia</th>
                    <th>Gestion</th>
                    <th>Estudiantes</th>
                    <th>Estado de Carga</th>
                    <th class="text-end">Accion</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($courses)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No tiene cursos asignados actualmente.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?= e($course['nivel']) ?></td>
                        <td><?= e($course['grado'] . '° ' . $course['paralelo']) ?></td>
                        <td>
                            <strong><?= e($course['materia']) ?></strong>
                            <?php if (!empty($course['abreviatura'])): ?>
                                <small class="text-muted">(<?= e($course['abreviatura']) ?>)</small>
                            <?php endif; ?>
                        </td>
                        <td><?= e($course['gestion'] . ' - ' . $course['anio']) ?></td>
                        <td><?= e($course['total_estudiantes']) ?></td>
                        <td>
                            <span class="badge bg-<?= $course['estado_carga'] === 'CARGADO' ? 'success' : 'secondary' ?>">
                                <?= e($course['estado_carga']) ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="<?= e(base_url('/docente/notas?asignacion=' . $course['id_asignacion'])) ?>"
                               class="btn btn-sm btn-primary">Cargar Notas</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
