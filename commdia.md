# Cambios implementados en p_edunote

> Documento generado por Command Code — resumen de todos los módulos replicados desde `p_edufile` al nuevo sistema `p_edunote` bajo patrón MVC.

---

## 1. Carga de Notas (docente)

**Origen:** `p_edufile/profesor/cargar_notas.php`

Archivos creados:
- `app/models/Grade.php` — Métodos: `assignmentInfo()`, `trimestres()`, `enrolledStudents()`, `existingGrades()`, `saveGrades()`.
- `app/controllers/GradeController.php` — Métodos: `index()` (mostrar formulario), `store()` (guardar calificaciones).
- `app/views/teacher/cargar_notas.php` — Vista con tabla sticky, columnas congeladas, inputs por trimestre, modal de Excel.

Archivos modificados:
- `app/views/teacher/dashboard.php` — Botón "Cargar Notas" en tarjeta del curso.
- `routes/web.php` — Rutas `GET /docente/notas` y `POST /docente/notas/store`.

Compatibilidad: usa `VALUES()` en lugar de `AS new ON DUPLICATE KEY UPDATE` para MariaDB 10.4.

---

## 2. Clases y Cursos (dashboards por nivel)

**Origen:** `p_edufile/dash_iniciales.php`, `dashboard_primaria.php`, `dashboard_secundaria.php`

Archivos modificados:
- `app/models/Dashboard.php` — Agregado método `coursesByLevel(string $nivel)` con JOIN a matriculas y estudiantes.
- `app/controllers/DashboardController.php` — Agregados métodos `inicial()`, `primaria()`, `secundaria()`.

Archivos creados:
- `app/views/dashboard/inicial.php` — Panel de cursos Inicial con tarjetas.
- `app/views/dashboard/primaria.php` — Panel con botones "Ver centralizador" y "Boletín".
- `app/views/dashboard/secundaria.php` — Panel con botones "Ver centralizador" y "Boletín".

Rutas:
- `GET /dashboard/inicial`, `/dashboard/primaria`, `/dashboard/secundaria`.

---

## 3. Centralizador / Vista de curso

**Origen:** `p_edufile/profesor/ver_curso.php` (vista unificada por curso)

Archivos creados:
- `app/models/CourseView.php` — Modelo con `courseWithRelations()` que obtiene datos del curso, materias, estudiantes y notas por trimestre activo.
- `app/controllers/CourseViewController.php` — Método `show()` que recibe `id_curso` por query string.
- `app/views/curso/ver.php` — Vista responsive con cabecera del curso, resumen de inscritos y tabla estudiantes × materias con notas por trimestre.

Ruta: `GET /ver-curso?id_curso=X`.

---

## 4. Boletín de notas

**Origen:** `p_edufile/admin/boletin_notas.php`

Archivos creados:
- `app/models/Report.php` — Modelo con `generateReportData()` que obtiene estudiante, materias, notas de todos los trimestres, promedios y nota final literal.
- `app/controllers/ReportController.php` — Método `boletin()` que recibe `id_estudiante` por query string.
- `app/views/boletin/index.php` — Vista del boletín con tabla notas por materia/trimestre, promedios y observaciones.

Ruta: `GET /boletin?id_estudiante=X`.

---

## 5. Módulo de Reportes (constructor dinámico)

**Origen:** `p_edufile/admin/reportes.php`, `reportes_generados.php`, `reportes_ajax.php`

Archivos creados:
- `app/models/ReportBuilder.php` — Modelo completo con:
  - `buildSql()` — Constructor SQL dinámico para `info_estudiantil` con 17 campos seleccionables, joins condicionales, filtros multi-select, rangos de edad.
  - `save()`, `find()`, `delete()`, `all()` — CRUD contra `reportes_guardados`, `reportes_columnas`, `reportes_filtros`.
  - `getFilterableFields()`, `getColumnAliases()` — Metadatos para la UI.
- `app/controllers/ReportBuilderController.php` — Métodos: `index()` (lista), `constructor()` (GET mostrar + POST generar/guardar), `ver()`, `delete()`.
- `app/views/reportes/index.php` — Lista de reportes guardados con botones Ver/Editar/Eliminar.
- `app/views/reportes/constructor.php` — Interfaz completa: panel izquierdo de filtros (checkboxes multi-nivel, selects, rangos de edad, CI/RUDE), panel derecho de columnas seleccionables por click, formulario de guardado, tabla de resultados ordenable.
- `app/views/reportes/ver.php` — Visualización de reporte guardado con tabla ordenable por columna.

Rutas:
- `GET /reportes` / `GET /reportes/constructor` / `POST /reportes/constructor` / `GET /reportes/ver` / `GET /reportes/eliminar`

---

## 6. Sidebar (acordeón dinámico en MVC)

**Origen:** `p_edufile/includes/sidebar.php`

Archivos creados:
- `app/Helpers/SidebarHelper.php` — Helper que construye el menú por rol, detecta link activo por ruta exacta, determina sección del acordeón a abrir, provee íconos SVG inline (Feather).
- `app/views/partials/sidebar.php` — Vista parcial que renderiza el sidebar con los datos del helper.

Archivos modificados:
- `app/views/layouts/main.php` — Incluye sidebar partial + modal de confirmación de logout.
- `app/helpers/functions.php` — Agregada función `view_partial()`.
- `public/assets/css/app.css` — Sidebar rediseñado con layout flex, scroll interno, animación de acordeón y flecha giratoria.

Comportamiento del acordeón: click en título cierra todos y abre solo el clickeado (toggle). Al cargar la página se abre la sección del link activo. Si no hay match, se abre la primera por defecto.

---

## Resumen de archivos

### Creados (18)
| Archivo | Módulo |
|---|---|
| `app/models/Grade.php` | Carga de notas |
| `app/controllers/GradeController.php` | Carga de notas |
| `app/views/teacher/cargar_notas.php` | Carga de notas |
| `app/views/dashboard/inicial.php` | Clases y cursos |
| `app/views/dashboard/primaria.php` | Clases y cursos |
| `app/views/dashboard/secundaria.php` | Clases y cursos |
| `app/models/CourseView.php` | Centralizador |
| `app/controllers/CourseViewController.php` | Centralizador |
| `app/views/curso/ver.php` | Centralizador |
| `app/models/Report.php` | Boletín |
| `app/controllers/ReportController.php` | Boletín |
| `app/views/boletin/index.php` | Boletín |
| `app/models/ReportBuilder.php` | Reportes |
| `app/controllers/ReportBuilderController.php` | Reportes |
| `app/views/reportes/index.php` | Reportes |
| `app/views/reportes/constructor.php` | Reportes |
| `app/views/reportes/ver.php` | Reportes |
| `app/Helpers/SidebarHelper.php` | Sidebar |
| `app/views/partials/sidebar.php` | Sidebar |

### Modificados (7)
| Archivo | Cambio |
|---|---|
| `routes/web.php` | +15 rutas nuevas |
| `app/views/layouts/main.php` | Sidebar partial + modal logout |
| `app/helpers/functions.php` | Función `view_partial()` |
| `public/assets/css/app.css` | Sidebar rediseñado + logout modal |
| `app/views/teacher/dashboard.php` | Botón "Cargar Notas" |
| `app/models/Dashboard.php` | Método `coursesByLevel()` |
| `app/controllers/DashboardController.php` | Métodos `inicial()`, `primaria()`, `secundaria()` |
