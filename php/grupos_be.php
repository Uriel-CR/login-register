<?php
include 'conexion_be.php';

// Verificar si el formulario ha sido enviado
if (isset($_POST['register'])) {
    $nombre_grupo = $_POST['nombre_grupo'];
    $materias = [
        $_POST['materia1'],
        $_POST['materia2'],
        $_POST['materia3'],
        $_POST['materia4'],
        $_POST['materia5'],
        $_POST['materia6']
    ];

    // Insertar el grupo en la base de datos
    $insertar_grupo = "INSERT INTO grupos (nombre_grupo, id_materia1, id_materia2, id_materia3, id_materia4, id_materia5, id_materia6) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conexion, $insertar_grupo);

    // Asegurarse de que todos los campos de materias están definidos
    $materias = array_pad($materias, 6, null); // Asegurarse de tener 6 valores (null si no hay selección)
    mysqli_stmt_bind_param($stmt, "siiiiii", $nombre_grupo, $materias[0], $materias[1], $materias[2], $materias[3], $materias[4], $materias[5]);

    if (mysqli_stmt_execute($stmt)) {
        echo'
        <script>
        alert("Grupo registrado correctamente");
        window.location= "grupos.php";
        </script>
        ';
    } else {
        echo "Error al registrar el grupo: " . mysqli_error($conexion);
    }

    mysqli_stmt_close($stmt);
}

// Cerrar la conexión a la base de datos
mysqli_close($conexion);
?>
