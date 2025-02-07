<?php
include 'conexion_be.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_grupo = intval($_POST['id_grupo']);
    
    for ($i = 1; $i <= 6; $i++) {
        if (isset($_POST["materia$i"])) {
            $materia_nombre = mysqli_real_escape_string($conexion, $_POST["materia$i"]);
            $clave_materia = mysqli_real_escape_string($conexion, $_POST["clave_materia$i"]);
            $hrs_teoricas = intval($_POST["hrs_teoricas$i"]);
            $hrs_practicas = intval($_POST["hrs_practicas$i"]);
            $creditos = intval($_POST["creditos$i"]); // Obtener créditos desde el formulario
            
            // Actualizar la materia en la base de datos
            $consulta_update = "
                UPDATE materias SET 
                    nombre = '$materia_nombre',
                    clave_materia = '$clave_materia',
                    HRS_TEORICAS = $hrs_teoricas,
                    HRS_PRACTICAS = $hrs_practicas,
                    creditos = $creditos
                WHERE id_materia = (
                    SELECT id_materia$i FROM grupos WHERE id_grupo = $id_grupo
                )
            ";

            if (!mysqli_query($conexion, $consulta_update)) {
                die('Error al actualizar la materia ' . $i . ': ' . mysqli_error($conexion));
            }
        }
    }

    mysqli_close($conexion);
    header('Location: grupos.php?id_grupo=' . $id_grupo);
}
?>
