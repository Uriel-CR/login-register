<?php 
include 'conexion_be.php'; // Asegúrate de que este archivo establece la conexión en la variable $conexion

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['actualizar'])) {
        // Actualizar calificaciones
        $id_grupo = $_POST['grupo'];
        $id_materia = $_POST['materia'];
        $parcial = $_POST['parcial'];
        $campo_parcial = "parcial_" . $parcial;

        foreach ($_POST['calificaciones'] as $id_alumno => $calificacion) {
            $calificacion = mysqli_real_escape_string($conexion, $calificacion);
            $query = "UPDATE calificaciones SET $campo_parcial = '$calificacion' WHERE id_alumno = '$id_alumno' AND id_grupo = '$id_grupo' AND id_materia = '$id_materia'";
            mysqli_query($conexion, $query);
        }

        $mensaje = "Calificaciones actualizadas correctamente.";
    } else {
        // Procesar selección de grupo, materia y parcial
        $id_grupo = $_POST['grupo'];
        $id_materia = $_POST['materia'];
        $parcial = $_POST['parcial'];

        $campo_parcial = "parcial_" . $parcial;
        $consulta_calificaciones = "
            SELECT c.id_alumno, c.$campo_parcial, a.matricula, CONCAT(a.ap_paterno, ' ', a.ap_materno, ' ', a.nombre) AS nombre_completo
            FROM calificaciones c
            JOIN alumnos a ON c.id_alumno = a.id_alumno
            WHERE c.id_grupo = '$id_grupo' AND c.id_materia = '$id_materia'
        ";
        $resultado_calificaciones = mysqli_query($conexion, $consulta_calificaciones);

        $calificaciones = [];
        if ($resultado_calificaciones && mysqli_num_rows($resultado_calificaciones) > 0) {
            while ($calificacion = mysqli_fetch_assoc($resultado_calificaciones)) {
                $calificaciones[] = $calificacion;
            }
        }
    }
}

mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados del Parcial</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('https://posgrados.tesi.org.mx/img/bg-img/gallery2.jpg'); /* Cambia esta ruta a la ubicación de tu imagen */
            background-size: cover;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 70%;
            margin: 100px auto;
            background-color: rgba(255, 255, 255, 0.9); /* Fondo semitransparente */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        input[type="text"] {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Calificaciones del Parcial <?php echo htmlspecialchars($parcial); ?></h1>
        <?php if (isset($mensaje)): ?>
            <p><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>
        <?php if (!empty($calificaciones)): ?>
            <form method="POST" action="procesar_grupos.php">
                <input type="hidden" name="grupo" value="<?php echo htmlspecialchars($id_grupo); ?>">
                <input type="hidden" name="materia" value="<?php echo htmlspecialchars($id_materia); ?>">
                <input type="hidden" name="parcial" value="<?php echo htmlspecialchars($parcial); ?>">
                <input type="hidden" name="actualizar" value="1">
                <table>
                    <tr>
                        <th>Matrícula</th>
                        <th>Nombre Completo</th>
                        <th>Calificación</th>
                    </tr>
                    <?php foreach ($calificaciones as $calificacion): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($calificacion['matricula']); ?></td>
                            <td><?php echo htmlspecialchars($calificacion['nombre_completo']); ?></td>
                            <td>
                                <input type="text" name="calificaciones[<?php echo $calificacion['id_alumno']; ?>]" value="<?php echo htmlspecialchars($calificacion[$campo_parcial]); ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <input type="submit" value="Actualizar Calificaciones">
            </form>
        <?php else: ?>
            <p>No se encontraron calificaciones para el grupo y materia seleccionados.</p>
        <?php endif; ?>
    </div>
</body>
</html>
