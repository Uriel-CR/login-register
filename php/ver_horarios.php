<?php
require 'verificar_sesion.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Horarios</title>
    <link rel="stylesheet" href="../assets/css/style.css" type="text/css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .header {
            width: 100%;
            background-color: #007BFF;
            color: #fff;
            padding: 10px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .header .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .logo {

            font-size: 24px;
            font-weight: bold;
            position: fixed;
            left: 0;
            margin: 10px;
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

        .nav-menu-link.selected,
        .nav-menu-link:hover {
            background-color: #00509e;
        }

        .nav-toggle {
            display: none;
        }

        .container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 90%;
            max-width: 1200px;
            margin: 100px auto 20px;
        }

        h2 {
            text-align: center;
            color: #007bff;
        }

        label {
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }

        .btn-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 10px 2px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .search-container {
            margin: 20px 0;
            text-align: center;
        }

        .search-input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 300px;
            max-width: 100%;
        }

        .search-button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-left: 10px;
        }

        .search-button:hover {
            background-color: #0056b3;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
            font-size: 16px;
            min-width: 600px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table th,
        table td {
            padding: 12px 15px;
            text-align: left;
        }

        table th {
            background-color: #007bff;
            color: #ffffff;
        }

        table tr:nth-of-type(even) {
            background-color: #f3f3f3;
        }

        table tr:hover {
            background-color: #e9e9e9;
        }

        table td {
            color: #555555;
        }

        .professor-container {
            margin-bottom: 40px;
        }

        .professor-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #007bff;
        }

        .edit-button {
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }

        .edit-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <header class="header">
        <nav class="nav">
            <a class="logo nav-link" href="../php/bienvenida.php">TESI</a>
            <ul class="nav-menu">
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/alumnos.php">Alumnos</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/materias.php">Materias</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/grupos.php">Grupos</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link selected" href="../php/profesores.php">Profesores</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/periodo.php">Periodo</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/asignacion_grupo.php">Calificaciones</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/resumen.php">Resumen</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="cerrar_sesion.php">Cerrar Sesión</a></li>
            </ul>
            <button class="nav-toggle">
                <img src="../assets/images/menu.svg" class="nav-toggle-icon" alt="Menu">
            </button>
        </nav>
    </header>
    <div class="container">
        <div class="btn-container">
            <a href="profesores.php" class="btn">Ver Profesores</a>
        </div>

        <!-- Formulario de búsqueda -->
        <div class="search-container">
            <form method="GET" action="">
                <input type="text" name="search" class="search-input" placeholder="Buscar por nombre del profesor" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="search-button">Buscar</button>
            </form>
        </div>

        <?php
        // Configuración de la base de datos
        $servername = "localhost";
        $username = "serviciosocial";
        $password = "FtW30yNo8hQd-x/G";
        $database = "login_register_db";

        // Crear conexión
        $conn = new mysqli($servername, $username, $password, $database);

        // Verificar conexión
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        // Obtener el término de búsqueda
        $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

        // Solo ejecutar la consulta si hay un término de búsqueda
        if (!empty($search)) {
            // Consulta SQL con filtro de búsqueda
            $sql = "
        SELECT
            turnos.id_turno,
            grupos.nombre_grupo,
            materias.nombre AS nombre_materia,
            CONCAT(profesores.Nombre_profesor, ' ', profesores.ap_paterno, ' ', profesores.ap_materno) AS nombre_profesor,
            periodos.periodo,
            salones.salon,
            turnos.lunes,
            turnos.martes,
            turnos.miercoles,
            turnos.jueves,
            turnos.viernes,
            turnos.horas
        FROM turnos
        LEFT JOIN grupos ON turnos.id_grupos = grupos.id_grupo
        LEFT JOIN materias ON turnos.id_materias = materias.id_materia
        LEFT JOIN profesores ON turnos.id_profesores = profesores.id_profesor
        LEFT JOIN periodos ON turnos.id_periodos = periodos.id_periodo
        LEFT JOIN salones ON turnos.id_salones = salones.id_salon
        WHERE CONCAT(profesores.Nombre_profesor, ' ', profesores.ap_paterno, ' ', profesores.ap_materno) LIKE '%$search%'
        ORDER BY nombre_profesor";

            // Ejecutar la consulta
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $current_profesor = "";
                while ($row = $result->fetch_assoc()) {
                    if ($row["nombre_profesor"] != $current_profesor) {
                        if ($current_profesor != "") {
                            echo "</table></div>"; // Cerrar la tabla y el contenedor del profesor anterior
                        }
                        $current_profesor = $row["nombre_profesor"];
                        echo "<div class='professor-container'>";
                        echo "<div class='professor-name'>Horarios de " . $current_profesor . "</div>";
                        echo "<table>
                    <tr>
                        <th>Grupo</th>
                        <th>Materia</th>
                        <th>Profesor</th>
                        <th>Periodo</th>
                        <th>Salón</th>
                        <th>Lunes</th>
                        <th>Martes</th>
                        <th>Miércoles</th>
                        <th>Jueves</th>
                        <th>Viernes</th>
                        <th>Horas</th>
                        <th>Acción</th>
                    </tr>";
                    }
                    echo "<tr>
                <td>" . $row["nombre_grupo"] . "</td>
                <td>" . $row["nombre_materia"] . "</td>
                <td>" . $row["nombre_profesor"] . "</td>
                <td>" . $row["periodo"] . "</td>
                <td>" . $row["salon"] . "</td>
                <td>" . $row["lunes"] . "</td>
                <td>" . $row["martes"] . "</td>
                <td>" . $row["miercoles"] . "</td>
                <td>" . $row["jueves"] . "</td>
                <td>" . $row["viernes"] . "</td>
                <td>" . $row["horas"] . "</td>
                <td><a href='editar_horario.php?id_turno=" . $row["id_turno"] . "&profesor_nombre=" . urlencode($row["nombre_profesor"]) . "' class='edit-button'>Editar</a></td>
                </tr>";
                }
                echo "</table></div>"; // Cerrar la tabla y el último contenedor del profesor
            } else {
                echo "<p>No se encontraron turnos para el profesor especificado.</p>";
            }
        }

        $conn->close();
        ?>
    </div>
</body>

</html>