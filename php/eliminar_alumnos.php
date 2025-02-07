<?php
include('conexion.php'); // Asegúrate de incluir tu archivo de conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['alumnos']) && is_array($_POST['alumnos'])) {
        $alumnos = $_POST['alumnos'];
        $placeholders = implode(',', array_fill(0, count($alumnos), '?'));
        
        $sql = "DELETE FROM alumnos WHERE id_alumno IN ($placeholders)";
        $stmt = $pdo->prepare($sql);
        
        try {
            $stmt->execute($alumnos);
            echo 'Alumnos eliminados exitosamente.';
        } catch (Exception $e) {
            echo 'Error al eliminar los alumnos: ' . $e->getMessage();
        }
    } else {
        echo 'No se recibieron IDs de alumnos para eliminar.';
    }
} else {
    echo 'Método de solicitud no permitido.';
}
?>
