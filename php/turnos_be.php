<?php 

// Incluye el archivo de conexión a la base de datos
include 'conexion_be.php';

// Verifica si el formulario de registro fue enviado
if(isset($_POST['register'])){
    // Verifica que al menos uno de los campos del formulario no esté vacío
    if(
        !empty($_POST['id_profesor']) ||
        !empty($_POST['id_grupo']) ||
        !empty($_POST['id_materia']) ||
        !empty($_POST['lunes']) ||
        !empty($_POST['martes']) ||
        !empty($_POST['miercoles']) ||
        !empty($_POST['jueves']) ||
        !empty($_POST['viernes']) ||
        !empty($_POST['sabado']) ||
        !empty($_POST['horas'])
    ){
        // Obtiene y limpia los valores enviados por el formulario
        $id_profesor = trim($_POST['id_profesor']);
        $id_grupo = trim($_POST['id_grupo']);
        $id_materia = trim($_POST['id_materia']);
        $lunes = trim($_POST['lunes']);
        $martes = trim($_POST['martes']);
        $miercoles = trim($_POST['miercoles']);
        $jueves = trim($_POST['jueves']);
        $viernes = trim($_POST['viernes']);
        $sabado = trim($_POST['sabado']);
        $horas = trim($_POST['horas']);
        
        // Prepara la consulta SQL para insertar los datos en la tabla "horario"
        $consulta = "INSERT INTO turnos (id_profesor, id_grupo, id_materia, lunes, martes, miercoles, jueves, viernes, sabado,horas) 
                     VALUES ('$id_profesor', '$id_grupo','$id_materia', '$lunes', '$martes', '$miercoles', '$jueves', '$viernes', '$sabado', '$horas')";
        
        // Ejecuta la consulta SQL
        $resultado = mysqli_query($conexion, $consulta);

        // Verifica si la consulta se ejecutó correctamente
        if($resultado){
            ?>
            <!-- Muestra un mensaje de éxito si el registro se completó correctamente -->
            <h3 class="success">Tu registro se ha completado</h3>
            <?php
        } else {
            ?>
            <!-- Muestra un mensaje de error si hubo un problema al ejecutar la consulta -->
            <h3 class="error">Ocurrió un error al registrar los datos</h3>
            <?php
        }
    } else {
        ?>
        <!-- Muestra un mensaje de error si todos los campos del formulario están vacíos -->
        <h3 class="error">Al menos un campo debe ser llenado</h3>
        <?php
    }
}
?>
