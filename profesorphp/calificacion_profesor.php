<?php
$conexion = mysqli_connect("localhost", "serviciosocial", "FtW30yNo8hQd-x/G","login_register_db");


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
    <title>Seleccionar Grupo y Materia</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 50%;
            margin: 100px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
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
    </style>
</head>

<body>
    <div class="container">
        <h1>Seleccionar Grupo, Materia y Período</h1>

        <form method="POST" action="procesar_calific.php">
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

        <div class="btn-regresar">
            <a href="profesores.php">Regresar a Alumnos</a>
        </div>
    </div>
</body>
</html>