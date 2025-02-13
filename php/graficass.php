<?php
// Obtener los parámetros de la solicitud
$porcentaje_alumnos_aprobados = isset($_GET['porcentaje_alumnos_aprobados']) ? floatval($_GET['porcentaje_alumnos_aprobados']) : 1.0;
$porcentaje_alumnos_reprobados = isset($_GET['porcentaje_alumnos_reprobados']) ? floatval($_GET['porcentaje_alumnos_reprobados']) : 1.0;
$porcentaje_materias_aprobadas = isset($_GET['porcentaje_materias_aprobadas']) ? floatval($_GET['porcentaje_materias_aprobadas']) : 1.0;
$porcentaje_materias_reprobadas = isset($_GET['porcentaje_materias_reprobadas']) ? floatval($_GET['porcentaje_materias_reprobadas']) : 1.0;
$porcentaje_creditos_aprobados = isset($_GET['porcentaje_creditos_aprobados']) ? floatval($_GET['porcentaje_creditos_aprobados']) : 1.0;
$porcentaje_creditos_reprobados = isset($_GET['porcentaje_creditos_reprobados']) ? floatval($_GET['porcentaje_creditos_reprobados']) : 1.0;

$nombre_grupo = isset($_GET['nombre_grupo']) ? $_GET['nombre_grupo'] : '';
$periodo = isset($_GET['periodo']) ? intval($_GET['periodo']) : 1;
$parcial = isset($_GET['parcial']) ? intval($_GET['parcial']) : 1;

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
                    <td><?php echo $porcentaje_alumnos_aprobados . '%'; ?></td>
                </tr>
                <tr>
                    <td>Porcentaje Alumnos No Acreditados</td>
                    <td><?php echo $porcentaje_alumnos_reprobados . '%' ; ?></td>
                </tr>
                <tr>
                    <td>Porcentaje Materias Acreditadas</td>
                    <td><?php echo $porcentaje_materias_aprobadas . '%'; ?></td>
                </tr>
                <tr>
                    <td>Porcentaje Materias No Acreditadas</td>
                    <td><?php echo $porcentaje_materias_reprobadas . '%'; ?></td>
                </tr>
                <tr>
                    <td>Porcentaje Créditos Acreditados</td>
                    <td><?php echo $porcentaje_creditos_aprobados . '%'; ?></td>
                </tr>
                <tr>
                    <td>Porcentaje Créditos No Acreditados</td>
                    <td><?php echo $porcentaje_creditos_reprobados . '%'; ?></td>
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
                    <?php echo $porcentaje_alumnos_aprobados; ?>,
                    <?php echo $porcentaje_alumnos_reprobados; ?>,
                    <?php echo $porcentaje_materias_aprobadas; ?>,
                    <?php echo $porcentaje_materias_reprobadas; ?>,
                    <?php echo $porcentaje_creditos_aprobados; ?>,
                    <?php echo $porcentaje_creditos_reprobados; ?>,
                    
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


