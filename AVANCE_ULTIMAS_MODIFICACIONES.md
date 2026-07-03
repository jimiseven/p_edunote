# Avance de Ultimas Modificaciones

Fecha: 2026-07-03

## Contexto

Proyecto nuevo: `C:\xampp\htdocs\p_edunote`

Base de datos: `edunote_p`

Sistema base de referencia: `C:\xampp\htdocs\p_edufile`

Arquitectura actual: MVC propio en PHP.

## Modificaciones Recientes Realizadas

### 1. Validacion y endurecimiento de carga de notas

Se reviso y valido el flujo actual de carga de notas para docente.

Archivos relacionados:

- `app/controllers/GradeController.php`
- `app/models/Grade.php`
- `app/views/teacher/cargar_notas.php`

Cambios/validaciones aplicadas:

- La asignacion docente se valida contra el usuario docente logueado.
- Se evita que un docente cargue notas usando manualmente una asignacion ajena por URL o POST.
- La carga solo permite trimestres activos.
- La carga soporta varios trimestres habilitados al mismo tiempo.
- La interfaz bloquea columnas de trimestres no habilitados y deshabilita el guardado si no hay trimestres habilitados.
- Se rechazan valores no numericos para cursos que usan nota numerica.
- Se rechazan notas fuera del rango `0-100`.
- Se rechazan matriculas que no pertenecen al curso/asignacion del docente.
- Para nivel Inicial se mantiene el uso de comentarios en lugar de nota numerica.
- Al guardar una nota valida, se actualiza `estado_carga` en `docente_asignaciones`.

Pruebas realizadas:

- Login temporal con docente existente `52188000`.
- Creacion de estudiante temporal para prueba.
- Carga con nota invalida `abc`: rechazada.
- Carga en trimestre inactivo: rechazada.
- Carga con nota valida `85`: guardada correctamente.
- Carga temporal con dos trimestres habilitados: guardada correctamente.
- Intento de guardar en trimestre no habilitado: rechazado.
- Intento de guardar una matricula ajena a la asignacion: rechazado.
- Verificacion en tabla `calificaciones`.
- Verificacion de `estado_carga = CARGADO`.
- Verificacion de rutas:
  - `/docente/notas?asignacion=3`
  - `/ver-curso?curso=5`
  - `/boletin?id_curso=5`
- Limpieza de datos temporales.
- Restauracion del hash original del docente usado para prueba.

Resultado: flujo de notas validado correctamente.

### 2. Nuevo modulo Control de Trimestres

Se implemento un modulo administrativo para controlar los trimestres academicos.

Archivos creados:

- `app/controllers/AcademicPeriodController.php`
- `app/models/AcademicPeriod.php`
- `app/views/academic_periods/index.php`

Archivos modificados:

- `routes/web.php`
- `app/helpers/SidebarHelper.php`

Rutas agregadas:

- `GET /trimestres`
- `POST /trimestres/update`
- `POST /trimestres/activate`

Funcionalidad implementada:

- Listar gestiones academicas.
- Ver trimestres por gestion.
- Editar fechas de inicio y fin de cada trimestre.
- Activar un trimestre para carga de notas.
- Al habilitar o deshabilitar un trimestre, los demas trimestres de la gestion conservan su estado.
- El sidebar de Administrador ahora incluye el enlace `Trimestres` dentro de la seccion `ACADEMICO`.

Regla actual actualizada:

- Puede existir mas de un trimestre habilitado por gestion.
- Cada trimestre se habilita o deshabilita de forma independiente.
- La carga de notas solo permite guardar en los trimestres habilitados.

Validaciones realizadas:

- Validacion CSRF en acciones POST.
- Validacion de existencia de gestion.
- Validacion de existencia de trimestre.
- Validacion de fechas con formato `Y-m-d`.
- Validacion de que la fecha de inicio no sea posterior a la fecha de fin.

Pruebas realizadas:

- `php -l` en los archivos nuevos y modificados.
- `php -l` completo sobre archivos PHP del proyecto.
- Login con administrador `admin / admin123`.
- Carga de `/trimestres` con HTTP 200.
- Activacion/desactivacion individual de trimestres.
- Verificacion en base de datos de que pueden quedar varios trimestres habilitados para la misma gestion.

Resultado: modulo `Control de Trimestres` implementado y validado.

## Estado Actual de Archivos Relevantes

Archivos modificados o agregados en las ultimas tareas:

- `app/controllers/GradeController.php`
- `app/models/Grade.php`
- `app/controllers/AcademicPeriodController.php`
- `app/models/AcademicPeriod.php`
- `app/views/academic_periods/index.php`
- `app/helpers/SidebarHelper.php`
- `routes/web.php`

Nota: `GradeController.php` y `Grade.php` ya tenian cambios relacionados con seguridad y validacion de carga de notas antes de crear el modulo de trimestres.

## Estado Funcional Actual

### Funciona actualmente

- Login/logout.
- Dashboard base.
- Usuarios.
- Estudiantes.
- Cursos.
- Materias.
- Materias por curso.
- Asignacion docente.
- Dashboard docente.
- Carga de notas por docente.
- Centralizador por curso.
- Boletin.
- Reportes dinamicos.
- Control de trimestres.

### Decisiones funcionales ya respondidas

- Director: solo lectura de dashboards, centralizadores, boletines y reportes creados.
- Secretaria: mismos accesos que Administrador.
- Trimestres: pueden existir varios trimestres habilitados por gestion.
- Carga de notas: se realiza solo en trimestres habilitados.
- Nivel Inicial: solo comentarios, no nota numerica.
- Responsables: por ahora basta con un responsable principal.
- Boletin: debe existir por curso y por estudiante.
- Reportes dinamicos: son prioridad alta del proyecto.
- Anuncios: visibles para todos y administrados por Secretaria/Administrador.
- Auditoria: no se implementa por ahora.

### Validaciones recientes exitosas

- Sintaxis PHP completa del proyecto: OK.
- Ruta `/trimestres`: OK.
- Activacion/desactivacion individual de trimestres: OK.
- Multiples trimestres habilitados por gestion: OK.
- Carga de notas en multiples trimestres habilitados: OK.
- Rechazo de matricula ajena a la asignacion: OK.
- Flujo de carga de notas: OK.
- Rechazo de nota invalida: OK.
- Rechazo de carga en trimestre inactivo: OK.

## Decisiones Respondidas Para Continuar

1. Roles Director y Secretaria: Director solo lectura; Secretaria con mismos accesos que Administrador.

2. Control de Trimestres: se permite mas de un trimestre habilitado por gestion.

3. Carga de notas: la carga se realiza de acuerdo a los trimestres habilitados.

4. Nivel Inicial: solo comentarios para guardar notas.

5. Responsables: por ahora basta con un responsable principal.

6. Boletin: deben existir ambas opciones, por curso y por estudiante.

7. Reportes dinamicos: son la base mas importante del proyecto y tienen prioridad alta.

8. Anuncios/notificaciones: seran redactados por Secretaria/Administrador y visibles para todos.

9. Auditoria: no se implementa por ahora.

## Pendientes Priorizados

- Ajustar boletin por estudiante individual.
- Endurecer seguridad y permisos de reportes dinamicos.
- Implementar descarga de reportes creados para Director.
- Implementar modulo de anuncios.

## Recomendacion Tecnica Para Seguir

Antes de agregar mas modulos grandes, conviene definir permisos por rol y la regla final de cierre/modificacion de notas. Esas decisiones afectan sidebar, rutas, controladores, carga de notas, reportes y auditoria.
