<?php
$mainTabs = [
    ['id' => 'tab-estudiante', 'label' => 'Información del Estudiante', 'icon' => 'user', 'gradient' => 'linear-gradient(135deg, #388cff, #4abff9)'],
    ['id' => 'tab-responsable', 'label' => 'Información del Responsable', 'icon' => 'users', 'gradient' => 'linear-gradient(135deg, #10b981, #34d399)'],
    ['id' => 'tab-adicional', 'label' => 'Información Adicional', 'icon' => 'info', 'gradient' => 'linear-gradient(135deg, #f59e0b, #fbbf24)'],
];

$adicionalTabs = [
    ['id' => 'direccion', 'label' => 'Dirección'],
    ['id' => 'salud', 'label' => 'Salud'],
    ['id' => 'idioma', 'label' => 'Idioma/Cultura'],
    ['id' => 'transporte', 'label' => 'Transporte'],
    ['id' => 'servicios', 'label' => 'Servicios'],
    ['id' => 'laboral', 'label' => 'Laboral'],
    ['id' => 'dificultades', 'label' => 'Dificultades'],
    ['id' => 'abandono', 'label' => 'Abandono'],
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="main-title mb-1">Registro de Estudiante</h1>
        <p class="text-muted mb-0">Complete la información en cada una de las pestañas.</p>
    </div>
    <a href="<?= e(base_url('/estudiantes')) ?>" class="btn btn-outline-secondary">
        <i class="feather-arrow-left me-2"></i>Volver
    </a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= e($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<form method="POST" action="<?= e(base_url('/estudiantes/store')) ?>" class="student-form">
    <?= csrf_field() ?>

    <div class="card shadow-lg" style="border: 0; border-radius: 16px; overflow: hidden; border-top: 4px solid transparent; border-image: linear-gradient(90deg, #388cff, #10b981, #f59e0b) 1;">
        <!-- Tabs principales -->
        <div class="card-header p-3" style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
            <ul class="nav nav-pills nav-fill" role="tablist" style="gap: 0.5rem;">
                <?php $first = true; foreach ($mainTabs as $index => $tab): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $first ? 'active' : '' ?> fw-medium text-nowrap d-flex align-items-center justify-content-center"
                                data-bs-toggle="tab" data-bs-target="#<?= e($tab['id']) ?>"
                                type="button" role="tab"
                                data-default-bg="<?= e($tab['gradient']) ?>"
                                style="<?= $first ? 'background: ' . $tab['gradient'] . '; color: #fff;' : 'background: #fff; color: #475569;' ?> border-radius: 10px; border: 1px solid #e2e8f0; padding: 0.85rem 1rem;">
                            <i class="feather-<?= e($tab['icon']) ?> me-2" style="width: 16px; height: 16px;"></i><?= e($tab['label']) ?>
                        </button>
                    </li>
                <?php $first = false; endforeach; ?>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content">
                <!-- Pestaña 1: Información del Estudiante -->
                <div class="tab-pane fade show active" id="tab-estudiante" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-medium">Nombres <span class="text-danger">*</span></label>
                            <input type="text" name="nombres" class="form-control" required placeholder="Ej: Juan Carlos">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-medium">Ap. Paterno <span class="text-danger">*</span></label>
                            <input type="text" name="apellido_paterno" class="form-control" required placeholder="Ej: Pérez">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-medium">Ap. Materno</label>
                            <input type="text" name="apellido_materno" class="form-control" placeholder="Ej: García">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-secondary fw-medium">RUDE <span class="text-danger">*</span></label>
                            <input type="text" name="rude" class="form-control" required placeholder="N° RUDE">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-secondary fw-medium">CI <span class="text-danger">*</span></label>
                            <input type="text" name="ci" class="form-control" required placeholder="N° CI">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-secondary fw-medium">F. Nacimiento <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_nacimiento" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-secondary fw-medium">Género <span class="text-danger">*</span></label>
                            <select name="genero" class="form-select" required>
                                <option value="">Seleccionar</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-medium">País</label>
                            <select name="pais" class="form-select">
                                <option value="Bolivia">Bolivia</option>
                                <option value="Chile">Chile</option>
                                <option value="Argentina">Argentina</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-medium">Provincia/Departamento</label>
                            <input type="text" name="provincia_departamento" class="form-control" placeholder="Ej: La Paz">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-medium">Curso <span class="text-danger">*</span></label>
                            <select name="id_curso" class="form-select" required>
                                <option value="">Seleccionar curso</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?= e($course['id_curso']) ?>"><?= e($course['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Pestaña 2: Información del Responsable -->
                <div class="tab-pane fade" id="tab-responsable" role="tabpanel">
                    <p class="text-muted small mb-3">El sistema permite agregar más responsables después; este será el principal.</p>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-medium">Nombres <span class="text-danger">*</span></label>
                            <input type="text" name="resp_nombres" class="form-control" required placeholder="Ej: María Elena">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-medium">Ap. Paterno <span class="text-danger">*</span></label>
                            <input type="text" name="resp_apellido_paterno" class="form-control" required placeholder="Ej: Pérez">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-medium">Ap. Materno</label>
                            <input type="text" name="resp_apellido_materno" class="form-control" placeholder="Ej: García">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-secondary fw-medium">CI <span class="text-danger">*</span></label>
                            <input type="text" name="resp_ci" class="form-control" required placeholder="N° CI">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-secondary fw-medium">F. Nacimiento</label>
                            <input type="date" name="resp_fecha_nacimiento" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-secondary fw-medium">Parentesco <span class="text-danger">*</span></label>
                            <select name="resp_parentesco" class="form-select" required>
                                <option value="">Seleccionar</option>
                                <option value="Padre">Padre</option>
                                <option value="Madre">Madre</option>
                                <option value="Tutor">Tutor</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-secondary fw-medium">Celular</label>
                            <input type="text" name="resp_celular" class="form-control" placeholder="Ej: 71234567">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-medium">Grado de Instrucción</label>
                            <select name="resp_grado_instruccion" class="form-select">
                                <option value="">Seleccionar</option>
                                <option value="Ninguno">Ninguno</option>
                                <option value="Primaria">Primaria</option>
                                <option value="Secundaria">Secundaria</option>
                                <option value="Tecnico">Técnico</option>
                                <option value="Universitario">Universitario</option>
                                <option value="Postgrado">Postgrado</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-medium">Idioma Frecuente</label>
                            <input type="text" name="resp_idioma_frecuente" class="form-control" placeholder="Español, Quechua, Aymara...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-medium">Ocupación</label>
                            <input type="text" name="resp_ocupacion" class="form-control" placeholder="Ej: Comerciante">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small text-secondary fw-medium">Dirección</label>
                            <input type="text" name="resp_direccion" class="form-control" placeholder="Dirección completa">
                        </div>
                        <div class="col-md-4 d-flex align-items-end gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="resp_vive_con_estudiante" id="vive">
                                <label class="form-check-label small" for="vive">Vive con el estudiante</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="resp_autorizado_recoger" id="recoge">
                                <label class="form-check-label small" for="recoge">Autorizado a recoger</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pestaña 3: Información Adicional -->
                <div class="tab-pane fade" id="tab-adicional" role="tabpanel">
                    <div class="horizontal-tabs-wrapper mb-3" style="overflow-x: auto;">
                        <ul class="nav nav-pills flex-nowrap" role="tablist" style="gap: 0.5rem;">
                            <?php $firstAd = true; foreach ($adicionalTabs as $tab): ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link text-nowrap <?= $firstAd ? 'active' : '' ?>"
                                            data-bs-toggle="tab" data-bs-target="#<?= e($tab['id']) ?>"
                                            type="button" role="tab">
                                        <?= e($tab['label']) ?>
                                    </button>
                                </li>
                            <?php $firstAd = false; endforeach; ?>
                        </ul>
                    </div>

                    <div class="tab-content border rounded-3 p-3 bg-white">
                        <div class="tab-pane fade show active" id="direccion">
                            <div class="row g-3">
                                <div class="col-md-4"><label class="form-label small text-secondary">Departamento</label><input name="dir_departamento" class="form-control"></div>
                                <div class="col-md-4"><label class="form-label small text-secondary">Provincia</label><input name="dir_provincia" class="form-control"></div>
                                <div class="col-md-4"><label class="form-label small text-secondary">Municipio</label><input name="dir_municipio" class="form-control"></div>
                                <div class="col-md-4"><label class="form-label small text-secondary">Localidad</label><input name="dir_localidad" class="form-control"></div>
                                <div class="col-md-4"><label class="form-label small text-secondary">Comunidad</label><input name="dir_comunidad" class="form-control"></div>
                                <div class="col-md-4"><label class="form-label small text-secondary">Zona</label><input name="dir_zona" class="form-control"></div>
                                <div class="col-md-4"><label class="form-label small text-secondary">Nro. Vivienda</label><input name="dir_numero_vivienda" class="form-control"></div>
                                <div class="col-md-4"><label class="form-label small text-secondary">Teléfono</label><input name="dir_telefono" class="form-control"></div>
                                <div class="col-md-4"><label class="form-label small text-secondary">Celular</label><input name="dir_celular" class="form-control"></div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="salud">
                            <div class="row g-3">
                                <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="sal_tiene_seguro" id="seguro"><label class="form-check-label" for="seguro">Tiene seguro</label></div></div>
                                <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="sal_acceso_posta" id="posta"><label class="form-check-label" for="posta">Acceso a posta</label></div></div>
                                <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="sal_acceso_centro_salud" id="centro"><label class="form-check-label" for="centro">Centro de salud</label></div></div>
                                <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="sal_acceso_hospital" id="hospital"><label class="form-check-label" for="hospital">Hospital</label></div></div>
                                <div class="col-12"><label class="form-label small text-secondary">Observación</label><input name="sal_observacion" class="form-control"></div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="idioma">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label small text-secondary">Idioma</label><input name="idi_idioma" class="form-control"></div>
                                <div class="col-md-6"><label class="form-label small text-secondary">Cultura</label><input name="idi_cultura" class="form-control"></div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="transporte">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label small text-secondary">Medio</label><select name="trans_medio" class="form-select"><option value="">Seleccionar</option><option value="a_pie">A pie</option><option value="vehiculo">Vehículo</option><option value="fluvial">Fluvial</option><option value="otro">Otro</option></select></div>
                                <div class="col-md-6"><label class="form-label small text-secondary">Tiempo de llegada</label><select name="trans_tiempo_llegada" class="form-select"><option value="">Seleccionar</option><option value="menos_media_hora">Menos de media hora</option><option value="mas_media_hora">Más de media hora</option></select></div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="servicios">
                            <div class="row g-3">
                                <?php foreach (['serv_agua_caneria' => 'Agua por cañería', 'serv_bano' => 'Baño', 'serv_alcantarillado' => 'Alcantarillado', 'serv_internet' => 'Internet', 'serv_energia' => 'Energía', 'serv_recojo_basura' => 'Recojo basura'] as $name => $label): ?>
                                    <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="<?= e($name) ?>" id="<?= e($name) ?>"><label class="form-check-label" for="<?= e($name) ?>"><?= e($label) ?></label></div></div>
                                <?php endforeach; ?>
                                <div class="col-md-4"><label class="form-label small text-secondary">Tipo vivienda</label><select name="serv_tipo_vivienda" class="form-select"><option value="">Seleccionar</option><option value="alquilada">Alquilada</option><option value="propia">Propia</option><option value="cedida">Cedida</option><option value="anticretico">Anticrético</option></select></div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="laboral">
                            <div class="row g-3">
                                <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="lab_trabajo" id="trabajo"><label class="form-check-label" for="trabajo">Trabaja</label></div></div>
                                <div class="col-md-9"><label class="form-label small text-secondary">Actividad</label><input name="lab_actividad" class="form-control"></div>
                                <div class="col-md-4"><label class="form-label small text-secondary">Meses</label><select name="lab_meses_trabajo[]" class="form-select" multiple><option value="enero">Enero</option><option value="febrero">Febrero</option><option value="marzo">Marzo</option><option value="abril">Abril</option><option value="mayo">Mayo</option><option value="junio">Junio</option><option value="julio">Julio</option><option value="agosto">Agosto</option><option value="septiembre">Septiembre</option><option value="octubre">Octubre</option><option value="noviembre">Noviembre</option><option value="diciembre">Diciembre</option></select></div>
                                <div class="col-md-4"><label class="form-label small text-secondary">Frecuencia</label><select name="lab_frecuencia" class="form-select"><option value="">Seleccionar</option><option value="todos_dias">Todos los días</option><option value="dias_habiles">Días hábiles</option><option value="fin_de_semana">Fin de semana</option><option value="esporadico">Esporádico</option><option value="dias_festivos">Días festivos</option><option value="vacaciones">Vacaciones</option></select></div>
                                <div class="col-md-4 d-flex align-items-end gap-3"><label><input type="checkbox" name="lab_turno_manana"> Mañana</label><label><input type="checkbox" name="lab_turno_tarde"> Tarde</label><label><input type="checkbox" name="lab_turno_noche"> Noche</label></div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="dificultades">
                            <div class="row g-3">
                                <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="dif_tiene_dificultad" id="dif"><label class="form-check-label" for="dif">Tiene dificultad</label></div></div>
                                <?php foreach (['dif_auditiva' => 'Auditiva', 'dif_visual' => 'Visual', 'dif_intelectual' => 'Intelectual', 'dif_fisico_motora' => 'Físico motora', 'dif_psiquica_mental' => 'Psíquica mental', 'dif_autista' => 'Autista'] as $name => $label): ?>
                                    <div class="col-md-4"><label class="form-label small text-secondary"><?= e($label) ?></label><select name="<?= e($name) ?>" class="form-select"><option value="ninguna">Ninguna</option><option value="leve">Leve</option><option value="grave">Grave</option><option value="muy_grave">Muy grave</option><option value="multiple">Múltiple</option></select></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="abandono">
                            <div class="row g-3">
                                <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="aba_abandono" id="abandonoCheck"><label class="form-check-label" for="abandonoCheck">Abandono</label></div></div>
                                <div class="col-md-4"><label class="form-label small text-secondary">Motivo</label><select name="aba_motivo" class="form-select"><option value="">Seleccionar</option><option value="trabajo">Trabajo</option><option value="falta_dinero">Falta de dinero</option><option value="otro">Otro</option></select></div>
                                <div class="col-md-5"><label class="form-label small text-secondary">Observación</label><input name="aba_observacion" class="form-control"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer con botones -->
        <div class="card-footer d-flex justify-content-between align-items-center bg-white border-top" style="padding: 1.25rem 1.5rem;">
            <a href="<?= e(base_url('/estudiantes')) ?>" class="btn btn-outline-secondary px-4">
                <i class="feather-arrow-left me-2"></i>Cancelar
            </a>
            <button type="submit" class="btn btn-primary px-5 fw-semibold" style="background: linear-gradient(135deg, #388cff, #4abff9); border: 0;">
                <i class="feather-save me-2"></i>Guardar Estudiante
            </button>
        </div>
    </div>
</form>
