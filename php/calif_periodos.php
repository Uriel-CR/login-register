<?php
include 'conexion_be.php';

$id_grupo = ''; // Define el id_grupo desde donde lo obtienes
$id_materia = ''; // Define el id_materia desde donde lo obtienes

// Verificar si se enviaron datos por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_grupo = $_POST['grupo'];
    $id_materia = $_POST['materia'];
}

// Consulta para obtener los datos de los alumnos y sus calificaciones
$consulta_alumnos = "
    SELECT a.id_alumno, a.matricula, a.ap_paterno, a.ap_materno, a.nombre,
           c.parcial_1, c.parcial_2, c.parcial_3, c.promedio, c.segunda_oportunidad, c.calif_final,
           p.periodo as nombre_periodo
    FROM alumnos a 
    JOIN calificaciones c ON a.id_alumno = c.id_alumno 
    LEFT JOIN periodos p ON c.id_periodo = p.id_periodo
    WHERE c.id_grupo = '$id_grupo' AND c.id_materia = '$id_materia'
    ORDER BY a.ap_paterno, a.ap_materno, a.nombre, p.periodo";
$resultado_alumnos = mysqli_query($conexion, $consulta_alumnos);

// Cerrar conexión después de la consulta
mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificaciones de Alumnos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        h2 {
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .button-container {
            margin-top: 20px;
        }
        .button-container a {
            text-decoration: none;
        }
        .button-container a button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .button-container a button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<?php
if ($resultado_alumnos && mysqli_num_rows($resultado_alumnos) > 0) {
    echo '<h2>Calificaciones de Alumnos</h2>';
    echo '<table>';
    echo '<thead><tr><th>Matrícula</th><th>Apellido Paterno</th><th>Apellido Materno</th><th>Nombre</th><th>Parcial 1</th><th>Parcial 2</th><th>Parcial 3</th><th>Promedio</th><th>Segunda Oportunidad</th><th>Calificación Final</th><th>Periodo</th></tr></thead>';
    echo '<tbody>';
    while ($alumno = mysqli_fetch_assoc($resultado_alumnos)) {
        echo '<tr>';
        echo '<td>'.$alumno['matricula'].'</td>';
        echo '<td>'.$alumno['ap_paterno'].'</td>';
        echo '<td>'.$alumno['ap_materno'].'</td>';
        echo '<td>'.$alumno['nombre'].'</td>';
        echo '<td>'.$alumno['parcial_1'].'</td>';
        echo '<td>'.$alumno['parcial_2'].'</td>';
        echo '<td>'.$alumno['parcial_3'].'</td>';
        echo '<td>'.$alumno['promedio'].'</td>';
        echo '<td>'.$alumno['segunda_oportunidad'].'</td>';
        echo '<td>'.$alumno['calif_final'].'</td>';
        echo '<td>'.$alumno['periodo'].'</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
} else {
    echo '<p>No se encontraron alumnos con calificaciones para el grupo y materia seleccionados.</p>';
}
?>

<div class="button-container">
    <a href="asignacion_grupo.php"><button>Regresar</button></a>
</div>

</body>
</html>
