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

// Obtener datos del formulario
$alumnos = isset($_POST['alumnos']) ? $_POST['alumnos'] : [];
$nuevo_grupo = isset($_POST['nuevo_grupo']) ? intval($_POST['nuevo_grupo']) : 0;
$nuevo_periodo = isset($_POST['nuevo_periodo']) ? intval($_POST['nuevo_periodo']) : 0;

if (!empty($alumnos) && $nuevo_grupo > 0 && $nuevo_periodo > 0) {
    // Obtener las materias asociadas al nuevo grupo
    $sql = "SELECT id_materia1, id_materia2, id_materia3, id_materia4, id_materia5, id_materia6 FROM grupos WHERE id_grupo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $nuevo_grupo);
    $stmt->execute();
    $result = $stmt->get_result();
    $materias = [];

    if ($row = $result->fetch_assoc()) {
        // Recolectar IDs de materias no nulas
        foreach ($row as $key => $value) {
            if (!is_null($value)) {
                $materias[] = $value;
            }
        }
    }

    // Insertar nueva asignación en la tabla calificaciones
    $stmt = $conn->prepare("INSERT INTO calificaciones (id_alumno, id_grupo, id_periodo, id_materia) VALUES (?, ?, ?, ?)");
    
    foreach ($alumnos as $alumno_id) {
        $alumno_id = intval($alumno_id);
        foreach ($materias as $id_materia) {
            $stmt->bind_param("iiii", $alumno_id, $nuevo_grupo, $nuevo_periodo, $id_materia);
            if (!$stmt->execute()) {
                echo "Error al asignar el nuevo grupo, periodo y materia para el alumno con ID: $alumno_id. Error: " . $stmt->error;
                exit();
            }
        }
    }

    echo "Nuevo grupo, periodo y materias asignados correctamente.";
} else {
    echo "Datos inválidos.";
}

// Cerrar la conexión
$conn->close();
?>