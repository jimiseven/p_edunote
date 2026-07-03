<div class="container-fluid p-0">
    <div class="login-header">
        <div class="container">
            <h3 class="text-white py-2 mb-0">Inicio de Sesion</h3>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="card login-card shadow">
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <div class="col-md-6 d-flex align-items-center justify-content-center p-5 brand-panel">
                                <div class="text-center">
                                    <div class="login-logo mx-auto mb-3">E</div>
                                    <h4 class="fw-bold text-primary mb-1">EDUFILE</h4>
                                    <p class="text-muted mb-0">Sistema administrativo educativo</p>
                                </div>
                            </div>
                            <div class="col-md-6 bg-light p-4 rounded-end d-flex flex-column justify-content-center">
                                <h2 class="text-center mb-4">Bienvenido</h2>

                                <?php if (!empty($error)): ?>
                                    <div class="alert alert-danger"> <?= e($error) ?> </div>
                                <?php endif; ?>

                                <form action="<?= e(base_url('/login')) ?>" method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Usuario</label>
                                        <input type="text" class="form-control" name="username" required autofocus>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Contrasena</label>
                                        <input type="password" class="form-control" name="password" required>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">Ingresar</button>
                                    </div>
                                </form>

                                <small class="text-muted mt-3 text-center">Usuario inicial: admin | clave: admin123</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
