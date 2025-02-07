<?php
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

// Verificar si se envió el formulario de registro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo_usuario = $_POST["tipo_usuario"]; // Obtener el tipo de usuario del formulario

    // Procesar según el tipo de usuario
    switch ($tipo_usuario) {
        case 'administrador':
        case 'alumno':
        case 'profesor':
            // Realizar el registro en la base de datos
            // Aquí debes agregar el código para insertar el usuario en la tabla correspondiente según el tipo de usuario
            // Por ejemplo:
            $sql = "INSERT INTO usuarios (tipo_usuario) VALUES ('$tipo_usuario')";
            if ($conn->query($sql) === TRUE) {
                echo "Usuario registrado como $tipo_usuario correctamente.";
            } else {
                echo "Error al registrar el usuario: " . $conn->error;
            }
            break;
        default:
            echo "Tipo de usuario no válido.";
            break;
    }
}

// Cerrar la conexión
$conn->close();
?>
