<?php 
include 'conexion_be.php';

if(isset($_POST['register'])){
    // Tu código actual para insertar un nuevo registro
    // ...
}

// Obtener los grupos para el botón desplegable
$grupos = [];
$consulta_grupos = "SELECT * FROM grupos";
$resultado_grupos = mysqli_query($conexion, $consulta_grupos);

if($resultado_grupos && mysqli_num_rows($resultado_grupos) > 0){
    while($grupo = mysqli_fetch_assoc($resultado_grupos)){
        $grupos[] = $grupo;
    }
}

if(isset($_POST['consulta'])){
    $consulta = "SELECT * FROM alumnos";
    $resultado = mysqli_query($conexion, $consulta);

    if($resultado && mysqli_num_rows($resultado) > 0){
        echo '<h2>Datos de Alumnos</h2>';
        echo '<table>';
        echo '<thead><tr><th>Matrícula</th><th>Apellido Paterno</th><th>Apellido Materno</th><th>Nombre</th><th>Grupo</th></tr></thead>';
        echo '<tbody>';
        while($fila = mysqli_fetch_assoc($resultado)){
            echo '<tr>';
            echo '<td>'.$fila['matricula'].'</td>';
            echo '<td>'.$fila['ap_paterno'].'</td>';
            echo '<td>'.$fila['ap_materno'].'</td>';
            echo '<td>'.$fila['nombre'].'</td>';
            
            
            // Mostrar el grupo correspondiente (esto puede necesitar una consulta adicional o cambios en la estructura de la base de datos)
            
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

