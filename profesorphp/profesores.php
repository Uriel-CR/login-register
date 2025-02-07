<?php
// Configuración de la conexión a la base de datos
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
// Definir variables para almacenar los datos del profesor y los turnos
$nombre_profesor = "";
$apellido_paterno = "";
$apellido_materno = "";
$turnos = array(); // Para almacenar los turnos del profesor

// Manejar la solicitud de consulta cuando se envía el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nombre_profesor'])) {
    // Obtener el nombre ingresado por el usuario
    $nombre_profesor = $_POST['nombre_profesor'];

  
    // Consulta SQL para obtener los datos del profesor
    $sql_profesor = "SELECT id_profesor, Nombre_profesor, ap_paterno, ap_materno FROM profesores WHERE Nombre_profesor = '$nombre_profesor'";

    // Ejecutar la consulta para obtener los datos del profesor
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

        // Consulta SQL para obtener los turnos del profesor
        $sql_turnos = "SELECT 
                        materias.clave_materia,
                        materias.nombre,
                        grupos.nombre_grupo,
                        turnos.lunes,
                        turnos.martes,
                        turnos.miercoles,
                        turnos.jueves,
                        turnos.viernes
                      
                    FROM 
                        turnos
                    INNER JOIN 
                        materias ON turnos.id_materias = materias.id_materia
                    INNER JOIN 
                        grupos ON turnos.id_grupos = grupos.id_grupo
                    WHERE 
                        turnos.id_profesores = '$id_profesor'";

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
        $id_profesor = "No se encontraron resultados para el nombre '$nombre_profesor'";
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
        <a href="#" class="logo nav-link">TESI</a>
        <ul class="nav-menu">
            <li class="nav-menu-item"><a class="btn nav-menu-link nav-link selected">Profesores</a></li>
            <li class="nav-menu-item"><a class="btn nav-menu-link nav-link" href="../profesor.php">Cerrar Sesion</a></li>
        </ul>
    </nav>
</header>

<h2></h2>
<table class="tables">
    <tbody>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <tr>
        <th colspan="2" label for="nombre_profesor">Ingresa Nombre del Profesor:</th>
        <td><input type="text" id="nombre_profesor" name="nombre_profesor" required></td>
        <td>
            <button type="submit">Consultar</button>
            <a href="../profesorphp/calificacion_profesor.php" class="btn"> CALIFICACIONES</a>
        </td>
    </tr>
</form>

<style>
    .btn {
        display: inline-block;
        padding: 10px 20px;
        background-color: #007BFF;
        color: white;
        text-decoration: none;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn:hover {
        background-color: #007BFF;
    }
</style>

    </tbody>
</table>

<?php if (!empty($turnos)): ?>
    <table>
        <tr>
            <td colspan="2">PROFESOR:</td>
            <td colspan="9"><?php echo $nombre_profesor; ?> <?php echo $apellido_paterno; ?> <?php echo $apellido_materno; ?></td>
        </tr>
        <tr>
            <th>CLAVE</th>
            <th>MATERIA</th>
            <th>GRUPO</th>
            <th>LUNES</th>
            <th>MARTES</th>
            <th>MIERCOLES</th>
            <th>JUEVES</th>
            <th>VIERNES</th>
          
        </tr>
        <?php foreach ($turnos as $turno): ?>
            <tr>
                <td><?php echo $turno['clave_materia']; ?></td>
                <td><?php echo $turno['nombre']; ?></td>
                <td><?php echo $turno['nombre_grupo']; ?></td>
                <td><?php echo $turno['lunes']; ?></td>
                <td><?php echo $turno['martes']; ?></td>
                <td><?php echo $turno['miercoles']; ?></td>
                <td><?php echo $turno['jueves']; ?></td>
                <td><?php echo $turno['viernes']; ?></td>
               
            
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<script>
    // Incluye las funciones JavaScript
    <?php include '../assets/js/script1.js'; ?>
</script>
</body>
</html>
