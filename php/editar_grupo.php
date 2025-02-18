<?php
require 'verificar_sesion.php';
include 'conexion_be.php';

// Verificar si se ha enviado un ID de grupo válido
if (!isset($_GET['id_grupo']) || !is_numeric($_GET['id_grupo'])) {
    die('ID de grupo inválido.');
}

$id_grupo = intval($_GET['id_grupo']);

// Obtener la información del grupo
$consulta_grupo = "
    SELECT g.clave_grupo, 
           m.id_materia, m.nombre AS materia, m.clave_materia, 
           m.HRS_TEORICAS, m.HRS_PRACTICAS, m.creditos
    FROM grupos g
    LEFT JOIN grupo_materia gm ON g.id_grupo = gm.id_grupo
    LEFT JOIN materias m ON gm.id_materia = m.id_materia
    WHERE g.id_grupo = ?
";

// Preparar la consulta para evitar SQL Injection
$stmt = mysqli_prepare($conexion, $consulta_grupo);
mysqli_stmt_bind_param($stmt, "i", $id_grupo);
mysqli_stmt_execute($stmt);
$resultado_grupo = mysqli_stmt_get_result($stmt);

// Verificar si el grupo existe
if (!$resultado_grupo || mysqli_num_rows($resultado_grupo) == 0) {
    die('Grupo no encontrado.');
}

// Obtener los datos
$materias = [];
$clave_grupo = '';

while ($fila = mysqli_fetch_assoc($resultado_grupo)) {
    $clave_grupo = $fila['clave_grupo']; // Se repetirá en cada fila, pero es el mismo para todas
    if (!empty($fila['id_materia'])) {
        $materias[] = $fila;
    }
}

// Cerrar conexión
mysqli_stmt_close($stmt);
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
            if (!empty($materias)) {
                foreach ($materias as $index => $materia) {
                    $materia_id = $materia["id_materia"];
                    $materia_nombre = $materia["materia"];
                    $clave_materia = $materia["clave_materia"];
                    $hrs_teoricas = $materia["HRS_TEORICAS"];
                    $hrs_practicas = $materia["HRS_PRACTICAS"];
                    $creditos = $materia["creditos"];

                    echo "<tr>
                            <th>Materia " . ($index + 1) . "</th>
                            <td>
                                <input type='hidden' name='id_materia[]' value='" . htmlspecialchars($materia_id) . "'>
                                <label>Nombre:</label>
                                <input type='text' name='materia[]' value='" . htmlspecialchars($materia_nombre) . "' disabled>
                                <label>Clave:</label>
                                <input type='text' name='clave_materia[]' value='" . htmlspecialchars($clave_materia) . "' placeholder='Clave'>
                                <label>Horas Teóricas:</label>
                                <input type='number' name='hrs_teoricas[]' value='" . htmlspecialchars($hrs_teoricas) . "' placeholder='Horas Teóricas'>
                                <label>Horas Prácticas:</label>
                                <input type='number' name='hrs_practicas[]' value='" . htmlspecialchars($hrs_practicas) . "' placeholder='Horas Prácticas'>
                                <label>Créditos:</label>
                                <input type='number' name='creditos[]' value='" . htmlspecialchars($creditos) . "' placeholder='Créditos'>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No hay materias asignadas a este grupo.</td></tr>";
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
