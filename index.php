<?php
// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar qué botón se ha presionado y redirigir según corresponda
    if (isset($_POST["btn_admin"])) {
        header("Location: administrador.php"); // Redirigir a la página de administrador
        exit();
    } elseif (isset($_POST["btn_alumno"])) {
        header("Location: alumno.php"); // Redirigir a la página de alumno
        exit();
    } elseif (isset($_POST["btn_profesor"])) {
        header("Location: profesor.php"); // Redirigir a la página de profesor
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIENVENIDA</title>
    <link rel="stylesheet" href="../assets/css/style.css" type="text/css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <style>
        body {
            background-image: url('../login-register/assets/images/bj9.jpg');
            background-size: 110% auto; /* Ajusta la escala horizontal al 100% y la escala vertical de forma automática */
            background-position: center top; /* Centra la imagen horizontalmente y la alinea en la parte superior */
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #007BFF;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            margin-right: 20px; /* Ajusta este valor para mover el texto a la derecha */
        }

        .logo span {
            display: block;
            font-size: 16px;
            font-weight: normal;
        }

        .nav {
            display: flex;
            align-items: center;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            justify-content: space-between;
        }

        .ccai-image {
            width: 100px; /* Ajusta el tamaño de la imagen según lo necesites */
            height: auto; /* Mantiene la proporción de la imagen */
            float: right; /* Mueve la imagen hacia la izquierda */
            margin-right: 20px; /* Usa un valor negativo para mover la imagen más a la izquierda */
        }

        .tesi-image {
            width: 100px; /* Ajusta el tamaño de la imagen según lo necesites */
            height: auto; /* Mantiene la proporción de la imagen */
            float: right; /* Mueve la imagen hacia la derecha */
            margin-right: 20px; /* Añade espacio a la izquierda de la imagen */
        }

        .button-container {
            display: flex;
            align-items: center;
            margin-left: auto;
        }

        .button-container form {
            margin-left: 10px;
        }

        .button-container button {
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007BFF;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .button-container button:hover {
            background-color: #0056b3;
        }

        .credits-button {
            margin-left: 100px; /* Aumenta la distancia del botón de créditos */
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            text-align: center; /* Centra el texto dentro del modal */
        }

        .modal-content p {
            font-size: 18px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            font-family: 'Roboto', sans-serif; /* Fuente más estilizada */
            font-style: italic; /* Texto en cursiva */
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="nav">
        <img src="../login-register/assets/images/tesi.jpg" alt="CCAI" class="ccai-image">
            <a class="logo nav-link">
                TESI
                <span>SADRISC-2024</span>
            </a>
          
            <div class="button-container">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <button type="submit" name="btn_admin">Login Admin</button>
                </form>
                <!-- Formulario para el login de alumno -->
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <button type="submit" name="btn_alumno">Login Alumno</button>
                </form>
                <!-- Formulario para el login de profesor -->
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <button type="submit" name="btn_profesor">Login Profesor</button>
                </form>

                <button id="btn_credits" class="credits-button">Créditos</button>
            </div>
            <img src="../login-register/assets/images/tesi.org.mx.jpg" alt="TESI" class="tesi-image">
        </div>
    </header>

    <div id="creditsModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p>
                El proyecto se realizó por:<br><br>
                Dionicio Ortiz José Cruz<br>
                Gómez Reyes Brenda Anahí<br>
                Flores Mora José Armando<br>
                Sánchez Villegas José Ignacio<br><br>
                Alumnos de 6to semestre de Servicio Social.<br><br>
                Coordinado por el Ing. Ebner Juarez Elias<br><br>
                Agosto-2024<br>
                Version 1.0@
            </p>
        </div>
    </div>

    <script>
        var modal = document.getElementById("creditsModal");
        var btn = document.getElementById("btn_credits");
        var span = document.getElementsByClassName("close")[0];

        btn.onclick = function() {
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
