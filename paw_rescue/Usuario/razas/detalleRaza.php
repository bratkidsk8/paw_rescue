<?php
session_start();
include(__DIR__ . "/../../conexion.php");
pg_query($conexion, "SET search_path TO paw_rescue");

/* =========================
   PARÁMETROS
========================= */
$id_raza = (int)($_GET['id_raza'] ?? 0);
$especie = $_GET['especie'] ?? '';

if ($id_raza === 0) {
    die("Raza no válida");
}

/* =========================
   FILTROS
========================= */
$where = "WHERE 1=1 AND a.id_raza = $id_raza";

$id_color = $_GET['color'] ?? '';
$id_tam   = $_GET['tam'] ?? '';
$id_temp  = $_GET['temp'] ?? '';
$id_est   = $_GET['estatus'] ?? '';

if ($id_color !== '') $where .= " AND a.id_color = " . (int)$id_color;
if ($id_tam !== '')   $where .= " AND a.id_tam = " . (int)$id_tam;
if ($id_temp !== '')  $where .= " AND a.id_temp = " . (int)$id_temp;
if ($id_est !== '')   $where .= " AND a.id_estatus = " . (int)$id_est;

/* =========================
   CONSULTA PRINCIPAL
========================= */
$sql = "
SELECT
    a.id_animal,
    a.nombre,
    a.edad_aprox,
    a.foto AS imagen,
    c.nombre AS color,
    t.nombre AS tamano,
    temp.nombre AS temperamento,
    est.nombre AS estatus
FROM animal a
LEFT JOIN color c ON a.id_color = c.id_color
LEFT JOIN tam t ON a.id_tam = t.id_tam
LEFT JOIN temperamento temp ON a.id_temp = temp.id_temp
LEFT JOIN estatus_adop est ON a.id_estatus = est.id_estatus
$where
ORDER BY a.nombre
";

$animales = pg_query($conexion, $sql);
if (!$animales) die(pg_last_error($conexion));

/* =========================
   SELECTS FILTROS
========================= */
$colores = pg_query($conexion, "SELECT id_color, nombre FROM color ORDER BY nombre");
$tamanos = pg_query($conexion, "SELECT id_tam, nombre FROM tam ORDER BY nombre");
$temps   = pg_query($conexion, "SELECT id_temp, nombre FROM temperamento ORDER BY nombre");
$estatus = pg_query($conexion, "SELECT id_estatus, nombre FROM estatus_adop ORDER BY nombre");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mascotas disponibles</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
       <nav class="navbar navbar-expand-lg bg-white shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="../index.php">
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

        <li class="nav-item"><a class="nav-link" href="../info.php">Acerca de</a></li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="adoptar.php"
             role="button" data-bs-toggle="dropdown">
            Adoptar
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="../adoptar.php">Ver mascotas</a></li>
            <li><a class="dropdown-item" href="../cuestionario.php">Cuestionario</a></li>
          </ul>
        </li>

        <li class="nav-item"><a class="nav-link" href="../donar.php">Donaciones</a></li>
        <li class="nav-item"><a class="nav-link" href="../reporte.php">Reportar</a></li>
        <li class="nav-item"><a class="nav-link" href="../contacto.php">Contacto</a></li>

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

<section class="container my-4">

<h3 class="fw-bold mb-4">Mascotas disponibles</h3>

<!-- ================= FILTROS ================= -->
<form method="GET" class="row g-3 mb-4">

<input type="hidden" name="id_raza" value="<?= $id_raza ?>">
<input type="hidden" name="especie" value="<?= htmlspecialchars($especie) ?>">

<div class="col-md-3">
<select name="color" class="form-select">
<option value="">Color</option>
<?php while ($c = pg_fetch_assoc($colores)): ?>
<option value="<?= $c['id_color'] ?>" <?= ($id_color == $c['id_color']) ? 'selected' : '' ?>>
<?= htmlspecialchars($c['nombre']) ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-3">
<select name="tam" class="form-select">
<option value="">Tamaño</option>
<?php while ($t = pg_fetch_assoc($tamanos)): ?>
<option value="<?= $t['id_tam'] ?>" <?= ($id_tam == $t['id_tam']) ? 'selected' : '' ?>>
<?= htmlspecialchars($t['nombre']) ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-3">
<select name="temp" class="form-select">
<option value="">Temperamento</option>
<?php while ($te = pg_fetch_assoc($temps)): ?>
<option value="<?= $te['id_temp'] ?>" <?= ($id_temp == $te['id_temp']) ? 'selected' : '' ?>>
<?= htmlspecialchars($te['nombre']) ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-3">
<select name="estatus" class="form-select">
<option value="">Estatus</option>
<?php while ($e = pg_fetch_assoc($estatus)): ?>
<option value="<?= $e['id_estatus'] ?>" <?= ($id_est == $e['id_estatus']) ? 'selected' : '' ?>>
<?= htmlspecialchars($e['nombre']) ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div class="col-12 text-end">
<button class="btn btn-dark">Filtrar</button>
<a href="detalleRaza.php?id_raza=<?= $id_raza ?>&especie=<?= $especie ?>" class="btn btn-outline-secondary">
Limpiar
</a>
</div>

</form>

<!-- ================= GRID ================= -->
<div class="row g-4">

<?php if (pg_num_rows($animales) === 0): ?>
<div class="col-12 text-center text-muted">
No hay mascotas con estos filtros
</div>
<?php endif; ?>

<?php while ($a = pg_fetch_assoc($animales)): ?>
<div class="col-md-3">
<div class="card h-100 shadow-sm">

<img src="<?= $a['imagen'] ?: 'https://via.placeholder.com/300x200' ?>"
     class="card-img-top" style="height:200px; object-fit:cover;">

<div class="card-body">

<h6 class="fw-bold"><?= htmlspecialchars($a['nombre']) ?></h6>

<p class="small mb-1"><strong>Edad:</strong> <?= $a['edad_aprox'] ?> años</p>
<p class="small mb-1"><strong>Tamaño:</strong> <?= $a['tamano'] ?? 'N/D' ?></p>
<p class="small mb-1"><strong>Color:</strong> <?= $a['color'] ?? 'N/D' ?></p>
<p class="small"><strong>Temperamento:</strong> <?= $a['temperamento'] ?? 'N/D' ?></p>

<span class="badge <?= ($a['estatus'] === 'No adoptado') ? 'bg-success' : 'bg-danger' ?>">
<?= $a['estatus'] ?>
</span>

<div class="d-grid gap-2 mt-3">
<a href="../galeria.php?id=<?= $a['id_animal'] ?>" class="btn btn-outline-dark btn-sm">
Ver galería
</a>
<a href="../detalleMascota.php?id=<?= $a['id_animal'] ?>" class="btn btn-dark btn-sm">
Ver información
</a>
</div>

</div>
</div>
</div>
<?php endwhile; ?>

</div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
