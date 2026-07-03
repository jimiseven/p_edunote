<?php
$isEdit = isset($subject);
$action = $isEdit ? base_url('/materias/update') : base_url('/materias/store');
?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<form method="POST" action="<?= e($action) ?>" class="card shadow-sm">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?>
        <input type="hidden" name="id_materia" value="<?= e($subject['id_materia']) ?>">
    <?php endif; ?>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label">Nombre de Materia *</label>
                <input type="text" name="nombre" class="form-control" maxlength="150" value="<?= e($subject['nombre'] ?? '') ?>" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Abreviatura</label>
                <input type="text" name="abreviatura" class="form-control" maxlength="30" value="<?= e($subject['abreviatura'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Materia Padre</label>
                <select name="id_materia_padre" class="form-select">
                    <option value="">Sin materia padre</option>
                    <?php foreach ($parents as $parent): ?>
                        <option value="<?= e($parent['id_materia']) ?>" <?= (int)($subject['id_materia_padre'] ?? 0) === (int)$parent['id_materia'] ? 'selected' : '' ?>>
                            <?= e($parent['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted">Solo se usa si marca submateria.</small>
            </div>
            <div class="col-md-2">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="1" <?= (int)($subject['estado'] ?? 1) === 1 ? 'selected' : '' ?>>Activa</option>
                    <option value="0" <?= (int)($subject['estado'] ?? 1) === 0 ? 'selected' : '' ?>>Inactiva</option>
                </select>
            </div>
            <div class="col-md-12">
                <label class="form-label">Descripcion</label>
                <input type="text" name="descripcion" class="form-control" maxlength="255" value="<?= e($subject['descripcion'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="es_submateria" id="es_submateria" <?= (int)($subject['es_submateria'] ?? 0) === 1 ? 'checked' : '' ?>>
                    <label class="form-check-label" for="es_submateria">Es submateria</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="es_extra" id="es_extra" <?= (int)($subject['es_extra'] ?? 0) === 1 ? 'checked' : '' ?>>
                    <label class="form-check-label" for="es_extra">Materia extra</label>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
        <a href="<?= e(base_url('/materias')) ?>" class="btn btn-secondary">Cancelar</a>
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>
