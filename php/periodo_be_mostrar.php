<?php 
include 'conexion_be.php';

if(isset($_POST['register'])){
    // Tu código actual para insertar un nuevo registro
    // ...
}

if(isset($_POST['consulta'])){
    $consulta = "SELECT * FROM periodos";
    $resultado = mysqli_query($conexion, $consulta);

    if($resultado && mysqli_num_rows($resultado) > 0){
        echo '<h2>Periodos</h2>';
        echo '<table>';
        echo '<thead><tr><th>No</th><th>Periodo</th></thead>';
        echo '<tbody>';
        while($fila = mysqli_fetch_assoc($resultado)){
            echo '<tr>';
            echo '<td>'.$fila['id_periodo'].'</td>';
            echo '<td>'.$fila['periodo'].'</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No hay datos de alumnos disponibles.</p>';
    }
}

mysqli_close($conexion);
?>