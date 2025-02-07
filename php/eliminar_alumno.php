<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "serviciosocial";
$password = "FtW30yNo8hQd-x/G";
$dbname = "login_register_db";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos enviados
$id_alumno = isset($_POST['id_alumno']) ? intval($_POST['id_alumno']) : 0;
$id_grupo = isset($_POST['id_grupo']) ? intval($_POST['id_grupo']) : 0;
$id_periodo = isset($_POST['id_periodo']) ? intval($_POST['id_periodo']) : 0;

// Verificar si el alumno, grupo y periodo son válidos
if ($id_alumno > 0 && $id_grupo > 0 && $id_periodo > 0) {
    // Eliminar al alumno del grupo específico en el periodo especificado
    $sql = "DELETE FROM calificaciones WHERE id_alumno = ? AND id_grupo = ? AND id_periodo = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    $stmt->bind_param("iii", $id_alumno, $id_grupo, $id_periodo);

    if ($stmt->execute() === false) {
        die("Error en la ejecución de la consulta: " . $stmt->error);
    }

    echo "Alumno eliminado del grupo con éxito.";
    $stmt->close();
} else {
    echo "Datos inválidos para la eliminación.";
}

// Cerrar la conexión
$conn->close();
?>
