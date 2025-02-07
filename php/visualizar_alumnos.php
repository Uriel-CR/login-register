<?php
// Verificar si se recibió el ID del grupo
if (isset($_GET['id_grupo'])) {
    $id_grupo = $_GET['id_grupo'];

    // Configuración de la conexión a la base de datos
    $servername = "localhost"; // Cambia esto por tu servidor de base de datos
$username = "serviciosocial"; // Cambia esto por tu nombre de usuario de la base de datos
$password = "FtW30yNo8hQd-x/G"; // Cambia esto por tu contraseña de la base de datos
$database = "login_register_db"; // Cambia esto por el nombre de tu base de datos

    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $database);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("La conexión falló: " . $conn->connect_error);
    }

    // Consulta SQL para obtener los alumnos del grupo específico
    $sql_alumnos = "SELECT * FROM alumnos WHERE id_grupo = $id_grupo";

    // Ejecutar la consulta para obtener los alumnos del grupo específico
    $result_alumnos = $conn->query($sql_alumnos);

    // Verificar si la consulta arrojó resultados
    if ($result_alumnos->num_rows > 0) {
        // Mostrar los resultados de la consulta
        echo "<h2>Alumnos del Grupo $id_grupo</h2>";
        echo "<table>";
        echo "<tr><th>ID Alumno</th><th>Matrícula</th><th>Nombre</th><th>Apellido Paterno</th><th>Apellido Materno</th></tr>";
        // Recorrer los resultados y mostrar cada alumno
        while ($row_alumno = $result_alumnos->fetch_assoc()) {
            echo "<tr><td>" . $row_alumno["id_alumno"] . "</td><td>" . $row_alumno["matricula"] . "</td><td>" . $row_alumno["nombre"] . "</td><td>" . $row_alumno["ap_paterno"] . "</td><td>" . $row_alumno["ap_materno"] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "No se encontraron alumnos en el grupo $id_grupo.";
    }

    // Cerrar la conexión
    $conn->close();
} else {
    // Si no se recibió el ID del grupo, mostrar un mensaje de error
    echo "Error: No se recibió el ID del grupo.";
}
?>
