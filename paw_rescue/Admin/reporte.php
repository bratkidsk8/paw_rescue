<?php
session_start();
include("../conexion.php");

$nombreAdmin = $_SESSION['admin_nombre'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Admin | Reportes</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- NAVBAR -->
<?php include("navbar.php"); ?>


<div class="container my-5">
<h2 class="mb-4 text-center">Administración de Reportes</h2>

<table class="table table-bordered text-center">
<thead class="table-dark">
<tr>
  <th>Nombre</th>
  <th>Ubicación</th>
  <th>Fecha</th>
  <th>Estatus</th>
  <th>Acciones</th>
</tr>
</thead>
<tbody>

<?php
$sql = "
SELECT r.id_reporte, r.nombre, r.ubicacion, r.fecha, r.id_estatus
FROM paw_rescue.reporte_animal r
ORDER BY r.fecha DESC
";
$res = pg_query($conexion, $sql);

while ($row = pg_fetch_assoc($res)):
?>
<tr>
<td><?= $row['nombre'] ?? 'Sin nombre' ?></td>
<td><?= htmlspecialchars($row['ubicacion']) ?></td>
<td><?= date("d/m/Y H:i", strtotime($row['fecha'])) ?></td>

<td>
<form action="actualizarEstatus.php" method="POST" class="d-flex gap-2">
<input type="hidden" name="id_reporte" value="<?= $row['id_reporte'] ?>">
<select name="id_estatus" class="form-select form-select-sm">
  <option value="1" <?= $row['id_estatus']==1?'selected':'' ?>>No encontrado</option>
  <option value="2" <?= $row['id_estatus']==2?'selected':'' ?>>No rescatado</option>
  <option value="3" <?= $row['id_estatus']==3?'selected':'' ?>>Rescatado</option>
</select>
<button class="btn btn-success btn-sm">Guardar</button>
</form>
</td>

<td>
<form action="eliminarReporte.php" method="POST"
onsubmit="return confirm('¿Eliminar este reporte?')">
<input type="hidden" name="id_reporte" value="<?= $row['id_reporte'] ?>">
<button class="btn btn-danger btn-sm">Eliminar</button>
</form>
</td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
</div>
</body>
</html>
