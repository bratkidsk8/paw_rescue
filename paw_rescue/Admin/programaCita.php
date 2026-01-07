<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$idSolicitud = $_GET['id'] ?? null;
if (!$idSolicitud) die("Solicitud no válida");

/* ================= DATOS DE SOLICITUD + REFUGIO ================= */

$sql = "
SELECT
    s.id_solicitud,
    a.nombre AS mascota,
    r.nombre AS refugio,
    r.calle,
    s.id_animal
FROM paw_rescue.solicitud_adopcion s
JOIN paw_rescue.animal a ON a.id_animal = s.id_animal
JOIN paw_rescue.refugio r ON r.id_ref = a.id_ref
WHERE s.id_solicitud = $1
";

$res = pg_query_params($conexion, $sql, [$idSolicitud]);
$data = pg_fetch_assoc($res);

if (!$data) die("Datos no encontrados");

/* ================= TIPOS DE CITA ================= */
$tipos = pg_query($conexion, "SELECT * FROM paw_rescue.tipo_cita");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Programar cita</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

<h3> Programar cita — <?= htmlspecialchars($data['mascota']) ?></h3>

<div class="alert alert-info">
<b>Refugio:</b> <?= $data['refugio'] ?><br>
<b>Dirección:</b> <?= $data['calle'] ?>
</div>

<form method="POST" action="guardarCita.php">

<input type="hidden" name="id_solicitud" value="<?= $data['id_solicitud'] ?>">

<div class="mb-3">
<label>Tipo de cita</label>
<select name="id_tipo" class="form-select" required>
<?php while ($t = pg_fetch_assoc($tipos)): ?>
<option value="<?= $t['id_tipo'] ?>">
<?= $t['nombre'] ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div class="mb-3">
<label>Fecha</label>
<input type="date" name="fecha" class="form-control" required>
</div>

<div class="mb-3">
<label>Hora</label>
<input type="time" name="hora" class="form-control" required>
</div>

<div class="mb-3">
<label>Observaciones</label>
<textarea name="observaciones" class="form-control"></textarea>
</div>

<button class="btn btn-success">Guardar cita</button>
<a href="verSolicitud.php?id=<?= $idSolicitud ?>" class="btn btn-secondary">Cancelar</a>

</form>
</div>

</body>
</html>
