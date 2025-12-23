<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Adopcion</title>

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

  <!-- Hero Section -->
  <section class="titulo">
    <div>
      <h2>Tu nuevo amigo espera un nuevo hogar</h2>
    </div>
  </section>

  <section class="l1">
    <div>
      <h2>Busca a tu mascota ideal</h2>
    </div>
  </section>

  <!-- Filtro de búsqueda -->
<section class="container my-5">
  <div class="row mb-4">
    <div class="col-md-12 d-flex justify-content-center">
      <div class="input-group w-75">
        <!-- Dropdown -->
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
          🐶 perros
        </button>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="#">Perros</a></li>
          <li><a class="dropdown-item" href="#">edad</a></li>
          <li><a class="dropdown-item" href="#">tamaño</a></li>
          <li><a class="dropdown-item" href="#">sexo</a></li>
        </ul>

        <!-- Input búsqueda -->
        <input type="text" class="form-control" placeholder="Buscar raza...">
        
        <!-- Botón buscar -->
        <button class="btn btn-dark">buscar</button>
      </div>
    </div>
  </div>

  <!-- Título -->
  <h3 class="fw-bold mb-4">Perros <span class="text-muted fs-5">137</span></h3>

  <!-- Grid de perros -->
<div class="row g-4">
  <!-- tarjeta 1 -->
  <div class="col-md-3">
    <a href="razas/huskies.html" class="text-decoration-none text-dark">
      <div class="card shadow-sm h-100">
        <img src="../img/husky.jpeg" class="card-img-top" alt="Husky">
        <div class="card-body">
          <h5 class="card-title">Huskies</h5>
          <p class="card-text text-muted">15</p>
        </div>
      </div>
    </a>
  </div>

  <!-- tarjeta 2 -->
  <div class="col-md-3">
    <a href="razas/golden.html" class="text-decoration-none text-dark">
      <div class="card shadow-sm h-100">
        <img src="../img/golden.jpeg" class="card-img-top" alt="Golden Retriever">
        <div class="card-body">
          <h5 class="card-title">Labrador Retriever</h5>
          <p class="card-text text-muted">20</p>
        </div>
      </div>
    </a>
  </div>

  <!-- tarjeta 3 -->
  <div class="col-md-3">
    <a href="razas/pastor.html" class="text-decoration-none text-dark">
      <div class="card shadow-sm h-100">
        <img src="../img/pastor.jpeg" class="card-img-top" alt="Pastor Alemán">
        <div class="card-body">
          <h5 class="card-title">Pastor Alemán</h5>
          <p class="card-text text-muted">25</p>
        </div>
      </div>
    </a>
  </div>

  <!-- tarjeta 4 -->
  <div class="col-md-3">
    <a href="razas/bulldog.html" class="text-decoration-none text-dark">
      <div class="card shadow-sm h-100">
        <img src="../img/bulldog.jpeg" class="card-img-top" alt="Bull Dog">
        <div class="card-body">
          <h5 class="card-title">Bull Dog</h5>
          <p class="card-text text-muted">2</p>
        </div>
      </div>
    </a>
  </div>
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
