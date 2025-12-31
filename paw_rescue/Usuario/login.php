<?php
session_start();
include("../../paw_rescue/conexion.php");

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $correo   = trim($_POST["correo"]);
    $password = $_POST["password"];

    try {
        $stmt = $conn->prepare(
            "SELECT u.id_usuario, u.nombre, u.correo, u.password, r.nombre AS rol
             FROM paw_rescue.usuario u
             JOIN paw_rescue.usuario_rol ur ON u.id_usuario = ur.id_usuario
             JOIN paw_rescue.rol r ON ur.id_rol = r.id_rol
             WHERE u.correo = :correo"
        );
        $stmt->execute([":correo" => $correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            throw new Exception("Correo o contraseña incorrectos");
        }

        if (!password_verify($password, $usuario["password"])) {
            throw new Exception("Correo o contraseña incorrectos");
        }

        // Crear sesión
        $_SESSION["id_usuario"] = $usuario["id_usuario"];
        $_SESSION["nombre"]     = $usuario["nombre"];
        $_SESSION["correo"]     = $usuario["correo"];
        $_SESSION["rol"]        = $usuario["rol"];

        // Redirección según rol (puedes ajustarlo)
        header("Location: index.php");
        exit;

    } catch (Exception $e) {
        $mensaje = "❌ " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Paw Rescue</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">

  <!-- CSS -->
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/login.css">
</head>

<body>

<!-- ================= NAVBAR (RESPETADO) ================= -->
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
             id="navbarDropdown" role="button"
             data-bs-toggle="dropdown">
            Adoptar
          </a>
          <ul class="dropdown-menu">
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
<!-- ================= FIN NAVBAR ================= -->

<div class="principal">
  <div class="contc">
    Inicio de Sesión
  </div>

  <div class="formulario">

    <?php if ($mensaje): ?>
      <div class="alert alert-danger text-center">
        <?= htmlspecialchars($mensaje) ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Correo</label>
        <input type="email" name="correo" class="form-control"
               placeholder="ejemplo@correo.com" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input type="password" name="password" class="form-control"
               placeholder="********" required>
      </div>

      <button type="submit" class="btn btn-dark w-100">
        Iniciar Sesión
      </button>
    </form>

    <div class="extra d-flex justify-content-between mt-3">
      <a href="#" class="text">Olvidé mi contraseña</a>
      <a class="text" href="registro.php">Registrarse</a>
    </div>
  </div>
</div>

<footer class="text-center mt-5">
  MURASAKI 2026. ©
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
