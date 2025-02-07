<?php
// Configuración de la conexión a la base de datos
$servername = "localhost"; // Cambia esto por tu servidor de base de datos
$username = "root"; // Cambia esto por tu nombre de usuario de la base de datos
$password = ""; // Cambia esto por tu contraseña de la base de datos
$database = "login_register_db"; // Cambia esto por el nombre de tu base de datos

// Definir variable para almacenar las calificaciones del alumno
$matricula = "";
$calificaciones = array();
$materias = array(); // Variable para almacenar el nombre de las materias
$periodos = array(); // Variable para almacenar el periodo de cada materia
$nombre_alumno = ""; // Variable para almacenar el nombre del alumno
$mensaje_error = ""; // Variable para almacenar el mensaje de error

// Manejar la solicitud de consulta cuando se envía el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['matricula'])) {
    // Obtener la matrícula ingresada por el usuario
    $matricula = $_POST['matricula'];

    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $database);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("La conexión falló: " . $conn->connect_error);
    }

    // Consulta SQL para obtener el ID del alumno
    $sql_id_alumno = "SELECT id_alumno FROM alumnos WHERE matricula = '$matricula'";

    // Ejecutar la consulta
    $result_id_alumno = $conn->query($sql_id_alumno);

    // Verificar si la consulta arrojó resultados
    if ($result_id_alumno->num_rows > 0) {
        // Obtener el resultado como un array asociativo
        $row_id_alumno = $result_id_alumno->fetch_assoc();
        // Obtener el ID del alumno
        $id_alumno = $row_id_alumno["id_alumno"];

        // Consulta SQL para obtener el nombre completo del alumno
        $sql_nombre_alumno = "SELECT CONCAT(ap_paterno, ' ', ap_materno, ' ', nombre) AS nombre_completo FROM alumnos WHERE id_alumno = '$id_alumno'";

        // Ejecutar la consulta
        $result_nombre_alumno = $conn->query($sql_nombre_alumno);

        // Verificar si la consulta arrojó resultados
        if ($result_nombre_alumno->num_rows > 0) {
            // Obtener el nombre completo del alumno
            $row_nombre_alumno = $result_nombre_alumno->fetch_assoc();
            $nombre_alumno = $row_nombre_alumno["nombre_completo"];
        }

        // Consulta SQL para obtener las calificaciones del alumno con el periodo de cada materia
        $sql_calificaciones = "SELECT calificaciones.*, materias.nombre AS nombre_materia, calificaciones.periodo 
                               FROM calificaciones 
                               INNER JOIN materias ON calificaciones.id_materia = materias.id_materia 
                               WHERE calificaciones.id_alumno = '$id_alumno'";

        // Ejecutar la consulta
        $result_calificaciones = $conn->query($sql_calificaciones);

        // Verificar si la consulta arrojó resultados
        if ($result_calificaciones->num_rows > 0) {
            // Obtener las calificaciones como un array asociativo
            while ($row_calificaciones = $result_calificaciones->fetch_assoc()) {
                $calificaciones[] = $row_calificaciones;
                $materias[] = $row_calificaciones["nombre_materia"];
                $periodos[] = $row_calificaciones["periodo"];
            }
        } else {
            $mensaje_error = "No se han encontrado calificaciones para la matrícula '$matricula'";
        }
    } else {
        $mensaje_error = "No se ha encontrado la matrícula '$matricula'";
    }

    // Cerrar la conexión
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CALIFICACIONES</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="../assets/css/minimal-table.css" rel="stylesheet" type="text/css">
    <style>
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
            <a href="#" class="logo nav-link"> TESI</a>
            <ul class="nav-menu">
                <li class="nav-menu-item"><a class="btn nav-menu-link nav-link" href="../php/bienvenida.php">Inicio</a></li>
                <li class="nav-menu-item"><a class="btn nav-menu-link nav-link" href="../php/alumnos.php" >Alumnos</a></li>
                <li class="nav-menu-item"><a class="btn nav-menu-link nav-link" href="../php/materias.php">Materias</a></li>
                <li class="nav-menu-item"><a class="btn nav-menu-link nav-link selected " href="../php/calificaciones.php">Calificaciones</a></li>
                <li class="nav-menu-item"><a class="btn nav-menu-link nav-link" href="../php/grupos.php">Grupos</a></li>
                <li class="nav-menu-item"><a class="btn nav-menu-link nav-link"  href="../index.php">Cerrar Sesion</a></li>
            </ul>
        </nav>
    </header>
    <h1>Calificaciones</h1>
    <table>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <tr>
                <th colspan="2" label for="matricula">Ingresa la matrícula:</th>
                <td colspan="2"><input type="text" id="matricula" name="matricula"></input></td>
                <td colspan="2"><button type="submit">Consultar</button></td>
               
            </tr>
            </table>
            <table>
        </form>
        <!-- Mostrar el resultado de la consulta -->
        <?php if (!empty($calificaciones)): ?>
            <th colspan="15"><h>Alumno: <?php echo $nombre_alumno; ?></h></th>
            
            <tr>
                    <td>MATERIA</td>
                    <td>PARCIAL 1</td>
                    <td>PARCIAL 2</td>
                    <td>PARCIAL 3</td>
                    <td>PROMEDIO</td>
                    <td>2DA OPORTUNIDAD</td>
                    <td>CALIFICACION FINAL</td>
                    <td>PERIODO</td>
            

                </tr>
            <ul>
                <?php for ($i = 0; $i < count($calificaciones); $i++): ?>
                    
                    <tr>
                        
                            <th><?php echo $materias[$i]; ?></th>
                            <th><?php echo $calificaciones[$i]['parcial_1'];?></th>
                            <th><?php echo $calificaciones[$i]['parcial_2']; ?></th>
                            <th><?php echo $calificaciones[$i]['parcial_3']; ?></th>
                            <th><?php echo $calificaciones[$i]['promedio']; ?></th>
                            <th><?php echo $calificaciones[$i]['segunda_oportunidad']; ?></th>
                            <th><?php echo $calificaciones[$i]['calif_final']; ?></th>
                            <th><?php echo $periodos[$i]; ?></th>
                        </tr>
                    
                <?php endfor; ?>
            
        <?php elseif (!empty($mensaje_error)): ?>
            </ul>
        <?php endif; ?>
    </table>
</body>
</html>
