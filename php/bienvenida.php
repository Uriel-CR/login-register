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
            background-size: 107% auto; /* Ajusta la escala horizontal al 100% y la escala vertical de forma automática */
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
            position: fixed;
            left: 0;
            top: 0; /* Opcional: para alinear el logo en la parte superior */
            margin: 10px; /* Opcional: para agregar algo de espacio alrededor del logo */
        }

        .nav {
            display: flex;
            align-items: center;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            margin-left: 104px;
        }

        .nav-menu-item {
            margin-right: 15px;
        }

        .nav-menu-link {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            padding: 10px 15px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .nav-menu-link:hover, .nav-menu-link.selected {
            background-color: #0056b3;
        }

        .nav-toggle {
            display: none; /* Mostrar solo en dispositivos móviles */
            background: none;
            border: none;
            cursor: pointer;
        }

        .nav-toggle-icon {
            width: 24px;
            height: 24px;
        }

        /* Adicional para el diseño responsive, si es necesario */
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
                flex-direction: column;
                width: 100%;
            }

            .nav-menu-item {
                margin: 0;
            }

            .nav-toggle {
                display: block;
            }

            .nav-menu.show {
                display: flex;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav">
            <a class="logo nav-link">TESI</a>
            <ul class="nav-menu">
                <li class="nav-menu-item"><a class="btn nav-menu-link nav-link" href="../php/alumnos.php">Alumnos</a></li>
                <li class="nav-menu-item"><a class="btn nav-menu-link nav-link" href="../php/materias.php">Materias</a></li>
                <li class="nav-menu-item"><a class="btn nav-menu-link nav-link" href="../php/grupos.php">Grupos</a></li>
                <li class="nav-menu-item"><a class="btn nav-menu-link nav-link" href="../php/profesores.php">Profesores</a></li>
                <li class="nav-menu-item"><a class="btn nav-menu-link nav-link" href="../php/periodo.php">Periodo</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/asignacion_grupo.php">Calificaciones</a></li>
                <li class="nav-menu-item"><a class="btn nav-menu-link nav-link" href="../php/resumen.php">Resumen</a></li>
                <li class="nav-menu-item"><a class="btn nav-menu-link nav-link" href="../index.php">Cerrar Sesión</a></li>
            </ul>
            <button class="nav-toggle">
                <img src="../assets/images/menu.svg" class="nav-toggle-icon" alt="Menu">
            </button>
        </nav>
    </header>

    <script>
        // Script para alternar el menú en dispositivos móviles
        document.querySelector('.nav-toggle').addEventListener('click', function() {
            document.querySelector('.nav-menu').classList.toggle('show');
        });
    </script>
</body>
</html>
