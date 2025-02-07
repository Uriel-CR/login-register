<?php
// Configuración de la base de datos
$servername = "localhost"; // Cambia esto por tu servidor de base de datos
$username = "serviciosocial"; // Cambia esto por tu nombre de usuario de la base de datos
$password = "FtW30yNo8hQd-x/G"; // Cambia esto por tu contraseña de la base de datos
$database = "login_register_db"; // Cambia esto por el nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (isset($_GET['grupo'])) {
    $grupo_id = intval($_GET['grupo']);

    // Obtener los IDs de las materias para el grupo
    $stmt = $conn->prepare("SELECT id_materia1, id_materia2, id_materia3, id_materia4, id_materia5, id_materia6 FROM grupos WHERE id_grupo = ?");
    $stmt->bind_param("i", $grupo_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $grupo = $result->fetch_assoc();

    if ($grupo) {
        $materia_ids = [
            $grupo['id_materia1'],
            $grupo['id_materia2'],
            $grupo['id_materia3'],
            $grupo['id_materia4'],
            $grupo['id_materia5'],
            $grupo['id_materia6']
        ];

        $materia_labels = ['Materia 1:', 'Materia 2:', 'Materia 3:', 'Materia 4:', 'Materia 5:', 'Materia 6:'];
        $materia_index = 0;

        foreach ($materia_ids as $id_materia) {
            if ($id_materia) {
                $stmt_materia = $conn->prepare("SELECT nombre FROM materias WHERE id_materia = ?");
                $stmt_materia->bind_param("i", $id_materia);
                $stmt_materia->execute();
                $result_materia = $stmt_materia->get_result();
                $materia = $result_materia->fetch_assoc();

                if ($materia) {
                    echo $materia_labels[$materia_index] . ' ' . htmlspecialchars($materia['nombre']) . '<br>';
                }
                $stmt_materia->close();
                $materia_index++;
            }
        }
    } else {
        echo "No se encontraron materias para este grupo.";
    }

    $stmt->close();
}

$conn->close();
?>