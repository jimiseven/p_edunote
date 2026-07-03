<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="main-title mb-1">Cursos de Nivel Secundaria</h1>
        <p class="text-muted mb-0">Seleccione el curso que desea visualizar.</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="stat-value"><?= e($totalCursos) ?></div>
            <div class="stat-label">Total cursos</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="stat-value"><?= e($totalEstudiantes) ?></div>
            <div class="stat-label">Total estudiantes</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between mb-2">
                <span class="stat-label">Hombres</span>
                <span class="fw-bold"><?= e($totalHombres) ?></span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="stat-label">Mujeres</span>
                <span class="fw-bold"><?= e($totalMujeres) ?></span>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 60px;">#</th>
                    <th>Curso</th>
                    <th style="width: 80px;">Total</th>
                    <th style="width: 80px;">Hombres</th>
                    <th style="width: 80px;">Mujeres</th>
                    <th style="width: 220px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cursos)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No hay cursos de secundaria registrados.</td>
                    </tr>
                <?php else: ?>
                    <?php $n = 1; foreach ($cursos as $curso): ?>
                        <tr>
                            <td><?= $n++ ?></td>
                            <td><strong><?= e($curso['grado'] . '° ' . $curso['paralelo']) ?></strong></td>
                            <td class="text-center"><?= e($curso['total_estudiantes']) ?></td>
                            <td class="text-center"><?= e($curso['hombres']) ?></td>
                            <td class="text-center"><?= e($curso['mujeres']) ?></td>
                            <td>
                                <a href="<?= e(base_url('/ver-curso?curso=' . $curso['id_curso'])) ?>" class="btn btn-primary btn-sm">VER</a>
                                <a href="<?= e(base_url('/boletin?id_curso=' . $curso['id_curso'])) ?>" class="btn btn-success btn-sm">Boletín</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
