<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- CSS -->
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

  <!-- Navbar -->
         <nav class="navbar navbar-expand-lg bg-white shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">
      <img src="https://cdn-icons-png.flaticon.com/512/616/616409.png"
           alt="logo" width="30" class="me-2">
      Paw Rescue
    </a>

    <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">

        <li class="nav-item"><a class="nav-link" href="info.php">Acerca de</a></li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="adoptar.php"
             role="button" data-bs-toggle="dropdown">
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

      <!-- ===== SESIÓN ===== -->
      <?php if (isset($_SESSION['id_usuario'])): ?>

        <div class="dropdown ms-3">
          <button class="btn btn-outline-dark dropdown-toggle"
                  type="button" data-bs-toggle="dropdown">
            Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?>
          </button>

          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item" href="perfil.php">Mi perfil</a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item text-danger" href="logout.php">
                Cerrar sesión
              </a>
            </li>
          </ul>
        </div>

      <?php else: ?>

        <a href="login.php" class="btn btn-outline-dark ms-3">
          Login
        </a>

      <?php endif; ?>

        </div>
      </div>
    </nav>
  <!-- Hero Section -->
  <section class="hero">
    <div>
      <h1>ADOPTA <br> A TU <br> NUEVO MEJOR <br> AMIGO</h1>
      <a href="adoptar.php" class="btn btn-adoptar btn-lg">ADOPTA AQUÍ</a>
    </div>
  </section>

  <!-- pie pagina -->
  <footer>
    MURASAKI 2026. ©
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Script -->
  <script src="script.js"></script>
</body>
</html>
