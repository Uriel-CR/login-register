<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RESUMEN DE CALIFICACIONES</title>
<link rel="stylesheet" href="../assets/css/style.css" type="text/css">
<style>
    /* Estilos generales */
    body {
        font-family: 'Arial', sans-serif;
        background-image: url('https://www.lavanguardia.com/files/image_449_220/files/fp/uploads/2022/07/11/62cc167b1cf73.r_d.611-245.jpeg');
        line-height: 1.6;
        background-color: #f5f5f5;
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 900px auto;
        margin: 20px auto;
        background-color: #aed6f1;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    h2 {
        color: #333;
        margin-bottom: 20px;
        font-size: 24px;
        text-align: center;
    }
    .form-container {
        text-align: center;
        margin-bottom: 20px;
    }
    .form-container label, .form-container select {
        font-size: 16px;
        margin-right: 10px;
    }
    .form-container select {
        padding: 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }
    th {
        background-color: #4CAF50;
        color: white;
    }
    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    .recurse {
        background-color: #fdd;
    }
    .no-recursar {  
        background-color: #dfd;
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

        .logo {
            font-size: 24px;
            font-weight: bold;
            position: fixed;
            left: 0;
            margin: 10px;
        }

    .nav {
            display: flex;
            align-items: center;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
    }
    .nav-link {
        color: #fff;
        text-decoration: none;
        padding: 10px;
    }
    .nav-menu {
        list-style: none;
        display: flex;
        padding: 0;
        margin: 0;
    }
    .nav-menu-item {
        margin: 15px;
    }
    .nav-menu-link {
        color: #fff;
            text-decoration: none;
            font-size: 16px;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
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
</style>
<script>
    function updateGroups() {
        var selectedPeriod = document.getElementById("periodo").value;
        var groupSelect = document.getElementById("grupo");
        var request = new XMLHttpRequest();
        request.open("POST", "get_groups.php", true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.onload = function() {
            if (this.status >= 200 && this.status < 400) {
                groupSelect.innerHTML = this.responseText;
                groupSelect.disabled = false;
                loadTableData(); // Cargar datos de tabla
            } else {
                console.error("Error en la carga de grupos: " + this.statusText);
            }
        };
        request.onerror = function() {
            console.error("Error de red al intentar cargar los grupos.");
        };
        request.send("periodo=" + selectedPeriod);
    }

    function loadTableData() {
        var selectedPeriod = document.getElementById("periodo").value;
        var selectedGroup = document.getElementById("grupo").value;
        var tableContainer = document.getElementById("table-container");

        var request = new XMLHttpRequest();
        request.open("POST", "get_table_data.php", true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.onload = function() {
            if (this.status >= 200 && this.status < 400) {
                tableContainer.innerHTML = this.responseText;
            } else {
                console.error("Error en la carga de datos de la tabla: " + this.statusText);
            }
        };
        request.onerror = function() {
            console.error("Error de red al intentar cargar los datos de la tabla.");
        };
        request.send("periodo=" + selectedPeriod + "&grupo=" + selectedGroup);
    }
</script>
</head>
<body>
<header class="header">
        <nav class="nav">
            <a class="logo nav-link">TESI</a>
            <ul class="nav-menu">
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/bienvenida.php">Inicio</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link"  href="../php/alumnos.php">Alumnos</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/materias.php">Materias</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/grupos.php">Grupos</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/profesores.php">Profesores</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/periodo.php">Periodo</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/asignacion_grupo.php">Calificaciones</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link selected" href="../php/resumen.php">Resumen</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../index.php">Cerrar Sesión</a></li>
            </ul>
            <button class="nav-toggle">
                <img src="../assets/images/menu.svg" class="nav-toggle-icon" alt="">
            </button>
        </nav>
    </header>
<div class="container">
    <h2>PROMEDIOS DE ALUMNO POR GRUPO</h2>

    <div class="form-container">
        <form id="reportForm" method="POST" action="">
            <label for="periodo">SELECCIONA UN PERÍODO:</label>
            <select name="periodo" id="periodo" onchange="updateGroups()" required>
                <option value="">Seleccionar</option>
                <?php
                // Configuración de la conexión a la base de datos
                $servername = "localhost"; 
                $username = "serviciosocial"; 
                $password = "FtW30yNo8hQd-x/G"; 
                $database = "login_register_db"; 

                $conn = new mysqli($servername, $username, $password, $database);

                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }

                // Consulta para obtener los períodos con sus nombres
                $sql_periodos = "
                    SELECT DISTINCT p.id_periodo, p.periodo 
                    FROM periodos p
                    ORDER BY p.periodo
                ";
                $result_periodos = $conn->query($sql_periodos);

                if ($result_periodos->num_rows > 0) {
                    while ($row_periodo = $result_periodos->fetch_assoc()) {
                        $id_periodo = $row_periodo["id_periodo"];
                        $nombre_periodo = $row_periodo["periodo"];
                        echo "<option value='$id_periodo'>$nombre_periodo</option>";
                    }
                } else {
                    echo "<option value=''>No hay períodos disponibles</option>";
                }

                $conn->close();
                ?>
            </select>

            <label for="grupo">SELECCIONA UN GRUPO:</label>
            <select name="grupo" id="grupo" onchange="loadTableData()" disabled required>
                <option value="">Seleccionar</option>
            </select>
        </form>
    </div>

    <div id="table-container">
        <!-- Las tablas se cargarán aquí mediante AJAX -->
    </div>
</div>
</body>
</html>
