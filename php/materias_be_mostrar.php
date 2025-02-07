<?php 
// Incluye el archivo de conexión a la base de datos
include 'conexion_be.php';

// Verifica si se ha enviado el formulario para registrar una nueva materia
if(isset($_POST['register'])){
    // Tu código actual para insertar un nuevo registro
    // ...
}

// Verifica si se ha enviado el formulario para consultar datos de materias
if(isset($_POST['consulta'])){
    // Consulta SQL para seleccionar todos los registros de la tabla 'materias'
    $consulta = "SELECT * FROM materias";
    // Ejecuta la consulta en la base de datos
    $resultado = mysqli_query($conexion, $consulta);

    // Verifica si se obtuvieron resultados de la consulta y si hay al menos un registro
    if($resultado && mysqli_num_rows($resultado) > 0){
        // Si hay resultados, muestra una tabla con los datos de las materias
        echo '<h2>Datos de Alumnos</h2>'; // Encabezado
        echo '<table>'; // Inicio de la tabla
        echo '<thead><tr><th>N°</th><th>Clave de la Materia</th><th>Materia</th><th>Hrs Teoricas</th><th>Horas Practicas</th><th>Creditos</th></tr></thead>'; // Encabezados de las columnas
        echo '<tbody>'; // Cuerpo de la tabla
        // Recorre los resultados obtenidos y muestra cada fila en la tabla
        while($fila = mysqli_fetch_assoc($resultado)){
            echo '<tr>'; // Inicio de una fila
            // Muestra cada campo de la fila en una celda de la tabla
            echo '<td>'.$fila['id_materia'].'</td>'; // ID de la materia
            echo '<td>'.$fila['clave_materia'].'</td>'; // Clave de la materia
            echo '<td>'.$fila['nombre'].'</td>'; // Nombre de la materia
            echo '<td>'.$fila['HRS_TEORICAS'].'</td>'; // Horas teóricas
            echo '<td>'.$fila['HRS_PRACTICAS'].'</td>'; // Horas prácticas
            echo '<td>'.$fila['creditos'].'</td>'; // Créditos
            echo '</tr>'; // Fin de la fila
        }
        echo '</tbody>'; // Fin del cuerpo de la tabla
        echo '</table>'; // Fin de la tabla
    } else {
        // Si no hay resultados de la consulta, muestra un mensaje indicando que no hay datos disponibles
        echo '<p>No hay datos de materias disponibles.</p>';
    }
}

// Cierra la conexión a la base de datos
mysqli_close($conexion);
?>
