
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

// Array para almacenar mensajes de advertencia
$messages = [];

// Obtener el ID del grupo y el período desde la solicitud
$id_grupo = isset($_POST['id_grupo']) ? intval($_POST['id_grupo']) : 0;
$id_periodo = isset($_POST['id_periodo']) ? intval($_POST['id_periodo']) : 0;

// Verificar que los IDs de grupo y periodo sean válidos
if ($id_grupo > 0 && $id_periodo > 0) {
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'comentario_') === 0) {
            // Extraer el id_alumno del nombre del campo
            $id_alumno = intval(substr($key, 11));
            $comentario = htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); // Asegurarse de que los caracteres especiales se manejen correctamente

            // Consultar para obtener el id_calificacion correspondiente
            $sql_select = "SELECT id_calificacion FROM calificaciones WHERE id_alumno = ? AND id_grupo = ? AND id_periodo = ?";
            $stmt_select = $conn->prepare($sql_select);
            $stmt_select->bind_param("iii", $id_alumno, $id_grupo, $id_periodo);
            if ($stmt_select->execute()) {
                $result_select = $stmt_select->get_result();

                if ($result_select->num_rows > 0) {
                    // Obtener el id_calificacion
                    $row = $result_select->fetch_assoc();
                    $id_calificacion = $row['id_calificacion'];

                    // Actualizar el comentario en la base de datos
                    $sql_update = "UPDATE calificaciones SET comentario = ? WHERE id_calificacion = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param("si", $comentario, $id_calificacion);
                    if (!$stmt_update->execute()) {
                        $messages[] = "Error al actualizar el comentario para el alumno con ID $id_alumno.";
                    }
                } else {
                    $messages[] = "No se encontró la calificación para el alumno con ID $id_alumno.";
                }
            } else {
                $messages[] = "Error al realizar la consulta para el alumno con ID $id_alumno.";
            }
        }
    }
    if (empty($messages)) {
        $messages[] = "Comentarios actualizados correctamente.";
    }
} else {
    $messages[] = "Grupo o periodo no válidos.";
}

// Mostrar los mensajes y redirigir
echo "<script>";
foreach ($messages as $message) {
    echo "alert('$message');";
}
echo "window.location.href = 'actualizar_grupoperiodo.php';";
echo "</script>";

// Cerrar la conexión
$conn->close();
?>
