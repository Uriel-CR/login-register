<?php 
// Incluye el archivo de conexión a la base de datos
include 'conexion_be.php';

// Verifica si se ha enviado el formulario de registro
if(isset($_POST['register'])){
    // Verifica que todos los campos del formulario estén llenos
    if(
        strlen($_POST['clave_materia']) >= 1 &&
        strlen($_POST['nombre']) >= 1 &&
        strlen($_POST['HRS_TEORICAS']) >= 1 &&
        strlen($_POST['HRS_PRACTICAS']) >= 1 &&
        strlen($_POST['creditos']) >= 1 
    ){
        // Obtiene y limpia los valores enviados por el formulario
        $clave_materia = trim($_POST['clave_materia']);
        $nombre = trim($_POST['nombre']);
        $HRS_TEORICAS = trim($_POST['HRS_TEORICAS']);
        $HRS_PRACTICAS = trim($_POST['HRS_PRACTICAS']);
        $creditos = trim($_POST['creditos']);
        
        // Prepara la consulta SQL para insertar los datos en la tabla 'materias'
        $consulta="INSERT INTO materias(clave_materia, nombre, HRS_TEORICAS, HRS_PRACTICAS, creditos) 
        VALUES('$clave_materia','$nombre','$HRS_TEORICAS','$HRS_PRACTICAS','$creditos')";
        
        // Ejecuta la consulta en la base de datos
        $resultado = mysqli_query($conexion, $consulta);

        // Verifica si la consulta se ejecutó con éxito
        if($resultado){
            ?>
            <h3 class="sucess">Tu registro se ha completado</h3> <!-- Muestra un mensaje de éxito -->
            <?php
        }else {
            ?>
            <h3 class="error">Ocurrió un error</h3> <!-- Muestra un mensaje de error si la consulta falla -->
            <?php
        }
    }else {
        ?>
        <h3 class="error">Llena todos los campos</h3> <!-- Muestra un mensaje de error si no se llenan todos los campos del formulario -->
        <?php
    }
}
?>
