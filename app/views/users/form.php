<?php
$isEdit = isset($user);
$action = $isEdit ? base_url('/usuarios/update') : base_url('/usuarios/store');
?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<form method="POST" action="<?= e($action) ?>" class="card shadow-sm">
    <div class="card-body">
        <?= csrf_field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id_usuario" value="<?= e($user['id_usuario']) ?>">
        <?php endif; ?>

        <h5 class="mb-3 text-primary">Datos personales</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Nombres *</label>
                <input type="text" name="nombres" class="form-control" value="<?= e($user['nombres'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Apellidos *</label>
                <input type="text" name="apellidos" class="form-control" value="<?= e($user['apellidos'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">CI *</label>
                <input type="text" name="ci" class="form-control" value="<?= e($user['ci'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Celular</label>
                <input type="text" name="celular" class="form-control" value="<?= e($user['celular'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= e($user['email'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Cargo</label>
                <input type="text" name="cargo" class="form-control" value="<?= e($user['cargo'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Especialidad</label>
                <input type="text" name="especialidad" class="form-control" value="<?= e($user['especialidad'] ?? '') ?>">
            </div>
        </div>

        <h5 class="mb-3 text-primary">Cuenta de acceso</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Usuario *</label>
                <input type="text" name="username" class="form-control" value="<?= e($user['username'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Rol *</label>
                <select name="id_rol" class="form-select" required>
                    <option value="">Seleccionar</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= e($role['id_rol']) ?>" <?= (int)($user['id_rol'] ?? 0) === (int)$role['id_rol'] ? 'selected' : '' ?>>
                            <?= e($role['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label"><?= $isEdit ? 'Nueva contrasena' : 'Contrasena *' ?></label>
                <input type="password" name="password" class="form-control" <?= $isEdit ? '' : 'required' ?>>
                <?php if ($isEdit): ?>
                    <small class="text-muted">Dejar vacio para mantener la actual.</small>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
        <a href="<?= e(base_url('/usuarios')) ?>" class="btn btn-secondary">Cancelar</a>
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>
