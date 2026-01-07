<?php
session_start();
include("../conexion.php");

/* ================== VALIDAR ADMIN ================== */
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

/* ================== VALIDAR POST ================== */
if (!isset($_POST['id_solicitud'], $_POST['resultado'], $_POST['observaciones'])) {
    die("Datos incompletos");
}

$idSolicitud = (int)$_POST['id_solicitud'];
$resultado = $_POST['resultado']; // apto / no_apto
$observaciones = trim($_POST['observaciones']);

pg_query($conexion, "BEGIN");

if ($resultado === 'apto') {
    $nuevoEstatus = 6; // Aprobada (firma)
} else {
    $nuevoEstatus = 7; // Denegada
}

$sql = "
UPDATE paw_rescue.solicitud_adopcion
SET
    id_estatus = $1,
    observaciones = $2,
    aprobada = $3
WHERE id_solicitud = $4
AND id_estatus = 5
";

$res = pg_query_params($conexion, $sql, [
    $nuevoEstatus,
    $observaciones,
    $resultado === 'apto',
    $idSolicitud
]);

if (!$res) {
    pg_query($conexion, "ROLLBACK");
    die("Error al evaluar periodo de prueba");
}

pg_query($conexion, "COMMIT");

/* ================== REDIRIGIR ================== */
header("Location: verSolicitud.php?id=".$idSolicitud);
exit;
