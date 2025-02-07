<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "serviciosocial";
$password = "FtW30yNo8hQd-x/G";
$database = "login_register_db";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el ID del grupo y el período desde la solicitud AJAX
$id_grupo = isset($_POST['id_grupo']) ? intval($_POST['id_grupo']) : 0;
$id_periodo = isset($_POST['id_periodo']) ? intval($_POST['id_periodo']) : 0;

if ($id_grupo > 0 && $id_periodo > 0) {
    // Consulta para extraer los datos de los alumnos, el nombre del grupo y el comentario
    $sql = "
        SELECT DISTINCT a.id_alumno, a.matricula, a.ap_paterno, a.ap_materno, a.nombre, g.nombre_grupo, c.comentario
        FROM calificaciones c
        JOIN alumnos a ON c.id_alumno = a.id_alumno
        JOIN grupos g ON c.id_grupo = g.id_grupo
        WHERE c.id_grupo = ? AND c.id_periodo = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_grupo, $id_periodo);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si se encontraron resultados
    if ($result->num_rows > 0) {
        echo "<style>
                .acciones {
                    text-align: center;
                }
                .acciones input[type='text'] {
                    width: 150px;
                    margin-bottom: 5px;
                }
                .acciones button {
                    margin: 0 5px;
                }
              </style>";

        echo "<form method='POST' action='guardar_comentarios.php'>";
        echo "<input type='hidden' name='id_grupo' value='" . htmlspecialchars($id_grupo) . "'>";
        echo "<input type='hidden' name='id_periodo' value='" . htmlspecialchars($id_periodo) . "'>";
        echo "<table border='1'>";
        echo "<tr><th>Seleccionar</th><th>Matrícula</th><th>Apellido Paterno</th><th>Apellido Materno</th><th>Nombre</th><th>Nombre del Grupo</th><th>Acciones</th></tr>";

        // Mostrar los datos de cada fila
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td><input type='checkbox' name='alumno_id[]' value='" . htmlspecialchars($row["id_alumno"]) . "'></td>
                    <td>" . htmlspecialchars($row["matricula"]). "</td>
                    <td>" . htmlspecialchars($row["ap_paterno"]). "</td>
                    <td>" . htmlspecialchars($row["ap_materno"]). "</td>
                    <td>" . htmlspecialchars($row["nombre"]). "</td>
                    <td>" . htmlspecialchars($row["nombre_grupo"]). "</td>
                    <td class='acciones'>
                        <input type='text' name='comentario_" . $row["id_alumno"] . "' value='" . htmlspecialchars($row["comentario"]) . "' placeholder='Agregar comentario'>
                        <br>
                        <button type='button' class='btn-editar' onclick='editarAlumno(" . $row["id_alumno"] . ")'>Editar</button>
                        <button type='button' class='btn-eliminar' onclick='eliminarAlumno(" . $row["id_alumno"] . ", " . $id_grupo . ", " . $id_periodo . ")'>Eliminar</button>
                    </td>
                  </tr>";
        }
        echo "</table>";
        echo "<button type='submit'>Guardar Comentarios</button>";
        echo "</form>";
    } else {
        echo "No se encontraron alumnos para el grupo y periodo especificados.";
    }
} else {
    echo "Grupo o periodo no válidos.";
}

// Cerrar la conexión
$conn->close();
?>
