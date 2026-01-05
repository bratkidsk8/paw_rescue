<?php
session_start();

/* ===== VALIDAR ADMIN ===== */
$adminLogueado = isset($_SESSION['admin_id']);
$nombreAdmin   = $_SESSION['admin_nombre'] ?? '';

/* ===== PROTEGER PÁGINA ===== */
if (!$adminLogueado) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio | Admin</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- CSS -->
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<!-- ================= NAVBAR ================= -->
<?php include("navbar.php"); ?>


<!-- ================= HERO ================= -->
<section class="hero text-center p-5">
  <h1>
    Bienvenido<br>
    <?= htmlspecialchars($nombreAdmin) ?>
  </h1>
</section>

<!-- ================= FOOTER ================= -->
<footer class="text-center py-3">
  MURASAKI 2026 ©
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
