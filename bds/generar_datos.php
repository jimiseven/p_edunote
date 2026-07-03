<?php
/**
 * Generador de datos de prueba para p_edunote
 * Uso: php bds/generar_datos.php
 * Genera: bds/datos_prueba.sql
 */

$GESTION_ID = 1;
$ANIO = 2026;
$POR_CURSO = 20; // 10H + 10M

// Nombres bolivianos
$nombresH = ['Juan Jose','Carlos Alberto','Luis Fernando','Miguel Angel','Jose Carlos','Diego Alejandro','Marco Antonio','Victor Hugo','Jorge Luis','Roberto Carlos','David','Sergio','Daniel','Rodrigo','Javier','Pablo','Cristian','Adrian','Kevin','Ramiro','Grover','Ronald','Edwin','Marcelo','Gonzalo','Ivan','Oscar','Henry','Fernando','Rene','Omar','Wilfredo','Freddy','Erick','Mario','Rolando','Ruben','Raul','Julio','Jaime','Richard'];
$nombresM = ['Maria Elena','Ana Maria','Carmen Rosa','Patricia','Claudia','Elizabeth','Ruth','Gloria','Veronica','Silvia','Sandra','Martha','Liliana','Jessica','Paola','Lorena','Lidia','Andrea','Monica','Erika','Marina','Rosa','Cecilia','Marcela','Nancy','Elena','Dora','Bertha','Juana','Magdalena','Francisca','Teresa','Ximena','Gabriela','Susana','Diana','Maribel','Daniela','Alejandra','Carla','Vanessa','Marisol','Beatriz','Luz','Yolanda','Graciela','Roxana','Natalia'];
$apellidos = ['Mamani','Quispe','Flores','Condori','Garcia','Rodriguez','Martinez','Lopez','Huarachi','Choque','Gutierrez','Alvarez','Vargas','Perez','Rojas','Morales','Cruz','Ortiz','Chambi','Castro','Huanca','Ramos','Villca','Mendoza','Chavez','Torrez','Ticona','Romero','Huayta','Calle','Suxo','Apaza','Tarqui','Chipana','Mayta','Pilco','Jorge','Cusi','Aruquipa','Veliz','Mita','Yujra','Lima','Callisaya','Quenta','Layme','Pari','Canaza','Cussi','Sirpa','Ayma'];
$zonas = ['Villa Fatima','Miraflores','Sopocachi','Calacoto','Obrajes','Achumani','Villa Copacabana','El Alto','Pampahasi','Alto Obrajes'];

// Cursos base: [idBase, nivel, grado, edadMin, paralelos: A, B]
// idBase = id del primer paralelo (A), el B es idBase+1
$cursosBase = [
    [1, 'Inicial', 1, 3],
    [3, 'Inicial', 2, 4],
    [5, 'Primaria', 1, 5],
    [7, 'Primaria', 2, 6],
    [9, 'Primaria', 3, 7],
    [11, 'Primaria', 4, 8],
    [13, 'Primaria', 5, 9],
    [15, 'Primaria', 6, 10],
    [17, 'Secundaria', 1, 11],
    [19, 'Secundaria', 2, 12],
    [21, 'Secundaria', 3, 13],
    [23, 'Secundaria', 4, 14],
    [25, 'Secundaria', 5, 15],
    [27, 'Secundaria', 6, 16],
];

$materias = [2,3,4,5,6,7,8,9,10,11]; // ids de materias existentes
$paralelos = ['A','B'];

// DOCENTES fijos (id ya existentes: 4=prof1)
// Los nuevos tendran id_personal 5..10
$docentes = [
    [4, 'prof1', 'test', '52188000'],                                  // existente
    [5, 'Lic. Juan', 'Perez Garcia', '11111111'],
    [6, 'Lic. Maria', 'Lopez Mendoza', '22222222'],
    [7, 'Lic. Carlos', 'Mamani Quispe', '33333333'],
    [8, 'Prof. Rosa', 'Condori Alvarez', '44444444'],
    [9, 'Prof. Luis', 'Huarachi Chavez', '55555555'],
    [10, 'Lic. Patricia', 'Rojas Flores', '66666666'],
];

$sql = "-- DATOS DE PRUEBA GENERADOS - " . date('Y-m-d H:i') . "\n";
$sql .= "-- =============================================================\n";
$sql .= "USE edunote_p;\n";
$sql .= "START TRANSACTION;\n\n";

// === 1. LIMPIAR DATOS ===
$sql .= "-- 1. LIMPIAR DATOS\n";
$sql .= "SET FOREIGN_KEY_CHECKS = 0;\n";
$sql .= "DELETE FROM calificaciones;\n";
$sql .= "DELETE FROM docente_asignaciones;\n";
$sql .= "DELETE FROM estudiante_abandono;\n";
$sql .= "DELETE FROM estudiante_actividad_laboral;\n";
$sql .= "DELETE FROM estudiante_idioma_cultura;\n";
$sql .= "DELETE FROM estudiante_transporte;\n";
$sql .= "DELETE FROM estudiante_servicios;\n";
$sql .= "DELETE FROM estudiante_dificultades;\n";
$sql .= "DELETE FROM estudiante_salud;\n";
$sql .= "DELETE FROM estudiante_direccion;\n";
$sql .= "DELETE FROM estudiante_responsable;\n";
$sql .= "DELETE FROM responsables;\n";
$sql .= "DELETE FROM matriculas;\n";
$sql .= "DELETE FROM estudiantes WHERE id_estudiante > 13;\n";
$sql .= "DELETE FROM curso_materia;\n";
$sql .= "DELETE FROM docente_asignaciones;\n";
$sql .= "ALTER TABLE personal AUTO_INCREMENT = 5;\n";
$sql .= "ALTER TABLE usuarios AUTO_INCREMENT = 5;\n";
$sql .= "SET FOREIGN_KEY_CHECKS = 1;\n\n";

// === 2. MATERIAS POR CURSO (28 cursos x 10 materias = 280 registros) ===
$sql .= "-- 2. ASIGNAR MATERIAS A TODOS LOS CURSOS\n";
$sql .= "INSERT INTO curso_materia (id_curso, id_materia, estado) VALUES\n";
$parts = [];
for ($idCurso = 1; $idCurso <= 28; $idCurso++) {
    foreach ($materias as $idMat) {
        $parts[] = "($idCurso, $idMat, 1)";
    }
}
$sql .= implode(",\n", $parts) . ";\n\n";

// === 3. DOCENTES Y USUARIOS ===
$sql .= "-- 3. DOCENTES NUEVOS\n";
$hash = password_hash('123456', PASSWORD_DEFAULT);
// Solo los nuevos (indices 1..6, que seran personal_id 5..10)
$nuevosDoc = array_slice($docentes, 1);
foreach ($nuevosDoc as $d) {
    list($id, $nom, $ape, $ci) = $d;
    $cel = '7' . str_pad((string)(1000000 + $id), 7, '0');
    $user = strtolower(explode(' ', $nom)[1] ?? $nom); // juan, maria, carlos...
    $sql .= "INSERT INTO personal (nombres, apellidos, ci, celular, email, cargo, estado) VALUES ('$nom', '$ape', '$ci', '$cel', '{$user}@edunote.local', 'Docente', 1);\n";
}
$sql .= "\n";
$sql .= "-- USUARIOS PARA DOCENTES (pass = 123456)\n";
$sql .= "INSERT INTO usuarios (id_personal, id_rol, username, email, password_hash, estado) VALUES\n";
$userParts = [];
foreach ($nuevosDoc as $d) {
    list($id, $nom, $ape, $ci) = $d;
    $user = strtolower(explode(' ', $nom)[1] ?? $nom);
    $userParts[] = "($id, 4, '$user', '{$user}@edunote.local', '$hash', 'activo')";
}
$sql .= implode(",\n", $userParts) . ";\n\n";

// === 4. ESTUDIANTES ===
$sql .= "-- 4. ESTUDIANTES, MATRICULAS, RESPONSABLES, DATOS COMPLEMENTARIOS\n\n";

$estId = 14;
$respId = 5;
$counter = 0;

$fecha_mat = $ANIO . '-02-01';

foreach ($cursosBase as $cb) {
    list($idBase, $nivel, $grado, $edad) = $cb;

    for ($pi = 0; $pi < 2; $pi++) {
        $idCurso = $idBase + $pi;
        $paralelo = $paralelos[$pi];

        for ($i = 0; $i < $POR_CURSO; $i++) {
            $genero = ($i < 10) ? 'Masculino' : 'Femenino';
            $nombresArr = ($genero === 'Masculino') ? $nombresH : $nombresM;

            $nombre = $nombresArr[$counter % count($nombresArr)];
            $ap = $apellidos[($counter * 3) % count($apellidos)];
            $am = $apellidos[($counter * 3 + 1) % count($apellidos)];
            $rude = 'RUDE' . str_pad((string)$estId, 10, '0', STR_PAD_LEFT);
            $ciNum = 7000000 + $estId;
            $ci = $ciNum . ' LP';
            $mes = ($counter % 12) + 1;
            $dia = ($counter % 28) + 1;
            $fecha_nac = ($ANIO - $edad) . '-' . str_pad($mes, 2, '0') . '-' . str_pad($dia, 2, '0');

            // Estudiante
            $sql .= "INSERT INTO estudiantes (rude, nombres, apellido_paterno, apellido_materno, genero, ci, fecha_nacimiento, provincia_departamento) VALUES ('$rude', '$nombre', '$ap', '$am', '$genero', '$ci', '$fecha_nac', 'La Paz');\n";

            // Matricula
            $sql .= "INSERT INTO matriculas (id_estudiante, id_curso, id_gestion, fecha_matricula, estado) VALUES ($estId, $idCurso, $GESTION_ID, '$fecha_mat', 'activo');\n";

            // Responsable
            $nomResp = ($genero === 'Masculino')
                ? $nombresM[$counter % count($nombresM)] . ' ' . $apellidos[($counter * 3 + 2) % count($apellidos)]
                : $nombresH[$counter % count($nombresH)] . ' ' . $apellidos[($counter * 3 + 2) % count($apellidos)];
            $ciResp = str_pad((string)(5000000 + $respId), 8, '0') . ' LP';
            $parenGen = ($genero === 'Masculino') ? 'Madre' : 'Padre';
            $paren = ($genero === 'Masculino') ? 'Madre' : 'Padre';
            $celResp = '7' . str_pad((string)(2000000 + $respId), 7, '0');
            $sql .= "INSERT INTO responsables (nombres, apellido_paterno, apellido_materno, ci, parentesco_general, celular) VALUES ('$nomResp', '$ap', '$am', '$ciResp', '$parenGen', '$celResp');\n";
            $sql .= "INSERT INTO estudiante_responsable (id_estudiante, id_responsable, parentesco, es_principal, vive_con_estudiante, autorizado_recoger) VALUES ($estId, $respId, '$paren', 1, 1, 1);\n";

            // Direccion
            $zona = $zonas[$estId % count($zonas)];
            $sql .= "INSERT INTO estudiante_direccion (id_estudiante, departamento, provincia, municipio, localidad, zona, celular) VALUES ($estId, 'La Paz', 'Murillo', 'La Paz', 'La Paz', '$zona', '$celResp');\n";

            // Salud
            $seguro = ($counter % 5 !== 0) ? 1 : 0;
            $sql .= "INSERT INTO estudiante_salud (id_estudiante, tiene_seguro, acceso_posta, acceso_hospital) VALUES ($estId, $seguro, 1, 1);\n";

            // Dificultades (10%)
            $tiene = ($counter % 10 === 0) ? 1 : 0;
            $sql .= "INSERT INTO estudiante_dificultades (id_estudiante, tiene_dificultad, visual, auditiva) VALUES ($estId, $tiene, '" . ($tiene ? 'leve' : 'ninguna') . "', 'ninguna');\n";

            // Servicios
            $sql .= "INSERT INTO estudiante_servicios (id_estudiante, agua_caneria, bano, alcantarillado, internet, energia, recojo_basura, tipo_vivienda) VALUES ($estId, 1, 1, 1, " . (($counter % 5 !== 0) ? 1 : 0) . ", 1, " . (($counter % 3 !== 0) ? 1 : 0) . ", '" . (($counter % 2 === 0) ? 'propia' : 'alquilada') . "');\n";

            // Transporte
            $medio = ($counter % 3 !== 0) ? 'a_pie' : 'vehiculo';
            $tiempo = ($counter % 2 === 0) ? 'menos_media_hora' : 'mas_media_hora';
            $sql .= "INSERT INTO estudiante_transporte (id_estudiante, medio, tiempo_llegada) VALUES ($estId, '$medio', '$tiempo');\n";

            // Idioma
            $sql .= "INSERT INTO estudiante_idioma_cultura (id_estudiante, idioma, cultura) VALUES ($estId, 'Castellano', 'Mestizo');\n";

            // Actividad laboral (5%)
            $trabaja = ($counter % 20 === 0 && $edad >= 10) ? 1 : 0;
            $sql .= "INSERT INTO estudiante_actividad_laboral (id_estudiante, trabajo) VALUES ($estId, $trabaja);\n";

            // Abandono
            $sql .= "INSERT INTO estudiante_abandono (id_estudiante, abandono) VALUES ($estId, 0);\n";

            $estId++;
            $respId++;
            $counter++;
        }
    }
}

// === 5. ASIGNACIONES DOCENTES (round-robin) ===
$sql .= "\n-- 5. ASIGNAR DOCENTES A MATERIAS (round-robin)\n";
$docentesIds = [4, 5, 6, 7, 8, 9, 10];
$sql .= "SET @rn = 0;\n";
$sql .= "INSERT INTO docente_asignaciones (id_personal, id_curso_materia, id_gestion, estado, estado_carga)\n";
$sql .= "SELECT\n";
$sql .= "    CASE (@rn := @rn + 1) % " . count($docentesIds) . "\n";
foreach ($docentesIds as $idx => $did) {
    $sql .= "        WHEN " . ($idx) . " THEN $did\n";
}
$sql .= "    END,\n";
$sql .= "    cm.id_curso_materia,\n";
$sql .= "    $GESTION_ID,\n";
$sql .= "    'activo',\n";
$sql .= "    'FALTA'\n";
$sql .= "FROM curso_materia cm\n";
$sql .= "WHERE cm.estado = 1\n";
$sql .= "ORDER BY cm.id_curso_materia;\n";

$sql .= "\nCOMMIT;\n";

// Estadisticas
$totalEst = $counter;
$totalCursos = count($cursosBase) * 2;
$outputPath = __DIR__ . '/datos_prueba.sql';
file_put_contents($outputPath, $sql);

echo "SQL generado: $outputPath\n";
echo "Total estudiantes: $totalEst\n";
echo "Total cursos: $totalCursos\n";
echo "Total registros (aprox): " . ($totalEst * 11 + $totalCursos * 10 + 6 + 6 + $totalCursos * 10) . "\n";
echo "Archivo: " . number_format(filesize($outputPath)) . " bytes\n";
