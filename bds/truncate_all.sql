USE edunote_p;
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE calificaciones;
TRUNCATE TABLE docente_asignaciones;
TRUNCATE TABLE estudiante_abandono;
TRUNCATE TABLE estudiante_actividad_laboral;
TRUNCATE TABLE estudiante_idioma_cultura;
TRUNCATE TABLE estudiante_transporte;
TRUNCATE TABLE estudiante_servicios;
TRUNCATE TABLE estudiante_dificultades;
TRUNCATE TABLE estudiante_salud;
TRUNCATE TABLE estudiante_direccion;
TRUNCATE TABLE estudiante_responsable;
TRUNCATE TABLE responsables;
TRUNCATE TABLE matriculas;
TRUNCATE TABLE estudiantes;
TRUNCATE TABLE curso_materia;
SET FOREIGN_KEY_CHECKS = 1;

-- Reset auto_increment but keep existing admin/prof1
ALTER TABLE estudiantes AUTO_INCREMENT = 14;
ALTER TABLE matriculas AUTO_INCREMENT = 14;
ALTER TABLE responsables AUTO_INCREMENT = 5;
