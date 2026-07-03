<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="main-title mb-1">Gestion de Usuarios</h1>
        <p class="text-muted mb-0">Administracion de cuentas, roles y estado de acceso.</p>
    </div>
    <a href="<?= e(base_url('/usuarios/create')) ?>" class="btn btn-success">Nuevo Usuario</a>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="<?= e(base_url('/usuarios')) ?>" class="row g-2">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="Buscar por usuario, nombre, apellido, CI o email" value="<?= e($search) ?>">
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary">Buscar</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>CI</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Ultimo login</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">No hay usuarios registrados.</td></tr>
                <?php endif; ?>

                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= e(trim(($user['apellidos'] ?? '') . ', ' . ($user['nombres'] ?? ''))) ?></td>
                        <td><?= e($user['username']) ?><br><small class="text-muted"><?= e($user['email'] ?? '') ?></small></td>
                        <td><?= e($user['ci'] ?? '') ?></td>
                        <td><span class="badge bg-primary"><?= e($user['rol_nombre']) ?></span></td>
                        <td>
                            <span class="badge bg-<?= $user['estado'] === 'activo' ? 'success' : 'secondary' ?>"><?= e($user['estado']) ?></span>
                        </td>
                        <td><?= e($user['ultimo_login'] ?? 'Sin acceso') ?></td>
                        <td class="text-end">
                            <a href="<?= e(base_url('/usuarios/edit?id=' . $user['id_usuario'])) ?>" class="btn btn-sm btn-info text-white">Editar</a>
                            <form method="POST" action="<?= e(base_url('/usuarios/status')) ?>" class="d-inline" onsubmit="return confirm('¿Cambiar estado del usuario?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id_usuario" value="<?= e($user['id_usuario']) ?>">
                                <input type="hidden" name="estado" value="<?= $user['estado'] === 'activo' ? 'inactivo' : 'activo' ?>">
                                <button class="btn btn-sm btn-<?= $user['estado'] === 'activo' ? 'warning' : 'success' ?>">
                                    <?= $user['estado'] === 'activo' ? 'Inactivar' : 'Activar' ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
