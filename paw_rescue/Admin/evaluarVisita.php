<?php
include("../conexion.php");

$idSolicitud   = (int)$_POST['id_solicitud'];
$resultado     = $_POST['resultado'];
$observaciones = $_POST['observaciones'] ?? null;

pg_query($conexion, "BEGIN");

if ($resultado === 'apto') {

    $inicio = $_POST['fecha_inicio'];
    $fin    = $_POST['fecha_fin'];

    if ($inicio >= $fin) {
        pg_query($conexion, "ROLLBACK");
        die("Las fechas del periodo de prueba no son válidas");
    }

    $sql = "
    UPDATE paw_rescue.solicitud_adopcion
    SET
        id_estatus = 5,
        fecha_inicio_prueba = $1,
        fecha_fin_prueba = $2,
        observaciones = $3
    WHERE id_solicitud = $4
    ";

    pg_query_params($conexion, $sql, [
        $inicio,
        $fin,
        $observaciones,
        $idSolicitud
    ]);

} else {

    $sql = "
    UPDATE paw_rescue.solicitud_adopcion
    SET
        id_estatus = 3,
        observaciones = $1
    WHERE id_solicitud = $2
    ";

    pg_query_params($conexion, $sql, [
        $observaciones,
        $idSolicitud
    ]);
}

pg_query($conexion, "COMMIT");

header("Location: verSolicitud.php?id=".$idSolicitud);
exit;
