<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "serviciosocial";
$password = "FtW30yNo8hQd-x/G";
$dbname = "login_register_db";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Procesar búsqueda de alumnos
$alumnos = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buscar'])) {
    // Comprobar si 'matricula_buscar' está definido en $_POST
    if (isset($_POST['matricula_buscar']) && !empty($_POST['matricula_buscar'])) {
        $matricula_buscar = $_POST['matricula_buscar'];
        
        // Preparar la consulta
        $stmt_buscar = $conn->prepare("
            SELECT a.matricula, a.ap_paterno, a.ap_materno, a.nombre, g.nombre_grupo, p.periodo, m.nombre AS nombre_materia,
                   c.parcial_1, c.parcial_2, c.parcial_3, c.calif_final
            FROM alumnos a
            JOIN calificaciones c ON a.id_alumno = c.id_alumno
            JOIN grupos g ON c.id_grupo = g.id_grupo
            JOIN periodos p ON c.id_periodo = p.id_periodo
            JOIN materias m ON c.id_materia = m.id_materia
            WHERE a.matricula = ?");
        
        if ($stmt_buscar === false) {
            die('Error en la preparación de la consulta: ' . $conn->error);
        }

        // Vincular parámetros
        $stmt_buscar->bind_param("s", $matricula_buscar);

        // Ejecutar consulta
        if (!$stmt_buscar->execute()) {
            die('Error al ejecutar la consulta: ' . $stmt_buscar->error);
        }

        // Obtener resultados
        $result_buscar = $stmt_buscar->get_result();
        if ($result_buscar === false) {
            die('Error al obtener el resultado: ' . $stmt_buscar->error);
        }

        $alumnos = $result_buscar->fetch_all(MYSQLI_ASSOC);
        $stmt_buscar->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Alumno</title>
    <link rel="stylesheet" href="../assets/css/style.css" type="text/css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }
        h2 {
            text-align: center;
            color: #007BFF;
            margin-top: 20px;
        }
        .container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
            width: 90%;
            max-width: 1150px;
            margin: 20px auto;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input[type="text"] {
            width: calc(100% - 24px);
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn-buscar, .btn-regresar, .btn-imprimir {
            background-color: #007BFF;
            border: none;
            color: #fff;
            padding: 12px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            margin: 10px 0;
            transition: background-color 0.3s ease;
        }
        .btn-buscar:hover, .btn-imprimir:hover {
            background-color: #0056b3;
        }
        .btn-regresar {
            background-color: #6c757d;
        }
        .btn-regresar:hover {
            background-color: #5a6268;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .no-results {
            text-align: center;
            font-size: 18px;
            color: #888;
            margin-top: 20px;
        }
        .header {
            background-color: #007BFF;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
        }
        .nav {
            display: flex;
            align-items: center;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }
        .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
        }
        .nav-menu-item {
            margin-right: 15px;
        }
        .nav-menu-link {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .nav-menu-link.selected, .nav-menu-link:hover {
            background-color: #00509e; /* Azul más oscuro */
        }
        .nav-toggle {
            display: none; /* Mostrar solo en dispositivos móviles */
            background: none;
            border: none;
            cursor: pointer;
        }
        .nav-toggle-icon {
            width: 24px;
            height: 24px;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                box-shadow: none;
                width: 100%;
                max-width: none;
            }
            .btn-buscar, .btn-regresar, .btn-imprimir {
                display: none;
            }
            table {
                border-collapse: collapse;
                width: 100%;
            }
            th, td {
                border: 1px solid #000;
                padding: 10px;
            }
            th {
                background-color: #007BFF;
                color: #fff;
            }
        }
    </style>
    <script>
        function printTable() {
            var printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Imprimir Resultados</title>');
            printWindow.document.write('<style>@media print { body { margin: 0; padding: 0; } table { border-collapse: collapse; width: 100%; } th, td { border: 1px solid #000; padding: 10px; } th { background-color: #007BFF; color: #fff; } } </style>');
            printWindow.document.write('</head><body >');
            printWindow.document.write(document.getElementById('resultTable').outerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        }
    </script>
</head>
<body>
<header class="header">
    <nav class="nav">
        <a class="logo nav-link">TESI</a>
        <ul class="nav-menu">
            <li class="nav-menu-item"><a class="nav-menu-link" href="../php/bienvenida.php">Inicio</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link selected" href="../php/alumnos.php">Alumnos</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link" href="../php/materias.php">Materias</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link" href="../php/grupos.php">Grupos</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link" href="../php/profesores.php">Profesores</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link" href="../php/periodo.php">Periodo</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link" href="../php/asignacion_grupo.php">Calificaciones</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link" href="../php/resumen.php">Resumen</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link" href="../index.php">Cerrar Sesión</a></li>
        </ul>
        <button class="nav-toggle">
            <img src="../assets/images/menu.svg" class="nav-toggle-icon" alt="">
        </button>
    </nav>
</header>

<div class="container">
    <h2>Buscar Alumno</h2>
    <form method="post" action="">
        <div class="form-group">
            <label for="matricula_buscar">Buscar por Matrícula:</label>
            <input type="text" id="matricula_buscar" name="matricula_buscar" placeholder="Ingrese matrícula" required>
            <input type="submit" name="buscar" value="Buscar" class="btn-buscar">
        </div>
    </form>
   
    <?php if (!empty($alumnos)): ?>
        <h3>Resultados de Búsqueda:</h3>
        <button onclick="printTable()" class="btn-imprimir">Imprimir Tabla</button>
        <table id="resultTable">
            <thead>
                <tr>
                    <th>Matrícula</th>
                    <th>Apellido Paterno</th>
                    <th>Apellido Materno</th>
                    <th>Nombre</th>
                    <th>Grupo</th>
                    <th>Periodo</th>
                    <th>Materia</th>
                    <th>Parcial 1</th>
                    <th>Parcial 2</th>
                    <th>Parcial 3</th>
                    <th>Calificación Final</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alumnos as $alumno): ?>
                    <?php
                    $calif_final = isset($alumno['calif_final']) && is_numeric($alumno['calif_final']) ? $alumno['calif_final'] : '0';
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($alumno['matricula']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['ap_paterno']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['ap_materno']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['nombre_grupo']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['periodo']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['nombre_materia']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['parcial_1']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['parcial_2']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['parcial_3']); ?></td>
                        <td><?php echo number_format(floatval($calif_final), 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-results">No se encontraron resultados.</p>
    <?php endif; ?>

    <!-- Botón de Regresar -->
    <a href="alumnos.php" class="btn-regresar">Regresar</a>
    
</div>
</body>
</html>
