<?php
require 'verificar_sesion.php';
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
        body {
            background-image: url('../assets/images/foto.jpeg');
            background-size: 100% auto;
            background-position: center top;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .header {
            background-color: #007BFF;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            /* Hace que el encabezado se quede fijo en la parte superior */
            top: 0;
            /* Asegura que el encabezado esté en la parte superior de la página */
            width: 100%;
            /* Asegura que el encabezado ocupe todo el ancho de la ventana */
            z-index: 1000;
            /* Asegura que el encabezado esté sobre otros elementos */
        }

        .logo {
            
            font-size: 24px;
            font-weight: bold;
            position: fixed;
            left: 0;
            margin: 10px;
        }

        .nav {
            display: flex;
            align-items: center;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
        }

        .nav-menu-item {
            margin-right: 15px;
        }

        .nav-menu-link {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            padding: 10px 15px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .nav-menu-link:hover, .nav-menu-link.selected {
            background-color: #0056b3;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            font-size: 28px;
            color: #333;
        }

        form {
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        input[type="number"], input[type="text"], select {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        label {
            display: block;
            margin: 5px 0;
            font-weight: bold;
        }

        .boton {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .boton:hover {
            background-color: #0056b3;
        }

        .materias-lista {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .materias-lista li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
<?php
include 'header.html';
?>

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
