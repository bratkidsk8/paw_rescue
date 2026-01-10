<?php
include("../conexion.php");
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

/* ===== VALIDAR DATOS ===== */
if (!isset($_POST['id_cita'], $_POST['id_solicitud'])) {
    die("Datos incompletos");
}

$idCita = (int)$_POST['id_cita'];
$idSolicitud = (int)$_POST['id_solicitud'];

/* ===== IDs REALES ===== */
$ID_CITA_NO_ASISTIO = 5;     // estatus_cita
$ID_SOLICITUD_CANCELADA = 8; // estatus_proceso_adopcion

pg_query($conexion, "BEGIN");

/* 1️⃣ Marcar cita como NO ASISTIÓ */
$sqlCita = "
UPDATE paw_rescue.cita_adopcion
SET id_estatus = $1
WHERE id_cita = $2
";
$resCita = pg_query_params($conexion, $sqlCita, [
    $ID_CITA_NO_ASISTIO,
    $idCita
]);

/* 2️⃣ Cancelar solicitud */
$sqlSolicitud = "
UPDATE paw_rescue.solicitud_adopcion
SET id_estatus = $1
WHERE id_solicitud = $2
";
$resSolicitud = pg_query_params($conexion, $sqlSolicitud, [
    $ID_SOLICITUD_CANCELADA,
    $idSolicitud
]);

if ($resCita && $resSolicitud) {
    pg_query($conexion, "COMMIT");
    header("Location: detalleSolicitud.php?id=$idSolicitud&cancelada=1");
    exit;
} else {
    pg_query($conexion, "ROLLBACK");
    die("Error al cancelar la solicitud");
}
