<?php
session_start();
include("../conexion.php");

ini_set('display_errors', 1);
error_reporting(E_ALL);

/* ===== VALIDAR ADMIN ===== */
if (!isset($_SESSION['admin_id'])) {
    die("Acceso denegado");
}

/* ===== VALIDAR POST ===== */
$campos = [
    'id_solicitud',
    'inicio_fecha','inicio_hora',
    'fin_fecha','fin_hora',
    'rev_fecha','rev_hora'
];

foreach ($campos as $c) {
    if (empty($_POST[$c])) {
        die("Faltan datos");
    }
}

$idSolicitud = (int)$_POST['id_solicitud'];

/* ===== OBTENER TIPOS DE CITA ===== */
$sqlTipos = "
SELECT id_tipo, nombre
FROM paw_rescue.tipo_cita
WHERE nombre IN (
    'Inicio periodo de prueba',
    'Fin periodo de prueba',
    'Visita de seguimiento'
)";
$resTipos = pg_query($conexion, $sqlTipos);

$tipos = [];
while ($t = pg_fetch_assoc($resTipos)) {
    $tipos[$t['nombre']] = $t['id_tipo'];
}

foreach ([
    'Inicio periodo de prueba',
    'Fin periodo de prueba',
    'Visita de seguimiento'
] as $req) {
    if (!isset($tipos[$req])) {
        die("Falta tipo de cita: $req");
    }
}

/* ===== FUNCIÓN INSERT ===== */
function insertarCita($conexion, $idSolicitud, $idTipo, $fecha, $hora) {
    pg_query_params(
        $conexion,
        "INSERT INTO paw_rescue.cita_adopcion
        (id_solicitud, id_tipo, fecha, hora, id_estatus)
        VALUES ($1,$2,$3,$4,1)",
        [$idSolicitud, $idTipo, $fecha, $hora]
    );
}

/* ===== INSERTAR CITAS ===== */
insertarCita(
    $conexion,
    $idSolicitud,
    $tipos['Inicio periodo de prueba'],
    $_POST['inicio_fecha'],
    $_POST['inicio_hora']
);

insertarCita(
    $conexion,
    $idSolicitud,
    $tipos['Fin periodo de prueba'],
    $_POST['fin_fecha'],
    $_POST['fin_hora']
);

insertarCita(
    $conexion,
    $idSolicitud,
    $tipos['Visita de seguimiento'],
    $_POST['rev_fecha'],
    $_POST['rev_hora']
);

/* ===== REDIRIGIR ===== */
header("Location: detalleSolicitud.php?id=$idSolicitud");
exit;
