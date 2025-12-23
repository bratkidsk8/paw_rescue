<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">

  <!-- CSS -->
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/login.css">
</head>
<body>

  <!-- Navbar -->
 <nav class="navbar navbar-expand-lg bg-white shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">
      <img src="https://cdn-icons-png.flaticon.com/512/616/616409.png" alt="logo" width="30" class="me-2">
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

  <div class="principal">
    <div class="contc">
      Inicio de Sesión
    </div>

    <div class="formulario">
      <form id="loginForm">
        <div class="mb-3">
          <label for="correo" class="form-label">Correo</label>
          <input type="email" class="form-control" id="correo" placeholder="ejemplo@correo.com" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Contraseña</label>
          <input type="password" class="form-control" id="password" placeholder="********" required>
        </div>
        <button type="submit" class="btn btn-dark w-100">Iniciar Sesión</button>
      </form>
      <div class="extra d-flex justify-content-between mt-3">
        <a href="#" class="text">Olvidé mi contraseña</a>
        <a class="text" href="registro.html">Registrarse</a>
      </div>
    </div>
  </div>

  <!-- Pie pagina -->
  <footer class="text-center mt-5">
    MURASAKI 2026. ©
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Script Login -->
   <script src="../js/login.js"></script>
</body>
</html>
