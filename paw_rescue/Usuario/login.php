<?php
session_start();
include("../../paw_rescue/conexion.php");

$mensaje = "";

$registro_ok = isset($_GET['registro']) && $_GET['registro'] === 'ok';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $correo   = trim($_POST["correo"] ?? '');
    $password = $_POST["password"] ?? '';

    if (empty($correo) || empty($password)) {
        $mensaje = "❌ Correo y contraseña obligatorios";
    } else {

      $sql = "
      SELECT 
          u.id_usuario,
          u.nombre,
          u.correo,
          u.password,
          COALESCE(r.nombre, 'usuario') AS rol
      FROM paw_rescue.usuario u
      LEFT JOIN paw_rescue.usuario_rol ur ON u.id_usuario = ur.id_usuario
      LEFT JOIN paw_rescue.rol r ON ur.id_rol = r.id_rol
      WHERE u.correo = $1
      ";


        $result = pg_query_params($conexion, $sql, [$correo]);

        if (!$result || pg_num_rows($result) === 0) {
            $mensaje = "❌ Correo o contraseña incorrectos";
        } else {

            $usuario = pg_fetch_assoc($result);

            if (!password_verify($password, $usuario["password"])) {
                $mensaje = "❌ Correo o contraseña incorrectos";
            } else {

                $_SESSION["id_usuario"] = $usuario["id_usuario"];
                $_SESSION["nombre"]     = $usuario["nombre"];
                $_SESSION["correo"]     = $usuario["correo"];
                $_SESSION["rol"]        = $usuario["rol"];

                header("Location: index.php");
                exit;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login | Paw Rescue</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<!-- ================= NAVBAR ================= -->
<?php include 'navbar.php'; ?>

<div class="container mt-5">
  <div class="card shadow p-4 mx-auto" style="max-width: 400px;">

    <h3 class="text-center mb-3">Iniciar Sesión</h3>

    <!-- ALERT REGISTRO -->
    <?php if ($registro_ok): ?>
      <div class="alert alert-success alert-dismissible fade show text-center">
        ✅ Registro exitoso. Ahora inicia sesión.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <!-- ALERT ERROR -->
    <?php if ($mensaje): ?>
      <div class="alert alert-danger text-center">
        <?= htmlspecialchars($mensaje) ?>
      </div>
    <?php endif; ?>

    <form method="POST">

      <div class="mb-3">
        <label class="form-label">Correo</label>
        <input type="email" name="correo" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-dark w-100">
        Iniciar Sesión
      </button>

    </form>

    <div class="d-flex justify-content-between mt-3">
      <a href="#" class="text-decoration-none">Olvidé mi contraseña</a>
      <a href="registro.php" class="text-decoration-none">Registrarse</a>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
