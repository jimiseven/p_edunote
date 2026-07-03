<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="main-title mb-1">Detalle de Estudiante</h1>
        <p class="text-muted mb-0"><?= e(trim($student['apellido_paterno'] . ' ' . $student['apellido_materno'] . ' ' . $student['nombres'])) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= e(base_url('/estudiantes/edit?id=' . $student['id_estudiante'])) ?>" class="btn btn-info text-white">Editar</a>
        <a href="<?= e(base_url('/estudiantes')) ?>" class="btn btn-outline-secondary">Volver</a>
    </div>
</div>

<div class="row g-3">
    <!-- BASICO -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">Datos del Estudiante</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Nombres</dt><dd class="col-sm-8"><?= e($student['nombres']) ?></dd>
                    <dt class="col-sm-4">Apellidos</dt><dd class="col-sm-8"><?= e(trim($student['apellido_paterno'] . ' ' . $student['apellido_materno'])) ?></dd>
                    <dt class="col-sm-4">RUDE</dt><dd class="col-sm-8"><?= e($student['rude']) ?></dd>
                    <dt class="col-sm-4">CI</dt><dd class="col-sm-8"><?= e($student['ci']) ?></dd>
                    <dt class="col-sm-4">Género</dt><dd class="col-sm-8"><?= e($student['genero']) ?></dd>
                    <dt class="col-sm-4">Nacimiento</dt><dd class="col-sm-8"><?= e($student['fecha_nacimiento']) ?></dd>
                    <dt class="col-sm-4">País</dt><dd class="col-sm-8"><?= e($student['pais']) ?></dd>
                    <dt class="col-sm-4">Provincia/Depto.</dt><dd class="col-sm-8"><?= e($student['provincia_departamento']) ?></dd>
                    <?php if ($student['nivel']): ?>
                    <dt class="col-sm-4">Curso</dt><dd class="col-sm-8"><?= e($student['nivel'] . ' ' . $student['grado'] . '° ' . $student['paralelo']) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>

    <!-- RESPONSABLE -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-success text-white">Responsable Principal</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Nombres</dt><dd class="col-sm-8"><?= e($student['resp_nombres'] ?? '') ?></dd>
                    <dt class="col-sm-4">Apellidos</dt><dd class="col-sm-8"><?= e(trim(($student['resp_apellido_paterno'] ?? '') . ' ' . ($student['resp_apellido_materno'] ?? ''))) ?></dd>
                    <dt class="col-sm-4">CI</dt><dd class="col-sm-8"><?= e($student['resp_ci'] ?? '') ?></dd>
                    <dt class="col-sm-4">Parentesco</dt><dd class="col-sm-8"><?= e($student['resp_parentesco'] ?? '') ?></dd>
                    <dt class="col-sm-4">Celular</dt><dd class="col-sm-8"><?= e($student['resp_celular'] ?? '') ?></dd>
                    <dt class="col-sm-4">Dirección</dt><dd class="col-sm-8"><?= e($student['resp_direccion'] ?? '') ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- DIRECCION -->
    <?php if ($student['direccion']): $d = $student['direccion']; ?>
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">Dirección</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Departamento</dt><dd class="col-sm-8"><?= e($d['departamento'] ?? '') ?></dd>
                    <dt class="col-sm-4">Provincia</dt><dd class="col-sm-8"><?= e($d['provincia'] ?? '') ?></dd>
                    <dt class="col-sm-4">Municipio</dt><dd class="col-sm-8"><?= e($d['municipio'] ?? '') ?></dd>
                    <dt class="col-sm-4">Localidad</dt><dd class="col-sm-8"><?= e($d['localidad'] ?? '') ?></dd>
                    <dt class="col-sm-4">Zona</dt><dd class="col-sm-8"><?= e($d['zona'] ?? '') ?></dt>
                    <dt class="col-sm-4">Celular</dt><dd class="col-sm-8"><?= e($d['celular'] ?? '') ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- SALUD -->
    <?php if ($student['salud']): $s = $student['salud']; ?>
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">Salud</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-6">Seguro</dt><dd class="col-sm-6"><?= $s['tiene_seguro'] ? 'Sí' : 'No' ?></dd>
                    <dt class="col-sm-6">Acceso a posta</dt><dd class="col-sm-6"><?= $s['acceso_posta'] ? 'Sí' : 'No' ?></dd>
                    <dt class="col-sm-6">Acceso a hospital</dt><dd class="col-sm-6"><?= $s['acceso_hospital'] ? 'Sí' : 'No' ?></dd>
                    <?php if ($s['observacion']): ?>
                    <dt class="col-sm-12">Observación</dt><dd class="col-sm-12"><?= e($s['observacion']) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- IDIOMA Y CULTURA -->
    <?php if ($student['idioma']): $i = $student['idioma']; ?>
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-warning">Idioma y Cultura</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Idioma</dt><dd class="col-sm-8"><?= e($i['idioma'] ?? '') ?></dd>
                    <dt class="col-sm-4">Cultura</dt><dd class="col-sm-8"><?= e($i['cultura'] ?? '') ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- TRANSPORTE -->
    <?php if ($student['transporte']): $t = $student['transporte']; ?>
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">Transporte</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Medio</dt><dd class="col-sm-7"><?= e($t['medio'] ?? '') ?></dd>
                    <dt class="col-sm-5">Tiempo de llegada</dt><dd class="col-sm-7"><?= e($t['tiempo_llegada'] ?? '') ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- SERVICIOS BASICOS -->
    <?php if ($student['servicios']): $sv = $student['servicios']; ?>
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">Servicios Básicos</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-6">Agua cañería</dt><dd class="col-sm-6"><?= $sv['agua_caneria'] ? 'Sí' : 'No' ?></dd>
                    <dt class="col-sm-6">Baño</dt><dd class="col-sm-6"><?= $sv['bano'] ? 'Sí' : 'No' ?></dd>
                    <dt class="col-sm-6">Alcantarillado</dt><dd class="col-sm-6"><?= $sv['alcantarillado'] ? 'Sí' : 'No' ?></dd>
                    <dt class="col-sm-6">Internet</dt><dd class="col-sm-6"><?= $sv['internet'] ? 'Sí' : 'No' ?></dd>
                    <dt class="col-sm-6">Energía</dt><dd class="col-sm-6"><?= $sv['energia'] ? 'Sí' : 'No' ?></dd>
                    <dt class="col-sm-6">Recojo basura</dt><dd class="col-sm-6"><?= $sv['recojo_basura'] ? 'Sí' : 'No' ?></dd>
                    <dt class="col-sm-6">Tipo vivienda</dt><dd class="col-sm-6"><?= e($sv['tipo_vivienda'] ?? '') ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- DIFICULTADES -->
    <?php if ($student['dificultades'] && $student['dificultades']['tiene_dificultad']): $df = $student['dificultades']; ?>
    <div class="col-lg-6">
        <div class="card shadow-sm border-danger">
            <div class="card-header bg-danger text-white">Dificultades de Aprendizaje</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <?php if ($df['auditiva'] !== 'ninguna'): ?><dt class="col-sm-5">Auditiva</dt><dd class="col-sm-7"><?= e($df['auditiva']) ?></dd><?php endif; ?>
                    <?php if ($df['visual'] !== 'ninguna'): ?><dt class="col-sm-5">Visual</dt><dd class="col-sm-7"><?= e($df['visual']) ?></dd><?php endif; ?>
                    <?php if ($df['intelectual'] !== 'ninguna'): ?><dt class="col-sm-5">Intelectual</dt><dd class="col-sm-7"><?= e($df['intelectual']) ?></dd><?php endif; ?>
                    <?php if ($df['fisico_motora'] !== 'ninguna'): ?><dt class="col-sm-5">Físico motora</dt><dd class="col-sm-7"><?= e($df['fisico_motora']) ?></dd><?php endif; ?>
                    <?php if ($df['psiquica_mental'] !== 'ninguna'): ?><dt class="col-sm-5">Psíquica mental</dt><dd class="col-sm-7"><?= e($df['psiquica_mental']) ?></dd><?php endif; ?>
                    <?php if ($df['autista'] !== 'ninguna'): ?><dt class="col-sm-5">Autista</dt><dd class="col-sm-7"><?= e($df['autista']) ?></dd><?php endif; ?>
                </dl>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ACTIVIDAD LABORAL -->
    <?php if ($student['actividad_laboral'] && $student['actividad_laboral']['trabajo']): $lb = $student['actividad_laboral']; ?>
    <div class="col-lg-6">
        <div class="card shadow-sm border-warning">
            <div class="card-header bg-warning">Actividad Laboral</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Actividad</dt><dd class="col-sm-8"><?= e($lb['actividad'] ?? '') ?></dd>
                    <dt class="col-sm-4">Frecuencia</dt><dd class="col-sm-8"><?= e($lb['frecuencia'] ?? '') ?></dd>
                    <dt class="col-sm-4">Turno mañana</dt><dd class="col-sm-8"><?= $lb['turno_manana'] ? 'Sí' : 'No' ?></dd>
                    <dt class="col-sm-4">Turno tarde</dt><dd class="col-sm-8"><?= $lb['turno_tarde'] ? 'Sí' : 'No' ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
