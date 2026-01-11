<?php
session_start();
include("../../paw_rescue/conexion.php");
pg_query($conexion, "SET search_path TO paw_rescue");

/* =========================
   FILTROS
========================= */
$titulo = "Razas disponibles";
$filtro = "";
$especieActiva = $_GET['especie'] ?? 'todos';

if ($especieActiva === 'perro') {
    $filtro = "WHERE LOWER(e.nombre) = 'perro'";
    $titulo = "Razas de Perros";
} elseif ($especieActiva === 'gato') {
    $filtro = "WHERE LOWER(e.nombre) = 'gato'";
    $titulo = "Razas de Gatos";
}

/* =========================
   CONSULTA
========================= */
$query = "
SELECT 
    r.id_raza,
    r.nombre AS raza,
    e.nombre AS especie,
    COUNT(a.id_animal) AS total
FROM animal a
JOIN raza r ON a.id_raza = r.id_raza
JOIN especie e ON a.id_esp = e.id_esp
$filtro
GROUP BY r.id_raza, r.nombre, e.nombre
ORDER BY r.nombre
";

$resultado = pg_query($conexion, $query);
if (!$resultado) die(pg_last_error($conexion));

$total_razas = pg_num_rows($resultado);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Adopción</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<!-- ================= NAVBAR ================= -->
<?php include 'navbar.php'; ?>


<!-- ================= FILTRO VISIBLE ================= -->
<section class="container mt-4">
  <div class="d-flex gap-2 justify-content-center">

    <a href="adoptar.php"
       class="btn <?= $especieActiva === 'todos' ? 'btn-dark' : 'btn-outline-dark' ?>">
       Todos
    </a>

    <a href="adoptar.php?especie=perro"
       class="btn <?= $especieActiva === 'perro' ? 'btn-dark' : 'btn-outline-dark' ?>">
       🐶 Perros
    </a>

    <a href="adoptar.php?especie=gato"
       class="btn <?= $especieActiva === 'gato' ? 'btn-dark' : 'btn-outline-dark' ?>">
       🐱 Gatos
    </a>

  </div>
</section>


<!-- ================= CONTENIDO ================= -->
<section class="container my-5">

<h3 class="fw-bold mb-4 text-center">
  <?= $titulo ?>
  <span class="text-muted">(<?= $total_razas ?>)</span>
</h3>

<div class="row g-4">

<?php if ($total_razas == 0): ?>
  <div class="col-12 text-center text-muted">
    No hay razas registradas.
  </div>
<?php endif; ?>

<?php while ($row = pg_fetch_assoc($resultado)): ?>
<div class="col-md-3">
  <div class="card shadow-sm h-100 text-center">
    <div class="card-body">
      <h5><?= htmlspecialchars($row['raza']) ?></h5>
      <p class="text-muted"><?= ucfirst($row['especie']) ?></p>

      <span class="badge bg-dark mb-2">
        <?= $row['total'] ?> disponibles
      </span>

      <div class="d-grid mt-3">
        <a href="razas/detalleRaza.php?id_raza=<?= $row['id_raza'] ?>&especie=<?= strtolower($row['especie']) ?>"
           class="btn btn-sm btn-outline-dark">
           Ver
        </a>
      </div>
    </div>
  </div>
</div>
<?php endwhile; ?>

</div>
</section>

<footer class="text-center py-4 text-muted">
  MURASAKI © 2026
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
