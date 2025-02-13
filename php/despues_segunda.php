<?php
require 'verificar_sesion.php';
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

// Suponemos que $periodo se pasa como parámetro GET
$periodo = isset($_GET['periodo']) ? intval($_GET['periodo']) : 1;

// Consulta para obtener los datos

$sql = "
    SELECT 
        g.nombre_grupo,
        COUNT(c.id_materia) AS total_materias,
        SUM(CASE WHEN c.calif_final >= 70 THEN 1 ELSE 0 END) AS materias_acreditadas,
        SUM(CASE WHEN c.calif_final = 'N/A' THEN 1 ELSE 0 END) AS materias_no_acreditadas,
        SUM(m.creditos) AS total_creditos,
        SUM(CASE WHEN c.calif_final >= 70 THEN m.creditos ELSE 0 END) AS total_creditos_aprobados,
        SUM(CASE WHEN c.calif_final = 'N/A' THEN m.creditos ELSE 0 END) AS total_creditos_reprobados,
        (SELECT COUNT(DISTINCT id_alumno) 
         FROM calificaciones
         WHERE id_grupo = c.id_grupo
         AND id_periodo = c.id_periodo
         AND calif_final = 'N/A') AS total_reprobados,
        (SELECT COUNT(DISTINCT id_alumno)
         FROM calificaciones
         WHERE id_grupo = c.id_grupo) AS total_alumnos
    FROM calificaciones c
    JOIN grupos g ON c.id_grupo = g.id_grupo
    JOIN materias m ON c.id_materia = m.id_materia
    WHERE c.id_periodo = ?
    GROUP BY g.nombre_grupo
";

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $periodo);
$stmt->execute();
$result = $stmt->get_result();

// Consulta para obtener los periodos disponibles
$sql_periodos = "SELECT id_periodo, periodo FROM periodos ORDER BY id_periodo";
$result_periodos = $conn->query($sql_periodos);

// Obtener el nombre del periodo seleccionado
$sql_periodo_nombre = "SELECT periodo FROM periodos WHERE id_periodo = ?";
$stmt_periodo = $conn->prepare($sql_periodo_nombre);
$stmt_periodo->bind_param("i", $periodo);
$stmt_periodo->execute();
$result_periodo_nombre = $stmt_periodo->get_result();
$periodo_nombre = $result_periodo_nombre->fetch_assoc()['periodo'];
$stmt_periodo->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen de Calificaciones</title>
    <link rel="stylesheet" href="../assets/css/style.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/segunda.css" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<body>
<?php include 'header.html'; ?>
<!-- Formulario para seleccionar el período -->
<form method="GET" action="">
        <div class="form-group">
            <label for="periodo">Selecciona un período:</label>
            <select name="periodo" id="periodo">
                <?php
                // Llenar el menú desplegable con los períodos disponibles
                if ($result_periodos->num_rows > 0) {
                    while ($row = $result_periodos->fetch_assoc()) {
                        $selected = ($row['id_periodo'] == $periodo) ? 'selected' : '';
                        echo "<option value='{$row['id_periodo']}' $selected>{$row['periodo']}</option>";
                }
            }
            ?>
        </select>
        <input type="submit" value="Filtrar">
    </div>
</form>
    <table>
        <h2>Resumen de Calificaciones</h2>
        <thead>
            <tr>
                <th>Grupo</th>
                <th>Total Alumnos</th>
                <th>Alumnos Acreditados</th>
                <th>Porcentaje Alumnos Acreditados</th>
                <th>Alumnos No Acreditados</th>
                <th>Porcentaje Alumnos No Acreditados</th>
                <th>Total Materias</th>
                <th>Materias Acreditadas</th>
                <th>Porcentaje Acreditadas</th>
                <th>Materias No Acreditadas</th>
                <th>Porcentaje No Acreditadas</th>
                <th>Total Créditos</th>
                <th>Créditos Aprobados</th>
                <th>Porcentaje Créditos Aprobados</th>
                <th>Créditos Reprobados</th>
                <th>Porcentaje Créditos Reprobados</th>
                <th>Gráficas</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            $total_materias = 0;
            $total_acreditadas = 0;
            $total_no_acreditadas = 0;
            $total_creditos = 0;
            $total_creditos_aprobados = 0;
            $total_creditos_reprobados = 0;
            $total_alumnos = 0;
            $total_alumnos_acreditados = 0;
            $total_alumnos_no_acreditados = 0;

            while ($row = $result->fetch_assoc()) {
            $total = $row['total_materias'];
            $acreditadas = $row['materias_acreditadas'];
            $no_acreditadas = $row['materias_no_acreditadas'];

            // Calcular porcentajes
            $porcentaje_acreditadas = ($total > 0) ? ($acreditadas / $total) * 100 : 0;
            $porcentaje_no_acreditadas = ($total > 0) ? ($no_acreditadas / $total) * 100 : 0;

            // Calcular porcentajes de créditos
            $porcentaje_creditos_aprobados = ($row['total_creditos'] > 0) ? ($row['total_creditos_aprobados'] / $row['total_creditos']) * 100 : 0;
            $porcentaje_creditos_reprobados = ($row['total_creditos'] > 0) ? ($row['total_creditos_reprobados'] / $row['total_creditos']) * 100 : 0;

            // Calcular porcentajes de alumnos
            $total_alumnos_grupo = $row['total_alumnos'];
            $total_reprobados = $row['total_reprobados'];
            $total_acreditados = $total_alumnos_grupo - $total_reprobados;
            $porcentaje_alumnos_acreditados = ($total_alumnos_grupo > 0) ? ($total_acreditados / $total_alumnos_grupo) * 100 : 0;
            $porcentaje_alumnos_no_acreditados = ($total_alumnos_grupo > 0) ? ($total_reprobados / $total_alumnos_grupo) * 100 : 0;

            // Enlace para ver gráficas
            $grupo = $row['nombre_grupo'];

            echo "<tr>
                <td>{$row['nombre_grupo']}</td>
                <td>{$total_alumnos_grupo}</td>
                <td>{$total_acreditados}</td>
                <td>" . number_format($porcentaje_alumnos_acreditados, 2) . "%</td>
                <td>{$total_reprobados}</td>
                <td>" . number_format($porcentaje_alumnos_no_acreditados, 2) . "%</td>
                <td>{$total}</td>
                <td>{$acreditadas}</td>
                <td>" . number_format($porcentaje_acreditadas, 2) . "%</td>
                <td>{$no_acreditadas}</td>
                <td>" . number_format($porcentaje_no_acreditadas, 2) . "%</td>
                <td>{$row['total_creditos']}</td>
                <td>{$row['total_creditos_aprobados']}</td>
                <td>" . number_format($porcentaje_creditos_aprobados, 2) . "%</td>
                <td>{$row['total_creditos_reprobados']}</td>
                <td>" . number_format($porcentaje_creditos_reprobados, 2) . "%</td>
                <td><button onclick=\"openGraphWindow('{$grupo}')\" class='btn-graph'>Ver Gráficas</button></td>
            </tr>";

            $total_materias += $total;
            $total_acreditadas += $acreditadas;
            $total_no_acreditadas += $no_acreditadas;
            $total_creditos += $row['total_creditos'];
            $total_creditos_aprobados += $row['total_creditos_aprobados'];
            $total_creditos_reprobados += $row['total_creditos_reprobados'];
            $total_alumnos += $total_alumnos_grupo;
            $total_alumnos_acreditados += $total_acreditados;
            $total_alumnos_no_acreditados += $total_reprobados;
            }

            // Calcular porcentajes totales
            $total_porcentaje_acreditadas = ($total_materias > 0) ? ($total_acreditadas / $total_materias) * 100 : 0;
            $total_porcentaje_no_acreditadas = ($total_materias > 0) ? ($total_no_acreditadas / $total_materias) * 100 : 0;
            $total_porcentaje_creditos_aprobados = ($total_creditos > 0) ? ($total_creditos_aprobados / $total_creditos) * 100 : 0;
            $total_porcentaje_creditos_reprobados = ($total_creditos > 0) ? ($total_creditos_reprobados / $total_creditos) * 100 : 0;
            $total_porcentaje_alumnos_acreditados = ($total_alumnos > 0) ? ($total_alumnos_acreditados / $total_alumnos) * 100 : 0;
            $total_porcentaje_alumnos_no_acreditados = ($total_alumnos > 0) ? ($total_alumnos_no_acreditados / $total_alumnos) * 100 : 0;

            echo "<tr class='total-row'>
            <td>Total</td>
            <td>{$total_alumnos}</td>
            <td>{$total_alumnos_acreditados}</td>
            <td>" . number_format($total_porcentaje_alumnos_acreditados, 2) . "%</td>
            <td>{$total_alumnos_no_acreditados}</td>
            <td>" . number_format($total_porcentaje_alumnos_no_acreditados, 2) . "%</td>
            <td>{$total_materias}</td>
            <td>{$total_acreditadas}</td>
            <td>" . number_format($total_porcentaje_acreditadas, 2) . "%</td>
            <td>{$total_no_acreditadas}</td>
            <td>" . number_format($total_porcentaje_no_acreditadas, 2) . "%</td>
            <td>{$total_creditos}</td>
            <td>{$total_creditos_aprobados}</td>
            <td>" . number_format($total_porcentaje_creditos_aprobados, 2) . "%</td>
            <td>{$total_creditos_reprobados}</td>
            <td>" . number_format($total_porcentaje_creditos_reprobados, 2) . "%</td>
            <td><a href='segunda_graficas.php?periodo={$periodo}' class='btn-graph'>Ver Gráficas</a></td>
            </tr>";
        } else {
            echo "<tr><td colspan='17'>No hay datos disponibles para el período seleccionado.</td></tr>";
        }

        $stmt->close();
        $conn->close();
        ?>
        </tbody>
    </table>
    <script>
        function openGraphWindow(grupo) {
            window.open(
                'verr_graficas.php?grupo=' + encodeURIComponent(grupo) + '&periodo=' + <?php echo $periodo; ?>,
                'GraficaWindow',
                'width=800,height=600,scrollbars=yes'
            );
        }
        
    </script>
    
</body>
</html>
