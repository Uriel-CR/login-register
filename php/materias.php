<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materia</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="../assets/css/minimal-table.css" rel="stylesheet" type="text/css">
    <style>
        body {
            background-image: url('../assets/images/foto.jpeg');
            background-size: 100% auto;
            background-position: center top;
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
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            max-width: 800px; /* Reducir el ancho de la tabla */
        }

        th, td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .boton {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .boton:hover {
            background-color: #0056b3;
        }

        .data-table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            display: none; /* Ocultar la tabla por defecto */
            max-width: 800px; /* Reducir el ancho de la tabla */
        }

        .data-table th, .data-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .data-table th {
            background-color: #f4f4f4;
        }

        .search-container {
            text-align: center;
            margin: 20px 0;
        }

        .search-container input {
            padding: 8px;
            font-size: 16px;
            width: 300px; /* Ajustar el ancho de la barra de búsqueda */
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
                <li class="nav-menu-item"><a class="nav-menu-link selected">Materias</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/grupos.php">Grupos</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/profesores.php">Profesores</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/periodo.php">Periodo</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/asignacion_grupo.php">Calificaciones</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../php/resumen.php">Resumen</a></li>
                <li class="nav-menu-item"><a class="nav-menu-link" href="../index.php">Cerrar Sesión</a></li>
            </ul>
            <button class="nav-toggle">
                <img src="../assets/images/menu.svg" class="nav-toggle-icon" alt="Menu">
            </button>
        </nav>
    </header>

    <h1>Registro de Materias</h1>

    <form id="materiaForm">
        <table>
            <tbody>
                <tr>
                    <th>Clave de la Materia</th>
                    <td><input type="text" name="clave_materia" oninput="convertirMayusculas(this)"></td>
                </tr>
                <tr>
                    <th>Nombre de la Materia</th>
                    <td><input type="text" name="nombre" oninput="convertirMayusculas(this)"></td>
                </tr>
                <tr>
                    <th>Hrs Teóricas</th>
                    <td><input type="number" id="hrs_teoricas" name="HRS_TEORICAS" oninput="calcularCreditos()"></td>
                </tr>
                <tr>
                    <th>Hrs Prácticas</th>
                    <td><input type="number" id="hrs_practicas" name="HRS_PRACTICAS" oninput="calcularCreditos()"></td>
                </tr>
                <tr>
                    <th>Créditos</th>
                    <td><input type="number" id="creditos" name="creditos" min="1" readonly></td>
                </tr>
                <tr>
                    <td colspan="2"><input class="boton" type="submit" value="Enviar"></td>
                </tr>
                <tr>
                    <td colspan="2"><button class="boton" id="mostrarDatos">Mostrar Datos</button></td>
                </tr>
            </tbody>
        </table>
    </form>

    <!-- Barra de búsqueda -->
    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Buscar materia por nombre...">
    </div>

    <!-- Tabla para mostrar datos de la base de datos -->
    <table class="data-table" id="dataTable">
        <thead>
            <tr>
                <th>Clave de la Materia</th>
                <th>Nombre de la Materia</th>
                <th>Hrs Teóricas</th>
                <th>Hrs Prácticas</th>
                <th>Créditos</th>
            </tr>
        </thead>
        <tbody id="dataTableBody">
            <!-- Aquí se insertarán los datos desde el archivo PHP -->
        </tbody>
    </table>

    <script>
        function convertirMayusculas(input) {
            input.value = input.value.toUpperCase();
        }

        function calcularCreditos() {
            const hrsTeoricas = parseFloat(document.getElementById('hrs_teoricas').value) || 0;
            const hrsPracticas = parseFloat(document.getElementById('hrs_practicas').value) || 0;
            const totalCreditos = hrsTeoricas + hrsPracticas;
            document.getElementById('creditos').value = totalCreditos;
        }

        document.querySelector('.nav-toggle').addEventListener('click', function() {
            document.querySelector('.nav-menu').classList.toggle('show');
        });

        document.getElementById('mostrarDatos').addEventListener('click', function(event) {
            event.preventDefault(); // Evitar que el botón envíe el formulario
            fetch('mostrar_datos.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('dataTableBody').innerHTML = data;
                    document.getElementById('dataTable').style.display = 'table'; // Mostrar la tabla
                });
        });

        // Función para filtrar los datos en la tabla
        document.getElementById('searchInput').addEventListener('input', function() {
            const filter = this.value.toUpperCase();
            const rows = document.querySelectorAll('#dataTable tbody tr');
            rows.forEach(row => {
                const nameCell = row.cells[1];
                if (nameCell) {
                    const text = nameCell.textContent || nameCell.innerText;
                    row.style.display = text.toUpperCase().includes(filter) ? '' : 'none';
                }
            });
        });

        // Manejar el envío del formulario mediante AJAX
        document.getElementById('materiaForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Evitar el envío del formulario tradicional

            const formData = new FormData(this);
            fetch('procesar_materia.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                // Aquí puedes manejar la respuesta del servidor
                console.log(result);
                alert('Materia guardada correctamente');
                // Opcionalmente, puedes limpiar el formulario o hacer otras acciones
                document.getElementById('materiaForm').reset();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error al guardar la materia');
            });
        });
    </script>
</body>
</html>
