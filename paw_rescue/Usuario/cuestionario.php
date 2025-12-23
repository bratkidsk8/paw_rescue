<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cuestionario de Adopción</title>

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

  <!-- Sección Cuestionario -->
  <section class="container my-5">
    <h2 class="text-center mb-4">Cuestionario de Adopción</h2>
    <p class="text-center mb-5">Completa este cuestionario para ayudarnos a conocer mejor tu perfil como adoptante. <br>Esto nos permitirá conocer si eres candidato.<br></p>

    <form id="form-cuestionario" class="shadow p-4 rounded bg-light">
      <!-- Datos personales -->
      <h5 class="mb-3">Datos personales</h5>
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre completo</label>
        <input type="text" id="nombre" name="nombre" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="correo" class="form-label">Correo electrónico</label>
        <input type="email" id="correo" name="correo" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="telefono" class="form-label">Teléfono</label>
        <input type="tel" id="telefono" name="telefono" class="form-control" required>
      </div>

      <!-- Información sobre la adopción -->
      <h5 class="mt-4 mb-3">Información sobre la adopción</h5>
      <div class="mb-3">
        <label for="tipoMascota" class="form-label">¿Qué mascota deseas adoptar?</label>
        <select id="tipoMascota" name="tipoMascota" class="form-select" required>
          <option value="">Selecciona</option>
          <option value="perro">Perro</option>
          <option value="gato">Gato</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="experiencia" class="form-label">¿Tienes experiencia cuidando mascotas?</label>
        <select id="experiencia" name="experiencia" class="form-select" required>
          <option value="">Selecciona</option>
          <option value="si">Sí</option>
          <option value="no">No</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="vivienda" class="form-label">Tipo de vivienda</label>
        <select id="vivienda" name="vivienda" class="form-select" required>
          <option value="">Selecciona</option>
          <option value="casa">Casa</option>
          <option value="departamento">Departamento</option>
          <option value="otro">Otro</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="razonAdopcion" class="form-label">¿Por qué quieres adoptar?</label>
        <textarea id="razonAdopcion" name="razonAdopcion" rows="3" class="form-control" required></textarea>
      </div>
      <div class="mb-3">
        <label for="tiempoDisponible" class="form-label">¿Cuánto tiempo puedes dedicarle al día?</label>
        <input type="text" id="tiempoDisponible" name="tiempoDisponible" class="form-control" required>
      </div>

      <!-- Botón enviar -->
      <div class="text-center">
        <button type="submit" class="btn btn-success">Enviar cuestionario</button>
      </div>
    </form>
  </section>

  <!-- Modal confirmación -->
  <div class="modal fade" id="modalConfirmacion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center p-4">
        <h5 class="mb-3">✅ Cuestionario enviado</h5>
        <p>Gracias por tu interés en adoptar. Nuestro equipo revisará tus respuestas y se pondrá en contacto contigo pronto.</p>
        <button class="btn btn-primary w-100" data-bs-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>

  <!-- Pie de página -->
  <footer class="text-center py-3 bg-light">
    MURASAKI 2026. ©
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../js/cuestionario.js"></script>
</body>
</html>
