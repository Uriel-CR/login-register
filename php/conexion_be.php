<?php 

$conexion = mysqli_connect("localhost", "serviciosocial", "FtW30yNo8hQd-x/G","login_register_db");

if (!$conexion) {
    error_log("Error de conexión a la base de datos: " . mysqli_connect_error());
    // Puedes redirigir a una página de error o mostrar un mensaje genérico
    die('Error de conexión a la base de datos. Por favor, inténtelo más tarde.');
}
?>