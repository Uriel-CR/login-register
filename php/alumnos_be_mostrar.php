<?php 

include 'conexion_be.php';

if(isset($_POST['register'])){
    if(
        strlen($_POST['matricula']) >= 1 &&
        strlen($_POST['ap_paterno']) >= 1 &&
        strlen($_POST['ap_materno']) >= 1 &&
        strlen($_POST['nombre']) >= 0
         ){
            $matricula = trim($_POST['matricula']);
            $ap_paterno = trim($_POST['ap_paterno']);
            $ap_materno = trim($_POST['ap_materno']);
            $nombre = trim($_POST['nombre']);
            
            $consulta="INSERT INTO alumnos(matricula, ap_paterno, ap_materno, nombre) 
            VALUES('$matricula','$ap_paterno','$ap_materno','$nombre')";
            $resultado = mysqli_query($conexion, $consulta);

            if($resultado){
                ?>
                <h3 class="sucess">Tu registro se ha completado</h3>
                <?php
            }else {
             ?>
             <h3 class="error">Ocurrio un error</h3>
             <?php
            }
    }else {
        ?>
        <h3 class="error">Llena todos los campos</h3>
        <?php
        
    }


}



?>