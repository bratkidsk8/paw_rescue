<?php
session_start();
include(__DIR__ . "/../../conexion.php");
pg_query($conexion, "SET search_path TO paw_rescue");

/* ===============================
   PARÁMETROS BASE 
================================ */
$id_raza = intval($_GET['id_raza'] ?? 0);
$especie = pg_escape_string($_GET['especie'] ?? '');

if ($id_raza === 0 || $especie === '') {
    die("Parámetros inválidos");
}

/* ===============================
   FILTROS
================================ */
$id_tam  = $_GET['tam'] ?? '';
$id_col  = $_GET['color'] ?? '';
$id_temp = $_GET['temp'] ?? '';

/* ===============================
   CONSULTA PRINCIPAL
================================ */
$sql = "
SELECT
    a.id_animal,
    a.nombre,
    a.edad_aprox,
    a.necesidades_especiales,
    r.nombre AS raza,
    t.nombre AS tamano,
    c.nombre AS color,
    temp.nombre AS temperamento,
    est.nombre AS estatus,
    img.url AS imagen
FROM animal a
JOIN raza r ON a.id_raza = r.id_raza
JOIN especie e ON a.id_esp = e.id_esp
LEFT JOIN tam t ON a.id_tam = t.id_tam
LEFT JOIN color c ON a.id_color = c.id_color
LEFT JOIN temperamento temp ON a.id_temp = temp.id_temp
LEFT JOIN estatus_adop est ON a.id_estatus = est.id_estatus
LEFT JOIN img_animal_principal img ON a.id_animal = img.id_animal
WHERE a.id_raza = $id_raza
AND e.nombre ILIKE '$especie'
";

if ($id_tam !== '')  $sql .= " AND a.id_tam = $id_tam";
if ($id_col !== '')  $sql .= " AND a.id_color = $id_col";
if ($id_temp !== '') $sql .= " AND a.id_temp = $id_temp";

$sql .= " ORDER BY a.nombre";

$resultado = pg_query($conexion, $sql);
if (!$resultado) die(pg_last_error($conexion));

/* ===============================
   SELECTS FILTROS
================================ */
$tamanios = pg_query($conexion, "SELECT id_tam, nombre FROM tam ORDER BY nombre");
$colores  = pg_query($conexion, "SELECT id_color, nombre FROM color ORDER BY nombre");
$temps    = pg_query($conexion, "SELECT id_temp, nombre FROM temperamento ORDER BY nombre");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mascotas disponibles</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<!-- NAVBAR -->
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
          <a class="nav-link dropdown-toggle" href="../adoptar.php"
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
              <a class="dropdown-item" href="../perfil.php">Mi perfil</a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item text-danger" href="../logout.php">
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

<section class="container my-5">

<h3 class="fw-bold mb-4">Mascotas disponibles</h3>

<div class="row g-4">

<?php if (pg_num_rows($resultado) === 0): ?>
<div class="col-12 text-center text-muted">
No hay mascotas disponibles
</div>
<?php endif; ?>

<?php while ($a = pg_fetch_assoc($resultado)): ?>

<?php
$requiere_cuidados = ($a['necesidades_especiales'] === 't');
$cuidados = [];

if ($requiere_cuidados) {
    $q = pg_query($conexion, "
        SELECT tc.nombre
        FROM animal_cuidado_especial ace
        JOIN tipo_cuidado_especial tc ON ace.id_cuidado = tc.id_cuidado
        WHERE ace.id_animal = {$a['id_animal']}
    ");

    while ($row = pg_fetch_assoc($q)) {
        $cuidados[] = $row['nombre'];
    }
}
?>

<div class="col-md-3">
<div class="card h-100 shadow-sm">

<img src="<?= $a['imagen'] ?: 'https://via.placeholder.com/300x200' ?>" class="card-img-top">

<div class="card-body">

<p><strong>Nombre:</strong> <?= htmlspecialchars($a['nombre']) ?></p>
<p><strong>Edad:</strong> <?= $a['edad_aprox'] ?> años</p>
<p><strong>Tamaño:</strong> <?= $a['tamano'] ?? 'No registrado' ?></p>
<p><strong>Color:</strong> <?= $a['color'] ?? 'No registrado' ?></p>
<p><strong>Temperamento:</strong> <?= $a['temperamento'] ?? 'No registrado' ?></p>

<p>
<strong>Cuidados especiales:</strong>
<?= $requiere_cuidados ? 'Sí' : 'No' ?>
</p>

<?php if ($requiere_cuidados && count($cuidados) > 0): ?>
<ul class="small ps-3">
<?php foreach ($cuidados as $c): ?>
<li><?= htmlspecialchars($c) ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<span class="badge <?= ($a['estatus'] === 'No adoptado') ? 'bg-success' : 'bg-danger' ?>">
<?= $a['estatus'] ?>
</span>

<div class="d-grid gap-2 mt-3">
<a href="detalleMascota.php?id=<?= $a['id_animal'] ?>" class="btn btn-outline-dark">Ver más</a>

<?php if ($a['estatus'] === 'No adoptado'): ?>
<a href="adoptarFormulario.php?id=<?= $a['id_animal'] ?>" class="btn btn-dark">Adoptar</a>
<?php endif; ?>
</div>

</div>
</div>
</div>

<?php endwhile; ?>

</div>
</section>

<footer class="text-center py-4 text-muted">
MURASAKI © 2026
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
