<?php
session_start();
include("../conexion.php");

/* ===== VALIDAR ADMIN ===== */
if (!isset($_SESSION['admin_id'])) {
    die("Acceso denegado");
}

/* ===== CONSULTA PERSONAL ===== */
$sql = "
SELECT
    p.id_personal,
    u.nombre,
    u.primer_apellido,
    u.segundo_apellido,
    u.correo,
    tp.nombre AS tipo_personal,
    p.telefono,
    STRING_AGG(r.nombre, ', ') AS refugios
FROM paw_rescue.personal p
JOIN paw_rescue.usuario u ON u.id_usuario = p.id_usuario
JOIN paw_rescue.tipo_personal tp ON tp.id_tipo = p.id_tipo
LEFT JOIN paw_rescue.personal_refugio pr ON pr.id_personal = p.id_personal
LEFT JOIN paw_rescue.refugio r ON r.id_ref = pr.id_ref
GROUP BY
    p.id_personal, u.nombre, u.primer_apellido,
    u.segundo_apellido, u.correo, tp.nombre, p.telefono
ORDER BY tp.nombre, u.nombre
";

$res = pg_query($conexion, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Personal de refugios</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include("navbar.php"); ?>

<div class="container mt-5">
<div class="d-flex justify-content-between align-items-center">
    <h2>Personal de refugios</h2>
    <a href="agregarPersonal.php" class="btn btn-success">
        ➕ Agregar personal
    </a>
</div>

<table class="table table-bordered table-hover mt-4">
<thead class="table-dark">
<tr>
    <th>Nombre</th>
    <th>Tipo</th>
    <th>Correo</th>
    <th>Teléfono</th>
    <th>Refugios</th>
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
    <td><?= $row['tipo_personal'] ?></td>
    <td><?= $row['correo'] ?></td>
    <td><?= $row['telefono'] ?></td>
    <td><?= $row['refugios'] ?: '—' ?></td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="5" class="text-center">No hay personal registrado</td>
</tr>
<?php endif; ?>

</tbody>
</table>
</div>

</body>
</html>
