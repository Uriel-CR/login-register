<?php
require 'verificar_sesion.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIENVENIDA</title>
    <link rel="stylesheet" href="../assets/css/style.css" type="text/css">
    <style>
        body {
            background-image: url('../assets/images/bj9.jpg');
            background-size: 107% auto;
            /* Ajusta la escala horizontal al 100% y la escala vertical de forma automática */
            background-position: center top;
            /* Centra la imagen horizontalmente y la alinea en la parte superior */
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body>

    <?php include 'header.html'; ?>

    <script>
        // Script para alternar el menú en dispositivos móviles
        document.querySelector('.nav-toggle').addEventListener('click', function() {
            document.querySelector('.nav-menu').classList.toggle('show');
        });
    </script>
</body>

</html>