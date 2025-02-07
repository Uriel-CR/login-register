<?php 
include 'conexion_be.php';

if(isset($_POST['register'])){
    // Tu código actual para insertar un nuevo registro
    // ...
}

if(isset($_POST['consulta'])){
    $consulta = "SELECT * FROM profesores";
    $resultado = mysqli_query($conexion, $consulta);

    if($resultado && mysqli_num_rows($resultado) > 0){
        echo '<h2>Profesores</h2>';
        echo '<table>';
        echo '<thead><tr><th>Clave</th><th colspan="3"><b>Profesor</b></th></tr></thead>';
        echo '<tbody>';
        while($fila = mysqli_fetch_assoc($resultado)){
            echo '<tr>';
            echo '<td>'.$fila['Clave'].'</td>';
            echo '<td>'.$fila['Nombre_profesor'].'</td>';
            echo '<td>'.$fila['ap_paterno'].'</td>';
            echo '<td>'.$fila['ap_materno'].'</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No hay datos de profesores disponibles.</p>';
    }
}

mysqli_close($conexion);
?>