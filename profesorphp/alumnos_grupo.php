<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Alumnos en el Grupo</title>
<style>
/* Estilos para la ventana modal */
.modal {
    display: none; /* Por defecto, la ventana modal está oculta */
    position: fixed; /* Fijar la posición */
    z-index: 1; /* Hacer que la ventana modal esté por encima de todo */
    left: 0;
    top: 0;
    width: 100%; /* Ancho completo */
    height: 100%; /* Altura completa */
    overflow: auto; /* Permitir desplazamiento si el contenido es demasiado grande */
    background-color: rgba(0, 0, 0, 0.4); /* Fondo oscuro */
}

/* Estilos para el contenido de la ventana modal */
.modal-content {
    background-color: #fefefe;
    margin: 15% auto; /* Margen superior e inferior */
    padding: 20px;
    border: 1px solid #888;
    width: 80%; /* Ancho del contenido */
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
    animation-name: modalopen; /* Animación de apertura */
    animation-duration: 0.4s;
}

/* Estilos para el botón de cierre */
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

/* Animación de apertura de la ventana modal */
@keyframes modalopen {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
</style>
</head>
<body>

<?php
// Verificar si se ha enviado el id_grupo
if (isset($_GET['id_grupo'])) {
    // Obtener el id_grupo enviado
    $id_grupo = $_GET['id_grupo'];

    // Configurar la conexión a la base de datos
    $servername = "localhost"; // Cambia esto por tu servidor de base de datos
    $username = "serviciosocial"; // Cambia esto por tu nombre de usuario de la base de datos
    $password = "FtW30yNo8hQd-x/G"; // Cambia esto por tu contraseña de la base de datos
    $database = "login_register_db"; // Cambia esto por el nombre de tu base de datos
    
    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $database);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("La conexión falló: " . $conn->connect_error);
    }

    // Consulta SQL para obtener los nombres de los alumnos y sus calificaciones en ese grupo
    $sql_alumnos_calificaciones = "SELECT alumnos.id_alumno, alumnos.nombre AS nombre_alumno, 
                                    alumnos.ap_paterno, alumnos.ap_materno, 
                                    calificaciones.id_materia, 
                                    calificaciones.parcial_1, calificaciones.parcial_2, calificaciones.parcial_3, 
                                    calificaciones.promedio, calificaciones.segunda_oportunidad, calificaciones.calif_final 
                            FROM alumnos 
                            INNER JOIN calificaciones ON alumnos.id_alumno = calificaciones.id_alumno 
                            WHERE alumnos.id_grupo = '$id_grupo'
                            AND calificaciones.id_materia IN (
                                SELECT id_materia FROM turnos WHERE id_grupo = '$id_grupo'
                            )";

    // Ejecutar la consulta
    $result_alumnos_calificaciones = $conn->query($sql_alumnos_calificaciones);

    // Mostrar los nombres de los alumnos y sus calificaciones en una tabla
    if ($result_alumnos_calificaciones->num_rows > 0) {
        echo "<h2>Alumnos en el Grupo:</h2>";
        echo "<table border='1'>";
        echo "<tr><th>ID Alumno</th><th>Apellido Paterno</th><th>Apellido Materno</th><th>Nombre</th>
                  <th>ID Materia</th><th>Parcial 1</th><th>Parcial 2</th><th>Parcial 3</th>
                  <th>Promedio</th><th>Segunda Oportunidad</th><th>Calificación Final</th><th>Acción</th></tr>";
        while ($row = $result_alumnos_calificaciones->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id_alumno'] . "</td>";
            echo "<td>" . $row['ap_paterno'] . "</td>";
            echo "<td>" . $row['ap_materno'] . "</td>";
            echo "<td>" . $row['nombre_alumno'] . "</td>";
            echo "<td>" . $row['id_materia'] . "</td>";
            echo "<td>" . $row['parcial_1'] . "</td>";
            echo "<td>" . $row['parcial_2'] . "</td>";
            echo "<td>" . $row['parcial_3'] . "</td>";
            echo "<td>" . $row['promedio'] . "</td>";
            echo "<td>" . $row['segunda_oportunidad'] . "</td>";
            echo "<td>" . $row['calif_final'] . "</td>";
            // Agregar un botón que abra la ventana modal con el nombre completo del alumno
            echo "<td><button onclick=\"openModal('{$row['nombre_alumno']} {$row['ap_paterno']} {$row['ap_materno']}', '{$row['id_alumno']}', '{$row['parcial_1']}', '{$row['parcial_2']}')\">Ver nombre</button></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No se encontraron alumnos en este grupo o no hay calificaciones asociadas a la materia del grupo.";
    }

    // Cerrar la conexión
    $conn->close();
} else {
    // Si no se ha enviado el id_grupo, redirigir a alguna página de error o manejar el caso según tu necesidad
    header("Location: pagina_de_error.php");
    exit(); // Asegurarse de terminar la ejecución del script después de redirigir
}
?>

<!-- Agregar la ventana modal -->
<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <p id="alumnoNombre"></p>
    <!-- Agregar el cuadro de texto para registrar calificaciones -->
    <form action="guardar_calificaciones.php" method="post">
        <label for="parcial1">Primer Parcial:</label>
        <input type="text" id="parcial1" name="parcial_1">
        <br>
        <?php
        // Verificar si ya existe un dato para el primer parcial
        if ($row['parcial_1'] != "") {
            // Si ya existe un dato, mostrar el cuadro de texto para el segundo parcial
            echo "<label for='parcial2'>Segundo Parcial:</label>";
            echo "<input type='text' id='parcial2' name='parcial_2'>";
        }
        ?>
        <br>
        <label for="parcial3">Tercer Parcial:</label>
        <input type="text" id="parcial3" name="parcial_3">
        <br>
        <input type="submit" value="Guardar Calificaciones">
    </form>
  </div>
</div>

<script>
// Función para abrir la ventana modal con el nombre completo del alumno
function openModal(nombreCompleto, idAlumno, parcial1, parcial2) {
  var modal = document.getElementById("myModal");
  var alumnoNombre = document.getElementById("alumnoNombre");
  alumnoNombre.innerHTML = nombreCompleto;
  // Agregar el ID del alumno al formulario
  var form = document.querySelector("form");
  var inputIdAlumno = document.createElement("input");
  inputIdAlumno.type = "hidden";
  inputIdAlumno.name = "id_alumno";
  inputIdAlumno.value = idAlumno;
  form.appendChild(inputIdAlumno);
  // Verificar si ya existe un dato para el primer parcial
  if (parcial1 != "") {
    // Si ya existe un dato, ocultar el cuadro de texto para el primer parcial
    document.getElementById("parcial1").style.display = "none";
  }
  // Verificar si ya existe un dato para el segundo parcial
  if (parcial2 != "") {
    // Si ya existe un dato, mostrar el cuadro de texto para el segundo parcial
    document.getElementById("parcial2").style.display = "block";
  }
  modal.style.display = "block";
}

// Función para cerrar la ventana modal
function closeModal() {
  var modal = document.getElementById("myModal");
  modal.style.display = "none";
}
</script>

</body>
</html>
