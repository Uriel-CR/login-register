<?php 

include 'conexion_be.php';

$correo = $_POST['correo'];
$contrasena = $_POST['contrasena'];



// validar que los datos sean iguales 
$validar_login = mysqli_query($conexion, "SELECT * FROM usuarios WHERE correo= '$correo' 
and contrasena='$contrasena'" );

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
