<?php
$isEdit = !empty($assignment);
$action = $isEdit ? base_url('/docentes-asignaciones/update') : base_url('/docentes-asignaciones/store');
?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<?php if (empty($teachers)): ?>
    <div class="alert alert-warning">No hay docentes activos. Cree un usuario con rol Docente antes de asignar.</div>
<?php endif; ?>

<?php if (empty($courseSubjects)): ?>
    <div class="alert alert-warning">No hay materias asignadas a cursos. Complete primero el modulo Materias por Curso.</div>
<?php endif; ?>

<form method="POST" action="<?= e($action) ?>" class="card shadow-sm">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?>
        <input type="hidden" name="id_asignacion" value="<?= e($assignment['id_asignacion']) ?>">
    <?php endif; ?>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Docente *</label>
                <select name="id_personal" class="form-select" required>
                    <option value="">Seleccionar docente</option>
                    <?php foreach ($teachers as $teacher): ?>
                        <option value="<?= e($teacher['id_personal']) ?>" <?= (int)($assignment['id_personal'] ?? 0) === (int)$teacher['id_personal'] ? 'selected' : '' ?>>
                            <?= e($teacher['apellidos'] . ', ' . $teacher['nombres'] . ' - CI ' . $teacher['ci']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Curso - Materia *</label>
                <select name="id_curso_materia" class="form-select" required>
                    <option value="">Seleccionar curso y materia</option>
                    <?php foreach ($courseSubjects as $courseSubject): ?>
                        <option value="<?= e($courseSubject['id_curso_materia']) ?>" <?= (int)($assignment['id_curso_materia'] ?? 0) === (int)$courseSubject['id_curso_materia'] ? 'selected' : '' ?>>
                            <?= e($courseSubject['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Gestion *</label>
                <select name="id_gestion" class="form-select" required>
                    <option value="">Seleccionar gestion</option>
                    <?php foreach ($gestiones as $gestion): ?>
                        <option value="<?= e($gestion['id_gestion']) ?>" <?= (int)($assignment['id_gestion'] ?? 0) === (int)$gestion['id_gestion'] ? 'selected' : '' ?>>
                            <?= e($gestion['nombre'] . ' - ' . $gestion['anio']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="activo" <?= ($assignment['estado'] ?? 'activo') === 'activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="inactivo" <?= ($assignment['estado'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Estado de carga</label>
                <select name="estado_carga" class="form-select">
                    <option value="FALTA" <?= ($assignment['estado_carga'] ?? 'FALTA') === 'FALTA' ? 'selected' : '' ?>>FALTA</option>
                    <option value="CARGADO" <?= ($assignment['estado_carga'] ?? '') === 'CARGADO' ? 'selected' : '' ?>>CARGADO</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
        <a href="<?= e(base_url('/docentes-asignaciones')) ?>" class="btn btn-secondary">Cancelar</a>
        <button class="btn btn-primary" <?= empty($teachers) || empty($courseSubjects) || empty($gestiones) ? 'disabled' : '' ?>>Guardar</button>
    </div>
</form>
