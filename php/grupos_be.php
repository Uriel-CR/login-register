<?php
include 'conexion_be.php';
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
    $consulta_carrera = "SELECT id_carrera FROM carreras WHERE clave_carrera = $d1";
    $resultado_carrera = mysqli_query($conexion, $consulta_carrera);
    $fila_carrera = mysqli_fetch_assoc($resultado_carrera);
    $id_carrera = $fila_carrera['id_carrera'] ?? null;

    $consulta_semestre = "SELECT id_semestre FROM semestres WHERE clave_semestre = $d2";
    $resultado_semestre = mysqli_query($conexion, $consulta_semestre);
    $fila_semestre = mysqli_fetch_assoc($resultado_semestre);
    $id_semestre = $fila_semestre['id_semestre'] ?? null;

    $consulta_turno = "SELECT id_turno FROM turnos WHERE clave_turno = $d3";
    $resultado_turno = mysqli_query($conexion, $consulta_turno);
    $fila_turno = mysqli_fetch_assoc($resultado_turno);
    $id_turno = $fila_turno['id_turno'] ?? null;

    // Verificar que se obtuvieron los valores
    if (!$id_carrera || !$id_semestre || !$id_turno) {
        die("Error: No se encontraron valores para carrera, semestre o turno.");
    }

    // Insertar el grupo
    $insertar_grupo = "INSERT INTO grupos (id_carrera, id_semestre, id_turno, clave_grupo, id_profesor, id_periodo, id_salon) 
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
