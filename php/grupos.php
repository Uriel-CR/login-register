<?php
require 'verificar_sesion.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grupos</title>
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
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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

        .nav-menu-link:hover,
        .nav-menu-link.selected {
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
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
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

        input[type="string"],
        input[type="text"],
        select {
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

    <?php include 'header.html'; ?>

    <h1>Registro de Grupos</h1>

    <form method="POST" action="grupos_be.php">
        <table>
            <tbody>
                <tr>
                    <th>Grupo</th>
                    <td><input type="string" name="nombre_grupo" oninput="convertirMayusculas(this)" required></td>
                </tr>
                <tr>
                    <th>Materias</th>
                    <td>
                        <?php
                        include 'conexion_be.php';

                        // Consulta SQL para obtener las materias disponibles
                        $consulta_materias = "SELECT id_materia, nombre FROM materias";
                        $resultado_materias = mysqli_query($conexion, $consulta_materias);

                        if ($resultado_materias && mysqli_num_rows($resultado_materias) > 0) {
                            for ($i = 1; $i <= 6; $i++) {
                                echo "<label for='materia$i'>Materia $i:</label>";
                                echo "<select name='materia$i' id='materia$i'>";
                                echo "<option value=''>Selecciona una materia</option>";
                                while ($fila_materia = mysqli_fetch_assoc($resultado_materias)) {
                                    echo "<option value='{$fila_materia['id_materia']}'>{$fila_materia['nombre']}</option>";
                                }
                                echo "</select><br>";
                                mysqli_data_seek($resultado_materias, 0); // Resetear el puntero del resultado
                            }
                        } else {
                            echo "No hay materias disponibles";
                        }

                        mysqli_close($conexion);
                        ?>
                    </td>
                </tr>
                <!-- Nuevo Campo Profesor -->
                <tr>
                    <th>Profesor</th>
                    <td>
                        <?php
                        include 'conexion_be.php';
                        // Consulta para obtener los profesores
                        $consulta_profesores = "SELECT id_profesor, nombre, ap_paterno, ap_materno FROM profesores";
                        $resultado_profesores = mysqli_query($conexion, $consulta_profesores);

                        echo "<select name='profesor' id='profesor'>";
                        echo "<option value=''>Selecciona un profesor</option>";
                        while ($fila_profesor = mysqli_fetch_assoc($resultado_profesores)) {
                            echo "<option value='{$fila_profesor['id_profesor']}'>" .
                                "{$fila_profesor['nombre']} {$fila_profesor['ap_paterno']} {$fila_profesor['ap_materno']}</option>";
                        }
                        echo "</select>";
                        mysqli_close($conexion);
                        ?>
                    </td>
                </tr>

                <!-- Nuevo Campo Periodo -->
                <tr>
                    <th>Periodo</th>
                    <td>
                        <?php
                        include 'conexion_be.php';
                        // Consulta para obtener los periodos
                        $consulta_periodos = "SELECT id_periodo, periodo FROM periodos";
                        $resultado_periodos = mysqli_query($conexion, $consulta_periodos);

                        echo "<select name='periodo' id='periodo'>";
                        echo "<option value=''>Selecciona un periodo</option>";
                        while ($fila_periodo = mysqli_fetch_assoc($resultado_periodos)) {
                            echo "<option value='{$fila_periodo['id_periodo']}'>" .
                                "{$fila_periodo['periodo']}</option>";
                        }
                        echo "</select>";
                        mysqli_close($conexion);
                        ?>
                    </td>
                </tr>

                <!-- Nuevo Campo Salón -->
                <tr>
                    <th>Salón</th>
                    <td>
                        <?php
                        include 'conexion_be.php';
                        // Consulta para obtener los salones
                        $consulta_salones = "SELECT id_salon, clave_salon FROM salones";
                        $resultado_salones = mysqli_query($conexion, $consulta_salones);

                        echo "<select name='salon' id='salon'>";
                        echo "<option value=''>Selecciona un salón</option>";
                        while ($fila_salon = mysqli_fetch_assoc($resultado_salones)) {
                            echo "<option value='{$fila_salon['id_salon']}'>" .
                                "{$fila_salon['clave_salon']}</option>";
                        }
                        echo "</select>";
                        mysqli_close($conexion);
                        ?>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <input class="boton" type="submit" name="register" value="Enviar">
                    </td>
                </tr>
            </tbody>
        </table>
    </form>


    <?php
    include 'conexion_be.php';

    // Consulta SQL para obtener los grupos y sus materias asignadas
    $consulta_grupos = "
    SELECT g.id_grupo, g.clave_grupo, 
           GROUP_CONCAT(m.nombre ORDER BY m.nombre SEPARATOR ', ') AS materias
    FROM grupos g
    LEFT JOIN grupo_materia gm ON g.id_grupo = gm.id_grupo
    LEFT JOIN materias m ON gm.id_materia = m.id_materia
    GROUP BY g.id_grupo, g.clave_grupo
";
    $resultado_grupos = mysqli_query($conexion, $consulta_grupos);
    ?>

    <h1>Grupos Registrados</h1>
    <table>
        <thead>
            <tr>
                <th>Grupo</th>
                <th>Materias Asignadas</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($resultado_grupos && mysqli_num_rows($resultado_grupos) > 0) {
                while ($fila_grupo = mysqli_fetch_assoc($resultado_grupos)) {
                    $id_grupo = $fila_grupo['id_grupo'];
                    $materias = !empty($fila_grupo['materias']) ? $fila_grupo['materias'] : 'Sin materias asignadas';

                    echo "<tr>
                    <td>{$fila_grupo['clave_grupo']}</td>
                    <td>{$materias}</td>
                    <td><a href='editar_grupo.php?id_grupo=$id_grupo' class='boton'>Editar Materias</a></td>
                  </tr>";
                }
            } else {
                echo '<tr><td colspan="3">No hay grupos registrados</td></tr>';
            }

            mysqli_close($conexion);
            ?>
        </tbody>
    </table>

</body>

</html>