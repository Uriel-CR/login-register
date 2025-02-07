<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALUMNOS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="../assets/css/minimal-table.css" rel="stylesheet" type="text/css">
    <style>
        body {
            background-image: url('../assets/images/foto.jpeg');
            background-size: 100% auto; /* Ajusta la escala horizontal al 100% y la escala vertical de forma automática */
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
            margin: 10px;
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

        h1 {
            text-align: center;
            margin-top: 20px;
        }

        table {
            width: 80%; /* Ajusta el ancho de la tabla al 80% del contenedor */
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px; /* Reduce el padding para hacer la tabla más compacta */
            border: 1px solid #ddd;
        }

        .boton {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 8px 16px; /* Ajusta el padding para los botones */
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px; /* Reduce el tamaño de fuente de los botones */
        }

        .boton:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav">
            <a class="logo nav-link">TESI</a>
            <ul class="nav-menu">
            <li class="nav-menu-item"><a class="nav-menu-link" href="../php/bienvenida.php">Inicio</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/alumnos.php">Alumnos</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/materias.php">Materias</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/grupos.php">Grupos</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/profesores.php">Profesores</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link selected">Periodo</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/asignacion_grupo.php">Calificaciones</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/resumen.php">Resumen</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../index.php">Cerrar Sesión</a></li>
            </ul>
            <button class="nav-toggle">
                <img src="../assets/images/menu.svg" class="nav-toggle-icon" alt="Menu">
            </button>
        </nav>
    </header>

    <h1>Registro de Periodos</h1>

    <form method="POST">
        <table>
            <tbody>
                <tr>
                    <th rowspan="1">Periodo</th>
                    <td><input type="text" name="periodo" oninput="convertirMayusculas(this)"></td>
                </tr>
                <tr>
                    <td colspan="1"><input class="boton" type="submit" name="consulta" value="Datos"></td>
                    <td colspan="2"><input class="boton" type="submit" name="register" value="Enviar"></td></td>
                </tr>
                <tr>
                    <td colspan="3">
                        <!-- Aquí se mostrarán los datos de la consulta -->
                        <?php include 'periodo_be_mostrar.php'; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>

    <script>
        // Incluye las funciones JavaScript
        <?php include '../assets/js/script1.js'; ?>
        
        document.querySelector('.nav-toggle').addEventListener('click', function() {
            document.querySelector('.nav-menu').classList.toggle('show');
        });
    </script>
    <?php include 'periodo_be.php'; ?>
</body>
</html>
