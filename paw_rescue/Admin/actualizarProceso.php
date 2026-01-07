<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$idSolicitud  = $_POST['id_solicitud'];
$nuevoEstatus = $_POST['nuevo_estatus'];

/* OBTENER DATOS */
$sql = "
SELECT s.id_animal
FROM paw_rescue.solicitud_adopcion s
WHERE s.id_solicitud = $1
";
$res = pg_query_params($conexion, $sql, [$idSolicitud]);
$data = pg_fetch_assoc($res);

$idAnimal = $data['id_animal'];

/* ACTUALIZAR SOLICITUD */
pg_query_params(
    $conexion,
    "UPDATE paw_rescue.solicitud_adopcion SET id_estatus = $1 WHERE id_solicitud = $2",
    [$nuevoEstatus, $idSolicitud]
);

/* SI ENTRA A PROCESO → BLOQUEAR MASCOTA */
if (in_array($nuevoEstatus, [2,3,4])) {
    pg_query_params(
        $conexion,
        "UPDATE paw_rescue.animal SET id_estatus = (
            SELECT id_estatus FROM paw_rescue.estatus_adop WHERE nombre = 'En proceso'
        ) WHERE id_animal = $1",
        [$idAnimal]
    );
}

/* SI ADOPCIÓN APROBADA */
if ($nuevoEstatus == 5) {

    // Mascota adoptada
    pg_query_params(
        $conexion,
        "UPDATE paw_rescue.animal SET id_estatus = (
            SELECT id_estatus FROM paw_rescue.estatus_adop WHERE nombre = 'Adoptado'
        ) WHERE id_animal = $1",
        [$idAnimal]
    );

    // Rechazar otras solicitudes
    pg_query_params(
        $conexion,
        "UPDATE paw_rescue.solicitud_adopcion
         SET id_estatus = 6
         WHERE id_animal = $1 AND id_solicitud <> $2",
        [$idAnimal, $idSolicitud]
    );
}

header("Location: admin_solicitudes.php");
