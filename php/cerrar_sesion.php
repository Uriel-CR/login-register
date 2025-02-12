<?php
session_start();

// Eliminar todas las variables de sesión
session_unset();

// Destruir la sesión
session_destroy();

// Redirigir a la página de login o a una página de despedida
header("Location: ../index.php");
exit();
?>