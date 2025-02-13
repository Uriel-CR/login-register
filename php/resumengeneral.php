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
    <?php
    require 'verificar_sesion.php';
    include 'header.html';
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

    $sql_periodos = "SELECT id_periodo, periodo FROM periodos";

    $sql_grupos = "SELECT DISTINCT cr.id_grupo, g.nombre_grupo
    FROM calificaciones AS cr
    INNER JOIN grupos AS g ON cr.id_grupo = g.id_grupo
    WHERE cr.id_periodo = ?
    ORDER BY g.nombre_grupo ASC";

    $sql_alumnos = "SELECT COUNT(DISTINCT id_alumno)
    FROM calificaciones
    WHERE id_grupo = ?";

    $sql_materias = "SELECT COUNT(id_materia)
    FROM calificaciones
    WHERE id_grupo = ?
    AND id_periodo = ?";

    if($parcial == 4){
        
        $tipo_calif = "promedio";

    }else if($parcial == 5){

        $tipo_calif = "calif_final";

    }else{

        $tipo_calif = "parcial_$parcial";
        
    }

    $sql_reprobados = "SELECT COUNT(DISTINCT id_alumno) 
    FROM calificaciones
    WHERE id_grupo = ?
    AND id_periodo = ?
    AND $tipo_calif = 'N/A'";

    $sql_materias_reprobadas = "SELECT COUNT(id_materia)
    FROM calificaciones
    WHERE id_grupo = ?
    AND id_periodo = ?
    AND $tipo_calif = 'N/A'";

    $sql_creditos_aprobados = "SELECT SUM(creditos_aprobados_$tipo_calif)
    FROM calificaciones_resumen
    WHERE id_grupo = ?
    AND id_periodo = ?";

    $sql_creditos_reprobados = "SELECT SUM(creditos_reprobados_$tipo_calif)
    FROM calificaciones_resumen
    WHERE id_grupo = ?
    AND id_periodo = ?";

    // Ejecutar la consulta para obtener los periodos
    $result_periodos = $conn->query($sql_periodos);
    $periodos = [];
    $periodo_seleccionado = null;
    if ($result_periodos->num_rows > 0) {
        while ($row = $result_periodos->fetch_assoc()) {
            $periodos[] = $row;
            if ($row['id_periodo'] == $periodo) {
                $periodo_seleccionado = $row['periodo'];
            }
        }
    }

    // Preparar y ejecutar la consulta para obtener los grupos
    $stmt_grupos = $conn->prepare($sql_grupos);
    $stmt_grupos->bind_param("i", $periodo);
    $stmt_grupos->execute();
    $result_grupos = $stmt_grupos->get_result();
    $grupos = [];
    if ($result_grupos->num_rows > 0) {
        while ($row = $result_grupos->fetch_assoc()) {
            $grupos[] = $row;
        }
    }

    // Mostrar el formulario para seleccionar periodo y parcial
    echo '<title>Resumen por Grupos</title>';
    echo '<form method="GET" action="" style="margin-bottom: 20px;">';
    echo '<label for="periodo">Selecciona Periodo:</label>';
    echo '<select id="periodo" name="periodo">';
    foreach ($periodos as $row_periodos) {
        $selected = ($periodo == $row_periodos['id_periodo']) ? ' selected' : '';
        echo '<option value="' . $row_periodos['id_periodo'] . '"' . $selected . '>' . htmlspecialchars($row_periodos['periodo']) . '</option>';
    }
    echo '</select>';

    echo '<label for="parcial">Selecciona Parcial:</label>';
    echo '<select id="parcial" name="parcial">';
    echo '<option value="1"' . ($parcial == 1 ? ' selected' : '') . '>Parcial 1</option>';
    echo '<option value="2"' . ($parcial == 2 ? ' selected' : '') . '>Parcial 2</option>';
    echo '<option value="3"' . ($parcial == 3 ? ' selected' : '') . '>Parcial 3</option>';
    echo '<option value="4"' . ($parcial == 4 ? ' selected' : '') . '>Antes de Segunda Oportunidad</option>';
    echo '<option value="5"' . ($parcial == 5 ? ' selected' : '') . '>Despues de Segunda Oportunidad</option>';
    echo '</select>';

    echo '<input type="submit" value="Mostrar Reporte">';
    echo '</form>';

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

    foreach ($grupos as $grupo) {
        $id_grupo = $grupo['id_grupo'];
        $nombre_grupo = $grupo['nombre_grupo'];

        // Obtener el número de reprobados
        $stmt_reprobados = $conn->prepare($sql_reprobados);
        $stmt_reprobados->bind_param("ii", $id_grupo, $periodo);
        $stmt_reprobados->execute();
        $result_reprobados = $stmt_reprobados->get_result();
        $alumnos_reprobados = $result_reprobados->fetch_row()[0];

        // Obtener el número de alumnos
        $stmt_alumnos = $conn->prepare($sql_alumnos);
        $stmt_alumnos->bind_param("i", $id_grupo);
        $stmt_alumnos->execute();
        $result_alumnos = $stmt_alumnos->get_result();
        $total_alumnos = $result_alumnos->fetch_row()[0];

        // Calcular alumnos aprobados
        $alumnos_aprobados = $total_alumnos - $alumnos_reprobados;

        // Obtener el número de materias
        $stmt_materias = $conn->prepare($sql_materias);
        $stmt_materias->bind_param("ii", $id_grupo, $periodo);
        $stmt_materias->execute();
        $result_materias = $stmt_materias->get_result();
        $total_materias = $result_materias->fetch_row()[0];

        // Obtener el número de materias reprobadas
        $stmt_materias_reprobadas = $conn->prepare($sql_materias_reprobadas);
        $stmt_materias_reprobadas->bind_param("ii", $id_grupo, $periodo);
        $stmt_materias_reprobadas->execute();
        $result_materias_reprobadas = $stmt_materias_reprobadas->get_result();
        $materias_reprobadas = $result_materias_reprobadas->fetch_row()[0];

        // Calcular materias aprobadas
        $materias_aprobadas = $total_materias - $materias_reprobadas;

        // Obtener los créditos aprobados
        $stmt_creditos_aprobados = $conn->prepare($sql_creditos_aprobados);
        $stmt_creditos_aprobados->bind_param("ii", $id_grupo, $periodo);
        $stmt_creditos_aprobados->execute();
        $result_creditos_aprobados = $stmt_creditos_aprobados->get_result();
        $creditos_aprobados = $result_creditos_aprobados->fetch_row()[0];

        // Obtener los créditos reprobados
        $stmt_creditos_reprobados = $conn->prepare($sql_creditos_reprobados);
        $stmt_creditos_reprobados->bind_param("ii", $id_grupo, $periodo);
        $stmt_creditos_reprobados->execute();
        $result_creditos_reprobados = $stmt_creditos_reprobados->get_result();
        $creditos_reprobados = $result_creditos_reprobados->fetch_row()[0];

        // Calcular total de créditos
        $total_creditos = $creditos_aprobados + $creditos_reprobados;

        // Calcular porcentajes
        $porcentaje_alumnos_aprobados = ($total_alumnos > 0) ? round(($alumnos_aprobados / $total_alumnos) * 100, 2) : 0;
        $porcentaje_alumnos_reprobados = ($total_alumnos > 0) ? round(($alumnos_reprobados / $total_alumnos) * 100, 2) : 0;
        $porcentaje_materias_aprobadas = ($total_materias > 0) ? round(($materias_aprobadas / $total_materias) * 100, 2) : 0;
        $porcentaje_materias_reprobadas = ($total_materias > 0) ? round(($materias_reprobadas / $total_materias) * 100, 2) : 0;
        $porcentaje_creditos_aprobados = ($total_creditos > 0) ? round(($creditos_aprobados / $total_creditos) * 100, 2) : 0;
        $porcentaje_creditos_reprobados = ($total_creditos > 0) ? round(($creditos_reprobados / $total_creditos) * 100, 2) : 0;

        // Actualizar totales
        $totales['total_alumnos'] += $total_alumnos;
        $totales['alumnos_aprobados'] += $alumnos_aprobados;
        $totales['alumnos_reprobados'] += $alumnos_reprobados;
        $totales['materias_aprobadas'] += $materias_aprobadas;
        $totales['materias_reprobadas'] += $materias_reprobadas;
        $totales['total_creditos'] += $total_creditos;
        $totales['creditos_aprobados'] += $creditos_aprobados;
        $totales['creditos_reprobados'] += $creditos_reprobados;

        echo '<tr>';
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
        echo '<td>' . $creditos_aprobados . '</td>';
        echo '<td>' . $creditos_reprobados . '</td>';
        echo '<td>' . $porcentaje_creditos_aprobados . '%</td>';
        echo '<td>' . $porcentaje_creditos_reprobados . '%</td>';
        echo '<td><a href="#" onclick="openGraphWindow(' . $porcentaje_alumnos_aprobados . ', ' . $porcentaje_alumnos_reprobados . ', ' . $porcentaje_materias_aprobadas . ', ' . $porcentaje_materias_reprobadas . ', ' . $porcentaje_creditos_aprobados . ', ' . $porcentaje_creditos_reprobados . ', \'' . $nombre_grupo . '\', ' . $periodo . ', ' . $parcial . ')" class="button">Ver Gráfica</a></td>';
        echo '</tr>';
        }

        // Fila de totales
        echo '<tr class="total-row">';
        echo '<td>Totales</td>';
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
        echo '<td><a href="otro_archivo.php?
        alumnos_aprobados=' . urlencode(($totales['alumnos_aprobados'] / $totales['total_alumnos']) * 100) .
        '&alumnos_reprobados=' . urlencode(($totales['alumnos_reprobados'] / $totales['total_alumnos']) * 100) .
        '&materias_aprobadas=' . urlencode(($totales['materias_aprobadas'] / ($totales['materias_aprobadas'] + $totales['materias_reprobadas'])) * 100) .
        '&materias_reprobadas=' . urlencode(($totales['materias_reprobadas'] / ($totales['materias_aprobadas'] + $totales['materias_reprobadas'])) * 100) .
        '&periodo=' . urlencode($periodo_seleccionado) .
        '&parcial=' . urlencode($parcial) . '
        " class="button">Ver Gráfica</a></td>';
        echo '</tr>';

        echo '</table>';

        // Cerrar las conexiones
        $stmt_grupos->close();
        $stmt_reprobados->close();
        $stmt_alumnos->close();
        $conn->close();
        ?>

        <!-- Script JavaScript para abrir la nueva ventana -->
        <script>
        function openGraphWindow(porcentajeAlumnosAprobados, porcentajeAlumnosReprobados, porcentajeMateriasAprobadas, porcentajeMateriasReprobadas, porcentajeCreditosAprobados, porcentajeCreditosReprobados, nombreGrupo, periodo, parcial) {
            var url = 'graficass.php?porcentaje_alumnos_aprobados=' + porcentajeAlumnosAprobados +
            '&porcentaje_alumnos_reprobados=' + porcentajeAlumnosReprobados +
            '&porcentaje_materias_aprobadas=' + porcentajeMateriasAprobadas +
            '&porcentaje_materias_reprobadas=' + porcentajeMateriasReprobadas +
            '&porcentaje_creditos_aprobados=' + porcentajeCreditosAprobados +
            '&porcentaje_creditos_reprobados=' + porcentajeCreditosReprobados +
            '&nombre_grupo=' + encodeURIComponent(nombreGrupo) +
            '&periodo=' + periodo +
            '&parcial=' + parcial;
            var options = 'width=800,height=600,scrollbars=yes,resizable=yes';
            window.open(url, 'Gráfica de Alumnos, Materias y Créditos', options);
        }
        </script>
    </body>

    </html>
    </script>
</body>

</html>