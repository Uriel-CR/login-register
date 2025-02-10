<?php
include 'conexion_be.php';

$id_grupo = isset($_GET['id_grupo']) ? intval($_GET['id_grupo']) : 0;

$materias = [];
if ($id_grupo > 0) {
    $consulta_materias = "
        SELECT m.id_materia, m.nombre 
        FROM materias m
        JOIN grupos g ON m.id_materia IN (g.id_materia1, g.id_materia2, g.id_materia3, g.id_materia4, g.id_materia5, g.id_materia6)
        WHERE g.id_grupo = $id_grupo
    ";
    $resultado_materias = mysqli_query($conexion, $consulta_materias);

    if ($resultado_materias && mysqli_num_rows($resultado_materias) > 0) {
        while ($materia = mysqli_fetch_assoc($resultado_materias)) {
            $materias[] = $materia;
        }
    }
}

echo json_encode($materias);
?>
