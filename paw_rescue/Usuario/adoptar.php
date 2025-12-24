<?php
include("conexion.php");

/* Filtro por especie */
$filtro = "";
$titulo = "Mascotas";

if (isset($_GET['especie'])) {
  if ($_GET['especie'] == 'perro') {
    $filtro = "WHERE especie = 'perro'";
    $titulo = "Perros";
  } elseif ($_GET['especie'] == 'gato') {
    $filtro = "WHERE especie = 'gato'";
    $titulo = "Gatos";
  }
}

$query = "SELECT * FROM mascotas $filtro";
$resultado = $conexion->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Adopción</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<!-- Navbar -->
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
            <li><a class="dropdown-item" href="prueba.php">Prueba de adopción</a></li>
          </ul>
        </li>

        <li class="nav-item"><a class="nav-link" href="donar.php">Donaciones</a></li>
        <li class="nav-item"><a class="nav-link" href="reporte.php">Reportar</a></li>
        <li class="nav-item"><a class="nav-link" href="contacto.php">Contacto</a></li>
      </ul>

      <a href="login.php" class="btn btn-outline-dark ms-3">Login</a>
    </div>
  </div>
</nav>

<!-- Hero -->
<section class="titulo">
  <h2>Tu nuevo amigo espera un nuevo hogar</h2>
</section>

<section class="l1">
  <h2>Busca a tu mascota ideal</h2>
</section>

<!-- Filtro -->
<section class="container my-5">

  <div class="row mb-4">
    <div class="col-md-12 d-flex justify-content-center">
      <div class="input-group w-75">
        <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
          🐾 Filtrar
        </button>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="adoptar.php">🐾 Todas</a></li>
          <li><a class="dropdown-item" href="adoptar.php?especie=perro">🐶 Perros</a></li>
          <li><a class="dropdown-item" href="adoptar.php?especie=gato">🐈 Gatos</a></li>
        </ul>

        <input type="text" class="form-control" placeholder="Buscar raza..." disabled>
        <button class="btn btn-dark" disabled>Buscar</button>
      </div>
    </div>
  </div>

  <!-- Título dinámico -->
  <h3 class="fw-bold mb-4">
    <?= $titulo ?>
    <span class="text-muted fs-5"><?= $resultado->num_rows ?></span>
  </h3>

  <!-- Grid dinámico -->
  <div class="row g-4">
    <?php while ($mascota = $resultado->fetch_assoc()) { ?>
      <div class="col-md-3">
        <a href="detalle.php?id=<?= $mascota['id_mascota'] ?>" class="text-decoration-none text-dark">
          <div class="card shadow-sm h-100">
            <img src="../img/<?= $mascota['imagen'] ?>" class="card-img-top">
            <div class="card-body">
              <h5 class="card-title"><?= $mascota['raza'] ?></h5>
              <p class="card-text text-muted">
                <?= $mascota['disponibles'] ?> disponibles · <?= ucfirst($mascota['especie']) ?>
              </p>
            </div>
          </div>
        </a>
      </div>
    <?php } ?>
  </div>

</section>

<footer>
  MURASAKI 2026. ©
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
