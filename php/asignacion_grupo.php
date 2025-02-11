<?php
include 'conexion_be.php';

// Obtener los grupos para el botón desplegable
$grupos = [];
$consulta_grupos = "SELECT id_grupo, nombre_grupo FROM grupos";
$resultado_grupos = mysqli_query($conexion, $consulta_grupos);

if ($resultado_grupos && mysqli_num_rows($resultado_grupos) > 0) {
    while ($grupo = mysqli_fetch_assoc($resultado_grupos)) {
        $grupos[] = $grupo;
    }
}

// Obtener las materias
$materias = [];
$consulta_materias = "SELECT id_materia, nombre FROM materias";
$resultado_materias = mysqli_query($conexion, $consulta_materias);

if ($resultado_materias && mysqli_num_rows($resultado_materias) > 0) {
    while ($materia = mysqli_fetch_assoc($resultado_materias)) {
        $materias[] = $materia;
    }
}

// Obtener las periodos
$periodos = [];
$consulta_periodos = "SELECT id_periodo, periodo FROM periodos";
$resultado_periodos = mysqli_query($conexion, $consulta_periodos);

if ($resultado_periodos && mysqli_num_rows($resultado_periodos) > 0) {
    while ($periodo = mysqli_fetch_assoc($resultado_periodos)) {
        $periodos[] = $periodo;
    }
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css" type="text/css">
    <title>Seleccionar Grupo y Materia</title>
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
        
        .container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
            width: 80%;
            max-width: 600px;
            margin: 100px auto;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 10px;
            color: #666;
        }
        select, input[type="submit"] {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .btn-regresar {
            text-align: center;
            margin-top: 20px;
        }
        .btn-regresar a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #f44336;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-regresar a:hover {
            background-color: #d32f2f;
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
    </style>
</head>

<body>
<header class="header">
        <nav class="nav">
            <a class="logo nav-link">TESI</a>
            <ul class="nav-menu">
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/bienvenida.php">Inicio</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link " href="../php/alumnos.php">Alumnos</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/materias.php">Materias</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/grupos.php">Grupos</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/profesores.php">Profesores</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/periodo.php">Periodo</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link selected" >Calificaciones</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/resumen.php">Resumen</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../index.php">Cerrar Sesión</a></li>
            </ul>
            <button class="nav-toggle">
                <img src="../assets/images/menu.svg" class="nav-toggle-icon" alt="">
            </button>
        </nav>
    </header>

    <div class="container">
    <h1>Seleccionar Grupo, Materia y Período</h1>

    <form method="POST" action="procesar_grupo.php">
        <label for="grupo">Grupo:</label>
        <select name="grupo" id="grupo">
            <?php foreach ($grupos as $grupo): ?>
                <option value="<?php echo $grupo['id_grupo']; ?>"><?php echo $grupo['nombre_grupo']; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="materia">Materia:</label>
        <select name="materia" id="materia">
            <?php foreach ($materias as $materia): ?>
                <option value="<?php echo $materia['id_materia']; ?>"><?php echo $materia['nombre']; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="periodo">Periodo:</label>
        <select name="periodo" id="periodo">
            <?php foreach ($periodos as $periodo): ?>
                <option value="<?php echo $periodo['id_periodo']; ?>"><?php echo $periodo['periodo']; ?></option>
            <?php endforeach; ?>
        </select>

        <input type="submit" value="Enviar">
    </form>
</div>
</body>
</html>