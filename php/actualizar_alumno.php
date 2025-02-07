<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "serviciosocial";
$password = "FtW30yNo8hQd-x/G";
$database = "login_register_db";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los datos desde la solicitud AJAX
$id_alumno = isset($_POST['id_alumno']) ? intval($_POST['id_alumno']) : 0;
$nombre_alumno = isset($_POST['nombre_alumno']) ? $_POST['nombre_alumno'] : '';
$ap_paterno = isset($_POST['ap_paterno']) ? $_POST['ap_paterno'] : '';
$ap_materno = isset($_POST['ap_materno']) ? $_POST['ap_materno'] : '';

if ($id_alumno > 0) {
    // Consulta para actualizar los datos del alumno
    $sql = "UPDATE alumnos SET nombre = ?, ap_paterno = ?, ap_materno = ? WHERE id_alumno = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nombre_alumno, $ap_paterno, $ap_materno, $id_alumno);

    if ($stmt->execute()) {
        echo "Alumno actualizado con éxito";
    } else {
        echo "Error al actualizar el alumno: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "ID de alumno no válido";
}

// Cerrar la conexión
$conn->close();
?>
