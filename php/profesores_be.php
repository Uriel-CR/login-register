<?php 

// Incluye el archivo de conexión a la base de datos
include 'conexion_be.php';

// Verifica si el formulario de registro fue enviado
if(isset($_POST['register'])){
    // Verifica que todos los campos del formulario hayan sido llenados
    if(
        strlen($_POST['Clave']) >= 1 &&
        strlen($_POST['Nombre_profesor']) >= 1 &&
        strlen($_POST['ap_paterno']) >= 1 &&
        strlen($_POST['ap_materno']) >= 1
         ){
            // Obtiene y limpia los valores enviados por el formulario
            $Clave = trim($_POST['Clave']);
            $Nombre_profesor = trim($_POST['Nombre_profesor']);
            $ap_paterno = trim($_POST['ap_paterno']);
            $ap_materno = trim($_POST['ap_materno']);
            
            // Prepara la consulta SQL para insertar los datos en la tabla "profesores"
            $consulta="INSERT INTO profesores(Clave,Nombre_profesor, ap_paterno, ap_materno) 
            VALUES('$Clave','$Nombre_profesor','$ap_paterno','$ap_materno')";
            
            // Ejecuta la consulta SQL
            $resultado = mysqli_query($conexion, $consulta);

            // Verifica si la consulta se ejecutó correctamente
            if($resultado){
                ?>
                <!-- Muestra un mensaje de éxito si el registro se completó correctamente -->
                <h3 class="sucess">Tu registro se ha completado</h3>
                <?php
            }else {
             ?>
             <!-- Muestra un mensaje de error si hubo un problema al ejecutar la consulta -->
             <h3 class="error">Ocurrió un error</h3>
             <?php
            }
    }else {
        ?>
        <!-- Muestra un mensaje de error si no todos los campos del formulario fueron llenados -->
        <h3 class="error">Llena todos los campos</h3>
        <?php
        
    }
}
?>
