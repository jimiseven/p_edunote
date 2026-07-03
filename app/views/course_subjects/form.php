<?php
$isEdit = isset($assignment);
$action = $isEdit ? base_url('/cursos-materias/update') : base_url('/cursos-materias/store');
?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<form method="POST" action="<?= e($action) ?>" class="card shadow-sm">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?>
        <input type="hidden" name="id_curso_materia" value="<?= e($assignment['id_curso_materia']) ?>">
    <?php endif; ?>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label">Curso *</label>
                <select name="id_curso" class="form-select" required>
                    <option value="">Seleccionar curso</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= e($course['id_curso']) ?>" <?= (int)($assignment['id_curso'] ?? 0) === (int)$course['id_curso'] ? 'selected' : '' ?>>
                            <?= e($course['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Materia *</label>
                <select name="id_materia" class="form-select" required>
                    <option value="">Seleccionar materia</option>
                    <?php foreach ($subjects as $subject): ?>
                        <?php if ((int) $subject['estado'] !== 1) continue; ?>
                        <option value="<?= e($subject['id_materia']) ?>" <?= (int)($assignment['id_materia'] ?? 0) === (int)$subject['id_materia'] ? 'selected' : '' ?>>
                            <?= e($subject['nombre']) ?><?= !empty($subject['abreviatura']) ? ' (' . e($subject['abreviatura']) . ')' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="1" <?= (int)($assignment['estado'] ?? 1) === 1 ? 'selected' : '' ?>>Activa</option>
                    <option value="0" <?= (int)($assignment['estado'] ?? 1) === 0 ? 'selected' : '' ?>>Inactiva</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
        <a href="<?= e(base_url('/cursos-materias')) ?>" class="btn btn-secondary">Cancelar</a>
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>
