<?php 

include 'conexion_be.php';

if(isset($_POST['register']))
    if(
        strlen($_POST['periodo']) >= 0
         ){
            $periodo = trim($_POST['periodo']);
            $consulta="INSERT INTO periodos(periodo) 
            VALUES('$periodo')";
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