<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="main-title mb-1">Detalle de Estudiante</h1>
        <p class="text-muted mb-0"><?= e(trim($student['apellido_paterno'] . ' ' . $student['apellido_materno'] . ' ' . $student['nombres'])) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= e(base_url('/estudiantes/edit?id=' . $student['id_estudiante'])) ?>" class="btn btn-info text-white">Editar</a>
        <a href="<?= e(base_url('/estudiantes')) ?>" class="btn btn-secondary">Volver</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">Datos del Estudiante</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Nombres</dt><dd class="col-sm-8"><?= e($student['nombres']) ?></dd>
                    <dt class="col-sm-4">Apellidos</dt><dd class="col-sm-8"><?= e(trim($student['apellido_paterno'] . ' ' . $student['apellido_materno'])) ?></dd>
                    <dt class="col-sm-4">RUDE</dt><dd class="col-sm-8"><?= e($student['rude']) ?></dd>
                    <dt class="col-sm-4">CI</dt><dd class="col-sm-8"><?= e($student['ci']) ?></dd>
                    <dt class="col-sm-4">Genero</dt><dd class="col-sm-8"><?= e($student['genero']) ?></dd>
                    <dt class="col-sm-4">Nacimiento</dt><dd class="col-sm-8"><?= e($student['fecha_nacimiento']) ?></dd>
                    <dt class="col-sm-4">Pais</dt><dd class="col-sm-8"><?= e($student['pais']) ?></dd>
                    <dt class="col-sm-4">Provincia/Depto.</dt><dd class="col-sm-8"><?= e($student['provincia_departamento']) ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-success text-white">Responsable Principal</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Nombres</dt><dd class="col-sm-8"><?= e($student['resp_nombres'] ?? '') ?></dd>
                    <dt class="col-sm-4">Apellidos</dt><dd class="col-sm-8"><?= e(trim(($student['resp_apellido_paterno'] ?? '') . ' ' . ($student['resp_apellido_materno'] ?? ''))) ?></dd>
                    <dt class="col-sm-4">CI</dt><dd class="col-sm-8"><?= e($student['resp_ci'] ?? '') ?></dd>
                    <dt class="col-sm-4">Parentesco</dt><dd class="col-sm-8"><?= e($student['resp_parentesco'] ?? '') ?></dd>
                    <dt class="col-sm-4">Celular</dt><dd class="col-sm-8"><?= e($student['resp_celular'] ?? '') ?></dd>
                    <dt class="col-sm-4">Direccion</dt><dd class="col-sm-8"><?= e($student['resp_direccion'] ?? '') ?></dd>
                </dl>
            </div>
        </div>
    </div>
</div>
