<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$idSolicitud   = $_POST['id_solicitud'];
$idTipo        = $_POST['id_tipo'];
$fecha         = $_POST['fecha'];
$hora          = $_POST['hora'];
$observaciones = $_POST['observaciones'] ?? null;

/* ================= INSERTAR CITA ================= */

$sql = "
INSERT INTO paw_rescue.cita_adopcion
(id_solicitud, id_tipo, id_estatus, fecha, hora, observaciones)
VALUES ($1, $2, 1, $3, $4, $5)
";

pg_query_params($conexion, $sql, [
    $idSolicitud,
    $idTipo,
    $fecha,
    $hora,
    $observaciones
]);

/* ================= ACTUALIZAR ESTATUS SOLICITUD ================= */
pg_query_params(
    $conexion,
    "UPDATE paw_rescue.solicitud_adopcion
     SET id_estatus = 4
     WHERE id_solicitud = $1",
    [$idSolicitud]
);

/* ================= SEGUIMIENTO ================= */
pg_query_params(
    $conexion,
    "
    INSERT INTO paw_rescue.seguimiento_adopcion
    (id_solicitud, id_tipo_cita, id_estatus_cita, fecha, hora, observaciones)
    VALUES ($1, $2, 1, $3, $4, $5)
    ",
    [$idSolicitud, $idTipo, $fecha, $hora, $observaciones]
);

/* ================= REDIRECCIÓN ================= */
header("Location: verSolicitud.php?id=$idSolicitud");
exit;
