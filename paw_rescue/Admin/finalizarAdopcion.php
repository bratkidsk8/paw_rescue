<?php
include("../conexion.php");

/* ================= VALIDAR ID ================= */
if (!isset($_POST['id_solicitud'])) {
    die("Solicitud no válida");
}

$idSolicitud = (int) $_POST['id_solicitud'];

/* ================= OBTENER DATOS ================= */
$sql = "
SELECT 
    s.id_solicitud,
    s.id_estatus,
    s.id_animal
FROM solicitud_adopcion s
WHERE s.id_solicitud = $1
";

$res = pg_query_params($conexion, $sql, [$idSolicitud]);

if (!$res || pg_num_rows($res) === 0) {
    die("Solicitud no encontrada");
}

$data = pg_fetch_assoc($res);

/* ================= VALIDAR FASE ================= */
if ((int)$data['id_estatus'] !== 4) {
    die("La solicitud no está en fase de firma");
}

$idAnimal = (int)$data['id_animal'];

pg_query($conexion, "BEGIN");

/* ================= ACTUALIZAR CITA ================= */
$sqlCita = "
UPDATE cita_adopcion
SET estatus_cita = 'Realizada'
WHERE id_solicitud = $1
AND tipo_cita = 'Firma de adopción'
";

$resCita = pg_query_params($conexion, $sqlCita, [$idSolicitud]);

/* ================= ACTUALIZAR SOLICITUD ================= */
$sqlSolicitud = "
UPDATE solicitud_adopcion
SET id_estatus = 5
WHERE id_solicitud = $1
";

$resSolicitud = pg_query_params($conexion, $sqlSolicitud, [$idSolicitud]);

/* ================= ACTUALIZAR MASCOTA ================= */
$sqlMascota = "
UPDATE animal
SET estatus = 'Adoptada'
WHERE id_animal = $1
";

$resMascota = pg_query_params($conexion, $sqlMascota, [$idAnimal]);

/* ================= COMMIT / ROLLBACK ================= */
if ($resCita && $resSolicitud && $resMascota) {
    pg_query($conexion, "COMMIT");
    header("Location: verSolicitud.php?id=$idSolicitud&ok=adopcion");
    exit;
} else {
    pg_query($conexion, "ROLLBACK");
    die("Error al finalizar adopción");
}
