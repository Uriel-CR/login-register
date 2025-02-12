<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login y Register</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>

<main>

    <div class="contenedor__todo">
        <div class="caja__trasera">
            <div class="caja__trasera-login">
                <h3>¿Ya tienes una cuenta?</h3>
                <p>Inicia sesión para entrar en la página</p>
                <button id="btn__iniciar-sesion">Iniciar Sesión</button>
            </div>
            <div class="caja__trasera-register">
                <h3>¿Aún no tienes una cuenta?</h3>
                <p>Regístrate para que puedas iniciar sesión</p>
                <button id="btn__registrarse">Regístrarse</button>
            </div>
        </div>

        <!--Formulario de Login y registro-->
        <div class="contenedor__login-register">
            <!--Login-->
            <form action="alumnophp/login_usuario_be.php" method="POST" class="formulario__login">
                <h2>Iniciar Sesión</h2>
                <input type="Matricula" placeholder="matricula" name="matricula" required>
                <input type="password" placeholder="Contraseña" name="contrasena" required>
                <button>Ingresar</button>
            </form>

            <!--Register-->
            <form action="alumnophp/registro_usuario_be.php" method="POST" class="formulario__register">
                <h2>Regístrarse</h2>
                <input type="text" placeholder="Matricula" name="matricula" pattern="[0-9]{9}" title="Verifica tu matricula" required>
                <input type="text" placeholder="Nombre completo" name="nombre_completo" required>
                <input type="email" placeholder="Correo Institucional" name="correo" required>
                <input type="text" placeholder="Usuario" name="usuario" required>
                <input type="password" placeholder="Contraseña" name="contrasena" pattern=".{8,}" title="La contraseña debe tener al menos 8 caracteres" required>
                <button>Regístrarse</button>
            </form>
        </div>
    </div>

    <!-- Botón para ir a inicio.php -->
    <div style="text-align: center; margin-top: 20px;">
    <a href="index.php" class="boton_inicio" style="display: inline-block; padding: 15px 25px; background-color: red; color: white; text-decoration: none; border-radius: 5px; border: none; cursor: pointer; font-size: 16px; transition: background-color 0.3s;">Regresar</a>
</div>


</main>

<script src="assets/js/script.js"></script>
</body>
</html>
