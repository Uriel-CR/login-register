<?php

// Obtener el periodo y el parcial seleccionados
$periodo = isset($_GET['periodo']) ? $_GET['periodo'] : '1';
$parcial = isset($_GET['parcial']) ? intval($_GET['parcial']) : 1;

// Obtener los porcentajes de aprobados y reprobados
$alumnos_aprobados = isset($_GET['alumnos_aprobados']) ? floatval($_GET['alumnos_aprobados']) : 1.0;
$alumnos_reprobados = isset($_GET['alumnos_reprobados']) ? floatval($_GET['alumnos_reprobados']) : 1.0;
$materias_aprobadas = isset($_GET['materias_aprobadas']) ? floatval($_GET['materias_aprobadas']) : 1.0;
$materias_reprobadas = isset($_GET['materias_reprobadas']) ? floatval($_GET['materias_reprobadas']) : 1.0;

// Convertir los datos a formato JSON para usarlos en JavaScript
$porcentaje_aprobados_json = json_encode($alumnos_aprobados);
$porcentaje_reprobados_json = json_encode($alumnos_reprobados);

$porcentaje_aprobadas_json = json_encode($materias_aprobadas);
$porcentaje_reprobadas_json = json_encode($materias_reprobadas);
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
