-- =============================================================
-- ACTUALIZAR DATOS EXISTENTES A COCHABAMBA/QUILLACOLLO
-- Para: estudiante_direccion, estudiante_idioma_cultura,
--       estudiantes (provincia, CI, pais)
-- =============================================================
USE edunote_p;
START TRANSACTION;

-- 1. ACTUALIZAR estudiantes: provincia, pais, CI
UPDATE estudiantes
SET provincia_departamento = 'Cochabamba',
    pais = 'Bolivia',
    ci = CONCAT(SUBSTRING_INDEX(ci, ' ', 1), ' CB')
WHERE id_estudiante >= 14 AND deleted_at IS NULL;

-- 2. ACTUALIZAR direcciones con datos reales de Quillacollo
--    Distribuir estudiantes en distintas zonas del municipio

-- 2a. Inicial (cursos 1-4, id_estudiante 14-93) -> Quillacollo centro
UPDATE estudiante_direccion ed
INNER JOIN estudiantes e ON e.id_estudiante = ed.id_estudiante
INNER JOIN matriculas m ON m.id_estudiante = e.id_estudiante AND m.estado = 'activo'
INNER JOIN cursos c ON c.id_curso = m.id_curso
SET ed.departamento = 'Cochabamba',
    ed.provincia = 'Quillacollo',
    ed.municipio = 'Quillacollo',
    ed.localidad = 'Quillacollo',
    ed.zona = CASE (e.id_estudiante % 8)
        WHEN 0 THEN 'Central'
        WHEN 1 THEN 'Litoral'
        WHEN 2 THEN 'Villa Busch'
        WHEN 3 THEN 'San José'
        WHEN 4 THEN 'Potrero'
        WHEN 5 THEN 'El Paso'
        WHEN 6 THEN 'Villa Oruro'
        ELSE 'La Loma'
    END
WHERE c.nivel = 'Inicial' AND e.id_estudiante >= 14;

-- 2b. Primaria (cursos 5-16) -> dispersos entre Quillacollo centro, Vinto, Sipe Sipe
UPDATE estudiante_direccion ed
INNER JOIN estudiantes e ON e.id_estudiante = ed.id_estudiante
INNER JOIN matriculas m ON m.id_estudiante = e.id_estudiante AND m.estado = 'activo'
INNER JOIN cursos c ON c.id_curso = m.id_curso
SET ed.departamento = 'Cochabamba',
    ed.provincia = 'Quillacollo',
    ed.municipio = CASE (e.id_estudiante % 3)
        WHEN 0 THEN 'Quillacollo'
        WHEN 1 THEN 'Vinto'
        ELSE 'Sipe Sipe'
    END,
    ed.localidad = CASE (e.id_estudiante % 3)
        WHEN 0 THEN 'Quillacollo'
        WHEN 1 THEN 'Vinto'
        ELSE 'Sipe Sipe'
    END,
    ed.zona = CASE (e.id_estudiante % 10)
        WHEN 0 THEN 'Central'
        WHEN 1 THEN 'Nueva Esperanza'
        WHEN 2 THEN 'San Miguel'
        WHEN 3 THEN 'Villa Carmen'
        WHEN 4 THEN 'Andrés Ibañez'
        WHEN 5 THEN 'Santa Rosa'
        WHEN 6 THEN 'Linde'
        WHEN 7 THEN 'Rosedal'
        WHEN 8 THEN 'San Sebastián'
        ELSE 'Pucarita'
    END
WHERE c.nivel = 'Primaria' AND e.id_estudiante >= 14;

-- 2c. Secundaria (cursos 17-28) -> Quillacollo + Tiquipaya + Colcapirhua
UPDATE estudiante_direccion ed
INNER JOIN estudiantes e ON e.id_estudiante = ed.id_estudiante
INNER JOIN matriculas m ON m.id_estudiante = e.id_estudiante AND m.estado = 'activo'
INNER JOIN cursos c ON c.id_curso = m.id_curso
SET ed.departamento = 'Cochabamba',
    ed.provincia = 'Quillacollo',
    ed.municipio = CASE (e.id_estudiante % 4)
        WHEN 0 THEN 'Quillacollo'
        WHEN 1 THEN 'Tiquipaya'
        WHEN 2 THEN 'Colcapirhua'
        ELSE 'Vinto'
    END,
    ed.localidad = CASE (e.id_estudiante % 4)
        WHEN 0 THEN 'Quillacollo'
        WHEN 1 THEN 'Tiquipaya'
        WHEN 2 THEN 'Colcapirhua'
        ELSE 'Vinto'
    END,
    ed.zona = CASE (e.id_estudiante % 12)
        WHEN 0 THEN 'Central'
        WHEN 1 THEN 'San José'
        WHEN 2 THEN 'Villa Busch'
        WHEN 3 THEN 'Molino'
        WHEN 4 THEN 'Pajcha'
        WHEN 5 THEN 'Tunas'
        WHEN 6 THEN 'Lindoro'
        WHEN 7 THEN 'Payacollo'
        WHEN 8 THEN 'Titiri'
        WHEN 9 THEN 'Tacopaya'
        WHEN 10 THEN 'Chilimarca'
        ELSE 'Tiquipaya Centro'
    END
WHERE c.nivel = 'Secundaria' AND e.id_estudiante >= 14;

-- 3. ACTUALIZAR idioma/cultura (Cochabamba es región quechua)
UPDATE estudiante_idioma_cultura
SET idioma = CASE WHEN id_estudiante % 3 = 0 THEN 'Quechua' ELSE 'Castellano' END,
    cultura = CASE WHEN id_estudiante % 3 = 0 THEN 'Quechua' ELSE 'Mestizo' END
WHERE id_estudiante >= 14;

-- 4. ACTUALIZAR responsables: teléfonos con código 4 (Cochabamba)
UPDATE responsables
SET celular = CONCAT('4', SUBSTRING(celular, 2))
WHERE id_responsable >= 5;

-- 5. ACTUALIZAR celular en direcciones
UPDATE estudiante_direccion
SET celular = CONCAT('4', SUBSTRING(celular, 2))
WHERE id_estudiante >= 14;

COMMIT;
