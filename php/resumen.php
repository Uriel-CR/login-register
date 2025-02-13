<?php require 'verificar_sesion.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIENVENIDA</title>
    <link rel="stylesheet" href="../assets/css/style.css" type="text/css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-image: url('../assets/images/foto.jpeg');
            background-size: cover; /* Ajusta la imagen para cubrir todo el fondo */
            background-position: center;
            background-repeat: no-repeat;
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
            position: sticky;
            /* Hace que el encabezado se quede fijo en la parte superior */
            top: 0;
            /* Asegura que el encabezado esté en la parte superior de la página */
            width: 100%;
            /* Asegura que el encabezado ocupe todo el ancho de la ventana */
            z-index: 1000;
            /* Asegura que el encabezado esté sobre otros elementos */
        }

        .logo {
            
            font-size: 24px;
            font-weight: bold;
            position: fixed;
            left: 0;
            margin: 10px;
        }

        .nav-menu-link:hover, .nav-menu-link.selected {
            background-color: #00509e; /* Azul más oscuro */
            color: white;
        }

        .button-container {
            margin: 100px auto 20px; /* Añadido margen superior para no solapar con el encabezado fijo */
            text-align: center;
            max-width: 600px;
        }

        .button-container h2 {
            color: #003366;
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .custom-button {
            padding: 12px 24px;
            font-size: 18px;
            border: none;
            background-color: #007bff; /* Azul */
            color: white;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s;
        }

        .custom-button:hover {
            background-color: #0056b3; /* Azul más oscuro al pasar el ratón */
            transform: scale(1.05); /* Agranda ligeramente el botón al pasar el ratón */
        }

        /* Estilo para las secciones de resumen */
        .button-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
    </style>
</head>
<body>
    <?php include 'header.html'; ?>
    <div class="button-container">
        <h2>Promedios de Alumnos Por Grupo</h2>
        <button onclick="window.location.href='tablas.php'" class="custom-button">INGRESAR</button>
    </div>

    <div class="button-container">
        <h2>Resumen General</h2>
        <button onclick="window.location.href='resumengeneral.php'" class="custom-button">INGRESAR</button>
    </div>
    
    <div class="button-container">
        <h2>Resumen Por Materia</h2>
        <button onclick="window.location.href='resumen_materia.php'" class="custom-button">INGRESAR</button>
    </div>
    <div class="button-container">
        <h2>Despues de Segunda Oportunidad</h2>
        <button onclick="window.location.href='despues_segunda.php'" class="custom-button">INGRESAR</button>
    </div>
    
</body>
</html>
