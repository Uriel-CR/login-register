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
<header class="header">
    <nav class="nav">
        <a class="logo nav-link"> TESI </a>
        <ul class="nav-menu">
            <li class="nav-menu-item"><a class="nav-menu-link" href="../php/bienvenida.php">Inicio</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link" href="../php/alumnos.php">Alumnos</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link" href="../php/materias.php">Materias</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link selected">Grupos</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link" href="../php/profesores.php">Profesores</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link" href="../php/periodo.php">Periodo</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link" href="../php/asignacion_grupo.php">Calificaciones</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link" href="../php/resumen.php">Resumen</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link" href="../index.php">Cerrar Sesión</a></li>
        </ul>
    </nav>
</header>

<h1>Registro de Grupos</h1>

<form method="POST" action="grupos_be.php">
    <table>
        <tbody>
            <tr>
                <th>Grupo</th>
                <td><input type="number" name="nombre_grupo" oninput="convertirMayusculas(this)" required></td>
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
            <tr>
                <td colspan="2">
                    <input class="boton" type="submit" name="register" value="Enviar">
                </td>
            </tr>
        </tbody>
    </table>
</form>

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
        include 'conexion_be.php';

        // Consulta SQL para obtener los grupos y las materias asignadas
        $consulta_grupos = "
            SELECT g.id_grupo, g.nombre_grupo,
            g.id_materia1, g.id_materia2, g.id_materia3, g.id_materia4, g.id_materia5, g.id_materia6,
            m1.nombre AS materia1, m2.nombre AS materia2, m3.nombre AS materia3, m4.nombre AS materia4, m5.nombre AS materia5, m6.nombre AS materia6
            FROM grupos g
            LEFT JOIN materias m1 ON g.id_materia1 = m1.id_materia
            LEFT JOIN materias m2 ON g.id_materia2 = m2.id_materia
            LEFT JOIN materias m3 ON g.id_materia3 = m3.id_materia
            LEFT JOIN materias m4 ON g.id_materia4 = m4.id_materia
            LEFT JOIN materias m5 ON g.id_materia5 = m5.id_materia
            LEFT JOIN materias m6 ON g.id_materia6 = m6.id_materia
        ";
        $resultado_grupos = mysqli_query($conexion, $consulta_grupos);

        if ($resultado_grupos && mysqli_num_rows($resultado_grupos) > 0) {
            while ($fila_grupo = mysqli_fetch_assoc($resultado_grupos)) {
                $materias = '';
                for ($i = 1; $i <= 6; $i++) {
                    $materia_key = "materia$i";
                    if (!empty($fila_grupo[$materia_key])) {
                        $id_materia = $fila_grupo["id_materia$i"];
                        $nombre_materia = $fila_grupo[$materia_key];
                        $materias .= "<li><a href='editar_materia.php?id_grupo={$fila_grupo['id_grupo']}&materia_id={$id_materia}'>{$nombre_materia}</a></li>";
                    }
                }
                $id_grupo = $fila_grupo['id_grupo'];
                echo "<tr>
                        <td>{$fila_grupo['nombre_grupo']}</td>
                        <td><ul class='materias-lista'>{$materias}</ul></td>
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
