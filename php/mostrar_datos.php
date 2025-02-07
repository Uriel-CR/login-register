<?php

header('Content-Type: text/html; charset=UTF-8');
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

// Consulta para obtener los datos
$sql = "SELECT clave_materia, nombre, hrs_teoricas, hrs_practicas, creditos FROM materias";
$result = $conn->query($sql);

// Mostrar los datos en formato HTML
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['clave_materia']}</td>
                <td>{$row['nombre']}</td>
                <td>{$row['hrs_teoricas']}</td>
                <td>{$row['hrs_practicas']}</td>
                <td>{$row['creditos']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='5'>No se encontraron datos</td></tr>";
}

$conn->close();
?>