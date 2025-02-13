<?php require 'verificar_sesion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Índice de Acreditados y No Acreditados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('https://static.vecteezy.com/system/resources/previews/003/452/875/non_2x/wireframe-human-ai-system-and-infographic-photo.jpg');
            background-size: cover;
            color: #333;
            text-align: center;
            padding: 2px;
        }
        h1 {
            color: #FDFEFE;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 10px;
            border-radius: 5px;
        }
        .form-container {
            margin: 10px auto;
            padding: 3px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 5px;
            width: 90%;
            text-align: center;
        }
        .form-container select,
        .form-container button {
            padding: 10px;
            margin: 2px;
            font-size: 15px;
        }
        .button-group {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr.total-row {
            background-color: #4CAF50;
            font-weight: bold;
        }
        tr.total-row td {
            border: none;
        }
        .hide {
            display: none;
        }
        .button {
            padding: 10px;
            text-align: center;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .print-button {
            background-color: #28a745;
        }
        .print-button:hover {
            background-color: #218838;
        }
        .header {
            background-color: #007BFF;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            /* Hace que el encabezado se quede fijo en la parte superior */
            top: 0;
            /* Asegura que el encabezado esté en la parte superior de la página */
            width: 100%;
            /* Asegura que el encabezado ocupe todo el ancho de la ventana */
            z-index: 1000;
            /* Asegura que el encabezado esté sobre otros elementos */
        }
        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .nav-menu {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 15px;
        }
        .nav-menu-item {
            margin: 0;
        }
        .nav-menu-link {
            color: white;
            text-decoration: none;
            font-size: 16px;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }
        .nav-menu-link:hover, .nav-menu-link.selected {
            background-color: #00509e; /* Azul más oscuro */
            color: white;
        }
        .button-container {
            margin: 100px auto 20px; /* Añadido margen superior para no solapar con el encabezado fijo */
            text-align: center;
            max-width: 600px;
        }
        .button-container h2 {
            color: #003366;
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .custom-button {
            padding: 12px 24px;
            font-size: 18px;
            border: none;
            background-color: #007bff; /* Azul */
            color: white;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s;
        }
        .custom-button:hover {
            background-color: #0056b3; /* Azul más oscuro al pasar el ratón */
            transform: scale(1.05); /* Agranda ligeramente el botón al pasar el ratón */
        }
        .logo {
            
            font-size: 24px;
            font-weight: bold;
            position: fixed;
            left: 0;
            margin: 10px;
        }
    </style>
</head>
<body>
    <?php include 'header.html'; ?>

    <h1>Índice de Acreditados y No Acreditados</h1>
    <div class="form-container">
        <form id="reportForm" method="POST" action="">
            <label for="periodo">Selecciona el Periodo:</label>
            <select name="periodo" id="periodo" required>
                <option value="">Seleccionar</option>
                <?php
                $servername = "localhost";
                $username = "serviciosocial";
                $password = "FtW30yNo8hQd-x/G";
                $dbname = "login_register_db";

                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }

                $sql_periodos = "SELECT id_periodo, periodo FROM periodos";
                $result_periodos = $conn->query($sql_periodos);

                if ($result_periodos->num_rows > 0) {
                    while ($row_periodo = $result_periodos->fetch_assoc()) {
                        echo "<option value='" . $row_periodo['id_periodo'] . "'>" . $row_periodo['periodo'] . "</option>";
                    }
                } else {
                    echo "<option value=''>No hay periodos disponibles</option>";
                }

                $conn->close();
                ?>
            </select>
            <label for="parcial">Selecciona el Parcial:</label>
            <select name="parcial" id="parcial" required>
                <option value="">Seleccionar</option>
                <option value="1">Parcial 1</option>
                <option value="2">Parcial 2</option>
                <option value="3">Parcial 3</option>
                <option value="2o">Segunda Oportunidad</option>
            </select>
            <div class="button-group">
                <button type="submit" class="button">Mostrar Reporte</button>
                <button type="button" class="button" id="printButton">Imprimir Tabla</button>
            </div>
        </form>
    </div>

    <div id="tabla-reportes">
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['periodo']) && isset($_POST['parcial'])) {
            $id_periodo = $_POST['periodo'];
            $parcial = $_POST['parcial'];

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Conexión fallida: " . $conn->connect_error);
            }

            $sql_nombre_periodo = "SELECT periodo FROM periodos WHERE id_periodo = ?";
            $stmt_nombre_periodo = $conn->prepare($sql_nombre_periodo);
            $stmt_nombre_periodo->bind_param("i", $id_periodo);
            $stmt_nombre_periodo->execute();
            $stmt_nombre_periodo->store_result();
            $stmt_nombre_periodo->bind_result($nombre_periodo);
            $stmt_nombre_periodo->fetch();
            $stmt_nombre_periodo->close();

            echo "<h2>Periodo: " . htmlspecialchars($nombre_periodo) . "</h2>";
            echo "<h2>Parcial " . htmlspecialchars($parcial) . "</h2>";

            // Ajustar las columnas según el valor de $parcial
            if ($parcial == '2o') {
                $columna_aprobados = 'segunda_oportunidad_aprobados';
                $columna_reprobados = 'segunda_oportunidad_reprobados';
                $columna_creditos_aprobados = 'creditos_aprobados_segunda_oportunidad';
                $columna_creditos_reprobados = 'creditos_reprobados_segunda_oportunidad';
            } else {
                $columna_aprobados = "parcial_{$parcial}_aprobados";
                $columna_reprobados = "parcial_{$parcial}_reprobados";
                $columna_creditos_aprobados = "creditos_aprobados_parcial_{$parcial}";
                $columna_creditos_reprobados = "creditos_reprobados_parcial_{$parcial}";
            }

            $sql_reporte = "SELECT DISTINCT
                g.id_grupo, 
                g.nombre_grupo,
                m.nombre AS nombre_materia,
                m.clave_materia AS clave_materia,
                m.creditos AS creditos_materia,
                r.total_alumnos,
                r.{$columna_aprobados} AS alumnos_aprobados,
                r.{$columna_reprobados} AS alumnos_reprobados,
                IFNULL((r.{$columna_aprobados} / r.total_alumnos) * 100, 0) AS porcentaje_aprobados,
                IFNULL((r.{$columna_reprobados} / r.total_alumnos) * 100, 0) AS porcentaje_reprobados,
                IFNULL(r.{$columna_creditos_aprobados}, 0) AS creditos_aprobados,
                IFNULL(r.{$columna_creditos_reprobados}, 0) AS creditos_reprobados,
                IFNULL((r.{$columna_creditos_aprobados} / (r.{$columna_creditos_aprobados} + r.{$columna_creditos_reprobados})) * 100, 0) AS porcentaje_creditos_aprobados,
                IFNULL((r.{$columna_creditos_reprobados} / (r.{$columna_creditos_aprobados} + r.{$columna_creditos_reprobados})) * 100, 0) AS porcentaje_creditos_reprobados
            FROM calificaciones_resumen r
            JOIN grupos g ON r.id_grupo = g.id_grupo
            JOIN materias m ON r.id_materia = m.id_materia
            WHERE r.id_periodo = ?
            ORDER BY g.nombre_grupo, m.nombre";

            $stmt_reporte = $conn->prepare($sql_reporte);
            $stmt_reporte->bind_param("i", $id_periodo);
            $stmt_reporte->execute();
            $result_reporte = $stmt_reporte->get_result();

            if ($result_reporte->num_rows > 0) {
                echo "<table id='reporteTable'>
                    <thead>
                        <tr>
                            <th>No</th>                       
                            <th>Grupo</th>
                            <th>Clave Materia</th>       
                            <th>Materia</th>
                            <th>Créditos Materia</th>      
                            <th>Total Alumnos</th>
                            <th>Alumnos Acreditados</th>
                            <th>Alumnos No Acreditados</th>
                            <th>% de Acreditacion</th>
                            <th>% de No Acreditacion</th>
                            <th>Creditos Acreditados</th>
                            <th>Creditos No Acreditados</th>
                            <th>% de Creditos Acreditados</th>
                            <th>% de Creditos No Acreditados</th>
                        </tr>
                    </thead>
                    <tbody>";

                $total_alumnos = 0;
                $total_aprobados = 0;
                $total_reprobados = 0;
                $total_materias_aprobadas = 0;
                $total_materias_reprobadas = 0;
                $numero_fila = 1;

                while ($row = $result_reporte->fetch_assoc()) {
                    if ($parcial == '2o') {
                        $total_alumnos_parcial = $row['alumnos_aprobados'] + $row['alumnos_reprobados'];
                    } else {
                        $total_alumnos_parcial = $row['total_alumnos'];
                    }

                    $porcentaje_aprobados = $total_alumnos_parcial > 0 ? ($row['alumnos_aprobados'] / $total_alumnos_parcial) * 100 : 0;
                    $porcentaje_reprobados = $total_alumnos_parcial > 0 ? ($row['alumnos_reprobados'] / $total_alumnos_parcial) * 100 : 0;
                    $total_creditos = $row['creditos_aprobados'] + $row['creditos_reprobados'];
                    $porcentaje_creditos_aprobados = $total_creditos > 0 ? ($row['creditos_aprobados'] / $total_creditos) * 100 : 0;
                    $porcentaje_creditos_reprobados = $total_creditos > 0 ? ($row['creditos_reprobados'] / $total_creditos) * 100 : 0;

                    echo "<tr>
                            <td>" . $numero_fila++ . "</td>   
                            <td>" . htmlspecialchars($row['nombre_grupo']) . "</td>
                            <td>" . htmlspecialchars($row['clave_materia']) . "</td>           
                            <td>" . htmlspecialchars($row['nombre_materia']) . "</td>
                            <td>" . htmlspecialchars($row['creditos_materia']) . "</td>         
                            <td>" . htmlspecialchars($total_alumnos_parcial) . "</td>
                            <td>" . htmlspecialchars($row['alumnos_aprobados']) . "</td>
                            <td>" . htmlspecialchars($row['alumnos_reprobados']) . "</td>
                            <td>" . number_format($porcentaje_aprobados, 2) . "%</td>
                            <td>" . number_format($porcentaje_reprobados, 2) . "%</td>
                            <td>" . htmlspecialchars($row['creditos_aprobados']) . "</td>
                            <td>" . htmlspecialchars($row['creditos_reprobados']) . "</td>
                            <td>" . number_format($porcentaje_creditos_aprobados, 2) . "%</td>
                            <td>" . number_format($porcentaje_creditos_reprobados, 2) . "%</td>
                        </tr>";

                    $total_alumnos += $total_alumnos_parcial;
                    $total_aprobados += $row['alumnos_aprobados'];
                    $total_reprobados += $row['alumnos_reprobados'];
                    $total_materias_aprobadas += $row['creditos_aprobados'];
                    $total_materias_reprobadas += $row['creditos_reprobados'];
                }

                $total_porcentaje_aprobados = ($total_alumnos > 0) ? ($total_aprobados / $total_alumnos) * 100 : 0;
                $total_porcentaje_reprobados = ($total_alumnos > 0) ? ($total_reprobados / $total_alumnos) * 100 : 0;
                $total_creditos = $total_materias_aprobadas + $total_materias_reprobadas;
                $total_porcentaje_materias_aprobadas = ($total_creditos > 0) ? ($total_materias_aprobadas / $total_creditos) * 100 : 0;
                $total_porcentaje_materias_reprobadas = ($total_creditos > 0) ? ($total_materias_reprobadas / $total_creditos) * 100 : 0;

                echo "<tr class='total-row'>
                        <td>Total</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>" . $total_alumnos . "</td>
                        <td>" . $total_aprobados . "</td>
                        <td>" . $total_reprobados . "</td>
                        <td>" . number_format($total_porcentaje_aprobados, 2) . "%</td>
                        <td>" . number_format($total_porcentaje_reprobados, 2) . "%</td>
                        <td>" . $total_materias_aprobadas . "</td>
                        <td>" . $total_materias_reprobadas . "</td>
                        <td>" . number_format($total_porcentaje_materias_aprobadas, 2) . "%</td>
                        <td>" . number_format($total_porcentaje_materias_reprobadas, 2) . "%</td>
                    </tr>";

                echo "</tbody>
                </table>";
            } else {
                echo "<p>No se encontraron datos para el periodo y parcial seleccionados.</p>";
            }

            $stmt_reporte->close();
            $conn->close();
        }
        ?>
    </div>

    <script>
        document.getElementById('printButton').addEventListener('click', function() {
            var printWindow = window.open('', '', 'height=600,width=800');
            var table = document.getElementById('reporteTable').outerHTML;
            printWindow.document.write('<html><head><title>Imprimir Tabla</title>');
            printWindow.document.write('<style>table { width: 100%; border-collapse: collapse; } table, th, td { border: 1px solid black; } th, td { padding: 10px; text-align: left; } .total-row { font-weight: bold; }</style>');
            printWindow.document.write('</head><body >');
            printWindow.document.write('<h1>Reporte</h1>');
            printWindow.document.write(table);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        });
    </script>
</body>
</html>