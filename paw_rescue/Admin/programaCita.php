<?php
session_start();
include("../conexion.php");

/* ================= VALIDAR SESIÓN ADMIN ================= */
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

/* ================= VALIDAR SOLICITUD ================= */
$idSolicitud = $_GET['id'] ?? null;
if (!$idSolicitud) {
    die("Solicitud no válida");
}

/* ================= DATOS DE SOLICITUD + MASCOTA + REFUGIO ================= */
$sql = "
SELECT
    s.id_solicitud,
    a.nombre AS mascota,
    r.nombre AS refugio,
    r.calle
FROM paw_rescue.solicitud_adopcion s
JOIN paw_rescue.animal a ON a.id_animal = s.id_animal
JOIN paw_rescue.refugio r ON r.id_ref = a.id_ref
WHERE s.id_solicitud = $1
";

$res = pg_query_params($conexion, $sql, [$idSolicitud]);
$data = pg_fetch_assoc($res);

if (!$data) {
    die("Datos no encontrados");
}

/* ================= ID FIJO DEL TIPO DE CITA ================= */
$idTipoCita = 1; // Visita a la mascota
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Programar visita</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-4">

<h3>📅 Programar visita — <?= htmlspecialchars($data['mascota']) ?></h3>

<div class="alert alert-info">
<b>Refugio:</b> <?= htmlspecialchars($data['refugio']) ?><br>
<b>Dirección:</b> <?= htmlspecialchars($data['calle']) ?>
</div>

<form method="POST" action="guardarCita.php">

<input type="hidden" name="id_solicitud" value="<?= $data['id_solicitud'] ?>">
<input type="hidden" name="id_tipo" value="<?= $idTipoCita ?>">

<div class="mb-3">
<label class="form-label">Tipo de cita</label>
<input type="text" class="form-control" value="Visita a la mascota" disabled>
</div>

<div class="mb-3">
<label class="form-label">Fecha</label>
<input type="date" name="fecha" class="form-control" required min="<?= date('Y-m-d') ?>">
</div>

<div class="mb-3">
<label class="form-label">Hora</label>
<input type="time" name="hora" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Observaciones</label>
<textarea name="observaciones" class="form-control" rows="3"></textarea>
</div>

<button class="btn btn-success">Guardar visita</button>
<a href="verSolicitud.php?id=<?= $idSolicitud ?>" class="btn btn-secondary">Cancelar</a>

</form>

</div>

</body>
</html>
