<?php
include 'conexion_be.php';

// Obtener el ID del grupo y la materia desde la URL
$id_grupo = isset($_GET['id_grupo']) ? intval($_GET['id_grupo']) : 0;
$materia_id = isset($_GET['materia_id']) ? intval($_GET['materia_id']) : 0;

if ($id_grupo === 0 || $materia_id === 0) {
    die('ID de grupo o materia inválido');
}

// Obtener la información actual de la materia
$query = "SELECT nombre FROM materias WHERE id_materia = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param('i', $materia_id);
$stmt->execute();
$result = $stmt->get_result();
$materia = $result->fetch_assoc();
$stmt->close();

if (!$materia) {
    die('Materia no encontrada');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Materia</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .nav-menu-link:hover, .nav-menu-link.selected {
            background-color: #0056b3;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            font-size: 28px;
            color: #333;
        }

        form {
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        input[type="number"], input[type="text"], select {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        label {
            display: block;
            margin: 5px 0;
            font-weight: bold;
        }

        .boton {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .boton:hover {
            background-color: #0056b3;
        }

        .materias-lista {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .materias-lista li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
<header class="header">
    <nav class="nav">
        <a class="logo nav-link"> TESI </a>
        <ul class="nav-menu">
            <li class="nav-menu-item"><a class="nav-menu-link" href="../php/bienvenida.php">Inicio</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link" href="../php/alumnos.php">Alumnos</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link" href="../php/materias.php">Materias</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link selected">Grupos</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link" href="../php/profesores.php">Profesores</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link" href="../php/periodo.php">Periodo</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link" href="../php/asignacion_grupo.php">Calificaciones</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link" href="../php/resumen.php">Resumen</a></li>
            <li class="nav-menu-item"><a class="nav-menu-link" href="../index.php">Cerrar Sesión</a></li>
        </ul>
    </nav>
</header>
<h1>Editar Nombre Materia</h1>

<form method="POST" action="editar_materia_be.php">
    <input type="hidden" name="id_grupo" value="<?php echo htmlspecialchars($id_grupo); ?>">
    <input type="hidden" name="materia_id" value="<?php echo htmlspecialchars($materia_id); ?>">
    <label for="nombre_materia">Nombre de la Materia:</label>
    <input type="text" id="nombre_materia" name="nombre_materia" value="<?php echo htmlspecialchars($materia['nombre']); ?>" required>
    <input class="boton" type="submit" value="Actualizar Materia">
</form>

<!-- Botón de Regresar con estilo -->
<div class="centered-button">
    <form action="grupos.php" method="get">
        <input type="hidden" name="id_grupo" value="<?php echo htmlspecialchars($id_grupo); ?>">
        <button type="submit" class="boton-regresar">Regresar</button>
    </form>
</div>

</body>
</html>
