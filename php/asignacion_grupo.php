<?php
require 'verificar_sesion.php';
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

// Obtener los periodos
$periodos = [];
$consulta_periodos = "SELECT id_periodo, periodo FROM periodos";
$resultado_periodos = mysqli_query($conexion, $consulta_periodos);

if ($resultado_periodos && mysqli_num_rows($resultado_periodos) > 0) {
    while ($periodo = mysqli_fetch_assoc($resultado_periodos)) {
        $periodos[] = $periodo;
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

// Procesar datos del formulario POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    // Obtener los alumnos y sus calificaciones
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
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 60%; /* Cambiado de 80% a 60% */
            max-width: 600px; /* Cambiado de 600px a 500px */
            margin: 10px auto;
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
        /* Estilos adicionales para la tabla y otros elementos */
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
    
        .transparent-input {
            background-color: transparent;
            border: none;
        }
    
        .na-cell {
            background-color: #f9ff33;
        }
    </style>  
    
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
    
        // Mantener los valores seleccionados en los selects
        document.getElementById('periodo').value = "<?php echo $id_periodo; ?>";
        document.getElementById('grupo').value = "<?php echo $id_grupo; ?>";
        document.getElementById('materia').value = "<?php echo $id_materia; ?>";
    </script>
    <script>
        function actualizarMaterias() {
            const grupoId = document.getElementById('grupo').value;
            const materiaSelect = document.getElementById('materia');
            materiaSelect.innerHTML = '<option value="">Cargando...</option>';

            fetch(`obtener_materias.php?id_grupo=${grupoId}`)
                .then(response => response.json())
                .then(data => {
                    materiaSelect.innerHTML = '';
                    data.forEach(materia => {
                        const option = document.createElement('option');
                        option.value = materia.id_materia;
                        option.textContent = materia.nombre;
                        materiaSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error al obtener las materias:', error);
                    materiaSelect.innerHTML = '<option value="">Error al cargar</option>';
                });
        }

        function validarFormulario(event) {
            const grupo = document.getElementById('grupo').value;
            const materia = document.getElementById('materia').value;
            const periodo = document.getElementById('periodo').value;

            if (!grupo || !materia || !periodo) {
                alert('Por favor, seleccione un grupo, una materia y un período.');
                event.preventDefault();
            }
        }

        function actualizarGrupos() {
            const periodoId = document.getElementById('periodo').value;
            const grupoSelect = document.getElementById('grupo');
            grupoSelect.innerHTML = '<option value="">Cargando...</option>';

            fetch(`obtener_grupos.php?id_periodo=${periodoId}`)
                .then(response => response.json())
                .then(data => {
                    grupoSelect.innerHTML = '<option value="">Seleccione un grupo</option>';
                    data.forEach(grupo => {
                        const option = document.createElement('option');
                        option.value = grupo.id_grupo;
                        option.textContent = grupo.nombre_grupo;
                        grupoSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error al obtener los grupos:', error);
                    grupoSelect.innerHTML = '<option value="">Error al cargar</option>';
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            form.addEventListener('submit', validarFormulario);
            document.getElementById('periodo').addEventListener('change', actualizarGrupos);
        });
    </script>
</head>

<body>
    <?php include 'header.html'; ?>
    <div class="container">
        <h1>Seleccionar Grupo, Materia y Período</h1>

        <form method="POST" action="asignacion_grupo.php">

            <label for="periodo">Periodo:</label>
            <select name="periodo" id="periodo">
                <option value="">Seleccione un periodo</option>
                <?php foreach ($periodos as $periodo): ?>
                    <option value="<?php echo $periodo['id_periodo']; ?>" 
                        <?php echo (isset($_POST['periodo']) && $_POST['periodo'] == $periodo['id_periodo']) ? 'selected' : ''; ?>>
                        <?php echo $periodo['periodo']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="grupo">Grupo:</label>
            <select name="grupo" id="grupo" onchange="actualizarMaterias()">
                <option value="">Seleccione un grupo</option>
                <?php foreach ($grupos as $grupo): ?>
                    <option value="<?php echo $grupo['id_grupo']; ?>" 
                        <?php echo (isset($_POST['grupo']) && $_POST['grupo'] == $grupo['id_grupo']) ? 'selected' : ''; ?>>
                        <?php echo $grupo['nombre_grupo']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="materia">Materia:</label>
            <select name="materia" id="materia">
                <option value="">Seleccione una materia</option>
                <?php foreach ($materias as $materia): ?>
                    <option value="<?php echo $materia['id_materia']; ?>" 
                        <?php echo (isset($_POST['materia']) && $_POST['materia'] == $materia['id_materia']) ? 'selected' : ''; ?>>
                        <?php echo $materia['nombre']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="submit" value="Enviar">
        </form>
    </div>

    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>

        <?php if ($resultado_alumnos && mysqli_num_rows($resultado_alumnos) > 0): ?>
            <form method="post" action="asignacion_grupo.php">
                <table border="1">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Matrícula</th>
                            <th>Apellido Paterno</th>
                            <th>Apellido Materno</th>
                            <th>Nombre</th>
                            <th>Parcial 1</th>
                            <th>Parcial 2</th>
                            <th>Parcial 3</th>
                            <th>Promedio</th>
                            <th>Segunda Oportunidad</th>
                            <th>Calif Final</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $contador = 1; ?>
                        <?php while ($alumno = mysqli_fetch_assoc($resultado_alumnos)): ?>
                            <tr>
                                <td><?php echo $contador++; ?></td>
                                <td><?php echo $alumno['matricula']; ?></td>
                                <td><?php echo $alumno['ap_paterno']; ?></td>
                                <td><?php echo $alumno['ap_materno']; ?></td>
                                <td><?php echo $alumno['nombre']; ?></td>
                                <td class="editable"><input type="text" name="alumnos[<?php echo $alumno['id_alumno']; ?>][parcial_1]" value="<?php echo $alumno['parcial_1']; ?>" style="text-transform: uppercase;" oninput="replaceNA(this)"></td>
                                <td class="editable"><input type="text" name="alumnos[<?php echo $alumno['id_alumno']; ?>][parcial_2]" value="<?php echo $alumno['parcial_2']; ?>" style="text-transform: uppercase;" oninput="replaceNA(this)"></td>
                                <td class="editable"><input type="text" name="alumnos[<?php echo $alumno['id_alumno']; ?>][parcial_3]" value="<?php echo $alumno['parcial_3']; ?>" style="text-transform: uppercase;" oninput="replaceNA(this)"></td>
                                <td><input type="text" class="transparent-input" name="alumnos[<?php echo $alumno['id_alumno']; ?>][promedio]" value="<?php echo $alumno['promedio']; ?>" readonly style="text-transform: uppercase;" oninput="replaceNA(this)"></td>
                                <td class="editable"><input type="text" name="alumnos[<?php echo $alumno['id_alumno']; ?>][segunda_oportunidad]" value="<?php echo $alumno['segunda_oportunidad']; ?>" style="text-transform: uppercase;" oninput="replaceNA(this)" <?php echo ($alumno['parcial_1'] !== 'N/A' && $alumno['parcial_2'] !== 'N/A' && $alumno['parcial_3'] !== 'N/A') ? 'disabled' : ''; ?>></td>
                                <td><input type="text" class="transparent-input" name="alumnos[<?php echo $alumno['id_alumno']; ?>][calif_final]" value="<?php echo $alumno['calif_final']; ?>" readonly style="text-transform: uppercase;" oninput="replaceNA(this)"></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <input type="hidden" name="grupo" value="<?php echo $id_grupo; ?>">
                <input type="hidden" name="materia" value="<?php echo $id_materia; ?>">
                <input type="hidden" name="periodo" value="<?php echo $id_periodo; ?>">
                <div class="button-container">
                    <button type="submit">Guardar Calificaciones</button>
                    <a class="regresar" href="asignacion_grupo.php"><button type="button">Regresar</button></a>
                </div>
            </form>
        <?php else: ?>
            <p>No hay alumnos para mostrar.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>

</html>