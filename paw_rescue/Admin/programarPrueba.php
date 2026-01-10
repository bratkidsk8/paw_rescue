<?php
include("../conexion.php");
ini_set('display_errors', 1);
error_reporting(E_ALL);

/* ================= VALIDAR ID ================= */
if (!isset($_GET['id'])) {
    die("Solicitud no válida");
}
$idSolicitud = (int)$_GET['id'];

/* ================= OBTENER IDS NECESARIOS ================= */
function obtenerId($conexion, $tabla, $campo, $valor) {
    $sql = "SELECT $campo FROM paw_rescue.$tabla WHERE nombre = $1";
    $res = pg_query_params($conexion, $sql, [$valor]);
    if ($row = pg_fetch_assoc($res)) {
        return (int)$row[$campo];
    }
    return null;
}

$idInicioPeriodo = obtenerId($conexion, 'tipo_cita', 'id_tipo', 'Inicio periodo de prueba');
$idFinPeriodo    = obtenerId($conexion, 'tipo_cita', 'id_tipo', 'Fin periodo de prueba');
$idSeguimiento   = obtenerId($conexion, 'tipo_cita', 'id_tipo', 'Visita de seguimiento');
$idProgramada    = obtenerId($conexion, 'estatus_cita', 'id_estatus', 'Programada');

if (!$idInicioPeriodo || !$idFinPeriodo || !$idSeguimiento || !$idProgramada) {
    die("Error: tipos de cita o estatus no configurados");
}

/* ================= GUARDAR ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $inicio_fecha = $_POST['inicio_fecha'];
    $inicio_hora  = $_POST['inicio_hora'];

    $inicio = new DateTime("$inicio_fecha $inicio_hora");

    /* === FIN (+15 DÍAS) === */
    $fin = clone $inicio;
    $fin->modify('+15 days');

    /* === SEGUIMIENTO (+7 DÍAS) === */
    $seguimiento = clone $inicio;
    $seguimiento->modify('+7 days');

    /* === INSERT INICIO === */
    pg_query_params($conexion, "
        INSERT INTO paw_rescue.cita_adopcion
        (id_solicitud, id_tipo, fecha, hora, id_estatus)
        VALUES ($1, $2, $3, $4, $5)
    ", [$idSolicitud, $idInicioPeriodo, $inicio->format('Y-m-d'), $inicio->format('H:i'), $idProgramada]);

    /* === INSERT SEGUIMIENTO === */
    pg_query_params($conexion, "
        INSERT INTO paw_rescue.cita_adopcion
        (id_solicitud, id_tipo, fecha, hora, id_estatus)
        VALUES ($1, $2, $3, $4, $5)
    ", [$idSolicitud, $idSeguimiento, $seguimiento->format('Y-m-d'), $seguimiento->format('H:i'), $idProgramada]);

    /* === INSERT FIN === */
    pg_query_params($conexion, "
        INSERT INTO paw_rescue.cita_adopcion
        (id_solicitud, id_tipo, fecha, hora, id_estatus)
        VALUES ($1, $2, $3, $4, $5)
    ", [$idSolicitud, $idFinPeriodo, $fin->format('Y-m-d'), $fin->format('H:i'), $idProgramada]);

    header("Location: verSolicitud.php?id=$idSolicitud");
    exit;
}
?>
