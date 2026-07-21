<?php
$isEdit = isset($user);
$action = $isEdit ? base_url('/usuarios/update') : base_url('/usuarios/store');
?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= e($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <form method="POST" action="<?= e($action) ?>">
            <?= csrf_field() ?>
            <?php if ($isEdit): ?>
                <input type="hidden" name="id_usuario" value="<?= e($user['id_usuario']) ?>">
            <?php endif; ?>

            <!-- Sección: Datos Personales -->
            <div class="card shadow-sm mb-4" style="border: 0; border-radius: 12px;">
                <div class="card-header py-3" style="background: linear-gradient(135deg, #388cff, #4abff9); border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0 text-white fw-semibold">
                        <i class="feather-user me-2"></i>Datos Personales
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-secondary">Nombres <span class="text-danger">*</span></label>
                            <input type="text" name="nombres" class="form-control" value="<?= e($user['nombres'] ?? '') ?>" required placeholder="Ej: Juan Carlos">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-secondary">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" name="apellidos" class="form-control" value="<?= e($user['apellidos'] ?? '') ?>" required placeholder="Ej: Pérez García">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium text-secondary">CI <span class="text-danger">*</span></label>
                            <input type="text" name="ci" class="form-control" value="<?= e($user['ci'] ?? '') ?>" required placeholder="Número de carnet">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium text-secondary">Celular</label>
                            <input type="text" name="celular" class="form-control" value="<?= e($user['celular'] ?? '') ?>" placeholder="Ej: 71234567">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium text-secondary">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= e($user['email'] ?? '') ?>" placeholder="correo@ejemplo.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-secondary">Cargo</label>
                            <input type="text" name="cargo" class="form-control" value="<?= e($user['cargo'] ?? '') ?>" placeholder="Ej: Director, Docente">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-secondary">Especialidad</label>
                            <input type="text" name="especialidad" class="form-control" value="<?= e($user['especialidad'] ?? '') ?>" placeholder="Área de especialización">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección: Cuenta de Acceso -->
            <div class="card shadow-sm mb-4" style="border: 0; border-radius: 12px;">
                <div class="card-header py-3" style="background: linear-gradient(135deg, #388cff, #4abff9); border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0 text-white fw-semibold">
                        <i class="feather-lock me-2"></i>Cuenta de Acceso
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-medium text-secondary">Usuario <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" value="<?= e($user['username'] ?? '') ?>" required placeholder="Nombre de usuario">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium text-secondary">Rol <span class="text-danger">*</span></label>
                            <select name="id_rol" class="form-select" required>
                                <option value="">Seleccionar rol</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= e($role['id_rol']) ?>" <?= (int)($user['id_rol'] ?? 0) === (int)$role['id_rol'] ? 'selected' : '' ?>>
                                        <?= e($role['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium text-secondary"><?= $isEdit ? 'Nueva contraseña' : 'Contraseña <span class="text-danger">*</span>' ?></label>
                            <input type="password" name="password" class="form-control" <?= $isEdit ? '' : 'required' ?> placeholder="<?= $isEdit ? 'Dejar vacío para mantener' : 'Ingrese contraseña' ?>">
                            <?php if ($isEdit): ?>
                                <small class="text-muted" style="font-size: 0.75rem;">Dejar vacío para mantener la actual</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="d-flex justify-content-between align-items-center">
                <a href="<?= e(base_url('/usuarios')) ?>" class="btn btn-outline-secondary px-4">
                    <i class="feather-arrow-left me-2"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary px-5 fw-semibold" style="background: linear-gradient(135deg, #388cff, #4abff9); border: 0;">
                    <i class="feather-save me-2"></i>Guardar
                </button>
            </div>
        </form>
    </div>
</div>