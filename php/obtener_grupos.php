<?php
include 'conexion_be.php';

$id_periodo = isset($_GET['id_periodo']) ? intval($_GET['id_periodo']) : 0;

$grupos = [];
if ($id_periodo > 0) {
    $consulta_periodo = "SELECT periodo FROM periodos WHERE id_periodo = $id_periodo";
    $resultado_periodo = mysqli_query($conexion, $consulta_periodo);
    $periodo = mysqli_fetch_assoc($resultado_periodo)['periodo'];

    $ultimo_digito = substr($periodo, -1);
    $patron = ($ultimo_digito == '1') ? '[13579]' : '[02468]';

    $consulta_grupos = "
        SELECT id_grupo, nombre_grupo 
        FROM grupos 
        WHERE nombre_grupo REGEXP '^.{$patron}..$'
        ORDER BY nombre_grupo ASC
    ";
    $resultado_grupos = mysqli_query($conexion, $consulta_grupos);

    if ($resultado_grupos && mysqli_num_rows($resultado_grupos) > 0) {
        while ($grupo = mysqli_fetch_assoc($resultado_grupos)) {
            $grupos[] = $grupo;
        }
    }
}

echo json_encode($grupos);
?>
