<?php
require 'verificar_sesion.php';
require 'conexion_be.php';

// Verificar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Recuperar id_horario del parámetro GET
$id_horario = isset($_GET['id_horario']) ? intval($_GET['id_horario']) : 0;

// Consulta para obtener datos del horario
$sql = "SELECT * FROM horarios WHERE id_horario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_horario);
$stmt->execute();
$result = $stmt->get_result();
$turno = $result->fetch_assoc();

if (!$turno) {
    die("Horario no encontrado.");
}

// Obtener listas de materias, periodos, salones y profesores
$materias = $conexion->query("SELECT id_materia, nombre FROM materias");
$periodos = $conexion->query("SELECT id_periodo, periodo FROM periodos");
$salones = $conexion->query("SELECT id_salon, clave_salon FROM salones");
$profesores = $conexion->query("SELECT id_profesor, CONCAT(nombre, ' ', ap_paterno, ' ', ap_materno) AS nombre_completo FROM profesores");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $lunes = $_POST['lunes'] ?? '';
    $martes = $_POST['martes'] ?? '';
    $miercoles = $_POST['miercoles'] ?? '';
    $jueves = $_POST['jueves'] ?? '';
    $viernes = $_POST['viernes'] ?? '';
    $horas = $_POST['horas'] ?? '';
    $materia_id = $_POST['materia'] ?? '';
    $periodo_id = $_POST['periodo'] ?? '';
    $salon_id = $_POST['salon'] ?? '';
    $nombre_profesor = $_POST['profesor'] ?? '';

    // Buscar el ID del profesor por nombre
    $profesor_sql = "SELECT id_profesor FROM profesores WHERE CONCAT(nombre, ' ', ap_paterno, ' ', ap_materno) = ?";
    $stmt = $conexion->prepare($profesor_sql);
    $stmt->bind_param("s", $nombre_profesor);
    $stmt->execute();
    $profesor_result = $stmt->get_result();

    if ($profesor_result->num_rows > 0) {
        $profesor = $profesor_result->fetch_assoc();
        $profesor_id = $profesor['id_profesor'];
    } else {
        die("Profesor no encontrado.");
    }

    // Actualizar el horario en la base de datos
    $update_sql = "UPDATE horarios 
                   SET lunes = ?, martes = ?, miercoles = ?, jueves = ?, viernes = ?, horas = ?, 
                       id_materia = ?, id_periodo = ?, id_salon = ?, id_profesor = ? 
                   WHERE id_horario = ?";
    $stmt = $conexion->prepare($update_sql);
    $stmt->bind_param("ssssssssssi", $lunes, $martes, $miercoles, $jueves, $viernes, $horas, 
                                    $materia_id, $periodo_id, $salon_id, $profesor_id, $id_horario);

    if ($stmt->execute()) {
        echo "<p>Horarios actualizados exitosamente.</p>";
    } else {
        echo "Error actualizando los horarios: " . $conexion->error;
    }
}

$conexion->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Horario</title>
    <link rel="stylesheet" href="../assets/css/style.css" type="text/css">
    <style>
        /* Estilos para el formulario de edición */
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
            background-color: #007BFF;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
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

        .edit-form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .edit-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .edit-form input[type="text"],
        .edit-form select {
            width: calc(100% - 22px);
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .edit-form button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .edit-form button:hover {
            background-color: #0056b3;
        }

        .volver-btn {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .volver-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav">
            <a class="logo nav-link">TESI</a>
            <ul class="nav-menu">
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/bienvenida.php">Inicio</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/alumnos.php">Alumnos</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/materias.php">Materias</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/grupos.php">Grupos</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link selected" href="../php/profesores.php">Profesores</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/periodo.php">Periodo</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/asignacion_grupo.php">Calificaciones</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/resumen.php">Resumen</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../index.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <div class="edit-form">
        <h2>Editar Horario</h2>
        <form method="POST" action="">
            <label for="lunes">Lunes:</label>
            <input type="text" id="lunes" name="lunes" value="<?php echo htmlspecialchars($turno['lunes'] ?? ''); ?>">

            <label for="martes">Martes:</label>
            <input type="text" id="martes" name="martes" value="<?php echo htmlspecialchars($turno['martes'] ?? ''); ?>">

            <label for="miercoles">Miércoles:</label>
            <input type="text" id="miercoles" name="miercoles" value="<?php echo htmlspecialchars($turno['miercoles'] ?? ''); ?>">

            <label for="jueves">Jueves:</label>
            <input type="text" id="jueves" name="jueves" value="<?php echo htmlspecialchars($turno['jueves'] ?? ''); ?>">

            <label for="viernes">Viernes:</label>
            <input type="text" id="viernes" name="viernes" value="<?php echo htmlspecialchars($turno['viernes'] ?? ''); ?>">

            <label for="horas">Horas:</label>
            <input type="text" id="horas" name="horas" value="<?php echo htmlspecialchars($turno['horas'] ?? ''); ?>">

            <label for="materia">Materia:</label>
            <select id="materia" name="materia">
                <?php while ($materia = $materias->fetch_assoc()) : ?>
                    <option value="<?php echo $materia['id_materia']; ?>" <?php echo ($materia['id_materia'] == $turno['id_materias']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($materia['nombre']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="periodo">Período:</label>
            <select id="periodo" name="periodo">
                <?php while ($periodo = $periodos->fetch_assoc()) : ?>
                    <option value="<?php echo $periodo['id_periodo']; ?>" <?php echo ($periodo['id_periodo'] == $turno['id_periodos']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($periodo['periodo']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="salon">Salón:</label>
            <select id="salon" name="salon">
                <?php while ($salon = $salones->fetch_assoc()) : ?>
                    <option value="<?php echo $salon['id_salon']; ?>" <?php echo ($salon['id_salon'] == $turno['id_salones']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($salon['salon']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="profesor">Profesor (Nombre completo):</label>
            <input type="text" id="profesor" name="profesor" value="<?php echo htmlspecialchars($turno['nombre_profesor'] ?? ''); ?>">

            <button type="submit">Actualizar Horario</button>
        </form>
        <a href="ver_horarios.php?search=<?php echo urlencode($_GET['profesor_nombre'] ?? ''); ?>" class="volver-btn">Volver</a>
    </div>
</body>
</html>
