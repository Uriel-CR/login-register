<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen por Grupos</title>
    <link rel="stylesheet" href="../assets/css/style.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/resumen.css" type="text/css">
</head>

<body>
    <header class="header">
        <nav class="nav">
            <a class="logo nav-link">TESI</a>
            <ul class="nav-menu">
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/bienvenida.php">Inicio</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link " href="../php/alumnos.php">Alumnos</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/materias.php">Materias</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/grupos.php">Grupos</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/profesores.php">Profesores</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/periodo.php">Periodo</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/asignacion_grupo.php">Calificaciones</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link selected" href="../php/resumen.php">Resumen</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../index.php">Cerrar Sesión</a></li>
            </ul>
            <button class="nav-toggle">
                <img src="../assets/images/menu.svg" class="nav-toggle-icon" alt="Menú">
            </button>
        </nav>
    </header>
    <?php
    // Configuración de la base de datos
    $servername = "localhost";
    $username = "serviciosocial";
    $password = "FtW30yNo8hQd-x/G";
    $dbname = "login_register_db";

    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Comprobar la conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Obtener el periodo y el parcial seleccionados de la solicitud (filtrado)
    $periodo = isset($_GET['periodo']) ? intval($_GET['periodo']) : 1; // Valor por defecto si no se proporciona
    $parcial = isset($_GET['parcial']) ? intval($_GET['parcial']) : 1; // Valor por defecto si no se proporciona

    // Consultas SQL
    $sql_grupos = "SELECT g.nombre_grupo, 
           c.id_grupo, 
           SUM(CASE WHEN CAST(c.parcial_1 AS DECIMAL) >= 70 THEN 1 ELSE 0 END) AS aprobados_p1,
           SUM(CASE WHEN c.parcial_1 = 'N/A' THEN 1 ELSE 0 END) AS reprobados_p1,
           SUM(CASE WHEN CAST(c.parcial_2 AS DECIMAL) >= 70 THEN 1 ELSE 0 END) AS aprobados_p2,
           SUM(CASE WHEN c.parcial_2 = 'N/A' THEN 1 ELSE 0 END) AS reprobados_p2,
           SUM(CASE WHEN CAST(c.parcial_3 AS DECIMAL) >= 70 THEN 1 ELSE 0 END) AS aprobados_p3,
           SUM(CASE WHEN c.parcial_3 = 'N/A' THEN 1 ELSE 0 END) AS reprobados_p3
    FROM calificaciones c
    JOIN grupos g ON c.id_grupo = g.id_grupo
    WHERE c.id_periodo = ? 
    GROUP BY c.id_grupo, g.nombre_grupo";

    $sql_alumnos = "SELECT id_grupo, COUNT(DISTINCT id_alumno) AS total_alumnos
    FROM calificaciones
    WHERE id_periodo = ?
    GROUP BY id_grupo";

    $sql_resumen = "SELECT id_grupo, 
           SUM(CASE WHEN ? = 1 THEN parcial_1_aprobados 
                    WHEN ? = 2 THEN parcial_2_aprobados 
                    WHEN ? = 3 THEN parcial_3_aprobados 
                    ELSE 0 END) AS materias_aprobadas
    FROM calificaciones_resumen
    WHERE id_periodo = ?
    GROUP BY id_grupo";

    $sql_reprobadas = "SELECT id_grupo, 
           SUM(CASE WHEN ? = 1 THEN parcial_1_reprobados 
                    WHEN ? = 2 THEN parcial_2_reprobados 
                    WHEN ? = 3 THEN parcial_3_reprobados 
                    ELSE 0 END) AS materias_reprobadas
    FROM calificaciones_resumen
    WHERE id_periodo = ?
    GROUP BY id_grupo";

    $sql_periodos = "SELECT id_periodo, periodo FROM periodos";
    // Preparar y ejecutar consultas
    $stmt_grupos = $conn->prepare($sql_grupos);
    $stmt_grupos->bind_param("i", $periodo);
    $stmt_grupos->execute();
    $result_grupos = $stmt_grupos->get_result();

    $stmt_alumnos = $conn->prepare($sql_alumnos);
    $stmt_alumnos->bind_param("i", $periodo);
    $stmt_alumnos->execute();
    $result_alumnos = $stmt_alumnos->get_result();

    $stmt_resumen = $conn->prepare($sql_resumen);
    $stmt_resumen->bind_param("iiii", $parcial, $parcial, $parcial, $periodo);
    $stmt_resumen->execute();
    $result_resumen = $stmt_resumen->get_result();

    $stmt_reprobadas = $conn->prepare($sql_reprobadas);
    $stmt_reprobadas->bind_param("iiii", $parcial, $parcial, $parcial, $periodo);
    $stmt_reprobadas->execute();
    $result_reprobadas = $stmt_reprobadas->get_result();

    $result_periodos = $conn->query($sql_periodos);

    // Crear arrays para almacenar resultados
    $total_alumnos_por_grupo = [];
    while ($row_alumnos = $result_alumnos->fetch_assoc()) {
        $total_alumnos_por_grupo[$row_alumnos['id_grupo']] = $row_alumnos['total_alumnos'];
    }

    $materias_aprobadas_por_grupo = [];
    while ($row_resumen = $result_resumen->fetch_assoc()) {
        $materias_aprobadas_por_grupo[$row_resumen['id_grupo']] = $row_resumen['materias_aprobadas'];
    }

    $materias_reprobadas_por_grupo = [];
    while ($row_reprobadas = $result_reprobadas->fetch_assoc()) {
        $materias_reprobadas_por_grupo[$row_reprobadas['id_grupo']] = $row_reprobadas['materias_reprobadas'];
    }

    // Consulta SQL para obtener los datos de la tabla ajustada al parcial seleccionado
    // Mostrar el formulario para seleccionar periodo y parcial
    echo '<title>Resumen por Grupos</title>';
    echo '<form method="GET" action="" style="margin-bottom: 20px;">';
    echo '<label for="periodo">Selecciona Periodo:</label>';
    echo '<select id="periodo" name="periodo">';
    while ($row_periodos = $result_periodos->fetch_assoc()) {
        $selected = ($periodo == $row_periodos['id_periodo']) ? ' selected' : '';
        echo '<option value="' . $row_periodos['id_periodo'] . '"' . $selected . '>' . htmlspecialchars($row_periodos['periodo']) . '</option>';
    }
    echo '</select>';

    echo '<label for="parcial">Selecciona Parcial:</label>';
    echo '<select id="parcial" name="parcial">';
    echo '<option value="1"' . ($parcial == 1 ? ' selected' : '') . '>Parcial 1</option>';
    echo '<option value="2"' . ($parcial == 2 ? ' selected' : '') . '>Parcial 2</option>';
    echo '<option value="3"' . ($parcial == 3 ? ' selected' : '') . '>Parcial 3</option>';
    echo '</select>';

    echo '<input type="submit" value="Mostrar Reporte">';
    echo '</form>';

    // Consulta SQL para obtener los datos de la tabla ajustada al parcial seleccionado
    $sql = "
    SELECT id_grupo,
           SUM(CASE WHEN ? = 1 THEN creditos_aprobados_parcial_1 ELSE 0 END) AS total_aprobados_parcial_1,
           SUM(CASE WHEN ? = 1 THEN creditos_reprobados_parcial_1 ELSE 0 END) AS total_reprobados_parcial_1,
           SUM(CASE WHEN ? = 2 THEN creditos_aprobados_parcial_2 ELSE 0 END) AS total_aprobados_parcial_2,
           SUM(CASE WHEN ? = 2 THEN creditos_reprobados_parcial_2 ELSE 0 END) AS total_reprobados_parcial_2,
           SUM(CASE WHEN ? = 3 THEN creditos_aprobados_parcial_3 ELSE 0 END) AS total_aprobados_parcial_3,
           SUM(CASE WHEN ? = 3 THEN creditos_reprobados_parcial_3 ELSE 0 END) AS total_reprobados_parcial_3
    FROM calificaciones_resumen
    GROUP BY id_grupo";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiiii", $parcial, $parcial, $parcial, $parcial, $parcial, $parcial);
    $stmt->execute();
    $resultados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);



    // Inicializar los totales
    $totales = [
        'total_alumnos' => 0,
        'alumnos_aprobados' => 0,
        'alumnos_reprobados' => 0,
        'materias_aprobadas' => 0,
        'materias_reprobadas' => 0,
        'total_creditos' => 0,
        'creditos_aprobados' => 0,
        'creditos_reprobados' => 0,
    ];

    // Mostrar la tabla con los resultados
    echo '<table>';
    echo '<caption>RESUMEN POR GRUPOS</caption>';
    echo '<tr>
        <th>No.</th>
        <th>Nombre del Grupo</th>
        <th>Total Alumnos</th>
        <th>Alumnos Acreditados</th>
        <th>Alumnos No Acreditados</th>
        <th>Porcentaje Alumnos Acreditados</th>
        <th>Porcentaje Alumnos No Acreditados</th>
        <th>Total Materias</th>
        <th>Materias Acreditadas</th>
        <th>Materias No Acreditadas</th>
        <th>Porcentaje Materias Acreditadas</th>
        <th>Porcentaje Materias No Acreditadas</th>
        <th>Total Créditos</th>
        <th>Créditos Acreditados</th>
        <th>Créditos No Acreditados</th>
        <th>Porcentaje Créditos Acreditados</th>
        <th>Porcentaje Créditos No Acreditados</th>
        <th>Gráficas</th>
      </tr>';

    while ($row = $result_grupos->fetch_assoc()) {
        $id_grupo = $row['id_grupo'];
        $nombre_grupo = $row['nombre_grupo'];
        $total_alumnos = isset($total_alumnos_por_grupo[$id_grupo]) ? $total_alumnos_por_grupo[$id_grupo] : 0;
        $materias_aprobadas = isset($materias_aprobadas_por_grupo[$id_grupo]) ? $materias_aprobadas_por_grupo[$id_grupo] : 0;
        $materias_reprobadas = isset($materias_reprobadas_por_grupo[$id_grupo]) ? $materias_reprobadas_por_grupo[$id_grupo] : 0;

        $alumnos_aprobados = $row['aprobados_p' . $parcial];
        $alumnos_reprobados = $row['reprobados_p' . $parcial];

        $total_materias = $materias_aprobadas + $materias_reprobadas;

        $porcentaje_alumnos_aprobados = $total_alumnos > 0 ? round(($alumnos_aprobados / $total_alumnos) * 100, 2) : 0;
        $porcentaje_alumnos_reprobados = $total_alumnos > 0 ? round(($alumnos_reprobados / $total_alumnos) * 100, 2) : 0;

        $porcentaje_materias_aprobadas = $total_materias > 0 ? round(($materias_aprobadas / $total_materias) * 100, 2) : 0;
        $porcentaje_materias_reprobadas = $total_materias > 0 ? round(($materias_reprobadas / $total_materias) * 100, 2) : 0;

        // Datos adicionales de créditos
        // Obtener los datos de créditos
        $resultado = array_filter($resultados, function ($item) use ($id_grupo) {
            return $item['id_grupo'] == $id_grupo;
        });
        $resultado = !empty($resultado) ? reset($resultado) : [];

        // Asegurarse de que los valores son numéricos
        $total_aprobados_creditos = isset($resultado['total_aprobados_parcial_' . $parcial]) && is_numeric($resultado['total_aprobados_parcial_' . $parcial]) ? (float)$resultado['total_aprobados_parcial_' . $parcial] : 0;
        $total_reprobados_creditos = isset($resultado['total_reprobados_parcial_' . $parcial]) && is_numeric($resultado['total_reprobados_parcial_' . $parcial]) ? (float)$resultado['total_reprobados_parcial_' . $parcial] : 0;

        $total_creditos = $total_aprobados_creditos + $total_reprobados_creditos;
        $porcentaje_creditos_aprobados = number_format(($total_creditos > 0) ? ($total_aprobados_creditos / $total_creditos) * 100 : 0, 2);
        $porcentaje_creditos_reprobados = number_format(($total_creditos > 0) ? ($total_reprobados_creditos / $total_creditos) * 100 : 0, 2);

        // Acumulación de totales
        $totales['total_alumnos'] += $total_alumnos;
        $totales['alumnos_aprobados'] += $alumnos_aprobados;
        $totales['alumnos_reprobados'] += $alumnos_reprobados;
        $totales['materias_aprobadas'] += $materias_aprobadas;
        $totales['materias_reprobadas'] += $materias_reprobadas;
        $totales['total_creditos'] += $total_creditos;
        $totales['creditos_aprobados'] += $total_aprobados_creditos;
        $totales['creditos_reprobados'] += $total_reprobados_creditos;

        echo '<tr>';
        echo '<td>' . $id_grupo . '</td>';
        echo '<td>' . $nombre_grupo . '</td>';
        echo '<td>' . $total_alumnos . '</td>';
        echo '<td>' . $alumnos_aprobados . '</td>';
        echo '<td>' . $alumnos_reprobados . '</td>';
        echo '<td>' . $porcentaje_alumnos_aprobados . '%</td>';
        echo '<td>' . $porcentaje_alumnos_reprobados . '%</td>';
        echo '<td>' . $total_materias . '</td>';
        echo '<td>' . $materias_aprobadas . '</td>';
        echo '<td>' . $materias_reprobadas . '</td>';
        echo '<td>' . $porcentaje_materias_aprobadas . '%</td>';
        echo '<td>' . $porcentaje_materias_reprobadas . '%</td>';
        echo '<td>' . $total_creditos . '</td>';
        echo '<td>' . $total_aprobados_creditos . '</td>';
        echo '<td>' . $total_reprobados_creditos . '</td>';
        echo '<td>' . $porcentaje_creditos_aprobados . '%</td>';
        echo '<td>' . $porcentaje_creditos_reprobados . '%</td>';
        echo '<td><a href="#" onclick="openGraphWindow(' . $id_grupo . ', ' . $periodo . ', ' . $parcial . ')" class="button">Ver Gráfica</a></td>';
        echo '</tr>';
    }

    // Fila de totales
    echo '<tr class="total-row">';
    echo '<td colspan="2">Totales</td>';
    echo '<td>' . $totales['total_alumnos'] . '</td>';
    echo '<td>' . $totales['alumnos_aprobados'] . '</td>';
    echo '<td>' . $totales['alumnos_reprobados'] . '</td>';
    echo '<td>' . ($totales['total_alumnos'] > 0 ? round(($totales['alumnos_aprobados'] / $totales['total_alumnos']) * 100, 2) . '%' : '0%') . '</td>';
    echo '<td>' . ($totales['total_alumnos'] > 0 ? round(($totales['alumnos_reprobados'] / $totales['total_alumnos']) * 100, 2) . '%' : '0%') . '</td>';
    echo '<td>' . ($totales['materias_aprobadas'] + $totales['materias_reprobadas']) . '</td>';
    echo '<td>' . $totales['materias_aprobadas'] . '</td>';
    echo '<td>' . $totales['materias_reprobadas'] . '</td>';
    echo '<td>' . ($totales['materias_aprobadas'] + $totales['materias_reprobadas'] > 0 ? round(($totales['materias_aprobadas'] / ($totales['materias_aprobadas'] + $totales['materias_reprobadas'])) * 100, 2) . '%' : '0%') . '</td>';
    echo '<td>' . ($totales['materias_aprobadas'] + $totales['materias_reprobadas'] > 0 ? round(($totales['materias_reprobadas'] / ($totales['materias_aprobadas'] + $totales['materias_reprobadas'])) * 100, 2) . '%' : '0%') . '</td>';
    echo '<td>' . $totales['total_creditos'] . '</td>';
    echo '<td>' . $totales['creditos_aprobados'] . '</td>';
    echo '<td>' . $totales['creditos_reprobados'] . '</td>';
    echo '<td>' . ($totales['total_creditos'] > 0 ? round(($totales['creditos_aprobados'] / $totales['total_creditos']) * 100, 2) . '%' : '0%') . '</td>';
    echo '<td>' . ($totales['total_creditos'] > 0 ? round(($totales['creditos_reprobados'] / $totales['total_creditos']) * 100, 2) . '%' : '0%') . '</td>';
    echo '<td><a href="otro_archivo.php?id_grupo=' . urlencode($id_grupo) . '&periodo=' . urlencode($periodo) . '&parcial=' . urlencode($parcial) . '" class="button">Ver Gráfica</a></td>';
    echo '</tr>';

    echo '</table>';



    // Cerrar conexiones
    $stmt_grupos->close();
    $stmt_alumnos->close();
    $stmt_resumen->close();
    $stmt_reprobadas->close();
    $conn->close();
    ?>

    <!-- Script JavaScript para abrir la nueva ventana -->
    <script>
        function openGraphWindow(idGrupo, periodo, parcial) {
            var url = 'graficass.php?id_grupo=' + idGrupo + '&periodo=' + periodo + '&parcial=' + parcial;
            var options = 'width=800,height=600,scrollbars=yes,resizable=yes';
            window.open(url, 'Gráfica de Alumnos, Materias y Créditos', options);
        }
    </script>
</body>

</html>