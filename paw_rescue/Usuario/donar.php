<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- CSS -->
  <link rel="stylesheet" href="../css/donar.css">
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

  <div class="container">
   
    <!-- Encabezado -->
    <div class="donaciones-header">
      <h1>Haz una Donación</h1>
      <p>Tu ayuda es fundamental para seguir rescatando y cuidando a más perros.</p>
    </div>

    <!-- Donación única -->
    <div class="row g-4 justify-content-center">
      <div class="col-md-4">
        <div class="card donacion-card shadow-sm h-100">
          <div class="card-body text-center">
            <h4 class="card-title fw-bold">Donación Única</h4>
            <p class="card-text">Con una sola aportación ayudas con comida, medicinas y refugio.</p>
            <!-- Botón que abre el modal -->
            <button class="btn btn-donar w-100" data-bs-toggle="modal" data-bs-target="#donacionModal">Donar ahora</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Datos donación -->
  <div class="modal fade" id="donacionModal" tabindex="-1" aria-labelledby="donacionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="donacionModalLabel">Completa tu donación</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <form>
            <div class="mb-3">
              <label for="nombre" class="form-label">Nombre completo</label>
              <input type="text" class="form-control" id="nombre" placeholder="Tu nombre" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control" id="email" placeholder="ejemplo@email.com" required>
            </div>
            <div class="mb-3">
              <label for="tarjeta" class="form-label">Número de tarjeta</label>
              <input type="text" class="form-control" id="tarjeta" placeholder="XXXX-XXXX-XXXX-XXXX" required>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="fecha" class="form-label">Fecha de vencimiento</label>
                <input type="text" class="form-control" id="fecha" placeholder="MM/AA" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="cvv" class="form-label">CVV</label>
                <input type="password" class="form-control" id="cvv" placeholder="***" required>
              </div>
            </div>
            <div class="mb-3">
              <label for="cantidad" class="form-label">Cantidad a donar ($)</label>
              <input type="number" class="form-control" id="cantidad" placeholder="50" required>
            </div>
            <button type="submit" class="btn btn-donar w-100">Confirmar Donación</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- pie pagina -->
  <footer class="text-center py-3">
    MURASAKI 2026. ©
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
