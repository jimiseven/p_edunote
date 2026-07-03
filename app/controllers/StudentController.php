<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Course;
use App\Models\Student;
use PDOException;

class StudentController extends Controller
{
    public function index(): void
    {
        require_any_role(['Administrador', 'Secretaria']);

        $search = trim($_GET['search'] ?? '');
        $this->view('students/index', [
            'title' => 'Listado de Estudiantes',
            'students' => Student::all($search),
            'search' => $search,
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function create(): void
    {
        require_any_role(['Administrador', 'Secretaria']);

        $this->view('students/create', [
            'title' => 'Registro de Estudiante',
            'courses' => Course::allActive(),
            'error' => flash('error'),
        ]);
    }

    public function store(): void
    {
        require_any_role(['Administrador', 'Secretaria']);
        verify_csrf();

        $data = $this->validatedData();
        if (is_string($data)) {
            flash('error', $data);
            $this->redirect('/estudiantes/create');
        }

        try {
            Student::create($data);
            flash('success', 'Estudiante y responsable registrados correctamente.');
            $this->redirect('/estudiantes');
        } catch (PDOException $exception) {
            flash('error', 'No se pudo registrar. Verifique RUDE, CI de responsable o datos duplicados.');
            $this->redirect('/estudiantes/create');
        }
    }

    public function show(): void
    {
        require_any_role(['Administrador', 'Secretaria']);

        $id = (int) ($_GET['id'] ?? 0);
        $student = Student::find($id);
        if (!$student) {
            flash('error', 'Estudiante no encontrado.');
            $this->redirect('/estudiantes');
        }

        $this->view('students/show', [
            'title' => 'Detalle de Estudiante',
            'student' => $student,
        ]);
    }

    public function edit(): void
    {
        require_any_role(['Administrador', 'Secretaria']);

        $id = (int) ($_GET['id'] ?? 0);
        $student = Student::find($id);
        if (!$student) {
            flash('error', 'Estudiante no encontrado.');
            $this->redirect('/estudiantes');
        }

        $this->view('students/edit', [
            'title' => 'Editar Estudiante',
            'student' => $student,
            'courses' => Course::allActive(),
            'error' => flash('error'),
        ]);
    }

    public function update(): void
    {
        require_any_role(['Administrador', 'Secretaria']);
        verify_csrf();

        $id = (int) ($_POST['id_estudiante'] ?? 0);
        $data = $this->validatedData();
        if (is_string($data)) {
            flash('error', $data);
            $this->redirect('/estudiantes/edit?id=' . $id);
        }

        try {
            Student::update($id, $data);
            flash('success', 'Estudiante actualizado correctamente.');
            $this->redirect('/estudiantes');
        } catch (PDOException $exception) {
            flash('error', 'No se pudo actualizar. Verifique RUDE, CI de responsable o datos duplicados.');
            $this->redirect('/estudiantes/edit?id=' . $id);
        }
    }

    public function delete(): void
    {
        require_any_role(['Administrador', 'Secretaria']);
        verify_csrf();

        $id = (int) ($_POST['id_estudiante'] ?? 0);
        Student::softDelete($id);
        flash('success', 'Estudiante retirado correctamente.');
        $this->redirect('/estudiantes');
    }

    private function validatedData(): array|string
    {
        $data = [
            'nombres' => trim($_POST['nombres'] ?? ''),
            'apellido_paterno' => trim($_POST['apellido_paterno'] ?? ''),
            'apellido_materno' => trim($_POST['apellido_materno'] ?? ''),
            'genero' => trim($_POST['genero'] ?? ''),
            'rude' => trim($_POST['rude'] ?? ''),
            'ci' => trim($_POST['ci'] ?? ''),
            'fecha_nacimiento' => trim($_POST['fecha_nacimiento'] ?? ''),
            'pais' => trim($_POST['pais'] ?? 'Bolivia'),
            'provincia_departamento' => trim($_POST['provincia_departamento'] ?? ''),
            'id_curso' => (int) ($_POST['id_curso'] ?? 0),

            'resp_nombres' => trim($_POST['resp_nombres'] ?? ''),
            'resp_apellido_paterno' => trim($_POST['resp_apellido_paterno'] ?? ''),
            'resp_apellido_materno' => trim($_POST['resp_apellido_materno'] ?? ''),
            'resp_ci' => trim($_POST['resp_ci'] ?? ''),
            'resp_fecha_nacimiento' => trim($_POST['resp_fecha_nacimiento'] ?? ''),
            'resp_parentesco' => trim($_POST['resp_parentesco'] ?? ''),
            'resp_grado_instruccion' => trim($_POST['resp_grado_instruccion'] ?? ''),
            'resp_idioma_frecuente' => trim($_POST['resp_idioma_frecuente'] ?? ''),
            'resp_ocupacion' => trim($_POST['resp_ocupacion'] ?? ''),
            'resp_celular' => trim($_POST['resp_celular'] ?? ''),
            'resp_direccion' => trim($_POST['resp_direccion'] ?? ''),
            'resp_vive_con_estudiante' => isset($_POST['resp_vive_con_estudiante']) ? 1 : 0,
            'resp_autorizado_recoger' => isset($_POST['resp_autorizado_recoger']) ? 1 : 0,

            'dir_departamento' => trim($_POST['dir_departamento'] ?? ''),
            'dir_provincia' => trim($_POST['dir_provincia'] ?? ''),
            'dir_municipio' => trim($_POST['dir_municipio'] ?? ''),
            'dir_localidad' => trim($_POST['dir_localidad'] ?? ''),
            'dir_comunidad' => trim($_POST['dir_comunidad'] ?? ''),
            'dir_zona' => trim($_POST['dir_zona'] ?? ''),
            'dir_numero_vivienda' => trim($_POST['dir_numero_vivienda'] ?? ''),
            'dir_telefono' => trim($_POST['dir_telefono'] ?? ''),
            'dir_celular' => trim($_POST['dir_celular'] ?? ''),

            'sal_tiene_seguro' => isset($_POST['sal_tiene_seguro']) ? 1 : 0,
            'sal_acceso_posta' => isset($_POST['sal_acceso_posta']) ? 1 : 0,
            'sal_acceso_centro_salud' => isset($_POST['sal_acceso_centro_salud']) ? 1 : 0,
            'sal_acceso_hospital' => isset($_POST['sal_acceso_hospital']) ? 1 : 0,
            'sal_observacion' => trim($_POST['sal_observacion'] ?? ''),

            'idi_idioma' => trim($_POST['idi_idioma'] ?? ''),
            'idi_cultura' => trim($_POST['idi_cultura'] ?? ''),

            'trans_medio' => trim($_POST['trans_medio'] ?? ''),
            'trans_tiempo_llegada' => trim($_POST['trans_tiempo_llegada'] ?? ''),

            'serv_agua_caneria' => isset($_POST['serv_agua_caneria']) ? 1 : 0,
            'serv_bano' => isset($_POST['serv_bano']) ? 1 : 0,
            'serv_alcantarillado' => isset($_POST['serv_alcantarillado']) ? 1 : 0,
            'serv_internet' => isset($_POST['serv_internet']) ? 1 : 0,
            'serv_energia' => isset($_POST['serv_energia']) ? 1 : 0,
            'serv_recojo_basura' => isset($_POST['serv_recojo_basura']) ? 1 : 0,
            'serv_tipo_vivienda' => trim($_POST['serv_tipo_vivienda'] ?? ''),

            'lab_trabajo' => isset($_POST['lab_trabajo']) ? 1 : 0,
            'lab_meses_trabajo' => isset($_POST['lab_meses_trabajo']) && is_array($_POST['lab_meses_trabajo']) ? implode(',', $_POST['lab_meses_trabajo']) : '',
            'lab_actividad' => trim($_POST['lab_actividad'] ?? ''),
            'lab_turno_manana' => isset($_POST['lab_turno_manana']) ? 1 : 0,
            'lab_turno_tarde' => isset($_POST['lab_turno_tarde']) ? 1 : 0,
            'lab_turno_noche' => isset($_POST['lab_turno_noche']) ? 1 : 0,
            'lab_frecuencia' => trim($_POST['lab_frecuencia'] ?? ''),

            'dif_tiene_dificultad' => isset($_POST['dif_tiene_dificultad']) ? 1 : 0,
            'dif_auditiva' => trim($_POST['dif_auditiva'] ?? 'ninguna'),
            'dif_visual' => trim($_POST['dif_visual'] ?? 'ninguna'),
            'dif_intelectual' => trim($_POST['dif_intelectual'] ?? 'ninguna'),
            'dif_fisico_motora' => trim($_POST['dif_fisico_motora'] ?? 'ninguna'),
            'dif_psiquica_mental' => trim($_POST['dif_psiquica_mental'] ?? 'ninguna'),
            'dif_autista' => trim($_POST['dif_autista'] ?? 'ninguna'),

            'aba_abandono' => isset($_POST['aba_abandono']) ? 1 : 0,
            'aba_motivo' => trim($_POST['aba_motivo'] ?? ''),
            'aba_observacion' => trim($_POST['aba_observacion'] ?? ''),
        ];

        if ($data['nombres'] === '' || $data['apellido_paterno'] === '' || $data['rude'] === '' || $data['ci'] === '' || $data['fecha_nacimiento'] === '' || $data['genero'] === '' || $data['id_curso'] <= 0) {
            return 'Complete todos los campos obligatorios del estudiante.';
        }

        if ($data['resp_nombres'] === '' || $data['resp_apellido_paterno'] === '' || $data['resp_ci'] === '' || $data['resp_parentesco'] === '') {
            return 'Complete todos los campos obligatorios del responsable.';
        }

        return $data;
    }
}
