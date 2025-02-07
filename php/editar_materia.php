<?php
include 'conexion_be.php';

// Obtener el ID del grupo y la materia desde la URL
$id_grupo = isset($_GET['id_grupo']) ? intval($_GET['id_grupo']) : 0;
$materia_id = isset($_GET['materia_id']) ? intval($_GET['materia_id']) : 0;

if ($id_grupo === 0 || $materia_id === 0) {
    die('ID de grupo o materia inválido');
}

// Obtener la información actual de la materia
$query = "SELECT nombre FROM materias WHERE id_materia = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param('i', $materia_id);
$stmt->execute();
$result = $stmt->get_result();
$materia = $result->fetch_assoc();
$stmt->close();

if (!$materia) {
    die('Materia no encontrada');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Materia</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .logo {
            
            font-size: 24px;
            font-weight: bold;
            position: fixed;
            left: 0;
            margin: 10px;
        } 
    </style>
</head>
<body>
<h1>Editar Nombre Materia</h1>

<form method="POST" action="editar_materia_be.php">
    <input type="hidden" name="id_grupo" value="<?php echo htmlspecialchars($id_grupo); ?>">
    <input type="hidden" name="materia_id" value="<?php echo htmlspecialchars($materia_id); ?>">
    <label for="nombre_materia">Nombre de la Materia:</label>
    <input type="text" id="nombre_materia" name="nombre_materia" value="<?php echo htmlspecialchars($materia['nombre']); ?>" required>
    <input class="boton" type="submit" value="Actualizar Materia">
</form>

</body>
</html>
