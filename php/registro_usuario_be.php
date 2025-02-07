<?php 

include 'conexion_be.php';

$nombre_completo = $_POST['nombre_completo'];
$correo =$_POST['correo'];
$usuario =$_POST['usuario'];
$contrasena = $_POST['contrasena'];



$query= "INSERT INTO usuarios(nombre_completo,correo,usuario,contrasena) 
         VALUES('$nombre_completo','$correo','$usuario','$contrasena')";

//veificar que el correo no se repita en la base de datos
$verificar_correo = mysqli_query($conexion, "SELECT * FROM usuarios WHERE correo = '$correo'");

if(mysqli_num_rows($verificar_correo) > 0){
echo' 
<script>
alert("El correo ya ha sido registrado, intenta con otro diferente");
window.location= "../administrador.php";

</script>
';

exit();
} 
//veificar que el usuario no se repita en la base de datos
$verificar_usuario = mysqli_query($conexion, "SELECT * FROM usuarios WHERE usuario  = '$usuario'");

if(mysqli_num_rows($verificar_usuario) > 0){
echo' 
<script>
alert("El usuario ya esta registrado, intenta con otro diferente");
window.location= "../administrador.php";

</script>
';

exit();
} 

$ejecutar = mysqli_query($conexion, $query);


if($ejecutar){

    echo'
    <script>
    alert("usuario regisrtrado exitosamente");
    window.location= "../administrador.php";
    </script>
    ';
}else {
    echo'
    <script>
    alerta("Intentelo de nuevo, usuario no almacenado");
    window.location = "../administrador.php";

    </script>
';
}

mysqli_close($conexion);

?>