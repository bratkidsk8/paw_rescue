<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

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
        <li class="nav-item">
          <a class="nav-link" href="info.php">Acerca de</a>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#"
             role="button" data-bs-toggle="dropdown">
            Adoptar
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="adoptar.php">Ver mascotas</a></li>
            <li><a class="dropdown-item" href="cuestionario.php">Cuestionario</a></li>
            <li><a class="dropdown-item" href="procesoAdopcion.php">Mi proceso de adopcion</a></li>
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
