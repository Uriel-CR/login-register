<?php
// Verificar si el formulario ha sido enviado
if (isset($_POST['register'])) {
    // Obtener los valores del formulario
    $nombre_grupo = $_POST['nombre_grupo'];
    $materias = [
        $_POST['materia1'],
        $_POST['materia2'],
        $_POST['materia3'],
        $_POST['materia4'],
        $_POST['materia5'],
        $_POST['materia6']
    ];
    $profesor_id = $_POST['profesor'];
    $periodo_id = $_POST['periodo'];
    $salon_id = $_POST['salon'];

    // Validaciones (como ya lo tenías)
    if (!isset($nombre_grupo) || !preg_match('/^\d{4}$/', $nombre_grupo)) {
        die("Error: El número de grupo debe ser un entero de 4 dígitos.");
    }
    $d1 = substr($nombre_grupo, 0, 1);
    $d2 = substr($nombre_grupo, 1, 1);
    $d3 = substr($nombre_grupo, 2, 1);
    $d4 = substr($nombre_grupo, 3, 1);

    // Consultas para obtener ids de carrera, semestre, turno

    // Insertar el grupo
    $insertar_grupo = "INSERT INTO grupos (id_carrera, id_semestre, id_turno, clave_grupo, id_profesor, id_periodos, id_salones) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conexion, $insertar_grupo);
    mysqli_stmt_bind_param($stmt, "iiiiiii", $id_carrera, $id_semestre, $id_turno, $d4, $profesor_id, $periodo_id, $salon_id);

    if (mysqli_stmt_execute($stmt)) {
        $id_grupo = mysqli_insert_id($conexion);

        // Insertar las materias
        foreach ($materias as $materia) {
            if (!empty($materia)) {
                $insertar_materia = "INSERT INTO grupo_materia (id_grupo, id_materia) VALUES (?, ?)";
                $stmt_materia = mysqli_prepare($conexion, $insertar_materia);
                mysqli_stmt_bind_param($stmt_materia, "ii", $id_grupo, $materia);
                mysqli_stmt_execute($stmt_materia);
                mysqli_stmt_close($stmt_materia);
            }
        }

        echo '
        <script>
        alert("Grupo y materias registrados correctamente");
        window.location= "grupos.php";
        </script>
        ';
    } else {
        echo "Error al registrar el grupo: " . mysqli_error($conexion);
    }

    mysqli_stmt_close($stmt);
}

// Cerrar la conexión
mysqli_close($conexion);
