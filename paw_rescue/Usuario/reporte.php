<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reporte de Perros</title>

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
      <img src="https://cdn-icons-png.flaticon.com/512/616/616409.png" alt="logo" width="30" class="me-2" href="index.php">
      Paw Rescue
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="info.php">Acerca de</a></li>
        
        <!-- Dropdown de Adoptar -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="adoptar.php" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Adoptar
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
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

  <!-- Sección Reportes -->
  <section class="container my-5">
    <h2 class="text-center mb-4">Reportes de Perros Extraviados o Abandonados</h2>
    
    <!-- Botón para abrir modal -->
    <div class="text-center mb-5">
      <button id="btn-reporte" class="btn btn-primary">
         Reporte de mascota 
      </button>
    </div>

    <!-- Lista de reportes estilo blog -->
    <div id="lista-reportes">
      <div class="card mb-4 shadow-sm">
        <div class="row g-0">
          <div class="col-md-4">
            <img src="../img/perro.jpeg" class="img-fluid rounded-start" alt="Perro perdido">
          </div>
          <div class="col-md-8">
            <div class="card-body">
              <h5 class="card-title">Perro extraviado</h5>
              <p class="card-text">Se vio este perrito de tamaño mediano, color marrón, cerca del metro.</p>
              <p class="text-muted">📍 Ubicación: Azcapotzalco</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Modal Reporte -->
  <div class="modal fade" id="modalReporte" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Reportar Perro</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="form-reporte">
            <div class="mb-3">
              <label for="nombrePet" class="form-label">Nombre (si se conoce)</label>
              <input type="text" id="nombrePet" class="form-control">
            </div>
            <div class="mb-3">
              <label for="descripcion" class="form-label">Descripción</label>
              <textarea id="descripcion" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label for="ubicacion" class="form-label">Ubicación vista</label>
              <input type="text" id="ubicacion" class="form-control">
            </div>
              <div class="mb-3">
              <label for="herida" class="form-label">Describir si tiene heridas </label>
              <input type="text" id="herida "class="form-control">
            </div>
            <div class="mb-3">
              <label for="foto" class="form-label">Subir foto</label>
              <input type="file" id="foto" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Publicar</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Login/Regristro -->
  <div class="modal fade" id="modalLogin" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center p-4">
        <h5 class="mb-3">🔒 Debes iniciar sesión</h5>
        <p>Para poder reportar un perro, primero debes estar registrado.</p>
        <a href="login.html" class="btn btn-primary w-100 mb-2">Iniciar sesión</a>
        <a href="registro.html" class="btn btn-outline-secondary w-100">Registrarse</a>
      </div>
    </div>
  </div>

  <!-- Pie de página -->
  <footer class="text-center py-3 bg-light">
    MURASAKI 2026. ©
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Script -->
 
  <script src="../js/reporte.js"></script>

</body>
</html>
