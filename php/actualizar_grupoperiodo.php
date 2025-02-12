<?php
require 'verificar_sesion.php';
// Configuración de la base de datos
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

// Obtener todos los periodos
$sql = "SELECT id_periodo, periodo FROM periodos";
$result = $conn->query($sql);
$periodos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $periodos[] = $row;
    }
}

// Obtener el periodo seleccionado (si existe) y filtrar los grupos
$id_periodo = isset($_GET['id_periodo']) ? intval($_GET['id_periodo']) : 0;
$grupos = [];

if ($id_periodo > 0) {
    // Filtrar grupos por id_periodo usando la tabla calificaciones
    $sql = "SELECT DISTINCT g.id_grupo, g.nombre_grupo
            FROM grupos g
            INNER JOIN calificaciones c ON g.id_grupo = c.id_grupo
            WHERE c.id_periodo = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    
    $stmt->bind_param("i", $id_periodo);
    
    if ($stmt->execute() === false) {
        die("Error en la ejecución de la consulta: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $grupos[] = $row;
    }
    
    $stmt->close();
} else {
    // Obtener todos los grupos si no se ha seleccionado periodo
    $sql = "SELECT id_grupo, nombre_grupo FROM grupos";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $grupos[] = $row;
        }
    }
}

// Obtener todos los grupos para el selector de nuevo grupo
$sql = "SELECT id_grupo, nombre_grupo FROM grupos";
$result = $conn->query($sql);
$all_grupos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $all_grupos[] = $row;
    }
}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alumnos por Grupo</title>
    <link rel="stylesheet" href="../assets/css/style.css" type="text/css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        h1, h2, h3 {
            color: #007bff;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        select, button, input[type="submit"] {
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        select {
            width: 200px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        #grupos div {
            margin-bottom: 10px;
        }
        #grupos button {
            display: inline-block;
            margin-right: 10px;
            background-color: #28a745;
            color: #fff;
        }
        #grupos button:hover {
            background-color: #218838;
        }
        .btn-eliminar {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 5px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
        }
        .btn-eliminar:hover {
            background-color: #c82333;
        }
        #alumnos {
            margin: 20px 0;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .btn-alumnos-irregulares {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #dc3545;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn-alumnos-irregulares:hover {
            background-color: #c82333;
        }
        /* Estilos para el modal de edición */
        #modalEditarAlumno {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 350px;
            padding: 20px;
            background: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            border-radius: 8px;
        }
        #modalEditarAlumno button {
            margin-right: 10px;
        }
        #modalEditarAlumno .close {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 18px;
            color: #333;
        }
        #modalEditarAlumno .close:hover {
            color: #007bff;
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

        h2 {
            text-align: center;
            color: #007bff; /* Azul */
        }

        label {
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 8px;
            margin: 8px 0 16px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #007bff; /* Azul */
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3; /* Azul más oscuro */
        }

        .form-group {
            margin-bottom: 15px;
        }

        #materias {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #f9f9f9;
        }

        .btn-alumnos-irregulares {
            display: block;
            width: 100%;
            background-color: #ff5733; /* Color para el botón */
            color: #fff;
            border: none;
            padding: 10px 1px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 20px;
            text-align: center;
            transition: background-color 0.3s ease;
            margin-top: 15px;
        }

        .btn-alumnos-irregulares:hover {
            background-color: #c70039; /* Color más oscuro para el botón */
        }

        .btn-buscar, .btn-borrar {
            background-color: #28a745; /* Verde para el botón */
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }

        .btn-buscar:hover, .btn-borrar:hover {
            background-color: #218838; /* Verde más oscuro para el botón */
        }
    </style>
</head>
<body>

<?php include 'header.html'; ?>

    </style>
</head>
<body>

<h1>Seleccione un Grupo y Periodo</h1>

<!-- Selector de periodo -->
<label for="select_periodo">Seleccione Periodo Anterior:</label>
<select id="select_periodo" name="select_periodo">
    <option value="" disabled selected>Seleccionar Periodo</option>
    <?php foreach ($periodos as $periodo): ?>
        <option value="<?php echo $periodo['id_periodo']; ?>" <?php echo $id_periodo == $periodo['id_periodo'] ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($periodo['periodo']); ?>
        </option>
    <?php endforeach; ?>
</select>

<button type="button" class="boton-regresar button-margin-right" onclick="window.location.href='alumnos.php'">Regresar</button>

<button id="cargarGrupos" class="button-margin-right">Cargar Grupos</button>

<div id="grupos">
    <?php foreach ($grupos as $grupo): ?>
        <div>
            <button onclick="mostrarAlumnos(<?php echo $grupo['id_grupo']; ?>)">
                <?php echo htmlspecialchars($grupo['nombre_grupo']); ?>
            </button>
            <button class="btn-eliminar" onclick="eliminarGrupo(<?php echo $grupo['id_grupo']; ?>)">
                Eliminar Grupo
            </button>
        </div>
    <?php endforeach; ?>
</div>

<h2>Alumnos del Grupo</h2>
<form id="asignarGrupoForm">
    <div id="alumnos"></div>
    
    <h3>Asignar Nuevo Grupo y Periodo</h3>
    <label for="nuevo_grupo">Nuevo Grupo:</label>
    <select id="nuevo_grupo" name="nuevo_grupo" required>
        <option value="" disabled selected>Seleccionar Grupo</option>
        <?php foreach ($all_grupos as $grupo): ?>
            <option value="<?php echo $grupo['id_grupo']; ?>">
                <?php echo htmlspecialchars($grupo['nombre_grupo']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    
    <label for="nuevo_periodo">Nuevo Periodo:</label>
    <select id="nuevo_periodo" name="nuevo_periodo" required>
        <option value="" disabled selected>Seleccionar Periodo</option>
        <?php foreach ($periodos as $periodo): ?>
            <option value="<?php echo $periodo['id_periodo']; ?>">
                <?php echo htmlspecialchars($periodo['periodo']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    
    <input type="submit" value="Asignar">
</form>

<!-- Modal para editar el alumno -->
<div id="modalEditarAlumno">
    <span class="close" onclick="cerrarModal()">&times;</span>
    <h3>Editar Datos del Alumno</h3>
    <form id="formEditarAlumno">
        <input type="hidden" id="alumno_id" name="alumno_id">
        <label for="nombre_alumno">Nombre del Alumno:</label>
        <input type="text" id="nombre_alumno" name="nombre_alumno" required>
        <label for="ap_paterno">Apellido Paterno:</label>
        <input type="text" id="ap_paterno" name="ap_paterno" required>
        <label for="ap_materno">Apellido Materno:</label>
        <input type="text" id="ap_materno" name="ap_materno" required>
        <button type="submit">Guardar</button>
        <button type="button" onclick="cerrarModal()">Cancelar</button>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#cargarGrupos').on('click', function() {
            var id_periodo = $('#select_periodo').val();
            if (id_periodo) {
                window.location.href = '?id_periodo=' + id_periodo;
            } else {
                alert('Por favor, seleccione un periodo.');
            }
        });

        $('#asignarGrupoForm').on('submit', function(event) {
            event.preventDefault();
            var selectedAlumnos = [];
            $('input[name="alumno_id[]"]:checked').each(function() {
                selectedAlumnos.push($(this).val());
            });

            if (selectedAlumnos.length > 0) {
                $.ajax({
                    url: 'asignar_nuevo_grupo.php',
                    type: 'POST',
                    data: {
                        alumnos: selectedAlumnos,
                        nuevo_grupo: $('#nuevo_grupo').val(),
                        nuevo_periodo: $('#nuevo_periodo').val()
                    },
                    success: function(response) {
                        alert(response);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        alert("Ocurrió un error al asignar el nuevo grupo y periodo. Verifique la consola para más detalles.");
                    }
                });
            } else {
                alert("Seleccione al menos un alumno.");
            }
        });

        window.eliminarAlumno = function(id_alumno, id_grupo) {
            var id_periodo = $('#select_periodo').val(); // Obtiene el periodo seleccionado

            if (confirm('¿Estás seguro de que quieres eliminar este alumno del grupo seleccionado?')) {
                $.ajax({
                    url: 'eliminar_alumno.php',
                    type: 'POST',
                    data: {
                        id_alumno: id_alumno,
                        id_grupo: id_grupo,
                        id_periodo: id_periodo
                    },
                    success: function(response) {
                        alert(response);
                        $('#cargarGrupos').click(); // Recargar la lista de alumnos
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        alert("Ocurrió un error al eliminar el alumno. Verifique la consola para más detalles.");
                    }
                });
            }
        }

        window.mostrarAlumnos = function(id_grupo) {
            var id_periodo = $('#select_periodo').val(); // Obtiene el periodo seleccionado

            $.ajax({
                url: 'obtener_alumnos.php',
                type: 'POST',
                data: { id_grupo: id_grupo, id_periodo: id_periodo },
                success: function(response) {
                    $('#alumnos').html(response);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    $('#alumnos').html("<p>Ocurrió un error al obtener los datos. Verifique la consola para más detalles.</p>");
                }
            });
        }

        window.eliminarGrupo = function(id_grupo) {
            var id_periodo = $('#select_periodo').val(); // Obtiene el periodo seleccionado

            if (confirm('¿Estás seguro de que quieres eliminar este grupo del periodo seleccionado?')) {
                $.ajax({
                    url: 'eliminar_grupo.php',
                    type: 'POST',
                    data: { id_grupo: id_grupo, id_periodo: id_periodo },
                    success: function(response) {
                        alert(response);
                        location.reload(); // Recarga la página para reflejar los cambios
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        alert("Ocurrió un error al eliminar el grupo. Verifique la consola para más detalles.");
                    }
                });
            }
        }

        window.editarAlumno = function(id_alumno) {
            $('#modalEditarAlumno').show();
            
            $.ajax({
                url: 'obtener_nombre_alumno.php',
                type: 'POST',
                data: { id_alumno: id_alumno },
                success: function(response) {
                    var alumno = JSON.parse(response);
                    $('#alumno_id').val(alumno.id_alumno);
                    $('#nombre_alumno').val(alumno.nombre);
                    $('#ap_paterno').val(alumno.ap_paterno);
                    $('#ap_materno').val(alumno.ap_materno);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert("Ocurrió un error al obtener los datos del alumno.");
                }
            });
        };

        window.cerrarModal = function() {
            $('#modalEditarAlumno').hide();
        };

        $('#formEditarAlumno').on('submit', function(event) {
            event.preventDefault();

            $.ajax({
                url: 'actualizar_alumno.php',
                type: 'POST',
                data: {
                    id_alumno: $('#alumno_id').val(),
                    nombre_alumno: $('#nombre_alumno').val(),
                    ap_paterno: $('#ap_paterno').val(),
                    ap_materno: $('#ap_materno').val()
                },
                success: function(response) {
                    alert(response);
                    cerrarModal();
                    // Recargar la lista de alumnos
                    $('#cargarGrupos').click();
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert("Ocurrió un error al actualizar los datos del alumno. Verifique la consola para más detalles.");
                }
            });
        });
    });
</script>
</body>
</html>