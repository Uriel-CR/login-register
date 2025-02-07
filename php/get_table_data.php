<?php
// Configuración de la conexión a la base de datos
$servername = "localhost"; 
$username = "serviciosocial"; 
$password = "FtW30yNo8hQd-x/G"; 
$database = "login_register_db"; 

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$periodo = $_POST['periodo'];
$grupo = $_POST['grupo'];

// Consulta SQL para obtener los promedios generales almacenados
$sql_almacenado = "SELECT id_alumno, Promedio_parcial1, Promedio_parcial2, Promedio_parcial3
                   FROM promedios_generales
                   WHERE id_periodo = ? AND id_grupo = ?";
$stmt_almacenado = $conn->prepare($sql_almacenado);
$stmt_almacenado->bind_param("ii", $periodo, $grupo);
$stmt_almacenado->execute();
$result_almacenado = $stmt_almacenado->get_result();

// Almacena los promedios en un array para fácil acceso
$promedios_almacenados = [];
while ($row = $result_almacenado->fetch_assoc()) {
    $promedios_almacenados[$row['id_alumno']] = $row;
}

// Consulta SQL para obtener los promedios actuales calculados
$sql_actual = "SELECT 
    g.id_grupo, 
    g.nombre_grupo, 
    a.id_alumno, 
    a.matricula, 
    a.nombre AS nombre_alumno, 
    a.ap_paterno AS apellido_paterno, 
    a.ap_materno AS apellido_materno,
    CASE 
        WHEN SUM(CASE WHEN c.parcial_1 IN ('N/A', 'N/P') THEN 1 ELSE 0 END) > 0 THEN 'N/A'
        ELSE AVG(CASE WHEN c.parcial_1 > 0 THEN c.parcial_1 ELSE NULL END)
    END AS promedio_parcial_1,
    CASE 
        WHEN SUM(CASE WHEN c.parcial_2 IN ('N/A', 'N/P') THEN 1 ELSE 0 END) > 0 THEN 'N/A'
        ELSE AVG(CASE WHEN c.parcial_2 > 0 THEN c.parcial_2 ELSE NULL END)
    END AS promedio_parcial_2,
    CASE 
        WHEN SUM(CASE WHEN c.parcial_3 IN ('N/A', 'N/P') THEN 1 ELSE 0 END) > 0 THEN 'N/A'
        ELSE AVG(CASE WHEN c.parcial_3 > 0 THEN c.parcial_3 ELSE NULL END)
    END AS promedio_parcial_3
FROM alumnos a
INNER JOIN calificaciones c ON a.id_alumno = c.id_alumno
INNER JOIN grupos g ON c.id_grupo = g.id_grupo
WHERE c.id_periodo = ? AND g.id_grupo = ?
GROUP BY g.id_grupo, a.id_alumno, a.matricula, a.nombre, a.ap_paterno, a.ap_materno
ORDER BY g.nombre_grupo, a.ap_paterno, a.ap_materno";

$stmt_actual = $conn->prepare($sql_actual);
$stmt_actual->bind_param("ii", $periodo, $grupo);
$stmt_actual->execute();
$result_actual = $stmt_actual->get_result();

echo "<div class='table-responsive'>";
echo "<table class='table table-bordered table-hover'>";
echo "<thead>";
echo "<tr>";
echo "<th>Matrícula</th>";
echo "<th>Apellido Paterno</th>";
echo "<th>Apellido Materno</th>";
echo "<th>Nombre</th>";
echo "<th>Promedio Parcial 1 (Actual)</th>";
echo "<th>Promedio Parcial 2 (Actual)</th>";
echo "<th>Promedio Parcial 3 (Actual)</th>";
echo "<th>Promedio Parcial 1 (Almacenado)</th>";
echo "<th>Promedio Parcial 2 (Almacenado)</th>";
echo "<th>Promedio Parcial 3 (Almacenado)</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";

if ($result_actual->num_rows > 0) {
    while ($row = $result_actual->fetch_assoc()) {
        $id_alumno = $row["id_alumno"];
        $promedio_parcial_1 = $row["promedio_parcial_1"];
        $promedio_parcial_2 = $row["promedio_parcial_2"];
        $promedio_parcial_3 = $row["promedio_parcial_3"];

        // Reemplazar promedios 0 por 'N/A' antes de almacenarlos
        $promedio_parcial_1 = ($promedio_parcial_1 == 0) ? 'N/A' : $promedio_parcial_1;
        $promedio_parcial_2 = ($promedio_parcial_2 == 0) ? 'N/A' : $promedio_parcial_2;
        $promedio_parcial_3 = ($promedio_parcial_3 == 0) ? 'N/A' : $promedio_parcial_3;

        // Verifica si existen promedios almacenados para este alumno
        if (isset($promedios_almacenados[$id_alumno])) {
            $almacenado_parcial_1 = $promedios_almacenados[$id_alumno]['Promedio_parcial1'];
            $almacenado_parcial_2 = $promedios_almacenados[$id_alumno]['Promedio_parcial2'];
            $almacenado_parcial_3 = $promedios_almacenados[$id_alumno]['Promedio_parcial3'];

            // Si ya existen los promedios, actualizamos los datos
            $sql_update = "UPDATE promedios_generales 
                           SET Promedio_parcial1 = ?, Promedio_parcial2 = ?, Promedio_parcial3 = ?
                           WHERE id_alumno = ? AND id_periodo = ? AND id_grupo = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("sssiis", $promedio_parcial_1, $promedio_parcial_2, $promedio_parcial_3, $id_alumno, $periodo, $grupo);
            $stmt_update->execute();
            $stmt_update->close();
        } else {
            // Si no existen los promedios, insertamos nuevos datos
            $sql_insert = "INSERT INTO promedios_generales (id_alumno, id_periodo, id_grupo, Promedio_parcial1, Promedio_parcial2, Promedio_parcial3)
                           VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("iiiddd", $id_alumno, $periodo, $grupo, $promedio_parcial_1, $promedio_parcial_2, $promedio_parcial_3);
            $stmt_insert->execute();
            $stmt_insert->close();
        }

        // Mostrar los datos en la tabla
        echo "<tr>";
        echo "<td>{$row['matricula']}</td>";
        echo "<td>{$row['apellido_paterno']}</td>";
        echo "<td>{$row['apellido_materno']}</td>";
        echo "<td>{$row['nombre_alumno']}</td>";

        // Muestra los promedios calculados
        echo "<td>{$promedio_parcial_1}</td>";
        echo "<td>{$promedio_parcial_2}</td>";
        echo "<td>{$promedio_parcial_3}</td>";

        // Muestra los promedios almacenados
        echo "<td>{$almacenado_parcial_1}</td>";
        echo "<td>{$almacenado_parcial_2}</td>";
        echo "<td>{$almacenado_parcial_3}</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
} else {
    echo "<p style='text-align: center;'>No se encontraron resultados.</p>";
}

$stmt_almacenado->close();
$stmt_actual->close();
$conn->close();
?>
