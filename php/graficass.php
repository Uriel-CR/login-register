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

// Obtener los parámetros de la solicitud
$id_grupo = isset($_GET['id_grupo']) ? intval($_GET['id_grupo']) : 0;
$periodo = isset($_GET['periodo']) ? intval($_GET['periodo']) : 1;
$parcial = isset($_GET['parcial']) ? intval($_GET['parcial']) : 1;

// Consultas SQL
$sql_grupos = "
    SELECT g.nombre_grupo, 
           pg.id_grupo, 
           SUM(CASE WHEN CAST(pg.Promedio_parcial1 AS DECIMAL) >= 70 THEN 1 ELSE 0 END) AS aprobados_p1,
           SUM(CASE WHEN pg.Promedio_parcial1 = 'N/A' THEN 1 ELSE 0 END) AS reprobados_p1,
           SUM(CASE WHEN CAST(pg.Promedio_parcial2 AS DECIMAL) >= 70 THEN 1 ELSE 0 END) AS aprobados_p2,
           SUM(CASE WHEN pg.Promedio_parcial2 = 'N/A' THEN 1 ELSE 0 END) AS reprobados_p2,
           SUM(CASE WHEN CAST(pg.Promedio_parcial3 AS DECIMAL) >= 70 THEN 1 ELSE 0 END) AS aprobados_p3,
           SUM(CASE WHEN pg.Promedio_parcial3 = 'N/A' THEN 1 ELSE 0 END) AS reprobados_p3
    FROM promedios_generales pg
    JOIN grupos g ON pg.id_grupo = g.id_grupo
    WHERE pg.id_grupo = ? AND pg.id_periodo = ?
    GROUP BY pg.id_grupo, g.nombre_grupo";

$stmt_grupos = $conn->prepare($sql_grupos);
$stmt_grupos->bind_param("ii", $id_grupo, $periodo);
$stmt_grupos->execute();
$result_grupos = $stmt_grupos->get_result()->fetch_assoc();

// Consultas adicionales
$sql_alumnos = "
    SELECT id_grupo, COUNT(DISTINCT id_alumno) AS total_alumnos
    FROM calificaciones
    WHERE id_periodo = ?
    GROUP BY id_grupo";

$sql_resumen = "
    SELECT id_grupo, 
           SUM(CASE WHEN ? = 1 THEN parcial_1_aprobados 
                    WHEN ? = 2 THEN parcial_2_aprobados 
                    WHEN ? = 3 THEN parcial_3_aprobados 
                    ELSE 0 END) AS materias_aprobadas
    FROM calificaciones_resumen
    WHERE id_periodo = ?
    GROUP BY id_grupo";

$sql_reprobadas = "
    SELECT id_grupo, 
           SUM(CASE WHEN ? = 1 THEN parcial_1_reprobados 
                    WHEN ? = 2 THEN parcial_2_reprobados 
                    WHEN ? = 3 THEN parcial_3_reprobados 
                    ELSE 0 END) AS materias_reprobadas
    FROM calificaciones_resumen
    WHERE id_periodo = ?
    GROUP BY id_grupo";

    $sql_creditos = "
    SELECT id_grupo,
           SUM(CASE WHEN ? = 1 THEN creditos_aprobados_parcial_1 ELSE 0 END) AS total_aprobados_parcial_1,
           SUM(CASE WHEN ? = 1 THEN creditos_reprobados_parcial_1 ELSE 0 END) AS total_reprobados_parcial_1,
           SUM(CASE WHEN ? = 2 THEN creditos_aprobados_parcial_2 ELSE 0 END) AS total_aprobados_parcial_2,
           SUM(CASE WHEN ? = 2 THEN creditos_reprobados_parcial_2 ELSE 0 END) AS total_reprobados_parcial_2,
           SUM(CASE WHEN ? = 3 THEN creditos_aprobados_parcial_3 ELSE 0 END) AS total_aprobados_parcial_3,
           SUM(CASE WHEN ? = 3 THEN creditos_reprobados_parcial_3 ELSE 0 END) AS total_reprobados_parcial_3
    FROM calificaciones_resumen
    WHERE id_grupo = ? AND id_periodo = ?
    GROUP BY id_grupo";


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

$stmt_creditos = $conn->prepare($sql_creditos);
$stmt_creditos->bind_param("iiiiiiii", $parcial, $parcial, $parcial, $parcial, $parcial, $parcial, $id_grupo, $periodo);
$stmt_creditos->execute();
$result_creditos = $stmt_creditos->get_result()->fetch_assoc();

$materias_aprobadas_por_grupo = [];
while ($row_resumen = $result_resumen->fetch_assoc()) {
    $materias_aprobadas_por_grupo[$row_resumen['id_grupo']] = $row_resumen['materias_aprobadas'];
}

$materias_reprobadas_por_grupo = [];
while ($row_reprobadas = $result_reprobadas->fetch_assoc()) {
    $materias_reprobadas_por_grupo[$row_reprobadas['id_grupo']] = $row_reprobadas['materias_reprobadas'];
}

$total_alumnos_por_grupo = [];
while ($row_alumnos = $result_alumnos->fetch_assoc()) {
    $total_alumnos_por_grupo[$row_alumnos['id_grupo']] = $row_alumnos['total_alumnos'];
}

// Verifica los resultados obtenidos
if ($result_grupos) {
    $nombre_grupo = htmlspecialchars($result_grupos['nombre_grupo']);
    $alumnos_aprobados = $result_grupos['aprobados_p' . $parcial];
    $alumnos_reprobados = $result_grupos['reprobados_p' . $parcial];
    $total_alumnos = $alumnos_aprobados + $alumnos_reprobados;
} else {
    $nombre_grupo = "Grupo desconocido"; // Valor predeterminado si no se encuentran resultados
    $alumnos_aprobados = 0;
    $alumnos_reprobados = 0;
    $total_alumnos = 0;
}

$materias_aprobadas = isset($materias_aprobadas_por_grupo[$id_grupo]) ? $materias_aprobadas_por_grupo[$id_grupo] : 0;
$materias_reprobadas = isset($materias_reprobadas_por_grupo[$id_grupo]) ? $materias_reprobadas_por_grupo[$id_grupo] : 0;
$total_materias = $materias_aprobadas + $materias_reprobadas;

$total_aprobados_creditos = isset($result_creditos['total_aprobados_parcial_' . $parcial]) && is_numeric($result_creditos['total_aprobados_parcial_' . $parcial]) ? (float)$result_creditos['total_aprobados_parcial_' . $parcial] : 0;
$total_reprobados_creditos = isset($result_creditos['total_reprobados_parcial_' . $parcial]) && is_numeric($result_creditos['total_reprobados_parcial_' . $parcial]) ? (float)$result_creditos['total_reprobados_parcial_' . $parcial] : 0;

$total_creditos = $total_aprobados_creditos + $total_reprobados_creditos;
$porcentaje_creditos_aprobados = number_format(($total_creditos > 0) ? ($total_aprobados_creditos / $total_creditos) * 100 : 0, 2);
$porcentaje_creditos_reprobados = number_format(($total_creditos > 0) ? ($total_reprobados_creditos / $total_creditos) * 100 : 0, 2);



// Cerrar conexión
$stmt_grupos->close();
$stmt_alumnos->close();
$stmt_resumen->close();
$stmt_reprobadas->close();
$stmt_creditos->close();
$conn->close();
?>

    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráfica de Resultados</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.0/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('https://revistaroadone.com/wp-content/uploads/2020/10/IA_inteligencia-artificial-IA.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 25px;
            text-align: center;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
        }
        table {
            width: 60%;
            margin: 20px auto; /* Centrando la tabla */
            border-collapse: collapse;
            /* Fondo blanco para la tabla */
            background-color: rgba(255, 255, 255, 0.8);
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 15px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        .highlight {
            font-weight: bold;
        }
        .print-button {
            display: block; /* Asegura que el botón se trate como un bloque */
            margin: 50px auto; /* Centra el botón */
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .print-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Grupo: <?php echo $nombre_grupo; ?></h2>
        <div class="chart-container">
            <canvas id="myChart"></canvas>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Porcentaje Alumnos Acreditados</td>
                    <td><?php echo $total_alumnos > 0 ? number_format(($alumnos_aprobados / $total_alumnos) * 100, 2) . '%' : '0%'; ?></td>
                </tr>
                <tr>
                    <td>Porcentaje Alumnos No Acreditados</td>
                    <td><?php echo $total_alumnos > 0 ? number_format(($alumnos_reprobados / $total_alumnos) * 100, 2) . '%' : '0%'; ?></td>
                </tr>
                <tr>
                    <td>Porcentaje Materias Acreditadas</td>
                    <td><?php echo $total_materias > 0 ? number_format(($materias_aprobadas / $total_materias) * 100, 2) . '%' : '0%'; ?></td>
                </tr>
                <tr>
                    <td>Porcentaje Materias No Acreditadas</td>
                    <td><?php echo $total_materias > 0 ? number_format(($materias_reprobadas / $total_materias) * 100, 2) . '%' : '0%'; ?></td>
                </tr>
                <tr>
                    <td>Porcentaje Créditos Acreditados</td>
                    <td><?php echo $total_creditos > 0 ? number_format(($total_aprobados_creditos / $total_creditos) * 100,2) . '%': '0%'; ?></td>
                </tr>
                <tr>
                    <td>Porcentaje Créditos No Acreditados</td>
                    <td><?php echo $total_creditos > 0 ? number_format(($total_reprobados_creditos / $total_creditos) * 100,2) . '%': '0%'; ?></td>
                </tr>
            </tbody>
        </table>
        <button class="print-button" onclick="window.print()">Imprimir</button>
    </div>
    <script>
        const ctx = document.getElementById('myChart').getContext('2d');

        const data = {
            labels: [
                'Porcentaje de Alumnos Acreditados',
                'Porcentaje de Alumnos No Acreditados',
                'Porcentaje de Materias Acreditadas',
                'Porcentaje de Materias No Acreditadas',
                'Porcentaje de Créditos Acreditados',
                'Porcentaje de Créditos No Acreditados'
            ],
            datasets: [{
                label: 'Porcentajes',
                data: [
                    <?php echo $total_alumnos > 0 ? number_format(($alumnos_aprobados / $total_alumnos) * 100, 2) : 0; ?>,
                    <?php echo $total_alumnos > 0 ? number_format(($alumnos_reprobados / $total_alumnos) * 100, 2) : 0; ?>,
                    <?php echo $total_materias > 0 ? number_format(($materias_aprobadas / $total_materias) * 100, 2) : 0; ?>,
                    <?php echo $total_materias > 0 ? number_format(($materias_reprobadas / $total_materias) * 100, 2) : 0; ?>,
                    <?php echo $total_creditos > 0 ? number_format(($total_aprobados_creditos / $total_creditos) * 100, 2) : 0; ?>,
                    <?php echo $total_creditos > 0 ? number_format(($total_reprobados_creditos / $total_creditos) * 100, 2) : 0; ?>,
                    
                ],
                backgroundColor: [
                    '#FF00FB',
                    '#06f3da',
                    '#2196F3',
                    '#FF9800',
                    '#F44336',
                    '#fbff00'
                ]
            }]
        };

        const config = {
    type: 'bar',
    data: data,
    options: {
        plugins: {
            datalabels: {
                formatter: (value) => value.toFixed(2) + '%',
                color: '#000',
                font: {
                    weight: 'bold'
                }
            },
            legend: {
                display: true,
                position: 'bottom'
            },
            title: {
                display: true,
                text: `Resultados para el Periodo ${<?php echo json_encode($periodo); ?>} y Parcial ${<?php echo json_encode($parcial); ?>}`
            }
        },
        scales: {
            x: {
                stacked: true,
                beginAtZero: true
            },
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        }
    },
    plugins: [ChartDataLabels]
};


        new Chart(ctx, config);
    </script>
</body>
</html>


