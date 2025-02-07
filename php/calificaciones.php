<?php
// Configuración de la conexión a la base de datos
$servername = "localhost"; // Cambia esto por tu servidor de base de datos
$username = "serviciosocial"; // Cambia esto por tu nombre de usuario de la base de datos
$password = "FtW30yNo8hQd-x/G"; // Cambia esto por tu contraseña de la base de datos
$database = "login_register_db"; // Cambia esto por el nombre de tu base de datos

// Definir variables para almacenar los datos del profesor y los turnos
$id_profesor = "";
$nombre_profesor = "";
$apellido_paterno = "";
$apellido_materno = "";
$turnos = array(); // Para almacenar los turnos del profesor

// Manejar la solicitud de consulta cuando se envía el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener la matrícula ingresada por el usuario
    $Clave = $_POST['Clave'];

    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $database);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("La conexión falló: " . $conn->connect_error);
    }

    // Consulta SQL para obtener el id_profesor y los datos del profesor
    $sql_profesor = "SELECT id_profesor, Nombre_profesor, ap_paterno, ap_materno FROM profesores WHERE Clave = '$Clave'";

    // Ejecutar la consulta para obtener el id_profesor y los datos del profesor
    $result_profesor = $conn->query($sql_profesor);

    // Verificar si la consulta arrojó resultados
    if ($result_profesor->num_rows > 0) {
        // Obtener el resultado como un array asociativo
        $row_profesor = $result_profesor->fetch_assoc();
        // Obtener los datos del profesor
        $id_profesor = $row_profesor["id_profesor"];
        $nombre_profesor = $row_profesor["Nombre_profesor"];
        $apellido_paterno = $row_profesor["ap_paterno"];
        $apellido_materno = $row_profesor["ap_materno"];

        $sql_turnos = "SELECT 
                    materias.nombre AS nombre_materia, 
                    grupos.nombre AS nombre_grupo, 
                    grupos.id_grupo,  -- Seleccionamos el id_grupo
                    profesores.Nombre_profesor AS nombre_profesor,
                    turnos.id_turno,
                    materias.clave_materia,
                    materias.nombre,
                    turnos.lunes,
                    turnos.martes,
                    turnos.miercoles,
                    turnos.jueves,
                    turnos.viernes,
                    turnos.sabado
                FROM 
                    materias
                INNER JOIN 
                    turnos ON materias.id_materia = turnos.id_materia
                INNER JOIN 
                    grupos ON turnos.id_grupo = grupos.id_grupo
                INNER JOIN 
                    profesores ON turnos.id_profesor = profesores.id_profesor
                WHERE 
                    profesores.id_profesor = '$id_profesor'";


        // Ejecutar la consulta para obtener los turnos del profesor
        $result_turnos = $conn->query($sql_turnos);

        // Verificar si la consulta arrojó resultados
        if ($result_turnos->num_rows > 0) {
            // Recorrer los resultados y almacenarlos en el array de turnos
            while ($row_turno = $result_turnos->fetch_assoc()) {
                $turnos[] = $row_turno;
            }
        }
    } else {
        $id_profesor = "No se encontraron resultados para la clave '$Clave'";
    }

    // Cerrar la conexión
    $conn->close();
}

?>


<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CALIFICACIONES</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="../assets/css/minimal-table.css" rel="stylesheet" type="text/css">
    <style>
    body {
        background-image: url('../assets/images/foto.jpeg');
        background-size: 100% auto; /* Ajusta la escala horizontal al 100% y la escala vertical de forma automática */
        background-position: center top; /* Centra la imagen horizontalmente y la alinea en la parte superior */
        background-repeat: no-repeat;
    }
    table {
        margin: 0 auto; /* Centrar la tabla */
        margin-bottom: 60px;
        padding-top: 70px;
        
    }
</style>
</head>
<body>
<header class="header">
        <nav class="nav">
            <a class="logo nav-link"> TESI</a>
            <ul class="nav-menu">
                <li class="nav-menu-item"><a class="btn nav-menu-link nav-link" href="../php/bienvenida.php">Inicio</a></li>
                <li class="nav-menu-item"><a class="btn nav-menu-link nav-link" href="../php/alumnos.php" >Alumnos</a></li>
                <li class="nav-menu-item"><a class="btn nav-menu-link nav-link" href="../php/materias.php">Materias</a></li>
                <li class="nav-menu-item"><a class="btn nav-menu-link nav-link selected " href="../php/calificaciones.php">Horarios</a></li>
                <li class="nav-menu-item"><a class="btn nav-menu-link nav-link" href="../php/grupos.php">Grupos</a></li>
                <li class="nav-menu-item"><a class="btn nav-menu-link nav-link" href="../php/profesores.php">Profesores</a></li>
                <li class="nav-menu-item"><a class="btn nav-menu-link nav-link" href="../php/resumen.php">Resumen</a></li>
                <li class="nav-menu-item"><a class="btn nav-menu-link nav-link" href="../modificar/indexx.php">Busqueda</a></li>
                <li class="nav-menu-item"><a class="btn nav-menu-link nav-link"  href="../inicio.php">Cerrar Sesion</a></li>
            </ul>
        </nav>
    </header>
    <h2></h2>
<table class=tables>
<tbody>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    
</action=>
       <tr>
        <th colspan="2"label for="Clave">Ingresa Clave de Trabajo:</th>
        <td><input type="text" id="clave" name="Clave" required></td>
        <td><button type="submit">Consultar</button></td>
        
       </tr>
    </form>
    <form method="post" action="consult.php">
    <td colspan="7"><button type="submit"> Ir a la página de Calificaciones</button></td>
</form>

</table>
<table>
<tbody>
    <!-- Mostrar el resultado de la consulta -->
    <?php if ($id_profesor !== ""): ?>
        <?php if (!empty($turnos)): ?>
            <tr><td colspan="2">PROFESOR:</td>
       <td colspan="9"><?php echo $nombre_profesor; ?> <?php echo $apellido_paterno; ?> <?php echo $apellido_materno; ?></td></tr>
            <tr>
                <th>CLAVE</th>
                <th>MATERIA</th>
                <th>ID GRUPO</th>
                <th>GRUPO</th>
                <th>LUNES</th>
                <th>MARTES</th>
                <th>MIERCOLES</th>
                <th>JUEVES</th>
                <th>VIERNES</th>
                <th>SABADO</th>
               
                
            </tr>
            <?php foreach ($turnos as $turno): ?>
                <tr>
                    <td><?php echo $turno['clave_materia']; ?></td> 
                    <td><?php echo $turno['nombre']; ?></td>
                    <td><?php echo $turno['id_grupo']; ?></td>
                    <td><?php echo $turno['nombre_grupo']; ?></td>
                    <td><?php echo $turno['lunes']; ?></td>
                    <td><?php echo $turno['martes']; ?></td>
                    <td><?php echo $turno['miercoles']; ?></td>
                    <td><?php echo $turno['jueves']; ?></td>
                    <td><?php echo $turno['viernes']; ?></td>
                    <td><?php echo $turno['sabado']; ?></td>
                    <td>
    <form method="get" action="alumnos_grupo.php">
        <input type="hidden" name="id_grupo" value="<?php echo $turno['id_grupo']; ?>">
        <button type="submit">Ver Alumnos</button>
    </form>
</td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="9">El profesor no tiene asignados turnos.</td>
            </tr>
        <?php endif; ?>
    <?php endif; ?>

</table>
<script>
        // Incluye las funciones JavaScript
        <?php include '../assets/js/script1.js';   
           ?>
    </script>
</body>
</html>
