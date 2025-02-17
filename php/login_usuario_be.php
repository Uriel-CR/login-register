<?php 
session_start();
include 'conexion_be.php';

$correo = $_POST['correo'];
$contrasena = $_POST['contrasena'];
$tipo = $_POST['tipo'];

echo($tipo);

// validar que los datos sean iguales 
$validar_login = mysqli_query($conexion, "SELECT * FROM users WHERE email= '$correo' AND password='$contrasena' AND user_type_id = $tipo");

if(mysqli_num_rows($validar_login)> 0 ){

    $_SESSION['usuario'] = $correo;

    header("location: bienvenida.php");
    exit;
}else{

    echo'
    <script>
    alert("Usuario o contraseña pueden ser incorrectos, por favor verifique los datos introducidos");
    window.location= "../administrador.php";
    </script>
    ';
exit;
}

?>
