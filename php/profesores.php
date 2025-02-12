<?php
// Conexión a la base de datos
$servername = "localhost"; // Cambia esto por tu servidor de base de datos
$username = "serviciosocial"; // Cambia esto por tu nombre de usuario de la base de datos
$password = "FtW30yNo8hQd-x/G"; // Cambia esto por tu contraseña de la base de datos
$database = "login_register_db"; // Cambia esto por el nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se envió el formulario de registro de usuario para profesor
/*if (isset($_POST['submit_usuario'])) {
    $clave = $_POST['clave_usuario'];
    $nombre_completo = $_POST['nombre_completo'];
    $correo = $_POST['correo'];
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    // Preparar la consulta de inserción
    $sql = "INSERT INTO profesores_usu (clave, nombre_completo, correo, usuario, contrasena) 
            VALUES (?, ?, ?, ?, ?)";
    
    // Preparar la declaración
    $stmt = $conn->prepare($sql);
    
    // Verificar si la preparación fue exitosa
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    // Vincular parámetros y ejecutar la declaración
    $stmt->bind_param("sssss", $clave, $nombre_completo, $correo, $usuario, $contrasena);
    
    // Ejecutar la declaración
    if ($stmt->execute()) {
        echo "Registro guardado exitosamente.";
    } else {
        echo "Error al guardar el registro: " . $stmt->error;
    }

    // Cerrar la declaración
    $stmt->close();
}*/

// Verificar si se envió el formulario de registro de profesor
if (isset($_POST['submit_profesor'])) {
    $clave = $_POST['clave'];
    $nombre = $_POST['nombre'];
    $ap_paterno = $_POST['ap_paterno'];
    $ap_materno = $_POST['ap_materno'];

    // Preparar la consulta de inserción
    $sql = "INSERT INTO profesores (Clave, Nombre_profesor, ap_paterno, ap_materno) 
            VALUES (?, ?, ?, ?)";
    
    // Preparar la declaración
    $stmt = $conn->prepare($sql);
    
    // Verificar si la preparación fue exitosa
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    // Vincular parámetros y ejecutar la declaración
    $stmt->bind_param("ssss", $clave, $nombre, $ap_paterno, $ap_materno);
    
    // Ejecutar la declaración
    if ($stmt->execute()) {
        echo'
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
    $periodo = $_POST['periodo'];
    $salon = $_POST['salon'];
    $lunes = $_POST['lunes'];
    $martes = $_POST['martes'];
    $miercoles = $_POST['miercoles'];
    $jueves = $_POST['jueves'];
    $viernes = $_POST['viernes'];
    $horas = $_POST['horas'];

    // Preparar la consulta de inserción
    $sql = "INSERT INTO turnos (id_grupos, id_materias, id_profesores, id_periodos, id_salones, lunes, martes, miercoles, jueves, viernes, horas) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    // Preparar la declaración
    $stmt = $conn->prepare($sql);
    
    // Verificar si la preparación fue exitosa
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    // Vincular parámetros y ejecutar la declaración
    $stmt->bind_param("iiiiissssss", $grupo, $materia, $profesor, $periodo, $salon, $lunes, $martes, $miercoles, $jueves, $viernes, $horas);
    
    // Ejecutar la declaración
    if ($stmt->execute()) {
        echo'
        <script>
        alert("Turno guardado exitosamente");
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
$conn->close();
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
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
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

        .nav-menu-link:hover, .nav-menu-link.selected {
            background-color: #0056b3;
        }

    </style>
</head>
<body>
    <header class="header">
        <nav class="nav">
            <a href="#" class="logo nav-link">TESI</a>
            <ul class="nav-menu">
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/bienvenida.php">Inicio</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/alumnos.php">Alumnos</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/materias.php">Materias</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/grupos.php">Grupos</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link selected">Profesores</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/periodo.php">Periodo</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/asignacion_grupo.php">Calificaciones</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/resumen.php">Resumen</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../index.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

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
                    // Conexión a la base de datos
                    $servername = "localhost"; // Cambia esto por tu servidor de base de datos
                    $username = "serviciosocial"; // Cambia esto por tu nombre de usuario de la base de datos
                    $password = "FtW30yNo8hQd-x/G"; // Cambia esto por tu contraseña de la base de datos
                    $database = "login_register_db"; // Cambia esto por el nombre de tu base de datos

                    // Crear conexión
                    $conn = new mysqli($servername, $username, $password, $database);

                    // Verificar conexión
                    if ($conn->connect_error) {
                        die("Conexión fallida: " . $conn->connect_error);
                    }

                    // Obtener nombres de los profesores
                    $sql = "SELECT id_profesor, CONCAT(Nombre_profesor, ' ', ap_paterno, ' ', ap_materno) AS nombre_completo FROM profesores";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
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
                    $sql = "SELECT id_grupo, nombre_grupo FROM grupos";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id_grupo'] . "'>" . $row['nombre_grupo'] . "</option>";
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
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id_materia'] . "'>" . $row['nombre'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No hay materias registradas</option>";
                    }
                    ?>
                </select><br><br>

                <label for="periodo" class="formulario-label">Periodo:</label>
                <select id="periodo" name="periodo" required>
                    <option value="" disabled selected>Seleccionar</option>
                    <?php
                    // Obtener periodos
                    $sql = "SELECT id_periodo, periodo FROM periodos";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id_periodo'] . "'>" . $row['periodo'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No hay periodos registrados</option>";
                    }
                    ?>
                </select><br><br>

                <label for="salon" class="formulario-label">Salón:</label>
                <select id="salon" name="salon" required>
                    <option value="" disabled selected>Seleccionar</option>
                    <?php
                    // Obtener nombres de los salones
                    $sql = "SELECT id_salon, salon FROM salones";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id_salon'] . "'>" . $row['salon'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No hay salones registrados</option>";
                    }
                    ?>
                </select><br><br>

                <label for="lunes" class="formulario-label">Lunes:</label>
                <input type="text" id="lunes" name="lunes" required style="text-transform: uppercase;"><br><br>

                <label for="martes" class="formulario-label">Martes:</label>
                <input type="text" id="martes" name="martes" required style="text-transform: uppercase;"><br><br>

                <label for="miercoles" class="formulario-label">Miércoles:</label>
                <input type="text" id="miercoles" name="miercoles" required style="text-transform: uppercase;"><br><br>

                <label for="jueves" class="formulario-label">Jueves:</label>
                <input type="text" id="jueves" name="jueves" required style="text-transform: uppercase;"><br><br>

                <label for="viernes" class="formulario-label">Viernes:</label>
                <input type="text" id="viernes" name="viernes" required style="text-transform: uppercase;"><br><br>

                <label for="horas" class="formulario-label">Horas:</label>
                <input type="text" id="horas" name="horas" required style="text-transform: uppercase;"><br><br>

                <input type="submit" name="submit_turno" value="Guardar">

                <div class="btn-regresar">
            <a href="ver_horarios.php">Ver horarios</a>
        </div>
            </form>
        </div>
    </div>
</body>
</html>