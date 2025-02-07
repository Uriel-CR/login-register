<?php 

include 'conexion_be.php';

if(isset($_POST['register'])){
    if(
        strlen($_POST['Clave']) >= 1 &&
        strlen($_POST['Nombre_profesor']) >= 1 &&
        strlen($_POST['ap_paterno']) >= 1 &&
        strlen($_POST['ap_materno']) >= 1
         ){
            $Clave = trim($_POST['Clave']);
            $Nombre_profesor = trim($_POST['Nombre_profesor']);
            $ap_paterno = trim($_POST['ap_paterno']);
            $ap_materno = trim($_POST['ap_materno']);
            
            $consulta="INSERT INTO profesores(Clave,Nombre_profesor, ap_paterno, ap_materno) 
            VALUES('$Clave','$Nombre_profesor','$ap_paterno','$ap_materno')";
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