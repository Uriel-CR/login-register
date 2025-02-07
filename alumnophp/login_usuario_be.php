<?php 

// Incluir el archivo de conexión a la base de datos
include '../php/conexion_be.php';

// Asegúrate de que el archivo 'conexion.php' tenga la conexión establecida

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $matricula = $_POST['matricula'];
    $contrasena = $_POST['contrasena'];

    // Consulta para verificar las credenciales del usuario
    $consulta = "SELECT * FROM alumnos_usu WHERE matricula = ? AND contrasena = ?";
    
    // Preparar la consulta
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param("ss", $matricula, $contrasena);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Usuario autenticado correctamente, iniciar sesión o establecer variables de sesión
        session_start();
        $_SESSION['matricula'] = $matricula; // Guardar la matrícula en la sesión
        
        // Redirigir al script que genera el PDF de calificaciones
        header("location: ../alumnophp/pdf/PruebaV.php");
        exit();
    } else {
        // Usuario no encontrado o credenciales incorrectas, manejar el error
        echo "Matrícula o contraseña incorrecta. <a href='../index.html'>Volver al inicio</a>";
    }

    // Cerrar la consulta preparada
    $stmt->close();
}

// Cerrar la conexión a la base de datos al finalizar
$conexion->close();
?>
