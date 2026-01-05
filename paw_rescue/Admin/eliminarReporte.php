<?php
session_start();
include("../conexion.php");


$id_reporte = (int)$_POST['id_reporte'];

/* (opcional) borrar imagen */
$sqlFoto = "SELECT foto FROM paw_rescue.reporte_animal WHERE id_reporte = $1";
$res = pg_query_params($conexion, $sqlFoto, [$id_reporte]);
$row = pg_fetch_assoc($res);

if ($row && $row['foto']) {
    $ruta = "../imgReportes/" . $row['foto'];
    if (file_exists($ruta)) unlink($ruta);
}

$sql = "DELETE FROM paw_rescue.reporte_animal WHERE id_reporte = $1";
pg_query_params($conexion, $sql, [$id_reporte]);

header("Location: reporte.php");
