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

// Obtener parámetros de la URL
$grupo = isset($_GET['grupo']) ? $_GET['grupo'] : '';
$periodo = isset($_GET['periodo']) ? intval($_GET['periodo']) : 1;

// Consulta para obtener los datos del grupo seleccionado
$sql = "
    SELECT 
        COUNT(c.id_materia) AS total_materias,
        SUM(CASE WHEN c.calif_final >= 70 THEN 1 ELSE 0 END) AS materias_acreditadas,
        SUM(CASE WHEN c.calif_final = 'N/A' THEN 1 ELSE 0 END) AS materias_no_acreditadas,
        SUM(m.creditos) AS total_creditos,
        SUM(CASE WHEN c.calif_final >= 70 THEN m.creditos ELSE 0 END) AS total_creditos_aprobados,
        SUM(CASE WHEN c.calif_final = 'N/A' THEN m.creditos ELSE 0 END) AS total_creditos_reprobados
    FROM calificaciones c
    JOIN grupos g ON c.id_grupo = g.id_grupo
    JOIN materias m ON c.id_materia = m.id_materia
    WHERE g.nombre_grupo = ? AND c.id_periodo = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $grupo, $periodo);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$total_materias = $data['total_materias'] ?? 0;
$acreditadas = $data['materias_acreditadas'] ?? 0;
$no_acreditadas = $data['materias_no_acreditadas'] ?? 0;
$total_creditos = $data['total_creditos'] ?? 0;
$creditos_aprobados = $data['total_creditos_aprobados'] ?? 0;
$creditos_reprobados = $data['total_creditos_reprobados'] ?? 0;

// Calcular porcentajes
$porcentaje_acreditadas = ($total_materias > 0) ? ($acreditadas / $total_materias) * 100 : 0;
$porcentaje_no_acreditadas = ($total_materias > 0) ? ($no_acreditadas / $total_materias) * 100 : 0;

// Calcular porcentajes de créditos
$porcentaje_creditos_aprobados = ($total_creditos > 0) ? ($creditos_aprobados / $total_creditos) * 100 : 0;
$porcentaje_creditos_reprobados = ($total_creditos > 0) ? ($creditos_reprobados / $total_creditos) * 100 : 0;

// Cerrar conexiones
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráficas del Grupo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('https://blogs.worldbank.org/content/dam/sites/blogs/img/detail/mgr/id4d_0.jpg') no-repeat center center fixed; /* Cambia 'tu-imagen-de-fondo.jpg' por el URL de tu imagen */
            background-size: cover;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            color: #17202a;
            margin: 20px 0;
            font-size: 24px;
            text-align: center;
        }

        .container {
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            background: rgba(255, 255, 255, 0.8); /* Fondo blanco con opacidad del 80% */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .button {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            margin-bottom: 20px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
            text-align: center;
        }

        .button:hover {
            background-color: #0056b3;
        }

        canvas {
            max-width: 100%;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                box-shadow: none;
                border: 1px solid #ddd;
                padding: 10px;
                display: block;
                text-align: center;
                background: rgba(255, 255, 255, 1); /* Fondo sólido en impresión */
            }

            .button {
                display: none;
            }

            canvas {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Gráficas Después de Segunda Oportunidad para el Grupo: <?php echo htmlspecialchars($grupo); ?></h2>
        <button class="button" onclick="printPage()"><i class="fas fa-print"></i> Imprimir</button>
        <canvas id="graficoMaterias" width="400" height="200"></canvas>
        <canvas id="graficoCreditos" width="400" height="200"></canvas>
        <script>
            const ctxMaterias = document.getElementById('graficoMaterias').getContext('2d');
            const ctxCreditos = document.getElementById('graficoCreditos').getContext('2d');

            const dataMaterias = {
                labels: ['Materias Acreditadas', 'Materias No Acreditadas'],
                datasets: [{
                    label: 'Porcentaje de Materias',
                    data: [
                        <?php echo number_format($porcentaje_acreditadas, 2); ?>,
                        <?php echo number_format($porcentaje_no_acreditadas, 2); ?>
                    ],
                    backgroundColor: [
                        '#FF00FB', // Color para Materias Acreditadas
                        '#06f3da'  // Color para Materias No Acreditadas
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
                            text: 'Porcentaje de Materias'
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

            new Chart(ctxMaterias, configMaterias);

            const dataCreditos = {
                labels: ['Créditos Aprobados', 'Créditos Reprobados'],
                datasets: [{
                    label: 'Porcentaje de Créditos',
                    data: [
                        <?php echo number_format($porcentaje_creditos_aprobados, 2); ?>,
                        <?php echo number_format($porcentaje_creditos_reprobados, 2); ?>
                    ],
                    backgroundColor: [
                        '#2196F3', // Color para Créditos Aprobados
                        '#FF9800'  // Color para Créditos Reprobados
                    ],
                    borderColor: [
                        '#2196F3',
                        '#FF9800'
                    ],
                    borderWidth: 1
                }]
            };

            const configCreditos = {
                type: 'bar',
                data: dataCreditos,
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
                            text: 'Porcentaje de Créditos'
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

            new Chart(ctxCreditos, configCreditos);

            function printPage() {
                window.print();
            }
        </script>
    </div>
</body>
</html>
