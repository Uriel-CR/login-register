<?php 

include '../php/conexion_be.php';

$matricula = $_POST['matricula'];
$nombre_completo = $_POST['nombre_completo'];
$correo = $_POST['correo'];
$usuario = $_POST['usuario'];
$contrasena = $_POST['contrasena'];

$query= "INSERT INTO alumnos_usu(matricula, nombre_completo, correo, usuario, contrasena) 
         VALUES('$matricula', '$nombre_completo', '$correo', '$usuario', '$contrasena')";

// Verificar que el correo no se repita en la base de datos
$verificar_correo = mysqli_query($conexion, "SELECT * FROM alumnos_usu WHERE correo = '$correo'");

if(mysqli_num_rows($verificar_correo) > 0){
    echo ' 
    <script>
    alert("El correo ya ha sido registrado, intenta con otro diferente");
    window.location= "../alumno.php";
    </script>
    ';

    exit();
} 

// Verificar que el usuario no se repita en la base de datos
$verificar_usuario = mysqli_query($conexion, "SELECT * FROM alumnos_usu WHERE usuario = '$usuario'");

if(mysqli_num_rows($verificar_usuario) > 0){
    echo ' 
    <script>
    alert("El usuario ya está registrado, intenta con otro diferente");
    window.location= "../alumno.php";
    </script>
    ';

    exit();
} 

$ejecutar = mysqli_query($conexion, $query);

if($ejecutar){
    echo '
    <script>
    alert("Usuario registrado exitosamente");
    window.location= "../alumno.php";
    </script>
    ';
} else {
    echo '
    <script>
    alert("Inténtelo de nuevo, usuario no almacenado");
    window.location = "../alumno.php";
    </script>
    ';
}

mysqli_close($conexion);

?>
