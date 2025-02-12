<?php
// Incluir archivo de conexión
include 'conexion_be.php';



// Inicialización de variables de filtro
$id_grupo = '';
$id_materia = '';
$id_periodo = '';

// Recuperar los valores seleccionados del formulario
$id_grupo = isset($_POST['grupo']) ? intval($_POST['grupo']) : 0;
$id_materia = isset($_POST['materia']) ? intval($_POST['materia']) : 0;
$id_periodo = isset($_POST['periodo']) ? intval($_POST['periodo']) : 0;

// Consultas adicionales para obtener los nombres de grupo, materia y periodo
$consulta_grupo = "SELECT nombre_grupo FROM grupos WHERE id_grupo = $id_grupo";
$consulta_materia = "SELECT nombre FROM materias WHERE id_materia = $id_materia";
$consulta_credito = "SELECT creditos FROM materias WHERE id_materia = $id_materia";
$consulta_periodo = "SELECT periodo FROM periodos WHERE id_periodo = $id_periodo";

$resultado_grupo = mysqli_query($conexion, $consulta_grupo);
$resultado_materia = mysqli_query($conexion, $consulta_materia);
$resultado_credito = mysqli_query($conexion, $consulta_credito);
$resultado_periodo = mysqli_query($conexion, $consulta_periodo);

$nombre_grupo = mysqli_fetch_assoc($resultado_grupo)['nombre_grupo'];
$nombre_materia = mysqli_fetch_assoc($resultado_materia)['nombre'];
$creditos_materia = mysqli_fetch_assoc($resultado_credito)['creditos'];
$nombre_periodo = mysqli_fetch_assoc($resultado_periodo)['periodo'];

// Inicializar variables de totales y créditos por parcial
$totales = [
    'parcial_1' => ['reprobados' => 0, 'aprobados' => 0],
    'parcial_2' => ['reprobados' => 0, 'aprobados' => 0],
    'parcial_3' => ['reprobados' => 0, 'aprobados' => 0],
    'promedio' => ['reprobados' => 0, 'aprobados' => 0],
    'segunda_oportunidad' => ['reprobados' => 0, 'aprobados' => 0],
    'calif_final' => ['reprobados' => 0, 'aprobados' => 0]
];
$creditos_por_parcial = [
    'parcial_1' => ['reprobados' => 0, 'aprobados' => 0],
    'parcial_2' => ['reprobados' => 0, 'aprobados' => 0],
    'parcial_3' => ['reprobados' => 0, 'aprobados' => 0],
    'promedio' => ['reprobados' => 0, 'aprobados' => 0],
    'segunda_oportunidad' => ['reprobados' => 0, 'aprobados' => 0],
    'calif_final' => ['reprobados' => 0, 'aprobados' => 0]
];

// Procesar datos del formulario POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capturar los valores del formulario
    $id_grupo = isset($_POST['grupo']) ? $_POST['grupo'] : '';
    $id_materia = isset($_POST['materia']) ? $_POST['materia'] : '';
    $id_periodo = isset($_POST['periodo']) ? $_POST['periodo'] : '';

    // Guardar las calificaciones
    if (isset($_POST['alumnos'])) {
        // Obtener los créditos de cada materia
        $materias_query = "SELECT id_materia, creditos FROM materias";
        $materias_result = mysqli_query($conexion, $materias_query);
        $creditos_materias = [];
        while ($materia = mysqli_fetch_assoc($materias_result)) {
            $creditos_materias[$materia['id_materia']] = $materia['creditos'];
        }

        foreach ($_POST['alumnos'] as $alumno_id => $calificaciones) {
            // Procesar y guardar las calificaciones en la base de datos
            $parcial_1 = isset($calificaciones['parcial_1']) ? $calificaciones['parcial_1'] : '';
            $parcial_2 = isset($calificaciones['parcial_2']) ? $calificaciones['parcial_2'] : '';
            $parcial_3 = isset($calificaciones['parcial_3']) ? $calificaciones['parcial_3'] : '';

            // Validar segunda oportunidad
            if ($parcial_1 !== 'N/A' && $parcial_2 !== 'N/A' && $parcial_3 !== 'N/A') {
                $segunda_oportunidad = '';
            } else {
                $segunda_oportunidad = isset($calificaciones['segunda_oportunidad']) ? $calificaciones['segunda_oportunidad'] : '';
            }

            // Convertir a número o marcar como 'N/A' si es necesario
            $parcial_1_num = ($parcial_1 === 'N/A' || $parcial_1 < 70) ? 'N/A' : floatval($parcial_1);
            $parcial_2_num = ($parcial_2 === 'N/A' || $parcial_2 < 70) ? 'N/A' : floatval($parcial_2);
            $parcial_3_num = ($parcial_3 === 'N/A' || $parcial_3 < 70) ? 'N/A' : floatval($parcial_3);

            // Validar segunda oportunidad
            if ($parcial_1_num !== 'N/A' && $parcial_2_num !== 'N/A' && $parcial_3_num !== 'N/A') {
                $segunda_oportunidad_num = '';
            } else {
                $segunda_oportunidad_num = (($segunda_oportunidad === 'N/A' || $segunda_oportunidad < 70) && $segunda_oportunidad !== '') ? 'N/A' : floatval($segunda_oportunidad);
            }

            // Calcular promedio solo si todas las calificaciones parciales son numéricas
            if ($parcial_1_num === 'N/A' || $parcial_2_num === 'N/A' || $parcial_3_num === 'N/A') {
                $promedio = 'N/A';
            } else {
                $promedio = ($parcial_1_num + $parcial_2_num + $parcial_3_num) / 3;
                if ($promedio < 70) {
                    $promedio = 'N/A';
                }
            }

            // Determinar calificación final
            if ($segunda_oportunidad_num !== 'N/A' && $segunda_oportunidad_num >= 70) {
                $calif_final = $segunda_oportunidad_num;
            } elseif ($promedio === 'N/A') {
                $calif_final = 'N/A';
            } elseif ($promedio < 70) {
                $calif_final = 'N/A';
            } else {
                $calif_final = $promedio;
            }

            // Actualizar las calificaciones en la base de datos
            $query = "UPDATE calificaciones SET 
                        parcial_1 = '$parcial_1', 
                        parcial_2 = '$parcial_2', 
                        parcial_3 = '$parcial_3', 
                        promedio = '$promedio', 
                        segunda_oportunidad = '$segunda_oportunidad', 
                        calif_final = '$calif_final'
                      WHERE id_grupo = '$id_grupo' AND id_alumno = '$alumno_id' AND id_materia = '$id_materia'";

            $resultado = mysqli_query($conexion, $query);
            if (!$resultado) {
                echo "Error al guardar las calificaciones del alumno ID $alumno_id: " . mysqli_error($conexion);
            }

            // Contar aprobados y reprobados por parcial
            foreach (['parcial_1', 'parcial_2', 'parcial_3'] as $parcial) {
                $materia_creditos = $creditos_materias[$id_materia] ?? 0;
                if ($calificaciones[$parcial] === 'N/A' || $calificaciones[$parcial] < 70) {
                    $totales[$parcial]['reprobados']++;
                    $creditos_por_parcial[$parcial]['reprobados'] += $materia_creditos;
                } else {
                    $totales[$parcial]['aprobados']++;
                    $creditos_por_parcial[$parcial]['aprobados'] += $materia_creditos;
                }
            }

            // Contar aprobados y reprobados por promedio final
            if ($promedio === 'N/A' || $promedio < 70) {
                $totales['promedio']['reprobados']++;
                $creditos_por_parcial['promedio']['reprobados'] += $materia_creditos;
            } else {
                $totales['promedio']['aprobados']++;
                $creditos_por_parcial['promedio']['aprobados'] += $materia_creditos;
            }

            if ($calif_final === 'N/A' || $calif_final < 70) {
                $totales['calif_final']['reprobados']++;
                $creditos_por_parcial['calif_final']['reprobados'] += $materia_creditos;
            } else {
                $totales['calif_final']['aprobados']++;
                $creditos_por_parcial['calif_final']['aprobados'] += $materia_creditos;
            }

            // Contar aprobados y reprobados en segunda oportunidad
            if (!isset($totales['segunda_oportunidad'])) {
                $totales['segunda_oportunidad'] = ['reprobados' => 0, 'aprobados' => 0];
                $creditos_por_parcial['segunda_oportunidad'] = ['reprobados' => 0, 'aprobados' => 0];
            }
            if ($segunda_oportunidad === 'N/A') {
                $totales['segunda_oportunidad']['reprobados']++;
                $creditos_por_parcial['segunda_oportunidad']['reprobados'] += $materia_creditos;
            } elseif (empty($segunda_oportunidad)) {
                // Mostrar 0 cuando es NULL o no tiene datos
                $totales['segunda_oportunidad']['aprobados'] += 0;
                $totales['segunda_oportunidad']['reprobados'] += 0;
            } else {
                $totales['segunda_oportunidad']['aprobados']++;
                $creditos_por_parcial['segunda_oportunidad']['aprobados'] += $materia_creditos;
            }
        }

        // Informar que las calificaciones se guardaron correctamente
        // echo "Calificaciones guardadas correctamente.";

        // Llamar a la función para guardar el resumen en la base de datos
        guardarResumenCalificaciones($conexion, $id_grupo, $id_materia, $id_periodo, $totales, $creditos_por_parcial);
    } else {
        // echo "No se recibieron datos de los alumnos.";
    }
}

function guardarResumenCalificaciones($conexion, $id_grupo, $id_materia, $id_periodo, $totales, $creditos_por_parcial)
{
    // Obtener el número total de alumnos
    $total_alumnos_query = "SELECT COUNT(DISTINCT a.id_alumno) AS total_alumnos
        FROM alumnos a 
        JOIN calificaciones c ON a.id_alumno = c.id_alumno 
        JOIN periodos p ON c.id_periodo = p.id_periodo
        WHERE c.id_grupo = '$id_grupo' AND c.id_materia = '$id_materia' AND p.id_periodo = '$id_periodo'
    ";
    $total_alumnos_result = mysqli_query($conexion, $total_alumnos_query);
    $total_alumnos_row = mysqli_fetch_assoc($total_alumnos_result);
    $total_alumnos = $total_alumnos_row['total_alumnos'];

    // Verificar si ya existe un resumen para esta combinación
    $check_query = "
        SELECT COUNT(*) AS count
        FROM calificaciones_resumen
        WHERE id_grupo = '$id_grupo' AND id_materia = '$id_materia' AND id_periodo = '$id_periodo'
    ";
    $check_result = mysqli_query($conexion, $check_query);
    $check_row = mysqli_fetch_assoc($check_result);
    $exists = $check_row['count'] > 0;

    if ($exists) {
        // Actualizar el resumen existente
        $query = "UPDATE calificaciones_resumen SET 
                total_alumnos = '$total_alumnos',
                parcial_1_reprobados = '{$totales['parcial_1']['reprobados']}',
                parcial_1_aprobados = '{$totales['parcial_1']['aprobados']}',
                parcial_2_reprobados = '{$totales['parcial_2']['reprobados']}',
                parcial_2_aprobados = '{$totales['parcial_2']['aprobados']}',
                parcial_3_reprobados = '{$totales['parcial_3']['reprobados']}',
                parcial_3_aprobados = '{$totales['parcial_3']['aprobados']}',
                promedio_reprobados = '{$totales['promedio']['reprobados']}',
                promedio_aprobados = '{$totales['promedio']['aprobados']}',
                segunda_oportunidad_reprobados = '{$totales['segunda_oportunidad']['reprobados']}',
                segunda_oportunidad_aprobados = '{$totales['segunda_oportunidad']['aprobados']}',
                creditos_reprobados_parcial_1 = '{$creditos_por_parcial['parcial_1']['reprobados']}',
                creditos_aprobados_parcial_1 = '{$creditos_por_parcial['parcial_1']['aprobados']}',
                creditos_reprobados_parcial_2 = '{$creditos_por_parcial['parcial_2']['reprobados']}',
                creditos_aprobados_parcial_2 = '{$creditos_por_parcial['parcial_2']['aprobados']}',
                creditos_reprobados_parcial_3 = '{$creditos_por_parcial['parcial_3']['reprobados']}',
                creditos_aprobados_parcial_3 = '{$creditos_por_parcial['parcial_3']['aprobados']}',
                creditos_reprobados_promedio = '{$creditos_por_parcial['promedio']['reprobados']}',
                creditos_aprobados_promedio = '{$creditos_por_parcial['promedio']['aprobados']}',
                creditos_reprobados_segunda_oportunidad = '{$creditos_por_parcial['segunda_oportunidad']['reprobados']}',
                creditos_aprobados_segunda_oportunidad = '{$creditos_por_parcial['segunda_oportunidad']['aprobados']}',
                calif_final_aprobados = '{$totales['calif_final']['aprobados']}',
                calif_final_reprobados = '{$totales['calif_final']['reprobados']}',
                creditos_reprobados_calif_final = '{$creditos_por_parcial['calif_final']['reprobados']}',
                creditos_aprobados_calif_final = '{$creditos_por_parcial['calif_final']['aprobados']}'
            WHERE id_grupo = '$id_grupo' AND id_materia = '$id_materia' AND id_periodo = '$id_periodo'
        ";
    } else {
        // Insertar un nuevo resumen
        $query = "INSERT INTO calificaciones_resumen (
                id_grupo, id_materia, id_periodo, total_alumnos,
                parcial_1_reprobados, parcial_1_aprobados,
                parcial_2_reprobados, parcial_2_aprobados,
                parcial_3_reprobados, parcial_3_aprobados,
                promedio_reprobados, promedio_aprobados,
                segunda_oportunidad_reprobados, segunda_oportunidad_aprobados,
                creditos_reprobados_parcial_1, creditos_aprobados_parcial_1,
                creditos_reprobados_parcial_2, creditos_aprobados_parcial_2,
                creditos_reprobados_parcial_3, creditos_aprobados_parcial_3,
                creditos_reprobados_promedio, creditos_aprobados_promedio,
                creditos_reprobados_segunda_oportunidad,
                creditos_aprobados_segunda_oportunidad,
                calif_final_aprobados,
                calif_final_reprobados,
                creditos_reprobados_calif_final,
                creditos_aprobados_calif_final
            ) VALUES (
                '$id_grupo', '$id_materia', '$id_periodo', '$total_alumnos',
                '{$totales['parcial_1']['reprobados']}', '{$totales['parcial_1']['aprobados']}',
                '{$totales['parcial_2']['reprobados']}', '{$totales['parcial_2']['aprobados']}',
                '{$totales['parcial_3']['reprobados']}', '{$totales['parcial_3']['aprobados']}',
                '{$totales['promedio']['reprobados']}', '{$totales['promedio']['aprobados']}',
                '{$totales['segunda_oportunidad']['reprobados']}', '{$totales['segunda_oportunidad']['aprobados']}',
                '{$creditos_por_parcial['parcial_1']['reprobados']}', '{$creditos_por_parcial['parcial_1']['aprobados']}',
                '{$creditos_por_parcial['parcial_2']['reprobados']}', '{$creditos_por_parcial['parcial_2']['aprobados']}',
                '{$creditos_por_parcial['parcial_3']['reprobados']}', '{$creditos_por_parcial['parcial_3']['aprobados']}',
                '{$creditos_por_parcial['promedio']['reprobados']}', '{$creditos_por_parcial['promedio']['aprobados']}',
                '{$creditos_por_parcial['segunda_oportunidad']['reprobados']}', '{$creditos_por_parcial['segunda_oportunidad']['aprobados']}',
                '{$totales['calif_final']['aprobados']}', '{$totales['calif_final']['reprobados']}',
                '{$creditos_por_parcial['calif_final']['reprobados']}', '{$creditos_por_parcial['calif_final']['aprobados']}'
            )
        ";
    }

    $resultado = mysqli_query($conexion, $query);
    if (!$resultado) {
        echo "Error al guardar el resumen de calificaciones: " . mysqli_error($conexion);
    }
}

$consulta_alumnos = "
    SELECT DISTINCT a.id_alumno, a.matricula, a.ap_paterno, a.ap_materno, a.nombre,
           c.parcial_1, c.parcial_2, c.parcial_3, c.promedio, c.segunda_oportunidad, c.calif_final
    FROM alumnos a 
    JOIN calificaciones c ON a.id_alumno = c.id_alumno 
    JOIN periodos p ON c.id_periodo = p.id_periodo
    WHERE c.id_grupo = '$id_grupo' AND c.id_materia = '$id_materia' AND p.id_periodo = '$id_periodo'
    ORDER BY a.ap_paterno, a.ap_materno, a.nombre
";

$resultado_alumnos = mysqli_query($conexion, $consulta_alumnos);

// Obtener el número total de alumnos
$total_alumnos = mysqli_num_rows($resultado_alumnos);

// Inicializar variables para porcentajes
$porcentaje_aprobados = [];
$porcentaje_reprobados = [];

if ($total_alumnos > 0) {
    foreach (['parcial_1', 'parcial_2', 'parcial_3', 'promedio', 'segunda_oportunidad'] as $tipo) {
        $porcentaje_aprobados[$tipo] = isset($totales[$tipo]['aprobados']) ? ($totales[$tipo]['aprobados'] / $total_alumnos) * 100 : 0;
        $porcentaje_reprobados[$tipo] = isset($totales[$tipo]['reprobados']) ? ($totales[$tipo]['reprobados'] / $total_alumnos) * 100 : 0;
    }
} else {
    foreach (['parcial_1', 'parcial_2', 'parcial_3', 'promedio', 'segunda_oportunidad'] as $tipo) {
        $porcentaje_aprobados[$tipo] = 0;
        $porcentaje_reprobados[$tipo] = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Calificaciones</title>
    <link rel="stylesheet" href="../assets/css/style.css" type="text/css">
    <style>
        /* Estilos para la página */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        h2 {
            margin-top: 0;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .editable {
            cursor: pointer;
        }

        .editable input[type="text"] {
            width: 80px;
            padding: 5px;
            border: 1px solid #ccc;
            background-color: #fff;
            text-align: center;
            /* Centrar el texto dentro de los campos de entrada */
        }

        .editable input[type="text"]:read-only {
            border: none;
            background-color: transparent;
        }

        .editable input[type="text"]:focus {
            outline: none;
            border: 1px solid blue;
        }

        .button-container {
            margin-top: 20px;
        }

        .button-container a {
            text-decoration: none;
        }

        .button-container a button {
            background-color: #fff;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px 20px;
        }

        .button-container a button:hover {
            background-color: #45a049;
        }

        .button-container a.regresar button {
            background-color: #f44336;
        }

        .button-container a.regresar button:hover {
            background-color: #d32f2f;
        }

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
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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

        select,
        input[type="submit"] {
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
            text-transform: uppercase;
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

        .nav-menu-link.selected,
        .nav-menu-link:hover {
            background-color: #00509e;
            /* Azul más oscuro */
        }

        .nav-toggle {
            display: none;
            /* Mostrar solo en dispositivos móviles */
            background: none;
            border: none;
            cursor: pointer;
        }

        .nav-toggle-icon {
            width: 24px;
            height: 24px;
        }

        /* Estilos específicos para las cajas de texto de promedio y calificación final */
        .transparent-input {
            background-color: transparent;
            border: none;
        }

        /* Estilo para celdas con valor "N/A" */
        .na-cell {
            background-color: #f9ff33;
            /* Color de fondo para celdas con "N/A" */
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
                <li class="nav-menu-item"><a class="nav-menu-link selected" href="../php/asignacion_grupo.php">Calificaciones</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/resumen.php">Resumen</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../index.php">Cerrar Sesión</a></li>
            </ul>
            <button class="nav-toggle">
                <img src="../assets/images/menu.svg" class="nav-toggle-icon" alt="">
            </button>
        </nav>
    </header>

    <h2><strong>Periodo:</strong> <?php echo htmlspecialchars($nombre_periodo); ?></h2>
    <h2><strong>Grupo:</strong> <?php echo htmlspecialchars($nombre_grupo); ?></h2>
    <h2><strong>Materia:</strong> <?php echo htmlspecialchars($nombre_materia); ?></h2>
    <h2><strong>Creditos:</strong> <?php echo htmlspecialchars($creditos_materia); ?></h2>

    <?php
    if ($resultado_alumnos && mysqli_num_rows($resultado_alumnos) > 0) {
        // Inicializar contadores
        $contador_aprobados = 0;
        $contador_reprobados = 0;

        echo '<form method="post" action="">';
        echo '<table border="1">';
        echo '<thead><tr><th>No.</th><th>Matrícula</th><th>Apellido Paterno</th><th>Apellido Materno</th><th>Nombre</th><th>Parcial 1</th><th>Parcial 2</th><th>Parcial 3</th><th>Promedio</th><th>Segunda Oportunidad</th><th>Calif Final</th></tr></thead>';
        echo '<tbody>';
        $contador = 1;
        while ($alumno = mysqli_fetch_assoc($resultado_alumnos)) {
            // Obtener la calificación final
            $calif_final = isset($alumno['calif_final']) ? $alumno['calif_final'] : '';

            // Contar aprobados y reprobados
            if (strtoupper($calif_final) === 'N/A') {
                $contador_reprobados++;
            } elseif (is_numeric($calif_final) && $calif_final >= 70) {
                $contador_aprobados++;
            } else {
                // Para valores que no sean numéricos o que no cumplan con el criterio de aprobado
                $contador_reprobados++;
            }

            $disable_segunda_oportunidad = ($alumno['parcial_1'] !== 'N/A' && $alumno['parcial_2'] !== 'N/A' && $alumno['parcial_3'] !== 'N/A') ? 'disabled' : '';
            $segunda_oportunidad_value = $disable_segunda_oportunidad ? '' : (isset($alumno['segunda_oportunidad']) ? $alumno['segunda_oportunidad'] : '');
            echo '<tr>';
            echo '<td>' . $contador . '</td>';
            echo '<td>' . $alumno['matricula'] . '</td>';
            echo '<td>' . $alumno['ap_paterno'] . '</td>';
            echo '<td>' . $alumno['ap_materno'] . '</td>';
            echo '<td>' . $alumno['nombre'] . '</td>';
            echo '<td class="editable"><input type="text" name="alumnos[' . $alumno['id_alumno'] . '][parcial_1]" value="' . (isset($alumno['parcial_1']) ? $alumno['parcial_1'] : '') . '" style="text-transform: uppercase;" oninput="replaceNA(this)"></td>';
            echo '<td class="editable"><input type="text" name="alumnos[' . $alumno['id_alumno'] . '][parcial_2]" value="' . (isset($alumno['parcial_2']) ? $alumno['parcial_2'] : '') . '" style="text-transform: uppercase;" oninput="replaceNA(this)"></td>';
            echo '<td class="editable"><input type="text" name="alumnos[' . $alumno['id_alumno'] . '][parcial_3]" value="' . (isset($alumno['parcial_3']) ? $alumno['parcial_3'] : '') . '" style="text-transform: uppercase;" oninput="replaceNA(this)"></td>';
            echo '<td><input type="text" class="transparent-input" name="alumnos[' . $alumno['id_alumno'] . '][promedio]" value="' . (isset($alumno['promedio']) ? $alumno['promedio'] : '') . '" readonly style="text-transform: uppercase;" oninput="replaceNA(this)"></td>';
            echo '<td class="editable"><input type="text" name="alumnos[' . $alumno['id_alumno'] . '][segunda_oportunidad]" value="' . $segunda_oportunidad_value . '" style="text-transform: uppercase;" oninput="replaceNA(this)" ' . $disable_segunda_oportunidad . '></td>';
            echo '<td><input type="text" class="transparent-input" name="alumnos[' . $alumno['id_alumno'] . '][calif_final]" value="' . (isset($alumno['calif_final']) ? $alumno['calif_final'] : '') . '" readonly style="text-transform: uppercase;" oninput="replaceNA(this)"></td>';
            echo '</tr>';
            $contador++;
        }
        echo '</tbody>';
        echo '</table>';
        echo '<input type="hidden" name="grupo" value="' . $id_grupo . '">';
        echo '<input type="hidden" name="materia" value="' . $id_materia . '">';
        echo '<input type="hidden" name="periodo" value="' . $id_periodo . '">';
        echo '<div class="button-container">';
        echo '<button type="submit">Guardar Calificaciones</button>';
        echo '<a class="regresar" href="asignacion_grupo.php"><button type="button">Regresar</button></a>';
        echo '</div>';
        echo '</form>';

        // Inicializar variables para cálculos
        $total_alumnos = mysqli_num_rows($resultado_alumnos);
        $totales = array_fill_keys(['parcial_1', 'parcial_2', 'parcial_3', 'calificacion_final', 'segunda_oportunidad'], ['reprobados' => 0, 'aprobados' => 0]);
        $creditos_por_parcial = array_fill_keys(['parcial_1', 'parcial_2', 'parcial_3', 'calificacion_final'], ['reprobados' => 0, 'aprobados' => 0]);

        // Volver a la consulta inicial para recalcular totales
        $resultado_alumnos = mysqli_query($conexion, $consulta_alumnos);
        $alumnos_segunda_oportunidad = 0; // Contador para alumnos en segunda oportunidad
        while ($alumno = mysqli_fetch_assoc($resultado_alumnos)) {
            $id_alumno = $alumno['id_alumno'];

            // Calcular calificación final si está disponible
            $calif_final = isset($alumno['calif_final']) ? $alumno['calif_final'] : 'N/A';

            // Procesar parciales

            $materias_query = "SELECT id_materia, creditos FROM materias";
            $materias_result = mysqli_query($conexion, $materias_query);
            $creditos_materias = [];
            while ($materia = mysqli_fetch_assoc($materias_result)) {
                $creditos_materias[$materia['id_materia']] = $materia['creditos'];
            }

            foreach (['parcial_1', 'parcial_2', 'parcial_3'] as $parcial) {
                if ($alumno[$parcial] === 'N/A' || $alumno[$parcial] < 70) {
                    $totales[$parcial]['reprobados']++;
                    $creditos_por_parcial[$parcial]['reprobados'] += $creditos_materias[$id_materia] ?? 0;
                } else {
                    $totales[$parcial]['aprobados']++;
                    $creditos_por_parcial[$parcial]['aprobados'] += $creditos_materias[$id_materia] ?? 0;
                }
            }

            // Procesar segunda oportunidad
            if ($alumno['segunda_oportunidad'] !== '') {
                $alumnos_segunda_oportunidad++; // Incrementar contador de alumnos en segunda oportunidad
                if ($alumno['segunda_oportunidad'] < 70 || $alumno['segunda_oportunidad'] === 'N/A') {
                    $totales['segunda_oportunidad']['reprobados']++;
                } else {
                    $totales['segunda_oportunidad']['aprobados']++;
                }
            }

            // Procesar calificación final
            if ($calif_final === 'N/A' || $calif_final < 70) {
                $totales['calificacion_final']['reprobados']++;
            } else {
                $totales['calificacion_final']['aprobados']++;
            }
        }

        // Calcular porcentajes
        $porcentaje_reprobados = [];
        $porcentaje_aprobados = [];
        foreach ($totales as $tipo => $data) {
            if ($tipo === 'segunda_oportunidad') {
                $porcentaje_reprobados[$tipo] = $alumnos_segunda_oportunidad > 0 ? ($data['reprobados'] / $alumnos_segunda_oportunidad) * 100 : 0;
                $porcentaje_aprobados[$tipo] = $alumnos_segunda_oportunidad > 0 ? ($data['aprobados'] / $alumnos_segunda_oportunidad) * 100 : 0;
            } else {
                $porcentaje_reprobados[$tipo] = $total_alumnos > 0 ? ($data['reprobados'] / $total_alumnos) * 100 : 0;
                $porcentaje_aprobados[$tipo] = $total_alumnos > 0 ? ($data['aprobados'] / $total_alumnos) * 100 : 0;
            }
        }

        // Mostrar título
        echo '<h2>Indicador de Acreditación del Grupo ' . htmlspecialchars($nombre_grupo) . ' de la Materia ' . htmlspecialchars($nombre_materia) . '</h2>';

        // Generar la tabla
        echo '<table border="1">';
        echo '<thead><tr><th>Parcial</th><th>No Acreditados</th><th>Porcentaje No Acreditados</th><th>Acreditados</th><th>Porcentaje Acreditados</th><th>Total</th></tr></thead>';
        echo '<tbody>';

        // Reordenar los elementos para que "promedio" aparezca después de "segunda_oportunidad"
        foreach (['parcial_1', 'parcial_2', 'parcial_3', 'segunda_oportunidad', 'calificacion_final'] as $tipo) {
            echo '<tr>';
            echo '<td>' . ucwords(str_replace('_', ' ', $tipo)) . '</td>';
            echo '<td>' . (isset($totales[$tipo]['reprobados']) ? $totales[$tipo]['reprobados'] : 0) . '</td>';
            echo '<td>' . (floor($porcentaje_reprobados[$tipo]) == $porcentaje_reprobados[$tipo] ? $porcentaje_reprobados[$tipo] : number_format($porcentaje_reprobados[$tipo], 2)) . '%</td>'; // Mostrar decimales solo si es necesario
            echo '<td>' . (isset($totales[$tipo]['aprobados']) ? $totales[$tipo]['aprobados'] : 0) . '</td>';
            echo '<td>' . (floor($porcentaje_aprobados[$tipo]) == $porcentaje_aprobados[$tipo] ? $porcentaje_aprobados[$tipo] : number_format($porcentaje_aprobados[$tipo], 2)) . '%</td>'; // Mostrar decimales solo si es necesario
            echo '<td>' . ($tipo === 'segunda_oportunidad' ? $alumnos_segunda_oportunidad : $total_alumnos) . '</td>'; // Mostrar el total de alumnos
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';

        // Mostrar tabla de Créditos
        echo '<h2>Indicador de Acreditacion de Creditos del Grupo ' . htmlspecialchars($nombre_grupo) . ' de la Materia ' . htmlspecialchars($nombre_materia) . '</h2>';
        echo '<table border="1">';
        echo '<thead><tr><th>Parcial</th><th>Créditos Acreditados</th><th>Créditos No Acreditados</th></tr></thead>';
        echo '<tbody>';
        foreach (['parcial_1', 'parcial_2', 'parcial_3'] as $tipo) {
            echo '<tr>';
            echo '<td>' . ucwords(str_replace('_', ' ', $tipo)) . '</td>';
            echo '<td>' . (isset($creditos_por_parcial[$tipo]['aprobados']) ? $creditos_por_parcial[$tipo]['aprobados'] : 0) . '</td>';
            echo '<td>' . (isset($creditos_por_parcial[$tipo]['reprobados']) ? $creditos_por_parcial[$tipo]['reprobados'] : 0) . '</td>';

            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo 'No hay alumnos para mostrar.';
    }

    mysqli_close($conexion);
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cells = document.querySelectorAll('.editable input[type="text"]');

            cells.forEach(cell => {
                cell.addEventListener('keydown', function(event) {
                    const currentCell = event.target;
                    const currentRow = currentCell.parentElement.parentElement;
                    const currentIndex = Array.from(currentRow.children).indexOf(currentCell.parentElement);

                    let nextCell;
                    if (event.key === 'ArrowRight' && currentCell.nextElementSibling) {
                        nextCell = currentCell.nextElementSibling.querySelector('input[type="text"]');
                    } else if (event.key === 'ArrowLeft' && currentCell.previousElementSibling) {
                        nextCell = currentCell.previousElementSibling.querySelector('input[type="text"]');
                    } else if (event.key === 'ArrowDown' && currentRow.nextElementSibling) {
                        nextCell = currentRow.nextElementSibling.children[currentIndex].querySelector('input[type="text"]');
                    } else if (event.key === 'ArrowUp' && currentRow.previousElementSibling) {
                        nextCell = currentRow.previousElementSibling.children[currentIndex].querySelector('input[type="text"]');
                    }

                    if (nextCell) {
                        nextCell.focus();
                    }
                });
            });

            // Cambiar el color de fondo de las celdas que contienen "N/A"
            const naCells = document.querySelectorAll('input[value="N/A"]');
            naCells.forEach(cell => {
                cell.parentElement.classList.add('na-cell');
            });
        });

        function replaceNAOnSubmit() {
            const inputs = document.querySelectorAll('.editable input[type="text"]');
            inputs.forEach(input => {
                input.value = input.value.trim();
                if (input.value === '' || input.value < 70 || input.value > 100) {
                    input.value = 'N/A';
                }
            });
        }

        function replaceNA(input) {
            input.value = input.value.toUpperCase();
            if (input.value === 'NA') {
                input.value = 'N/A';
            }
            if (input.value !== 'N/A' && (input.value < 0 || input.value > 100)) {
                input.value = '';
            }
        }

        document.querySelector('form').addEventListener('submit', function(event) {
            replaceNAOnSubmit();
        });
    </script>

</body>

</html>