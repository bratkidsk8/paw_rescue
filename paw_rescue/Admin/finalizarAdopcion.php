<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../conexion.php");

/* ===== VALIDAR ADMIN ===== */
if (!isset($_SESSION['admin_id'])) {
    die("Acceso denegado");
}

/* ===== VALIDAR ID ===== */
if (!isset($_POST['id_solicitud'])) {
    die("ID de solicitud faltante");
}

$idSolicitud = (int)$_POST['id_solicitud'];

/* ===== TRANSACCIÓN ===== */
pg_query($conexion, "BEGIN");

/* ===== DATOS SOLICITUD ===== */
$sql = "
SELECT id_usuario, id_animal
FROM paw_rescue.solicitud_adopcion
WHERE id_solicitud = $1
";
$res = pg_query_params($conexion, $sql, [$idSolicitud]);

if (!$res) {
    pg_query($conexion, "ROLLBACK");
    die(pg_last_error($conexion));
}

if (pg_num_rows($res) === 0) {
    pg_query($conexion, "ROLLBACK");
    die("Solicitud inexistente");
}

$data = pg_fetch_assoc($res);
$idUsuario = (int)$data['id_usuario'];
$idAnimal  = (int)$data['id_animal'];

/* ===== OBTENER ESTATUS ADOPTADO ===== */
$sqlEstatus = "
SELECT id_estatus
FROM paw_rescue.estatus_adop
WHERE LOWER(nombre) = 'adoptado'
LIMIT 1
";
$resEstatus = pg_query($conexion, $sqlEstatus);

if (!$resEstatus) {
    pg_query($conexion, "ROLLBACK");
    die(pg_last_error($conexion));
}

$idEstatusAdoptado = (int)pg_fetch_result($resEstatus, 0, 'id_estatus');

/* ===== ACTUALIZAR ANIMAL ===== */
$sqlAnimal = "
UPDATE paw_rescue.animal
SET id_estatus = $1
WHERE id_animal = $2
";
$resAnimal = pg_query_params($conexion, $sqlAnimal, [
    $idEstatusAdoptado,
    $idAnimal
]);

if (!$resAnimal) {
    pg_query($conexion, "ROLLBACK");
    die(pg_last_error($conexion));
}

/* ===== REGISTRAR ADOPCIÓN ===== */
$sqlAdopcion = "
INSERT INTO paw_rescue.adopcion (id_animal, id_usuario, fecha)
VALUES ($1, $2, CURRENT_DATE)
";
$resAdop = pg_query_params($conexion, $sqlAdopcion, [
    $idAnimal,
    $idUsuario
]);

if (!$resAdop) {
    pg_query($conexion, "ROLLBACK");
    die(pg_last_error($conexion));
}

/* ===== ACTUALIZAR SOLICITUD ===== */
$sqlSolicitud = "
UPDATE paw_rescue.solicitud_adopcion
SET
    id_estatus = (
        SELECT id_estatus
        FROM paw_rescue.estatus_proceso_adopcion
        WHERE LOWER(nombre) = 'aprobada'
        LIMIT 1
    ),
    aprobada = TRUE
WHERE id_solicitud = $1
";
$resSol = pg_query_params($conexion, $sqlSolicitud, [$idSolicitud]);

if (!$resSol) {
    pg_query($conexion, "ROLLBACK");
    die(pg_last_error($conexion));
}

/* ===== CONFIRMAR ===== */
pg_query($conexion, "COMMIT");

echo "✅ Adopción finalizada correctamente";
