<?php
session_start();
include("../conexion.php");

/* ===============================
   VALIDAR ADMIN
================================= */
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

/* ===============================
   OBTENER SOLICITUDES
================================= */
$sql = "
SELECT
    s.id_solicitud,
    u.id_usuario,
    u.nombre || ' ' || u.primer_apellido AS solicitante,
    a.nombre AS mascota,
    ep.nombre AS estatus_proceso,
    s.fecha_solicitud
FROM paw_rescue.solicitud_adopcion s
JOIN paw_rescue.usuario u 
    ON u.id_usuario = s.id_usuario
JOIN paw_rescue.animal a 
    ON a.id_animal = s.id_animal
JOIN paw_rescue.estatus_proceso_adopcion ep 
    ON ep.id_estatus = s.id_estatus
ORDER BY s.fecha_solicitud DESC
";

$res = pg_query($conexion, $sql);

if (!$res) {
    die("Error al obtener solicitudes: " . pg_last_error($conexion));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Solicitudes de adopción</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include("navbar.php"); ?>


<?php include("navbar_admin.php"); ?>

<div class="container mt-5">

<h2 class="mb-4">📋 Solicitudes de adopción</h2>

<?php if (pg_num_rows($res) > 0): ?>

<table class="table table-bordered table-hover align-middle">
<thead class="table-light">
<tr>
  <th>Solicitante</th>
  <th>Mascota</th>
  <th>Estatus del proceso</th>
  <th>Fecha de solicitud</th>
  <th>Acciones</th>
</tr>
</thead>
<tbody>

<?php while ($row = pg_fetch_assoc($res)): ?>
<tr>
  <td><?= htmlspecialchars($row['solicitante']) ?></td>
  <td><?= htmlspecialchars($row['mascota']) ?></td>
  <td>
    <span class="badge bg-secondary">
      <?= htmlspecialchars($row['estatus_proceso']) ?>
    </span>
  </td>
  <td><?= $row['fecha_solicitud'] ?></td>
  <td>
    <a href="verSolicitud.php?id=<?= $row['id_solicitud'] ?>"
       class="btn btn-sm btn-outline-primary">
       Ver solicitud
    </a>
  </td>
</tr>
<?php endwhile; ?>

</tbody>
</table>

<?php else: ?>

<div class="alert alert-info">
No hay solicitudes registradas.
</div>

<?php endif; ?>

</div>

</body>
</html>
