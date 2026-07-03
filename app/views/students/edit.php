<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="main-title mb-1">Editar Estudiante</h1>
        <p class="text-muted mb-0">Actualizacion de datos principales, responsable y curso actual.</p>
    </div>
    <a href="<?= e(base_url('/estudiantes')) ?>" class="btn btn-secondary">Volver</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<form method="POST" action="<?= e(base_url('/estudiantes/update')) ?>" class="card shadow-sm">
    <?= csrf_field() ?>
    <input type="hidden" name="id_estudiante" value="<?= e($student['id_estudiante']) ?>">
    <div class="card-body">
        <div class="step-container mb-4">
            <h5 class="text-primary mb-3">Datos del Estudiante</h5>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">Nombres *</label><input type="text" name="nombres" class="form-control" value="<?= e($student['nombres']) ?>" required></div>
                <div class="col-md-4"><label class="form-label">Ap. Paterno *</label><input type="text" name="apellido_paterno" class="form-control" value="<?= e($student['apellido_paterno']) ?>" required></div>
                <div class="col-md-4"><label class="form-label">Ap. Materno</label><input type="text" name="apellido_materno" class="form-control" value="<?= e($student['apellido_materno']) ?>"></div>
                <div class="col-md-3"><label class="form-label">RUDE *</label><input type="text" name="rude" class="form-control" value="<?= e($student['rude']) ?>" required></div>
                <div class="col-md-3"><label class="form-label">CI *</label><input type="text" name="ci" class="form-control" value="<?= e($student['ci']) ?>" required></div>
                <div class="col-md-3"><label class="form-label">F. Nacimiento *</label><input type="date" name="fecha_nacimiento" class="form-control" value="<?= e($student['fecha_nacimiento']) ?>" required></div>
                <div class="col-md-3"><label class="form-label">Genero *</label><select name="genero" class="form-select" required><option value="">Seleccionar</option><option value="Masculino" <?= $student['genero'] === 'Masculino' ? 'selected' : '' ?>>Masculino</option><option value="Femenino" <?= $student['genero'] === 'Femenino' ? 'selected' : '' ?>>Femenino</option></select></div>
                <div class="col-md-4"><label class="form-label">Pais</label><input type="text" name="pais" class="form-control" value="<?= e($student['pais']) ?>"></div>
                <div class="col-md-4"><label class="form-label">Provincia/Departamento</label><input type="text" name="provincia_departamento" class="form-control" value="<?= e($student['provincia_departamento']) ?>"></div>
                <div class="col-md-4"><label class="form-label">Curso *</label><select name="id_curso" class="form-select" required><option value="">Seleccionar</option><?php foreach ($courses as $course): ?><option value="<?= e($course['id_curso']) ?>" <?= (int)($student['id_curso'] ?? 0) === (int)$course['id_curso'] ? 'selected' : '' ?>><?= e($course['nombre']) ?></option><?php endforeach; ?></select></div>
            </div>
        </div>

        <div class="step-container border-success">
            <h5 class="text-success mb-3">Responsable Principal</h5>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">Nombres *</label><input type="text" name="resp_nombres" class="form-control" value="<?= e($student['resp_nombres'] ?? '') ?>" required></div>
                <div class="col-md-4"><label class="form-label">Ap. Paterno *</label><input type="text" name="resp_apellido_paterno" class="form-control" value="<?= e($student['resp_apellido_paterno'] ?? '') ?>" required></div>
                <div class="col-md-4"><label class="form-label">Ap. Materno</label><input type="text" name="resp_apellido_materno" class="form-control" value="<?= e($student['resp_apellido_materno'] ?? '') ?>"></div>
                <div class="col-md-3"><label class="form-label">CI *</label><input type="text" name="resp_ci" class="form-control" value="<?= e($student['resp_ci'] ?? '') ?>" required></div>
                <div class="col-md-3"><label class="form-label">F. Nacimiento</label><input type="date" name="resp_fecha_nacimiento" class="form-control" value="<?= e($student['resp_fecha_nacimiento'] ?? '') ?>"></div>
                <div class="col-md-3"><label class="form-label">Parentesco *</label><select name="resp_parentesco" class="form-select" required><?php foreach (['Padre','Madre','Tutor','Otro'] as $p): ?><option value="<?= e($p) ?>" <?= ($student['resp_parentesco'] ?? '') === $p ? 'selected' : '' ?>><?= e($p) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-3"><label class="form-label">Celular</label><input type="text" name="resp_celular" class="form-control" value="<?= e($student['resp_celular'] ?? '') ?>"></div>
                <div class="col-md-4"><label class="form-label">Grado de Instruccion</label><input type="text" name="resp_grado_instruccion" class="form-control" value="<?= e($student['resp_grado_instruccion'] ?? '') ?>"></div>
                <div class="col-md-4"><label class="form-label">Idioma Frecuente</label><input type="text" name="resp_idioma_frecuente" class="form-control" value="<?= e($student['resp_idioma_frecuente'] ?? '') ?>"></div>
                <div class="col-md-4"><label class="form-label">Ocupacion</label><input type="text" name="resp_ocupacion" class="form-control" value="<?= e($student['resp_ocupacion'] ?? '') ?>"></div>
                <div class="col-md-8"><label class="form-label">Direccion</label><input type="text" name="resp_direccion" class="form-control" value="<?= e($student['resp_direccion'] ?? '') ?>"></div>
                <div class="col-md-4 d-flex align-items-end gap-3"><label><input type="checkbox" name="resp_vive_con_estudiante" <?= !empty($student['resp_vive_con_estudiante']) ? 'checked' : '' ?>> Vive con estudiante</label><label><input type="checkbox" name="resp_autorizado_recoger" <?= !empty($student['resp_autorizado_recoger']) ? 'checked' : '' ?>> Puede recoger</label></div>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
        <a href="<?= e(base_url('/estudiantes')) ?>" class="btn btn-secondary">Cancelar</a>
        <button class="btn btn-primary">Guardar Cambios</button>
    </div>
</form>
