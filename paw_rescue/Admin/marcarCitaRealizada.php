<?php
include("../conexion.php");

$idCita = intval($_POST['id_cita']);
$idSolicitud = intval($_POST['id_solicitud']);

/* Estatus 3 = Realizada */
$sql = "
UPDATE paw_rescue.cita_adopcion
SET id_estatus = 3
WHERE id_cita = $1
";

pg_query_params($conexion, $sql, [$idCita]);

header("Location: verSolicitud.php?id=".$idSolicitud);
exit;
