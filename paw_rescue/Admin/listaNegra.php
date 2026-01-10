<?php
session_start();
include("../conexion.php");

/* ===== VALIDAR ADMIN ===== */
if (!isset($_SESSION['admin_id'])) {
    die("Acceso denegado");
}

/* ===== CONSULTA ===== */
$sql = "
SELECT
    id_persona,
    nombre,
    primer_apellido,
    segundo_apellido,
    curp,
    motivo,
    fecha
FROM paw_rescue.lista_negra
ORDER BY fecha DESC
";

$res = pg_query($conexion, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Lista negra</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include("navbar.php"); ?>

<div class="container mt-5">
<div class="d-flex justify-content-between align-items-center">
    <h2>🚫 Lista negra</h2>
</div>

<table class="table table-bordered table-hover mt-4">
<thead class="table-dark">
<tr>
    <th>Nombre</th>
    <th>CURP</th>
    <th>Motivo</th>
    <th>Fecha</th>
</tr>
</thead>
<tbody>

<?php if (pg_num_rows($res) > 0): ?>
<?php while ($row = pg_fetch_assoc($res)): ?>
<tr>
    <td>
        <?= htmlspecialchars(
            $row['nombre'].' '.$row['primer_apellido'].' '.$row['segundo_apellido']
        ) ?>
    </td>
    <td><?= htmlspecialchars($row['curp']) ?></td>
    <td><?= htmlspecialchars($row['motivo']) ?></td>
    <td><?= date('d/m/Y', strtotime($row['fecha'])) ?></td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="4" class="text-center">No hay personas en lista negra</td>
</tr>
<?php endif; ?>

</tbody>
</table>
</div>

</body>
</html>
