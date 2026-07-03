<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="main-title mb-1">Panel Principal</h1>
        <p class="text-muted mb-0">Rol activo: <?= e($_SESSION['user_role'] ?? '') ?></p>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Estudiantes</div>
            <div class="stat-value"><?= e($stats['estudiantes']) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Personal</div>
            <div class="stat-value"><?= e($stats['personal']) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Cursos</div>
            <div class="stat-value"><?= e($stats['cursos']) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Usuarios</div>
            <div class="stat-value"><?= e($stats['usuarios']) ?></div>
        </div>
    </div>
</div>

<div class="card mt-4 shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Sistema base listo</h5>
        <p class="card-text mb-0">La aplicacion MVC ya tiene conexion, autenticacion inicial, sesion protegida y dashboard base.</p>
    </div>
</div>
