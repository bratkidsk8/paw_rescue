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
<nav class="navbar navbar-expand-lg bg-white shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">
      <img src="https://cdn-icons-png.flaticon.com/512/616/616409.png" width="30" class="me-2">
      Paw Rescue
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="info.php">Acerca de</a></li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="adoptar.php" role="button" data-bs-toggle="dropdown">
            Adoptar
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="adoptar.php">Ver mascotas</a></li>
            <li><a class="dropdown-item" href="cuestionario.php">Cuestionario</a></li>
          </ul>
        </li>

        <li class="nav-item"><a class="nav-link" href="donar.php">Donaciones</a></li>
        <li class="nav-item"><a class="nav-link" href="reporte.php">Reportar</a></li>
        <li class="nav-item"><a class="nav-link" href="contacto.php">Contacto</a></li>
      </ul>

      <?php if (isset($_SESSION['id_usuario'])): ?>
        <div class="dropdown ms-3">
          <button class="btn btn-outline-dark dropdown-toggle" data-bs-toggle="dropdown">
            <?= htmlspecialchars($_SESSION['nombre']) ?>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="perfil.php">Mi perfil</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="logout.php">Cerrar sesión</a></li>
          </ul>
        </div>
      <?php else: ?>
        <a href="login.php" class="btn btn-outline-dark ms-3">Login</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

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
