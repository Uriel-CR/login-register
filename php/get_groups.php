<?php
// Configuración de la conexión a la base de datos
$servername = "localhost"; 
$username = "serviciosocial"; 
$password = "FtW30yNo8hQd-x/G"; 
$database = "login_register_db"; 

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$periodo = $_POST['periodo'];

// Consulta para obtener los grupos para el período seleccionado
$sql_groups = "
    SELECT DISTINCT g.id_grupo, g.nombre_grupo 
    FROM calificaciones c 
    JOIN grupos g ON c.id_grupo = g.id_grupo
    WHERE c.id_periodo = ?
    ORDER BY g.nombre_grupo
";

$stmt = $conn->prepare($sql_groups);
$stmt->bind_param("i", $periodo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id_grupo = $row["id_grupo"];
        $nombre_grupo = $row["nombre_grupo"];
        echo "<option value='$id_grupo'>$nombre_grupo</option>";
    }
} else {
    echo "<option value=''>No hay grupos disponibles</option>";
}

$stmt->close();
$conn->close();
?>
