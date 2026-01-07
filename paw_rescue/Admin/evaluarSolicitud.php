<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_POST['id_solicitud'], $_POST['resultado'])) {
    die("Datos incompletos");
}

$idSolicitud = $_POST['id_solicitud'];
$resultado = $_POST['resultado'];
$observaciones = $_POST['observaciones'] ?? null;

/* ================= DEFINIR RESULTADO ================= */

if ($resultado === 'apto') {
    $nuevoEstatus = 2; // Apto
    $esCandidato = true;
} else {
    $nuevoEstatus = 3; // No apto
    $esCandidato = false;
}

/* ================= ACTUALIZAR SOLICITUD ================= */

$sql = "
UPDATE paw_rescue.solicitud_adopcion
SET
    es_candidato = $1,
    observaciones = $2,
    id_estatus = $3
WHERE id_solicitud = $4
";

$res = pg_query_params($conexion, $sql, [
    $esCandidato,
    $observaciones,
    $nuevoEstatus,
    $idSolicitud
]);

if (!$res) {
    die("Error al guardar evaluación");
}

/* ================= REDIRECCIÓN ================= */
header("Location: verSolicitud.php?id=$idSolicitud");
exit;
