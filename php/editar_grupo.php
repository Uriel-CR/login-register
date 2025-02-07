<?php
include 'conexion_be.php';

// Verificar si se ha enviado un ID de grupo
if (!isset($_GET['id_grupo']) || !is_numeric($_GET['id_grupo'])) {
    die('ID de grupo inválido.');
}

$id_grupo = intval($_GET['id_grupo']);

// Obtener la información del grupo y las materias
$consulta_grupo = "
    SELECT g.nombre_grupo,
           g.id_materia1, g.id_materia2, g.id_materia3, g.id_materia4, g.id_materia5, g.id_materia6,
           m1.nombre AS materia1, m1.clave_materia AS clave_materia1, m1.HRS_TEORICAS AS hrs_teoricas1, m1.HRS_PRACTICAS AS hrs_practicas1, m1.creditos AS creditos1,
           m2.nombre AS materia2, m2.clave_materia AS clave_materia2, m2.HRS_TEORICAS AS hrs_teoricas2, m2.HRS_PRACTICAS AS hrs_practicas2, m2.creditos AS creditos2,
           m3.nombre AS materia3, m3.clave_materia AS clave_materia3, m3.HRS_TEORICAS AS hrs_teoricas3, m3.HRS_PRACTICAS AS hrs_practicas3, m3.creditos AS creditos3,
           m4.nombre AS materia4, m4.clave_materia AS clave_materia4, m4.HRS_TEORICAS AS hrs_teoricas4, m4.HRS_PRACTICAS AS hrs_practicas4, m4.creditos AS creditos4,
           m5.nombre AS materia5, m5.clave_materia AS clave_materia5, m5.HRS_TEORICAS AS hrs_teoricas5, m5.HRS_PRACTICAS AS hrs_practicas5, m5.creditos AS creditos5,
           m6.nombre AS materia6, m6.clave_materia AS clave_materia6, m6.HRS_TEORICAS AS hrs_teoricas6, m6.HRS_PRACTICAS AS hrs_practicas6, m6.creditos AS creditos6
    FROM grupos g
    LEFT JOIN materias m1 ON g.id_materia1 = m1.id_materia
    LEFT JOIN materias m2 ON g.id_materia2 = m2.id_materia
    LEFT JOIN materias m3 ON g.id_materia3 = m3.id_materia
    LEFT JOIN materias m4 ON g.id_materia4 = m4.id_materia
    LEFT JOIN materias m5 ON g.id_materia5 = m5.id_materia
    LEFT JOIN materias m6 ON g.id_materia6 = m6.id_materia
    WHERE g.id_grupo = $id_grupo
";
$resultado_grupo = mysqli_query($conexion, $consulta_grupo);

if (!$resultado_grupo || mysqli_num_rows($resultado_grupo) == 0) {
    die('Grupo no encontrado.');
}

$fila_grupo = mysqli_fetch_assoc($resultado_grupo);
mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Materias</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="../assets/css/minimal-table.css" rel="stylesheet" type="text/css">
    <style>
        /* Estilos personalizados para el botón de regreso */
        .boton-regresar {
            background-color: #007bff; /* Color de fondo azul */
            color: white; /* Color del texto blanco */
            border: none; /* Sin borde */
            padding: 10px 20px; /* Espaciado interno */
            text-align: center; /* Centrar texto */
            text-decoration: none; /* Sin subrayado */
            display: inline-block; /* Para el espaciado adecuado */
            font-size: 16px; /* Tamaño de fuente */
            border-radius: 5px; /* Bordes redondeados */
            cursor: pointer; /* Cursor en forma de mano */
            margin-top: 10px; /* Espacio superior */
        }
        
        .boton-regresar:hover {
            background-color: #0056b3; /* Color de fondo en hover */
        }

        /* Alineación del botón en el centro de la página */
        .centered-button {
            text-align: center; /* Centrar el contenido del contenedor */
            margin-top: 20px; /* Espacio superior */
        }
    </style>
</head>
<body>
<header class="header">
    <nav class="nav">
        <a class="logo nav-link"> TESI </a>
        <!-- Agrega el menú de navegación aquí si es necesario -->
    </nav>
</header>

<h1>Editar Materias para el Grupo</h1>

<form method="POST" action="editar_materia_be.php">
    <input type="hidden" name="id_grupo" value="<?php echo htmlspecialchars($id_grupo); ?>">

    <table>
        <tbody>
            <?php
            for ($i = 1; $i <= 6; $i++) {
                $materia_id = $fila_grupo["id_materia$i"];
                $materia_nombre = $fila_grupo["materia$i"];
                $clave_materia = $fila_grupo["clave_materia$i"];
                $hrs_teoricas = $fila_grupo["hrs_teoricas$i"];
                $hrs_practicas = $fila_grupo["hrs_practicas$i"];
                $creditos = $fila_grupo["creditos$i"]; // Obtener los créditos actuales

                // Calcular los créditos como la suma de horas teóricas y prácticas
                $creditos_calculados = $hrs_teoricas + $hrs_practicas;
                
                echo "<tr>
                        <th>Materia $i</th>
                        <td>
                            <input type='text' name='materia$i' value='" . htmlspecialchars($materia_nombre) . "' " . ($materia_id ? "" : "disabled") . ">
                            <input type='text' name='clave_materia$i' value='" . htmlspecialchars($clave_materia) . "' placeholder='Clave'>
                            <input type='number' name='hrs_teoricas$i' value='" . htmlspecialchars($hrs_teoricas) . "' placeholder='Horas Teóricas'>
                            <input type='number' name='hrs_practicas$i' value='" . htmlspecialchars($hrs_practicas) . "' placeholder='Horas Prácticas'>
                            <input type='number' name='creditos$i' value='" . htmlspecialchars($creditos_calculados) . "' placeholder='Créditos'>
                        </td>
                      </tr>";
            }
            ?>
            <tr>
                <td colspan="2">
                    <input class="boton" type="submit" name="update" value="Actualizar">
                </td>
            </tr>
        </tbody>
    </table>
</form>

<!-- Botón de Regresar con estilo -->
<div class="centered-button">
    <form action="grupos.php" method="get">
        <input type="hidden" name="id_grupo" value="<?php echo htmlspecialchars($id_grupo); ?>">
        <button type="submit" class="boton-regresar">Regresar</button>
    </form>
</div>

</body>
</html>
