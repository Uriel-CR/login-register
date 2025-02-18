<?php
include 'conexion_be.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_grupo = intval($_POST['id_grupo']);

    if (!empty($_POST['id_materia'])) {
        foreach ($_POST['id_materia'] as $index => $id_materia) {
            $id_materia = intval($id_materia);
            $clave_materia = mysqli_real_escape_string($conexion, $_POST['clave_materia'][$index]);
            $hrs_teoricas = intval($_POST['hrs_teoricas'][$index]);
            $hrs_practicas = intval($_POST['hrs_practicas'][$index]);
            $creditos = intval($_POST['creditos'][$index]);

            // Actualizar la materia en la base de datos
            $consulta_update = "
                UPDATE materias SET 
                    clave_materia = '$clave_materia',
                    HRS_TEORICAS = $hrs_teoricas,
                    HRS_PRACTICAS = $hrs_practicas,
                    creditos = $creditos
                WHERE id_materia = $id_materia
            ";

            if (!mysqli_query($conexion, $consulta_update)) {
                die('Error al actualizar la materia con ID ' . $id_materia . ': ' . mysqli_error($conexion));
            }
        }
    }

    mysqli_close($conexion);
    header('Location: grupos.php?id_grupo=' . $id_grupo);
    exit;
}
?>
