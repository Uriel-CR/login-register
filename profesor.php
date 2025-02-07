<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/estilos.css">
    <style>
        /* Ocultar el formulario de registro */
        .formulario__register {
            display: none;
        }

        /* Estilo adicional para el contenedor del login */
        .contenedor__login-register {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Ocupa toda la altura de la ventana */
            max-width: 400px;
            margin: 0 auto;
            text-align: center;
        }

        .boton-regresar {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            cursor: pointer;
            font-size: 16px;
        }
    </style>
</head>
<body>

<main>
    <!-- Formulario de Login -->
    <div class="contenedor__login-register">
        <form action="profesorphp/login_usuario_be.php" method="POST" class="formulario__login">
            <h2>Iniciar Sesión</h2>
            <input type="email" placeholder="Correo Electrónico" name="correo" required>
            <input type="password" placeholder="Contraseña" name="contrasena" required>
            <button>Ingresar</button>
            <button type="button" class="boton-regresar" onclick="window.location.href='inicio.php'">Regresar</button>
        </form>
    </div>
</main>

<script src="assets/js/script.js"></script>
</body>
</html>
