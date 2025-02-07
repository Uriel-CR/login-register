<?php
// Incluir archivo de conexión a la base de datos
include 'conexion_be.php';

// Recoger los datos del formulario
$clave_materia = $_POST['clave_materia'];
$nombre = $_POST['nombre'];
$hrs_teoricas = $_POST['HRS_TEORICAS'];
$hrs_practicas = $_POST['HRS_PRACTICAS'];
$creditos = $_POST['creditos'];

// Validar los datos (opcional)
if (empty($clave_materia) || empty($nombre) || empty($hrs_teoricas) || empty($hrs_practicas) || empty($creditos)) {
    echo "Todos los campos son obligatorios.";
    exit;
}

// Preparar la consulta SQL para insertar los datos en la tabla materias
$query = "INSERT INTO materias (clave_materia, nombre, HRS_TEORICAS, HRS_PRACTICAS, creditos) 
          VALUES (?, ?, ?, ?, ?)";

// Preparar la sentencia
$stmt = $conexion->prepare($query);
if ($stmt === false) {
    die('Error en la preparación de la consulta: ' . $conexion->error);
}

// Vincular parámetros
$stmt->bind_param('ssiii', $clave_materia, $nombre, $hrs_teoricas, $hrs_practicas, $creditos);

// Ejecutar la sentencia
if ($stmt->execute()) {
    echo "Materia registrada con éxito.";
} else {
    echo "Error al registrar la materia: " . $stmt->error;
}

// Cerrar la sentencia y la conexión
$stmt->close();
$conexion->close();
?>
