<div class="container-fluid p-0 min-vh-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="card shadow-lg" style="max-width: 800px; width: 95%; border: 0; border-radius: 24px; overflow: hidden;">
        <div class="row g-0">
            <!-- Left: Image -->
            <div class="col-md-5 d-none d-md-flex align-items-center justify-content-center position-relative overflow-hidden"
                 style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); min-height: 420px;">
                <!-- Soft colored glows matching the logo -->
                <div class="position-absolute top-0 start-0 end-0 bottom-0"
                     style="background: radial-gradient(circle at 25% 25%, rgba(239, 68, 68, 0.15) 0%, transparent 50%),
                            radial-gradient(circle at 75% 20%, rgba(59, 130, 246, 0.12) 0%, transparent 40%),
                            radial-gradient(circle at 20% 80%, rgba(250, 204, 21, 0.12) 0%, transparent 45%),
                            radial-gradient(circle at 80% 85%, rgba(132, 204, 22, 0.12) 0%, transparent 40%);"
                ></div>
                <!-- Logo centered with padding -->
                <div class="position-relative p-4">
                    <img src="<?= e(base_url('/assets/img/info1.png')) ?>" alt="EduNote"
                         class="img-fluid"
                         style="max-height: 200px; max-width: 100%; object-fit: contain;
                                filter: drop-shadow(0 8px 24px rgba(0,0,0,0.1));">
                </div>
            </div>

            <!-- Right: Form -->
            <div class="col-md-7 d-flex align-items-center justify-content-center p-4 p-md-5"
                 style="background: #fff;">
                <div class="w-100" style="max-width: 340px;">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 56px; height: 56px; border-radius: 16px;
                                    background: linear-gradient(135deg, #388cff, #4abff9);
                                    color: #fff; font-size: 1.5rem; font-weight: 700;">E</div>
                        <h4 class="fw-bold mb-1">Bienvenido</h4>
                        <p class="text-muted small mb-0">Inicia sesión para continuar</p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger py-2 small"> <?= e($error) ?> </div>
                    <?php endif; ?>

                    <form action="<?= e(base_url('/login')) ?>" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-medium text-secondary">Usuario</label>
                            <input type="text" class="form-control form-control-lg" name="username" required autofocus
                                   placeholder="Ingrese su usuario">
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-medium text-secondary">Contraseña</label>
                            <input type="password" class="form-control form-control-lg" name="password" required
                                   placeholder="Ingrese su contraseña">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg fw-semibold"
                                    style="background: linear-gradient(135deg, #388cff, #4abff9); border: 0;">
                                Ingresar
                            </button>
                        </div>
                    </form>

                    <p class="text-center text-muted small mt-4 mb-0">
                        Usuario inicial: <strong>admin</strong> | Clave: <strong>admin123</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>