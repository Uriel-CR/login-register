<?php
require 'verificar_sesion.php';
require 'conexion_be.php';

// Verificar si se envió el formulario de registro de profesor
if (isset($_POST['submit_profesor'])) {
    $carrera = $_POST['carrera'];
    $clave = $_POST['clave'];
    $nombre = $_POST['nombre'];
    $ap_paterno = $_POST['ap_paterno'];
    $ap_materno = $_POST['ap_materno'];
    // Preparar la consulta de inserción
    $sql = "INSERT INTO profesores (id_carrera, clave, nombre, ap_paterno, ap_materno) 
            VALUES (?, ?, ?, ?, ?)";

    // Preparar la declaración
    $stmt = $conexion->prepare($sql);

    // Verificar si la preparación fue exitosa
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }

    // Vincular parámetros y ejecutar la declaración
    $stmt->bind_param("sssss", $carrera, $clave, $nombre, $ap_paterno, $ap_materno);

    // Ejecutar la declaración
    if ($stmt->execute()) {
        echo '
        <script>
        alert("Profesor guardado correctamente");
        window.location= "profesores.php";
        </script>
        ';
    } else {
        echo "Error al guardar el profesor: " . $stmt->error;
    }

    // Cerrar la declaración
    $stmt->close();
}

// Verificar si se envió el formulario de registro de turno
if (isset($_POST['submit_turno'])) {
    $profesor = $_POST['profesor'];
    $grupo = $_POST['grupo'];
    $materia = $_POST['materia'];
    $dia = $_POST['dia'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    // Preparar la consulta de inserción
    $sql = "INSERT INTO horarios (id_grupo, id_profesor, id_materia, dia, hora_inicio, hora_fin) 
            VALUES (?, ?, ?, ?, ?, ?)";

    // Preparar la declaración
    $stmt = $conexion->prepare($sql);

    // Verificar si la preparación fue exitosa
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }

    // Vincular parámetros y ejecutar la declaración
    $stmt->bind_param("iiisss", $grupo, $profesor, $materia, $dia, $hora_inicio, $hora_fin);

    // Ejecutar la declaración
    if ($stmt->execute()) {
        echo '
        <script>
        alert("Horario guardado exitosamente");
        window.location= "profesores.php";
        </script>
        ';
    } else {
        echo "Error al guardar el turno: " . $stmt->error;
    }

    // Cerrar la declaración
    $stmt->close();
}

// Cerrar la conexión
$conexion->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Profesores y Turnos</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="../assets/css/minimal-table.css" rel="stylesheet" type="text/css">
    <style>
        body {
            background-image: url('../assets/images/foto.jpeg');
            background-size: 100% auto;
            background-position: center top;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .logo {

            font-size: 24px;
            font-weight: bold;
            position: fixed;
            left: 0;
            margin: 10px;
        }

        .header {
            background-color: #007BFF;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: sticky;
            /* Hace que el encabezado se quede fijo en la parte superior */
            top: 0;
            /* Asegura que el encabezado esté en la parte superior de la página */
            width: 100%;
            /* Asegura que el encabezado ocupe todo el ancho de la ventana */
            z-index: 1000;
            /* Asegura que el encabezado esté sobre otros elementos */
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            font-size: 28px;
            color: #333;
        }

        .formulario {
            width: 100%;
            margin-bottom: 20px;
        }

        .formulario input[type="text"],
        .formulario input[type="submit"],
        .formulario select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .formulario input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        .formulario input[type="submit"]:hover {
            background-color: #45a049;
        }

        .formulario-label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
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
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .nav-menu-link:hover,
        .nav-menu-link.selected {
            background-color: #0056b3;
        }

        .time-input {
            width: 100%;
            max-width: 200px;
            padding: 10px;
            font-size: 16px;
            border: 2px solid #007bff;
            border-radius: 8px;
            outline: none;
            transition: 0.3s;
        }
    </style>
</head>

<body>
    <?php
    include 'header.html';
    require 'conexion_be.php';

    // Obtener todas las carreras
    $consulta_carreras = "SELECT id_carrera, carrera FROM carreras";
    $resultado_carreras = mysqli_query($conexion, $consulta_carreras);

    // Verificar si se obtuvieron resultados
    if (!$resultado_carreras) {
        die('Error al obtener las carreras: ' . mysqli_error($conexion));
    }
    ?>

    <div class="container">
        <h1>Registro de Profesores y Turnos</h1>

        <div class="formulario">
            <h2>Registro de Profesor</h2>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <label for="clave" class="formulario-label">Clave:</label>
                <input type="text" id="clave" name="clave" required style="text-transform: uppercase;"><br><br>

                <label for="nombre" class="formulario-label">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required style="text-transform: uppercase;"><br><br>

                <label for="ap_paterno" class="formulario-label">Apellido Paterno:</label>
                <input type="text" id="ap_paterno" name="ap_paterno" required style="text-transform: uppercase;"><br><br>

                <label for="ap_materno" class="formulario-label">Apellido Materno:</label>
                <input type="text" id="ap_materno" name="ap_materno" required style="text-transform: uppercase;"><br><br>

                <label for="carrera" class="formulario-label">Carrera:</label>
                <select id="carrera" name="carrera" required>
                    <option value="" disabled selected>Selecciona una carrera</option>
                    <?php
                    // Generar las opciones del select con las carreras obtenidas
                    while ($fila = mysqli_fetch_assoc($resultado_carreras)) {
                        echo "<option value='" . htmlspecialchars($fila['id_carrera']) . "'>" . htmlspecialchars($fila['carrera']) . "</option>";
                    }
                    ?>
                </select><br><br>

                <input type="submit" name="submit_profesor" value="Guardar">
            </form>
        </div>

        <div class="formulario">
            <h2>Registro de Horarios del Profesor</h2>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <label for="profesor" class="formulario-label">Profesor:</label>
                <select id="profesor" name="profesor" required>
                    <option value="" disabled selected>Seleccionar</option>
                    <?php
                    // Obtener nombres de los profesores
                    $sql = "SELECT id_profesor, CONCAT(nombre, ' ', ap_paterno, ' ', ap_materno) AS nombre_completo FROM profesores";
                    $result = $conexion->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id_profesor'] . "'>" . $row['nombre_completo'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No hay profesores registrados</option>";
                    }
                    ?>
                </select><br><br>

                <label for="grupo" class="formulario-label">Grupo:</label>
                <select id="grupo" name="grupo" required>
                    <option value="" disabled selected>Seleccionar</option>
                    <?php
                    // Obtener nombres de los grupos
                    $sql = "SELECT g.id_grupo, g.id_carrera, g.id_semestre, t.clave_turno, g.clave_grupo FROM grupos g INNER JOIN turnos t";
                    $result = $conexion->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id_grupo'] . "'>" . $row['id_carrera'] . $row['id_semestre'] . $row['clave_turno'] . $row['clave_grupo'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No hay grupos registrados</option>";
                    }
                    ?>
                </select><br><br>

                <label for="materia" class="formulario-label">Materia:</label>
                <select id="materia" name="materia" required>
                    <option value="" disabled selected>Seleccionar</option>
                    <?php
                    // Obtener nombres de las materias
                    $sql = "SELECT id_materia, nombre FROM materias";
                    $result = $conexion->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id_materia'] . "'>" . $row['nombre'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No hay materias registradas</option>";
                    }
                    ?>
                </select><br><br>

                <label for="dia" class="formulario-label">Día:</label>
                <select id="dia" name="dia" required>
                    <option value="">Selecciona un día</option>
                    <option value="Lunes">Lunes</option>
                    <option value="Martes">Martes</option>
                    <option value="Miércoles">Miércoles</option>
                    <option value="Jueves">Jueves</option>
                    <option value="Viernes">Viernes</option>
                    <option value="Sabado">Sabado</option>
                </select>
                <br><br>

                <label for="hora_inicio" class="formulario-label">Hora de Inicio:</label>
                <input class="time-input" type="time" id="hora_inicio" name="hora_inicio" required step="60">
                <br><br>

                <label for="hora_fin" class="formulario-label">Hora de Fin:</label>
                <input class="time-input" type="time" id="hora_fin" name="hora_fin" required step="60">
                <br><br>


                <input type="submit" name="submit_turno" value="Guardar">

                <div class="btn-regresar">
                    <a href="ver_horarios.php">Ver horarios</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>