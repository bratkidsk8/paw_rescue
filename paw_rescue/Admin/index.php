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
        <img src="https://cdn-icons-png.flaticon.com/512/616/616408.png" alt="logo" width="30" class="me-2">
        Marca
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="info.php">Peticiones</a></li>
          <li class="nav-item"><a class="nav-link" href="adoptar.php">Reportes</a></li>
          <li class="nav-item"><a class="nav-link" href="agregar_mascota.php">Agregar mascotas</a></li>
          <li class="nav-item"><a class="nav-link" href="reporte.php">Reportar</a></li>
          <li class="nav-item"><a class="nav-link" href="adoptar.php">Catalogo</a></li>
        </ul>
        <a href="login.php" class="btn btn-outline-dark ms-3">Login</a>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero">
    <div>
      <h1>Bienvenido  <br> administrador</h1>
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
