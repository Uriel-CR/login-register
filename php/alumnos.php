<?php
require 'verificar_sesion.php';
// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "alociva07.PM";
$dbname = "login-register-new";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener grupos
$grupos_result = $conn->query("SELECT id_grupo, nombre_grupo FROM grupos");
$grupos = [];
if ($grupos_result->num_rows > 0) {
    while ($row = $grupos_result->fetch_assoc()) {
        $grupos[] = $row;
    }
}

// Obtener periodos
$periodos_result = $conn->query("SELECT id_periodo, periodo FROM periodos");
$periodos = [];
if ($periodos_result->num_rows > 0) {
    while ($row = $periodos_result->fetch_assoc()) {
        $periodos[] = $row;
    }
}

// Procesar búsqueda de alumnos
$alumnos = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buscar'])) {
    $matricula_buscar = $_POST['matricula_buscar'];
    if (!empty($matricula_buscar)) {
        $stmt_buscar = $conn->prepare("SELECT * FROM alumnos WHERE matricula = ?");
        $stmt_buscar->bind_param("s", $matricula_buscar);
        $stmt_buscar->execute();
        $result_buscar = $stmt_buscar->get_result();
        $alumnos = $result_buscar->fetch_all(MYSQLI_ASSOC);
        $stmt_buscar->close();
    }
}

// Procesar eliminación de alumnos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['borrar'])) {
    $matricula_borrar = $_POST['matricula_borrar'];
    if (!empty($matricula_borrar)) {
        $stmt_borrar = $conn->prepare("DELETE FROM alumnos WHERE matricula = ?");
        $stmt_borrar->bind_param("s", $matricula_borrar);
        if ($stmt_borrar->execute()) {
            echo "Alumno borrado exitosamente.";
        } else {
            echo "Error al borrar el alumno: " . $stmt_borrar->error;
        }
        $stmt_borrar->close();
    }
}

// Cerrar la conexión
$conn->close();

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['buscar']) && !isset($_POST['borrar'])) {
    // Reabrir la conexión para insertar datos
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Obtener datos del formulario
    $matricula = $_POST['matricula'];
    $ap_paterno = $_POST['ap_paterno'];
    $ap_materno = $_POST['ap_materno'];
    $nombre = $_POST['nombre'];

    // Obtener los IDs del grupo y periodo
    $id_grupo = isset($_POST['grupo']) && $_POST['grupo'] != '0' ? $_POST['grupo'] : null;
    $id_periodo = isset($_POST['periodo']) && $_POST['periodo'] != '0' ? $_POST['periodo'] : null;

    // Validar si los IDs están definidos
    if ($id_grupo && $id_periodo) {
        // Preparar y ejecutar la consulta para insertar el alumno
        $stmt = $conn->prepare("INSERT INTO alumnos (matricula, ap_paterno, ap_materno, nombre) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $matricula, $ap_paterno, $ap_materno, $nombre);

        if ($stmt->execute()) {
            $id_alumno = $stmt->insert_id; // Obtener el ID del alumno recién insertado

            // Limpiar la tabla calificaciones antes de insertar nuevas entradas
            $stmt_clear = $conn->prepare("DELETE FROM calificaciones WHERE id_alumno = ? AND id_grupo = ? AND id_periodo = ?");
            $stmt_clear->bind_param("iii", $id_alumno, $id_grupo, $id_periodo);
            $stmt_clear->execute();
            $stmt_clear->close();

            // Insertar los datos en la tabla calificaciones
            $stmt_calificaciones = $conn->prepare("INSERT INTO calificaciones (id_alumno, id_grupo, id_periodo, id_materia) VALUES (?, ?, ?, ?)");
            $stmt_calificaciones->bind_param("iiii", $id_alumno, $id_grupo, $id_periodo, $id_materia);

            // Insertar las materias del grupo en la tabla calificaciones
            $stmt_materias = $conn->prepare("SELECT id_materia1, id_materia2, id_materia3, id_materia4, id_materia5, id_materia6 FROM grupos WHERE id_grupo = ?");
            $stmt_materias->bind_param("i", $id_grupo);
            $stmt_materias->execute();
            $result_materias = $stmt_materias->get_result();

            while ($row = $result_materias->fetch_assoc()) {
                foreach ($row as $id_materia) {
                    if ($id_materia) {
                        $stmt_calificaciones->execute();
                    }
                }
            }

            $stmt_calificaciones->close();
            echo "Alumno y datos de calificaciones registrados exitosamente.";
        } else {
            echo "Error al registrar alumno: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error: Selecciona un grupo y un periodo válidos.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Alumno</title>
    <link rel="stylesheet" href="../assets/css/style.css" type="text/css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #007BFF;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: fixed;
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
            /* Azul más oscuro */
        }

        .nav-toggle {
            display: none;
            /* Mostrar solo en dispositivos móviles */
            background: none;
            border: none;
            cursor: pointer;
        }

        .nav-toggle-icon {
            width: 24px;
            height: 24px;
        }

        .container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 80%;
            max-width: 600px;
            margin: 100px auto;
            /* Añadido margen superior para no solapar con el encabezado fijo */
        }

        h2 {
            text-align: center;
            color: #007bff;
            /* Azul */
        }

        label {
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 8px;
            margin: 8px 0 16px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #007bff;
            /* Azul */
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
            /* Azul más oscuro */
        }

        .form-group {
            margin-bottom: 15px;
        }

        #materias {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #f9f9f9;
        }

        .btn-alumnos-irregulares {
            display: block;
            width: 100%;
            background-color: #ff5733;
            /* Color para el botón */
            color: #fff;
            border: none;
            padding: 10px 1px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 20px;
            text-align: center;
            transition: background-color 0.3s ease;
            margin-top: 15px;
        }

        .btn-alumnos-irregulares:hover {
            background-color: #c70039;
            /* Color más oscuro para el botón */
        }

        .btn-buscar,
        .btn-borrar {
            background-color: #28a745;
            /* Verde para el botón */
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }

        .btn-buscar:hover,
        .btn-borrar:hover {
            background-color: #218838;
            /* Verde más oscuro para el botón */
        }
    </style>
</head>

<body>
    <?php include 'header.html'; ?>

    <div class="container">
        <h2>Registrar Alumno</h2>
        <form method="post" action="" onsubmit="return validarFormulario()">
            <div class="form-group">
                <label for="matricula">Matrícula:</label>
                <input type="text" id="matricula" name="matricula" required style="text-transform: uppercase;">
            </div>

            <div class="form-group">
                <label for="ap_paterno">Apellido Paterno:</label>
                <input type="text" id="ap_paterno" name="ap_paterno" required style="text-transform: uppercase;">
            </div>

            <div class="form-group">
                <label for="ap_materno">Apellido Materno:</label>
                <input type="text" id="ap_materno" name="ap_materno" required style="text-transform: uppercase;">
            </div>

            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required style="text-transform: uppercase;">
            </div>

            <div class="form-group">
                <label for="grupo">Grupo:</label>
                <select id="grupo" name="grupo" onchange="mostrarMaterias()">
                    <option value="0">Selecciona un grupo</option>
                    <?php foreach ($grupos as $grupo): ?>
                        <option value="<?php echo htmlspecialchars($grupo['id_grupo']); ?>">
                            <?php echo htmlspecialchars($grupo['nombre_grupo']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="periodo">Periodo:</label>
                <select id="periodo" name="periodo">
                    <option value="0">Selecciona un periodo</option>
                    <?php foreach ($periodos as $periodo): ?>
                        <option value="<?php echo htmlspecialchars($periodo['id_periodo']); ?>">
                            <?php echo htmlspecialchars($periodo['periodo']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="materias">
                <!-- Materias se mostrarán aquí -->
            </div>

            <div id="error-message" class="error-message"></div>

            <input type="submit" value="Registrar Alumno">

            <!-- Botones adicionales -->
            <a href="alumnos_irregulares.php" class="btn-alumnos-irregulares">Registrar Alumnos Irregulares</a>
            <a href="actualizar_grupoperiodo.php" class="btn-alumnos-irregulares">Cargar Nuevo Periodo</a>
            <a href="buscar_alumnos.php" class="btn-alumnos-irregulares">Buscar Alumnos</a>
        </form>
    </div>

    <script>
        function mostrarMaterias() {
            var grupo_id = document.getElementById('grupo').value;
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_materias.php?grupo=' + grupo_id, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('materias').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        function validarFormulario() {
            var matricula = document.getElementById('matricula').value.trim();
            var ap_paterno = document.getElementById('ap_paterno').value.trim();
            var ap_materno = document.getElementById('ap_materno').value.trim();
            var nombre = document.getElementById('nombre').value.trim();
            var grupo = document.getElementById('grupo').value;
            var periodo = document.getElementById('periodo').value;
            var errorMessage = document.getElementById('error-message');
            errorMessage.textContent = '';

            if (!matricula || !ap_paterno || !ap_materno || !nombre || grupo === '0' || periodo === '0') {
                errorMessage.textContent = 'Por favor, completa todos los campos del formulario, incluyendo la selección de grupo y periodo.';
                return false;
            }

            return true;
        }
    </script>
</body>

</html>