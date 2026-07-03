<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="main-title mb-1">Registro de Estudiante</h1>
        <p class="text-muted mb-0">Datos del estudiante, responsable e informacion adicional.</p>
    </div>
    <a href="<?= e(base_url('/estudiantes')) ?>" class="btn btn-secondary">Volver</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<form method="POST" action="<?= e(base_url('/estudiantes/store')) ?>" class="card shadow-sm student-form">
    <?= csrf_field() ?>
    <div class="card-body">
        <div class="step-container mb-4">
            <div class="step-header mb-3">
                <h5 class="text-primary mb-0">Paso 1: Informacion del Estudiante</h5>
                <small class="text-muted">Complete los datos personales y academicos del estudiante.</small>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Nombres *</label>
                    <input type="text" name="nombres" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ap. Paterno *</label>
                    <input type="text" name="apellido_paterno" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ap. Materno</label>
                    <input type="text" name="apellido_materno" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">RUDE *</label>
                    <input type="text" name="rude" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">CI *</label>
                    <input type="text" name="ci" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">F. Nacimiento *</label>
                    <input type="date" name="fecha_nacimiento" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Genero *</label>
                    <select name="genero" class="form-select" required>
                        <option value="">Seleccionar</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pais</label>
                    <select name="pais" class="form-select">
                        <option value="Bolivia">Bolivia</option>
                        <option value="Chile">Chile</option>
                        <option value="Argentina">Argentina</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Provincia/Departamento</label>
                    <input type="text" name="provincia_departamento" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Curso *</label>
                    <select name="id_curso" class="form-select" required>
                        <option value="">Seleccionar</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= e($course['id_curso']) ?>"><?= e($course['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="step-container mb-4 border-success">
            <div class="step-header mb-3">
                <h5 class="text-success mb-0">Paso 2: Informacion del Responsable</h5>
                <small class="text-muted">El sistema permite agregar mas responsables despues; este sera el principal.</small>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Nombres del Responsable *</label>
                    <input type="text" name="resp_nombres" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ap. Paterno *</label>
                    <input type="text" name="resp_apellido_paterno" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ap. Materno</label>
                    <input type="text" name="resp_apellido_materno" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">CI Responsable *</label>
                    <input type="text" name="resp_ci" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">F. Nacimiento</label>
                    <input type="date" name="resp_fecha_nacimiento" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Parentesco *</label>
                    <select name="resp_parentesco" class="form-select" required>
                        <option value="">Seleccionar</option>
                        <option value="Padre">Padre</option>
                        <option value="Madre">Madre</option>
                        <option value="Tutor">Tutor</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Celular</label>
                    <input type="text" name="resp_celular" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Grado de Instruccion</label>
                    <select name="resp_grado_instruccion" class="form-select">
                        <option value="">Seleccionar</option>
                        <option value="Ninguno">Ninguno</option>
                        <option value="Primaria">Primaria</option>
                        <option value="Secundaria">Secundaria</option>
                        <option value="Tecnico">Tecnico</option>
                        <option value="Universitario">Universitario</option>
                        <option value="Postgrado">Postgrado</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Idioma Frecuente</label>
                    <input type="text" name="resp_idioma_frecuente" class="form-control" placeholder="Español, Quechua, Aymara...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ocupacion</label>
                    <input type="text" name="resp_ocupacion" class="form-control">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Direccion</label>
                    <input type="text" name="resp_direccion" class="form-control">
                </div>
                <div class="col-md-4 d-flex align-items-end gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="resp_vive_con_estudiante" id="vive">
                        <label class="form-check-label" for="vive">Vive con el estudiante</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="resp_autorizado_recoger" id="recoge">
                        <label class="form-check-label" for="recoge">Autorizado a recoger</label>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="text-info mb-3">Informacion Adicional (Opcional)</h6>
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#direccion" type="button">Direccion</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#salud" type="button">Salud</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#idioma" type="button">Idioma/Cultura</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#transporte" type="button">Transporte</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#servicios" type="button">Servicios</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#laboral" type="button">Laboral</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#dificultades" type="button">Dificultades</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#abandono" type="button">Abandono</button></li>
        </ul>

        <div class="tab-content border border-top-0 p-3 mb-3 bg-white">
            <div class="tab-pane fade show active" id="direccion">
                <div class="row g-3">
                    <div class="col-md-4"><label class="form-label">Departamento</label><input name="dir_departamento" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Provincia</label><input name="dir_provincia" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Municipio</label><input name="dir_municipio" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Localidad</label><input name="dir_localidad" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Comunidad</label><input name="dir_comunidad" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Zona</label><input name="dir_zona" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Nro. Vivienda</label><input name="dir_numero_vivienda" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Telefono</label><input name="dir_telefono" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Celular</label><input name="dir_celular" class="form-control"></div>
                </div>
            </div>
            <div class="tab-pane fade" id="salud">
                <div class="row g-3">
                    <div class="col-md-3 form-check ms-3"><input class="form-check-input" type="checkbox" name="sal_tiene_seguro" id="seguro"><label class="form-check-label" for="seguro">Tiene seguro</label></div>
                    <div class="col-md-3 form-check"><input class="form-check-input" type="checkbox" name="sal_acceso_posta" id="posta"><label class="form-check-label" for="posta">Acceso a posta</label></div>
                    <div class="col-md-3 form-check"><input class="form-check-input" type="checkbox" name="sal_acceso_centro_salud" id="centro"><label class="form-check-label" for="centro">Centro de salud</label></div>
                    <div class="col-md-3 form-check"><input class="form-check-input" type="checkbox" name="sal_acceso_hospital" id="hospital"><label class="form-check-label" for="hospital">Hospital</label></div>
                    <div class="col-12"><label class="form-label">Observacion</label><input name="sal_observacion" class="form-control"></div>
                </div>
            </div>
            <div class="tab-pane fade" id="idioma">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Idioma</label><input name="idi_idioma" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">Cultura</label><input name="idi_cultura" class="form-control"></div>
                </div>
            </div>
            <div class="tab-pane fade" id="transporte">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Medio</label><select name="trans_medio" class="form-select"><option value="">Seleccionar</option><option value="a_pie">A pie</option><option value="vehiculo">Vehiculo</option><option value="fluvial">Fluvial</option><option value="otro">Otro</option></select></div>
                    <div class="col-md-6"><label class="form-label">Tiempo de llegada</label><select name="trans_tiempo_llegada" class="form-select"><option value="">Seleccionar</option><option value="menos_media_hora">Menos de media hora</option><option value="mas_media_hora">Mas de media hora</option></select></div>
                </div>
            </div>
            <div class="tab-pane fade" id="servicios">
                <div class="row g-3">
                    <?php foreach (['serv_agua_caneria' => 'Agua por cañeria', 'serv_bano' => 'Baño', 'serv_alcantarillado' => 'Alcantarillado', 'serv_internet' => 'Internet', 'serv_energia' => 'Energia', 'serv_recojo_basura' => 'Recojo basura'] as $name => $label): ?>
                        <div class="col-md-2 form-check ms-3"><input class="form-check-input" type="checkbox" name="<?= e($name) ?>" id="<?= e($name) ?>"><label class="form-check-label" for="<?= e($name) ?>"><?= e($label) ?></label></div>
                    <?php endforeach; ?>
                    <div class="col-md-4"><label class="form-label">Tipo vivienda</label><select name="serv_tipo_vivienda" class="form-select"><option value="">Seleccionar</option><option value="alquilada">Alquilada</option><option value="propia">Propia</option><option value="cedida">Cedida</option><option value="anticretico">Anticretico</option></select></div>
                </div>
            </div>
            <div class="tab-pane fade" id="laboral">
                <div class="row g-3">
                    <div class="col-md-3 form-check ms-3"><input class="form-check-input" type="checkbox" name="lab_trabajo" id="trabajo"><label class="form-check-label" for="trabajo">Trabaja</label></div>
                    <div class="col-md-9"><label class="form-label">Actividad</label><input name="lab_actividad" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Meses</label><select name="lab_meses_trabajo[]" class="form-select" multiple><option value="enero">Enero</option><option value="febrero">Febrero</option><option value="marzo">Marzo</option><option value="abril">Abril</option><option value="mayo">Mayo</option><option value="junio">Junio</option><option value="julio">Julio</option><option value="agosto">Agosto</option><option value="septiembre">Septiembre</option><option value="octubre">Octubre</option><option value="noviembre">Noviembre</option><option value="diciembre">Diciembre</option></select></div>
                    <div class="col-md-4"><label class="form-label">Frecuencia</label><select name="lab_frecuencia" class="form-select"><option value="">Seleccionar</option><option value="todos_dias">Todos los dias</option><option value="dias_habiles">Dias habiles</option><option value="fin_de_semana">Fin de semana</option><option value="esporadico">Esporadico</option><option value="dias_festivos">Dias festivos</option><option value="vacaciones">Vacaciones</option></select></div>
                    <div class="col-md-4 d-flex align-items-end gap-3"><label><input type="checkbox" name="lab_turno_manana"> Mañana</label><label><input type="checkbox" name="lab_turno_tarde"> Tarde</label><label><input type="checkbox" name="lab_turno_noche"> Noche</label></div>
                </div>
            </div>
            <div class="tab-pane fade" id="dificultades">
                <div class="row g-3">
                    <div class="col-12 form-check ms-3"><input class="form-check-input" type="checkbox" name="dif_tiene_dificultad" id="dif"><label class="form-check-label" for="dif">Tiene dificultad</label></div>
                    <?php foreach (['dif_auditiva' => 'Auditiva', 'dif_visual' => 'Visual', 'dif_intelectual' => 'Intelectual', 'dif_fisico_motora' => 'Fisico motora', 'dif_psiquica_mental' => 'Psiquica mental', 'dif_autista' => 'Autista'] as $name => $label): ?>
                        <div class="col-md-4"><label class="form-label"><?= e($label) ?></label><select name="<?= e($name) ?>" class="form-select"><option value="ninguna">Ninguna</option><option value="leve">Leve</option><option value="grave">Grave</option><option value="muy_grave">Muy grave</option><option value="multiple">Multiple</option></select></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="tab-pane fade" id="abandono">
                <div class="row g-3">
                    <div class="col-md-3 form-check ms-3"><input class="form-check-input" type="checkbox" name="aba_abandono" id="abandonoCheck"><label class="form-check-label" for="abandonoCheck">Abandono</label></div>
                    <div class="col-md-4"><label class="form-label">Motivo</label><select name="aba_motivo" class="form-select"><option value="">Seleccionar</option><option value="trabajo">Trabajo</option><option value="falta_dinero">Falta de dinero</option><option value="otro">Otro</option></select></div>
                    <div class="col-md-5"><label class="form-label">Observacion</label><input name="aba_observacion" class="form-control"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
        <a href="<?= e(base_url('/estudiantes')) ?>" class="btn btn-secondary">Cancelar</a>
        <button class="btn btn-primary">Guardar Estudiante</button>
    </div>
</form>
