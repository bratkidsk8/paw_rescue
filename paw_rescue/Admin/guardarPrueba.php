<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['admin_id'])) {
    die("Acceso denegado");
}

$idSolicitud = (int)$_POST['id_solicitud'];

pg_query($conexion, "BEGIN");

/* ID tipos */
$idInicio = pg_fetch_result(
    pg_query($conexion, "SELECT id_tipo FROM paw_rescue.tipo_cita WHERE nombre='Inicio periodo de prueba'"),
    0, 0
);

$idFin = pg_fetch_result(
    pg_query($conexion, "SELECT id_tipo FROM paw_rescue.tipo_cita WHERE nombre='Fin periodo de prueba'"),
    0, 0
);

/* Estatus Programada */
$idProgramada = 1;

/* Insertar inicio */
pg_query_params($conexion, "
INSERT INTO paw_rescue.cita_adopcion (id_solicitud, id_tipo, fecha, hora, id_estatus)
VALUES ($1,$2,$3,$4,$5)
", [$idSolicitud, $idInicio, $_POST['inicio_fecha'], $_POST['inicio_hora'], $idProgramada]);

/* Insertar fin */
pg_query_params($conexion, "
INSERT INTO paw_rescue.cita_adopcion (id_solicitud, id_tipo, fecha, hora, id_estatus)
VALUES ($1,$2,$3,$4,$5)
", [$idSolicitud, $idFin, $_POST['fin_fecha'], $_POST['fin_hora'], $idProgramada]);

pg_query($conexion, "COMMIT");

header("Location: detalleSolicitud.php?id=$idSolicitud");
exit;
