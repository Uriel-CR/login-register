<?php
require 'verificar_sesion.php';
// Conexión a la base de datos
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

$grupos = array();
$materias = array();
$periodos = array();

// Obtener los grupos desde la base de datos
$sql = "SELECT id_grupo, nombre_grupo FROM grupos";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $grupos[] = $row;
    }
}

// Obtener las materias desde la base de datos
$sql = "SELECT id_materia, nombre FROM materias";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $materias[] = $row;
    }
}

// Obtener los periodos desde la base de datos
$sql = "SELECT id_periodo, periodo FROM periodos";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $periodos[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los valores del formulario
    $matricula = $_POST['matricula'];
    $ap_paterno = $_POST['ap_paterno'];
    $ap_materno = $_POST['ap_materno'];
    $nombre = $_POST['nombre'];
    $id_grupo = $_POST['grupo'];
    $id_periodo = $_POST['periodo'];
    $materias_seleccionadas = array(
        $_POST['materia1'],
        $_POST['materia2'],
        $_POST['materia3'],
        $_POST['materia4'],
        $_POST['materia5'],
        $_POST['materia6']
    );

    // Insertar los valores en la tabla alumnos
    $sql = "INSERT INTO alumnos (matricula, ap_paterno, ap_materno, nombre) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $matricula, $ap_paterno, $ap_materno, $nombre);

    if ($stmt->execute()) {
        echo "Nuevo estudiante registrado exitosamente.";
        $id_alumno = $stmt->insert_id;
        
        // Insertar la relación en la tabla calificaciones
        foreach ($materias_seleccionadas as $id_materia) {
            if (!empty($id_materia)) {  // Solo insertar si una materia ha sido seleccionada
                $sql = "INSERT INTO calificaciones (id_alumno, id_grupo, id_materia, id_periodo) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iiii", $id_alumno, $id_grupo, $id_materia, $id_periodo);
                $stmt->execute();
            }
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de alumnos recursando</title>
    <link rel="stylesheet" href="../assets/css/style.css" type="text/css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        h2 {
            color: #007bff;
            text-align: center;
        }
        form {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        input[type=text], select {
            width: calc(100% - 10px);
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type=submit], .btn-back {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type=submit]:hover, .btn-back:hover {
            background-color: #0056b3;
        }
        .btn-back {
            margin-top: 10px;
            display: inline-block;
            background-color: #6c757d;
            margin-right: 10px;
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

        .container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
            width: 80%;
            max-width: 600px;
            margin: 100px auto; /* Añadido margen superior para no solapar con el encabezado fijo */
        }
    </style>
</head>

<?php include 'header.html'; ?>

<body>
    <h2>Formulario de Registro de Estudiantes Irregulares</h2>
    <form method="post" action="">
        <label for="matricula">Matrícula:</label>
        <input type="text" id="matricula" name="matricula" required style="text-transform: uppercase;"><br><br>

        <label for="ap_paterno">Apellido Paterno:</label>
        <input type="text" id="ap_paterno" name="ap_paterno" style="text-transform: uppercase;"><br><br>

        <label for="ap_materno">Apellido Materno:</label>
        <input type="text" id="ap_materno" name="ap_materno" style="text-transform: uppercase;"><br><br>

        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" style="text-transform: uppercase;"><br><br>

        <label for="grupo">Grupo:</label>
        <select id="grupo" name="grupo" required>
            <option value="">Selecciona un grupo</option>
            <?php foreach($grupos as $grupo): ?>
                <option value="<?php echo $grupo['id_grupo']; ?>"><?php echo $grupo['nombre_grupo']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="periodo">Periodo:</label>
        <select id="periodo" name="periodo" required>
            <option value="">Selecciona un periodo</option>
            <?php foreach($periodos as $periodo): ?>
                <option value="<?php echo $periodo['id_periodo']; ?>"><?php echo $periodo['periodo']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="materia1">Materia 1:</label>
        <select id="materia1" name="materia1">
            <option value="">Selecciona una materia</option>
            <?php foreach($materias as $materia): ?>
                <option value="<?php echo $materia['id_materia']; ?>"><?php echo $materia['nombre']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="materia2">Materia 2:</label>
        <select id="materia2" name="materia2">
            <option value="">Selecciona una materia</option>
            <?php foreach($materias as $materia): ?>
                <option value="<?php echo $materia['id_materia']; ?>"><?php echo $materia['nombre']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="materia3">Materia 3:</label>
        <select id="materia3" name="materia3">
            <option value="">Selecciona una materia</option>
            <?php foreach($materias as $materia): ?>
                <option value="<?php echo $materia['id_materia']; ?>"><?php echo $materia['nombre']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="materia4">Materia 4:</label>
        <select id="materia4" name="materia4">
            <option value="">Selecciona una materia</option>
            <?php foreach($materias as $materia): ?>
                <option value="<?php echo $materia['id_materia']; ?>"><?php echo $materia['nombre']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="materia5">Materia 5:</label>
        <select id="materia5" name="materia5">
            <option value="">Selecciona una materia</option>
            <?php foreach($materias as $materia): ?>
                <option value="<?php echo $materia['id_materia']; ?>"><?php echo $materia['nombre']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="materia6">Materia 6:</label>
        <select id="materia6" name="materia6">
            <option value="">Selecciona una materia</option>
            <?php foreach($materias as $materia): ?>
                <option value="<?php echo $materia['id_materia']; ?>"><?php echo $materia['nombre']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <input type="submit" value="Registrar">
        <a href="alumnos.php" class="btn-back">Regresar</a>
    </form>
</body>
</html>