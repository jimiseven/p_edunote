# EduNote P

Nuevo sistema administrativo educativo basado en el sistema funcional `p_edufile`, pero reorganizado con patron MVC.

## Base De Datos

La estructura inicial esta en:

```text
bds/estructura_edunote_p.txt
```

Base propuesta:

```text
edunote_p
```

Este primer SQL solo contiene estructura. Los datos iniciales se agregaran en otro archivo SQL.

Datos iniciales:

```text
bds/datos_iniciales_edunote_p.txt
```

## Acceso Local

URL:

```text
http://localhost/p_edunote/public/login
```

Usuario inicial de desarrollo:

```text
usuario: admin
clave: admin123
```

Cambiar esta clave antes de usar el sistema fuera del entorno local.

Rutas iniciales:

```text
/login
/dashboard
/usuarios
/usuarios/create
```

## Estructura Inicial

```text
app/controllers
app/models
app/views
app/middlewares
app/helpers
app/services
config
public
routes
storage
bds
```

## Decisiones Tecnicas

- MVC como estructura principal.
- Trimestres como periodos academicos.
- Roles iniciales: Administrador, Director, Secretaria y Docente.
- Estudiantes con multiples responsables.
- Matriculas por gestion para conservar historial academico.
- Calificaciones ligadas a matricula, materia, trimestre y asignacion docente.
- Auditoria y sesiones preparadas desde la base de datos.
- Reportes guardados, filtros, columnas y generaciones separados.
