<?php
include("../conexion.php");
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_POST['id_cita'], $_POST['id_solicitud'])) {
    die("Datos incompletos");
}

$idCita = (int)$_POST['id_cita'];
$idSolicitud = (int)$_POST['id_solicitud'];

/* ===============================
   MARCAR CITA COMO REALIZADA
   =============================== */
pg_query_params(
    $conexion,
    "
    UPDATE paw_rescue.cita_adopcion
    SET id_estatus = (
        SELECT id_estatus
        FROM paw_rescue.estatus_cita
        WHERE nombre = 'Realizada'
    )
    WHERE id_cita = $1
    ",
    [$idCita]
);

/* =========================================
   2️ SINCRONIZAR CON SEGUIMIENTO (USUARIO)
   ========================================= */
pg_query_params(
    $conexion,
    "
    INSERT INTO paw_rescue.seguimiento_adopcion (
        id_solicitud,
        id_tipo_cita,
        id_estatus_cita,
        fecha,
        hora
    )
    SELECT
        ca.id_solicitud,
        ca.id_tipo,
        ca.id_estatus,
        ca.fecha,
        ca.hora
    FROM paw_rescue.cita_adopcion ca
    WHERE ca.id_cita = $1
    ",
    [$idCita]
);

header("Location: detalleSolicitud.php?id=$idSolicitud");
exit;
