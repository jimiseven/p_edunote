<?php
$isEdit = isset($course);
$action = $isEdit ? base_url('/cursos/update') : base_url('/cursos/store');
?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<form method="POST" action="<?= e($action) ?>" class="card shadow-sm">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?>
        <input type="hidden" name="id_curso" value="<?= e($course['id_curso']) ?>">
    <?php endif; ?>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Nivel *</label>
                <select name="nivel" class="form-select" required>
                    <option value="">Seleccionar</option>
                    <?php foreach (['Inicial', 'Primaria', 'Secundaria'] as $nivel): ?>
                        <option value="<?= e($nivel) ?>" <?= ($course['nivel'] ?? '') === $nivel ? 'selected' : '' ?>><?= e($nivel) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Grado *</label>
                <input type="number" name="grado" class="form-control" min="1" max="6" value="<?= e($course['grado'] ?? '') ?>" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Paralelo *</label>
                <input type="text" name="paralelo" class="form-control" maxlength="5" value="<?= e($course['paralelo'] ?? '') ?>" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Turno</label>
                <select name="turno" class="form-select">
                    <option value="">Sin turno</option>
                    <?php foreach (['manana' => 'Mañana', 'tarde' => 'Tarde', 'noche' => 'Noche'] as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= ($course['turno'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="1" <?= (int)($course['estado'] ?? 1) === 1 ? 'selected' : '' ?>>Activo</option>
                    <option value="0" <?= (int)($course['estado'] ?? 1) === 0 ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
        <a href="<?= e(base_url('/cursos')) ?>" class="btn btn-secondary">Cancelar</a>
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>
