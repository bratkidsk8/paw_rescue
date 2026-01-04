<?php
session_start();

/* ===== VALIDAR ADMIN ===== */
$adminLogueado = isset($_SESSION["admin_login"]) && $_SESSION["admin_login"] === true;
$nombreAdmin  = $_SESSION["admin_nombre"] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio | Admin</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- CSS -->
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<!-- ================= NAVBAR ================= -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">
      <img src="https://cdn-icons-png.flaticon.com/512/616/616408.png" alt="logo" width="30" class="me-2">
      Paw Rescue
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">

        <?php if ($adminLogueado): ?>
          <li class="nav-item"><a class="nav-link" href="info.php">Peticiones</a></li>
          <li class="nav-item"><a class="nav-link" href="adoptar.php">Reportes</a></li>
          <li class="nav-item"><a class="nav-link" href="agregarMascota.php">Agregar mascotas</a></li>
          <li class="nav-item"><a class="nav-link" href="reporte.php">Reportar</a></li>
          <li class="nav-item"><a class="nav-link" href="catalogo.php">Catálogo</a></li>
        <?php endif; ?>

      </ul>

      <?php if ($adminLogueado): ?>
        <span class="me-3 fw-semibold">
          admin: <?= htmlspecialchars($nombreAdmin) ?>
        </span>
        <a href="logoutAdmin.php" class="btn btn-outline-danger">
          Cerrar sesión
        </a>
      <?php else: ?>
        <a href="login.php" class="btn btn-outline-dark ms-3">
          Login
        </a>
      <?php endif; ?>

    </div>
  </div>
</nav>

<!-- ================= HERO ================= -->
<section class="hero text-center p-5">
  <h1>
    Bienvenido<br>
    <?= $adminLogueado ? htmlspecialchars($nombreAdmin) : 'Administrador' ?>
  </h1>
</section>

<!-- ================= FOOTER ================= -->
<footer class="text-center py-3">
  MURASAKI 2026 ©
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
