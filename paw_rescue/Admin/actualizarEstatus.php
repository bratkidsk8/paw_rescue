<?php
session_start();
include("../conexion.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_reporte = (int)$_POST['id_reporte'];
$id_estatus = (int)$_POST['id_estatus'];

$sql = "
UPDATE paw_rescue.reporte_animal
SET id_estatus = $1
WHERE id_reporte = $2
";

pg_query_params($conexion, $sql, [$id_estatus, $id_reporte]);

header("Location: reporte.php");
exit;
