<?php

include("../../paw_rescue/conexion.php");
pg_query($conexion, "SET search_path TO paw_rescue");

/* ================= VALIDACIÓN ID ================= */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die("ID inválido");
}

$id = (int)$_GET['id'];

/* ================= CONSULTA ================= */
$sql = "
SELECT
  a.id_animal,
  a.nombre,
  a.edad_aprox,

  e.nombre AS especie,
  r.nombre AS raza,
  t.nombre AS tam,
  c.nombre AS color,
  ea.nombre AS estado,

  sa.enfermo,
  sa.diagnostico,
  sa.obs,

  ia.tiene_id,
  ia.codigo

FROM animal a
LEFT JOIN especie e ON a.id_esp = e.id_esp
LEFT JOIN raza r ON a.id_raza = r.id_raza
LEFT JOIN tam t ON a.id_tam = t.id_tam
LEFT JOIN color c ON a.id_color = c.id_color
LEFT JOIN estado_animal ea ON a.id_estado = ea.id_estado
LEFT JOIN salud_actual sa ON sa.id_animal = a.id_animal
LEFT JOIN ident_animal ia ON ia.id_animal = a.id_animal
WHERE a.id_animal = $id
";

$res = pg_query($conexion, $sql);

if (!$res) {
  die(pg_last_error($conexion));
}

if (pg_num_rows($res) === 0) {
  die("Mascota no encontrada");
}

$m = pg_fetch_assoc($res);

/* ================= NORMALIZAR DATOS ================= */
$enfermo = ($m['enfermo'] === 't' || $m['enfermo'] === true);
$diagnostico = $m['diagnostico'] ?: 'No aplica';
$observaciones = $m['obs'] ?: 'Sin observaciones clínicas registradas';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Ficha de <?= htmlspecialchars($m['nombre']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

<!-- ================= NAVBAR ================= -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="catalogo.php">Paw Rescue</a>
    <a href="catalogo.php" class="btn btn-outline-dark">Volver</a>
  </div>
</nav>

<div class="container my-5">

<!-- ================= CABECERA ================= -->
<div class="row mb-4">
  <div class="col-md-4">
    <img src="https://via.placeholder.com/300x300?text=Sin+imagen"
         class="img-fluid rounded shadow"
         alt="Imagen de la mascota">
  </div>

  <div class="col-md-8">
    <h2><?= htmlspecialchars($m['nombre']) ?></h2>
    <span class="badge bg-success"><?= $m['estado'] ?></span>
    <p class="mt-3">
      <b><?= $m['especie'] ?></b> · <?= $m['raza'] ?> · <?= $m['tam'] ?>
    </p>
  </div>
</div>

<hr>

<!-- ================= DATOS GENERALES ================= -->
<h4>Datos generales</h4>
<ul>
  <li><b>Edad aproximada:</b> <?= $m['edad_aprox'] ?> años</li>
  <li><b>Color:</b> <?= $m['color'] ?></li>
  <li><b>Estado:</b> <?= $m['estado'] ?></li>
</ul>

<hr>

<!-- ================= SALUD ================= -->
<h4>Salud</h4>
<ul>
  <li>
    <b>Estado de salud:</b>
    <?= is_null($m['enfermo']) ? 'No registrado' : ($enfermo ? 'Presenta condición médica' : 'Clínicamente sano') ?>
  </li>

  <?php if ($enfermo) { ?>
    <li><b>Diagnóstico:</b> <?= htmlspecialchars($diagnostico) ?></li>
  <?php } ?>

  <li><b>Observaciones:</b> <?= htmlspecialchars($observaciones) ?></li>
</ul>

<hr>

<!-- ================= VACUNAS ================= -->
<h4>Vacunas</h4>
<ul>
<?php
$v = pg_query($conexion, "
SELECT v.nombre, hv.fecha_ap
FROM hist_vac hv
JOIN vacuna v ON hv.id_vac = v.id_vac
WHERE hv.id_animal = $id
");

if (pg_num_rows($v) === 0) {
  echo "<li>No cuenta con vacunas registradas</li>";
}

while ($vac = pg_fetch_assoc($v)) {
  echo "<li>{$vac['nombre']} ({$vac['fecha_ap']})</li>";
}
?>
</ul>

<hr>

<!-- ================= IDENTIFICACIÓN ================= -->
<h4>Identificación</h4>
<ul>
  <li>
    <b>Chip:</b>
    <?= is_null($m['tiene_id']) ? 'No registrado' : ($m['tiene_id'] ? 'Sí' : 'No') ?>
  </li>

  <?php if ($m['tiene_id']) { ?>
    <li><b>Código:</b> <?= htmlspecialchars($m['codigo']) ?></li>
  <?php } ?>
</ul>

</div>

<!-- ================= FOOTER ================= -->
<footer class="text-center py-3 bg-light">
  Paw Rescue © 2026
</footer>

</body>
</html>
