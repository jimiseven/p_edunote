<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="main-title mb-0">Panel Principal</h1>
        <small class="text-muted"><?= e($_SESSION['user_name'] ?? '') ?> · <?= e($_SESSION['user_role'] ?? '') ?></small>
    </div>
</div>

<div class="row g-2 mb-4">
    <div class="col-md-3">
        <div class="stat-card border-start border-primary border-4">
            <div class="stat-label">Estudiantes</div>
            <div class="stat-value"><?= e($stats['estudiantes']) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-start border-success border-4">
            <div class="stat-label">Personal</div>
            <div class="stat-value"><?= e($stats['personal']) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-start border-warning border-4">
            <div class="stat-label">Cursos</div>
            <div class="stat-value"><?= e($stats['cursos']) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-start border-info border-4">
            <div class="stat-label">Usuarios activos</div>
            <div class="stat-value"><?= e($stats['usuarios']) ?></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Resumen por nivel -->
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-bold py-2">Estudiantes por Nivel</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr><th>Nivel</th><th>Cursos</th><th>Est.</th><th>H</th><th>M</th></tr>
                    </thead>
                    <tbody>
                        <?php $totalN = ['cursos'=>0,'estudiantes'=>0,'hombres'=>0,'mujeres'=>0]; ?>
                        <?php foreach (['Inicial','Primaria','Secundaria'] as $n): $r = $statsNivel[$n] ?? []; ?>
                            <tr>
                                <td><strong><?= e($n) ?></strong></td>
                                <td><?= (int)($r['cursos'] ?? 0) ?></td>
                                <td><?= (int)($r['estudiantes'] ?? 0) ?></td>
                                <td><?= (int)($r['hombres'] ?? 0) ?></td>
                                <td><?= (int)($r['mujeres'] ?? 0) ?></td>
                            </tr>
                            <?php $totalN['cursos'] += (int)($r['cursos'] ?? 0); $totalN['estudiantes'] += (int)($r['estudiantes'] ?? 0); $totalN['hombres'] += (int)($r['hombres'] ?? 0); $totalN['mujeres'] += (int)($r['mujeres'] ?? 0); ?>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr><td>TOTAL</td><td><?= $totalN['cursos'] ?></td><td><?= $totalN['estudiantes'] ?></td><td><?= $totalN['hombres'] ?></td><td><?= $totalN['mujeres'] ?></td></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Usuarios por rol -->
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-bold py-2">Usuarios por Rol</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light"><tr><th>Rol</th><th>Total</th></tr></thead>
                    <tbody>
                        <?php foreach ($usersByRole as $ur): ?>
                            <tr><td><?= e($ur['nombre']) ?></td><td><?= (int)$ur['total'] ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Acceso rápido -->
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-bold py-2">Acceso Rápido</div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= e(base_url('/dashboard/inicial')) ?>" class="btn btn-sm btn-outline-primary text-start">Inicial</a>
                    <a href="<?= e(base_url('/dashboard/primaria')) ?>" class="btn btn-sm btn-outline-primary text-start">Primaria</a>
                    <a href="<?= e(base_url('/dashboard/secundaria')) ?>" class="btn btn-sm btn-outline-primary text-start">Secundaria</a>
                    <a href="<?= e(base_url('/reportes')) ?>" class="btn btn-sm btn-outline-info text-start">Reportes</a>
                </div>
            </div>
        </div>
    </div>
</div>
