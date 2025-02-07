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

// Obtener el periodo y el parcial seleccionados
$periodo = isset($_GET['periodo']) ? intval($_GET['periodo']) : 1;
$parcial = isset($_GET['parcial']) ? intval($_GET['parcial']) : 1;

// Consultas SQL para sumar totales y contar el total de alumnos
$sql_totales = "
    SELECT
        SUM(CASE WHEN CAST(pg.Promedio_parcial{$parcial} AS DECIMAL) >= 70 THEN 1 ELSE 0 END) AS total_aprobados,
        SUM(CASE WHEN pg.Promedio_parcial{$parcial} = 'N/A' THEN 1 ELSE 0 END) AS total_reprobados,
        COUNT(*) AS total_alumnos
    FROM promedios_generales pg
    WHERE pg.id_periodo = ?";

$stmt_totales = $conn->prepare($sql_totales);
$stmt_totales->bind_param("i", $periodo);
$stmt_totales->execute();
$result_totales = $stmt_totales->get_result();

$total_aprobados = 0;
$total_reprobados = 0;
$total_alumnos = 0;

// Procesar resultados
if ($row = $result_totales->fetch_assoc()) {
    $total_aprobados = $row['total_aprobados'];
    $total_reprobados = $row['total_reprobados'];
    $total_alumnos = $row['total_alumnos'];
}

// Calcular porcentajes
$porcentaje_aprobados = $total_alumnos > 0 ? ($total_aprobados / $total_alumnos) * 100 : 0;
$porcentaje_reprobados = $total_alumnos > 0 ? ($total_reprobados / $total_alumnos) * 100 : 0;

// Convertir los datos a formato JSON para usarlos en JavaScript
$porcentaje_aprobados_json = json_encode($porcentaje_aprobados);
$porcentaje_reprobados_json = json_encode($porcentaje_reprobados);

// Consultas SQL para obtener el total de materias aprobadas y reprobadas
$sql_total_aprobadas = "
    SELECT SUM(CASE WHEN ? = 1 THEN parcial_1_aprobados 
                    WHEN ? = 2 THEN parcial_2_aprobados 
                    WHEN ? = 3 THEN parcial_3_aprobados 
                    ELSE 0 END) AS total_aprobadas
    FROM calificaciones_resumen
    WHERE id_periodo = ?";

$sql_total_reprobadas = "
    SELECT SUM(CASE WHEN ? = 1 THEN parcial_1_reprobados 
                    WHEN ? = 2 THEN parcial_2_reprobados 
                    WHEN ? = 3 THEN parcial_3_reprobados 
                    ELSE 0 END) AS total_reprobadas
    FROM calificaciones_resumen
    WHERE id_periodo = ?";

// Preparar y ejecutar consultas
$stmt_total_aprobadas = $conn->prepare($sql_total_aprobadas);
$stmt_total_aprobadas->bind_param("iiii", $parcial, $parcial, $parcial, $periodo);
$stmt_total_aprobadas->execute();
$result_total_aprobadas = $stmt_total_aprobadas->get_result()->fetch_assoc();

$stmt_total_reprobadas = $conn->prepare($sql_total_reprobadas);
$stmt_total_reprobadas->bind_param("iiii", $parcial, $parcial, $parcial, $periodo);
$stmt_total_reprobadas->execute();
$result_total_reprobadas = $stmt_total_reprobadas->get_result()->fetch_assoc();

// Obtener los totales
$total_aprobadas = $result_total_aprobadas['total_aprobadas'];
$total_reprobadas = $result_total_reprobadas['total_reprobadas'];

// Calcular el total general
$total_materias = $total_aprobadas + $total_reprobadas;

// Calcular porcentajes
$porcentaje_aprobadas = ($total_materias > 0) ? ($total_aprobadas / $total_materias) * 100 : 0;
$porcentaje_reprobadas = ($total_materias > 0) ? ($total_reprobadas / $total_materias) * 100 : 0;

// Convertir los datos a formato JSON para usarlos en JavaScript
$porcentaje_aprobadas_json = json_encode($porcentaje_aprobadas);
$porcentaje_reprobadas_json = json_encode($porcentaje_reprobadas);

$stmt_total_aprobadas->close();
$stmt_total_reprobadas->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gráficos de Aprobados y Reprobados</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <style>
        body {
            /* Añadir una imagen de fondo */
            background-image: url('https://blog.interfell.com/hubfs/Qu%C3%A9%20est%C3%A1%20pasando%20en%20la%20industria%20de%20la%20tecnolog%C3%ADa.jpg');
            background-size: cover; /* Hace que la imagen cubra toda la pantalla */
            background-position: center; /* Centra la imagen */
            background-repeat: no-repeat; /* Evita que la imagen se repita */
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8); /* Fondo blanco con opacidad */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra ligera */
        }

        .chart-container {
            position: relative;
            margin-bottom: 20px;
        }

        button {
            margin: 10px 0;
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Resumen de Alumnos</h2>
        <button onclick="printPage()">Imprimir</button>
        <div class="chart-container">
            <canvas id="barChart"></canvas>
        </div>

        <h2>Resumen de Materias</h2>
        <div class="chart-container">
            <canvas id="barChartMaterias"></canvas>
        </div>
    </div>

    <script>
        // Datos para el gráfico de aprobados y reprobados
        const porcentajeAprobados = <?php echo $porcentaje_aprobados_json; ?>;
        const porcentajeReprobados = <?php echo $porcentaje_reprobados_json; ?>;

        const ctx = document.getElementById('barChart').getContext('2d');
        const dataAprobadosReprobados = {
            labels: ['Aprobados', 'Reprobados'],
            datasets: [{
                label: 'Porcentaje de Alumnos',
                data: [porcentajeAprobados, porcentajeReprobados],
                backgroundColor: [
                    '#FF00FB',
                    '#06f3da'
                ],
                borderColor: [
                    '#FF00FB',
                    '#06f3da'
                ],
                borderWidth: 1
            }]
        };

        const configAprobadosReprobados = {
            type: 'bar',
            data: dataAprobadosReprobados,
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
                        },
                        title: {
                            display: true,
                            text: 'Porcentaje (%)'
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        };

        new Chart(ctx, configAprobadosReprobados);

        // Datos para el gráfico de materias aprobadas y reprobadas
        const porcentajeAprobadasMaterias = <?php echo $porcentaje_aprobadas_json; ?>;
        const porcentajeReprobadasMaterias = <?php echo $porcentaje_reprobadas_json; ?>;

        const ctxMaterias = document.getElementById('barChartMaterias').getContext('2d');
        const dataMaterias = {
            labels: ['Materias Aprobadas', 'Materias Reprobadas'],
            datasets: [{
                label: 'Porcentaje de Materias',
                data: [porcentajeAprobadasMaterias, porcentajeReprobadasMaterias],
                backgroundColor: [
                    '#FF00FB',
                    '#06f3da'
                ],
                borderColor: [
                    '#FF00FB',
                    '#06f3da'
                ],
                borderWidth: 1
            }]
        };

        const configMaterias = {
            type: 'bar',
            data: dataMaterias,
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
                        },
                        title: {
                            display: true,
                            text: 'Porcentaje'
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        };

        new Chart(ctxMaterias, configMaterias);

        // Función para imprimir la página
        function printPage() {
            window.print();
        }
    </script>
</body>
</html>
