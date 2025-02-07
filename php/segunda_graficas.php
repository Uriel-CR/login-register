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

// Suponemos que $periodo se pasa como parámetro GET
$periodo = isset($_GET['periodo']) ? intval($_GET['periodo']) : 1;

// Consulta para obtener datos de materias
$sql_materias = "
    SELECT 
        COUNT(c.id_materia) AS total_materias,
        SUM(CASE WHEN c.calif_final >= 70 THEN 1 ELSE 0 END) AS materias_acreditadas,
        SUM(CASE WHEN c.calif_final = 'N/A' THEN 1 ELSE 0 END) AS materias_no_acreditadas
    FROM calificaciones c
    WHERE c.id_periodo = ?
";

// Preparar y ejecutar la consulta para materias
$stmt = $conn->prepare($sql_materias);
$stmt->bind_param("i", $periodo);
$stmt->execute();
$result = $stmt->get_result();

// Datos para el gráfico de materias
$total_materias = 0;
$materias_acreditadas = 0;
$materias_no_acreditadas = 0;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_materias = $row['total_materias'];
    $materias_acreditadas = $row['materias_acreditadas'];
    $materias_no_acreditadas = $row['materias_no_acreditadas'];
}

$porcentaje_acreditadas = ($total_materias > 0) ? ($materias_acreditadas / $total_materias) * 100 : 0;
$porcentaje_no_acreditadas = ($total_materias > 0) ? ($materias_no_acreditadas / $total_materias) * 100 : 0;

$data_labels_materias = json_encode(['Materias Acreditadas', 'Materias No Acreditadas']);
$data_percentages_materias = json_encode([$porcentaje_acreditadas, $porcentaje_no_acreditadas]);

// Consulta para obtener datos de créditos
$sql_creditos = "
    SELECT 
        SUM(m.creditos) AS total_creditos,
        SUM(CASE WHEN c.calif_final >= 70 THEN m.creditos ELSE 0 END) AS creditos_aprobados,
        SUM(CASE WHEN c.calif_final = 'N/A' THEN m.creditos ELSE 0 END) AS creditos_reprobados
    FROM calificaciones c
    JOIN materias m ON c.id_materia = m.id_materia
    WHERE c.id_periodo = ?
";

// Preparar y ejecutar la consulta para créditos
$stmt = $conn->prepare($sql_creditos);
$stmt->bind_param("i", $periodo);
$stmt->execute();
$result = $stmt->get_result();

// Datos para el gráfico de créditos
$total_creditos = 0;
$creditos_aprobados = 0;
$creditos_reprobados = 0;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_creditos = $row['total_creditos'];
    $creditos_aprobados = $row['creditos_aprobados'];
    $creditos_reprobados = $row['creditos_reprobados'];
}

$porcentaje_creditos_aprobados = ($total_creditos > 0) ? ($creditos_aprobados / $total_creditos) * 100 : 0;
$porcentaje_creditos_reprobados = ($total_creditos > 0) ? ($creditos_reprobados / $total_creditos) * 100 : 0;

$data_labels_creditos = json_encode(['Créditos Aprobados', 'Créditos No Aprobados']);
$data_percentages_creditos = json_encode([$porcentaje_creditos_aprobados, $porcentaje_creditos_reprobados]);

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen por Grupos</title>
    <link rel="stylesheet" href="../assets/css/style.css" type="text/css">
    <link rel="stylesheet" href="print.css" media="print">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('https://cdn.forbes.com.mx/2021/04/negocios-digitales.jpg'); /* Fondo de imagen */
            background-size: cover;
            background-position: center;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            background: rgba(255, 255, 255, 0.8); /* Fondo blanco con transparencia */
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            position: relative;
            min-height: 400px; /* Asegura que el contenedor tenga suficiente altura */
        }

        h2 {
            text-align: center;
            color: #007bff;
        }

        .chart-container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }

        .btn-print {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .btn-print:hover {
            background-color: #218838;
        }

        .boton-regresar {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
        }

        .boton-regresar:hover {
            background-color: #0056b3;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
</head>
<body>
    <div class="container">
        <h2>Porcentaje General de Materias y Créditos</h2>

        <div class="chart-container">
            <h3>Materias</h3>
            <canvas id="materiasChart"></canvas>
        </div>
        <div class="chart-container">
            <h3>Créditos</h3>
            <canvas id="creditosChart"></canvas>
        </div>

        <a href="#" class="btn-print" onclick="printPage()">Imprimir</a>

        <a href="despues_segunda.php" class="boton-regresar">Regresar</a>
    </div>

    <script>
        // Obtener los datos PHP en JavaScript
        const labelsMaterias = <?php echo $data_labels_materias; ?>;
        const porcentajesMaterias = <?php echo $data_percentages_materias; ?>;

        const labelsCreditos = <?php echo $data_labels_creditos; ?>;
        const porcentajesCreditos = <?php echo $data_percentages_creditos; ?>;

        // Crear gráfico para materias
        const ctxMaterias = document.getElementById('materiasChart').getContext('2d');
        new Chart(ctxMaterias, {
            type: 'bar',
            data: {
                labels: labelsMaterias,
                datasets: [{
                    label: 'Porcentaje de Materias',
                    data: porcentajesMaterias,
                    backgroundColor: ['#FF00FB', '#06f3da'],
                    borderColor: ['#FF00FB', '#06f3da'],
                    borderWidth: 1
                }]
            },
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
                        text: `Resultados para el Periodo ${<?php echo json_encode($periodo); ?>}`
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
        });

        // Crear gráfico para créditos
        const ctxCreditos = document.getElementById('creditosChart').getContext('2d');
        new Chart(ctxCreditos, {
            type: 'bar',
            data: {
                labels: labelsCreditos,
                datasets: [{
                    label: 'Porcentaje de Créditos',
                    data: porcentajesCreditos,
                    backgroundColor: ['#2196F3', '#FF9800'],
                    borderColor: ['#2196F3', '#FF9800'],
                    borderWidth: 1
                }]
            },
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
                        text: `Resultados para el Periodo ${<?php echo json_encode($periodo); ?>}`
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
        });

        // Función para imprimir la página
        function printPage() {
            window.print();
        }
    </script>
</body>
</html>
