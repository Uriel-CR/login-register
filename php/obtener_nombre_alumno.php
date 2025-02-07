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

// Obtener el ID del alumno desde la solicitud AJAX
$id_alumno = isset($_POST['id_alumno']) ? intval($_POST['id_alumno']) : 0;

if ($id_alumno > 0) {
    // Consulta para extraer los datos del alumno
    $sql = "SELECT id_alumno, nombre, ap_paterno, ap_materno FROM alumnos WHERE id_alumno = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_alumno);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'Alumno no encontrado']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'ID de alumno no válido']);
}

// Cerrar la conexión
$conn->close();
?>
