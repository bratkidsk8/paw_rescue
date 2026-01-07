<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$nombreAdmin = $_SESSION['admin_nombre'] ?? 'Admin';
?>


<nav class="navbar navbar-expand-lg bg-white shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
      <img src="https://cdn-icons-png.flaticon.com/512/616/616408.png"
           alt="logo" width="30" class="me-2">
      Paw Rescue
    </a>

    <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav me-3">

        <li class="nav-item"><a class="nav-link" href="info.php">Peticiones</a></li>
        <li class="nav-item"><a class="nav-link" href="solicitudesAdopcion.php">proceso de adopcion</a></li>
        <li class="nav-item"><a class="nav-link" href="agregarMascota.php">Agregar mascotas</a></li>
        <li class="nav-item"><a class="nav-link" href="reporte.php">Reportes</a></li>
        <li class="nav-item"><a class="nav-link" href="catalogo.php">Catálogo</a></li>

      </ul>

      <span class="me-3 fw-semibold">
        admin: <?= htmlspecialchars($nombreAdmin) ?>
      </span>

      <a href="logoutAdmin.php" class="btn btn-outline-danger btn-sm">
        Cerrar sesión
      </a>
    </div>
  </div>
</nav>
