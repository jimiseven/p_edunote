<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="main-title mb-0">📍 Secundaria</h1>
        <small class="text-muted"><?= $totalCursos ?> cursos · <?= $totalEstudiantes ?> estudiantes</small>
    </div>
    <a href="<?= e(base_url('/dashboard')) ?>" class="btn btn-outline-secondary btn-sm">← Panel</a>
</div>

<div class="row g-2 mb-3">
    <div class="col-4"><div class="stat-card text-center p-2"><div class="stat-value fs-4"><?= e($totalCursos) ?></div><div class="stat-label small">Cursos</div></div></div>
    <div class="col-4"><div class="stat-card text-center p-2"><div class="stat-value fs-4"><?= e($totalEstudiantes) ?></div><div class="stat-label small">Estudiantes</div></div></div>
    <div class="col-4"><div class="stat-card text-center p-2"><div class="stat-value fs-4"><?= e($totalHombres) ?>/<?= e($totalMujeres) ?></div><div class="stat-label small">H/M</div></div></div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Curso</th><th class="text-center">Est.</th><th class="text-center">H</th><th class="text-center">M</th><th>Acción</th></tr>
            </thead>
            <tbody>
                <?php if (empty($cursos)): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">No hay cursos de secundaria.</td></tr>
                <?php else: $n = 1; foreach ($cursos as $curso): ?>
                    <tr>
                        <td class="text-muted"><?= $n++ ?></td>
                        <td><strong><?= e($curso['grado'] . '° ' . $curso['paralelo']) ?></strong></td>
                        <td class="text-center"><?= e($curso['total_estudiantes']) ?></td>
                        <td class="text-center"><?= e($curso['hombres']) ?></td>
                        <td class="text-center"><?= e($curso['mujeres']) ?></td>
                        <td>
                            <a href="<?= e(base_url('/ver-curso?curso=' . $curso['id_curso'])) ?>" class="btn btn-primary btn-sm">Ver</a>
                            <a href="<?= e(base_url('/boletin?id_curso=' . $curso['id_curso'])) ?>" class="btn btn-success btn-sm">Boletín</a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
