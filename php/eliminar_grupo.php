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
$id_grupo = isset($_POST['id_grupo']) ? intval($_POST['id_grupo']) : 0;
$id_periodo = isset($_POST['id_periodo']) ? intval($_POST['id_periodo']) : 0;

if ($id_grupo > 0 && $id_periodo > 0) {
    // Preparar la consulta para eliminar el grupo del periodo
    $sql = "DELETE FROM calificaciones WHERE id_grupo = ? AND id_periodo = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    
    $stmt->bind_param("ii", $id_grupo, $id_periodo);
    
    if ($stmt->execute() === false) {
        die("Error en la ejecución de la consulta: " . $stmt->error);
    }
    
    $stmt->close();
    echo "Grupo eliminado correctamente.";
} else {
    echo "Datos incompletos.";
}

// Cerrar la conexión
$conn->close();
?>
