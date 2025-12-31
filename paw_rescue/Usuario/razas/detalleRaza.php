<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include(__DIR__ . "/../../conexion.php");
pg_query($conexion, "SET search_path TO paw_rescue");

/* ===============================
   PARÁMETROS BASE
================================ */
$id_raza = intval($_GET['id_raza'] ?? 0);
$especie = $_GET['especie'] ?? '';

if ($id_raza == 0 || $especie == '') {
    die("Parámetros inválidos");
}

/* ===============================
   FILTROS
================================ */
$id_tam  = $_GET['tam'] ?? '';
$id_col  = $_GET['color'] ?? '';
$id_temp = $_GET['temp'] ?? '';

/* ===============================
   CONSULTA
================================ */
$sql = "
SELECT
    a.id_animal,
    a.nombre,
    a.edad_aprox,
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

if ($id_tam != '')  $sql .= " AND a.id_tam = $id_tam";
if ($id_col != '')  $sql .= " AND a.id_color = $id_col";
if ($id_temp != '') $sql .= " AND a.id_temp = $id_temp";

$sql .= " ORDER BY a.nombre";

$resultado = pg_query($conexion, $sql);
if (!$resultado) die(pg_last_error($conexion));

/* ===============================
   SELECTS
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
    <a class="navbar-brand fw-bold" href="index.php">🐾 Paw Rescue</a>
    <a class="nav-link active ms-auto" href="adoptar.php">Adoptar</a>
  </div>
</nav>

<section class="container my-5">

<h3 class="fw-bold mb-4">Mascotas disponibles</h3>

<!-- ===============================
     FILTROS
================================ -->
<form method="GET" class="row g-3 mb-4">

  <input type="hidden" name="id_raza" value="<?= $id_raza ?>">
  <input type="hidden" name="especie" value="<?= htmlspecialchars($especie) ?>">

  <div class="col-md-3">
    <label class="form-label">Tamaño</label>
    <select name="tam" class="form-select">
      <option value="">Todos</option>
      <?php while ($t = pg_fetch_assoc($tamanios)): ?>
        <option value="<?= $t['id_tam'] ?>" <?= ($id_tam == $t['id_tam']) ? 'selected' : '' ?>>
          <?= $t['nombre'] ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <div class="col-md-3">
    <label class="form-label">Color</label>
    <select name="color" class="form-select">
      <option value="">Todos</option>
      <?php while ($c = pg_fetch_assoc($colores)): ?>
        <option value="<?= $c['id_color'] ?>" <?= ($id_col == $c['id_color']) ? 'selected' : '' ?>>
          <?= $c['nombre'] ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <div class="col-md-3">
    <label class="form-label">Temperamento</label>
    <select name="temp" class="form-select">
      <option value="">Todos</option>
      <?php while ($tp = pg_fetch_assoc($temps)): ?>
        <option value="<?= $tp['id_temp'] ?>" <?= ($id_temp == $tp['id_temp']) ? 'selected' : '' ?>>
          <?= $tp['nombre'] ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <div class="col-md-3 d-grid align-items-end">
    <button class="btn btn-dark mt-4">Aplicar filtros</button>
  </div>

</form>

<!-- ===============================
     GRID
================================ -->
<div class="row g-4">

<?php if (pg_num_rows($resultado) == 0): ?>
  <div class="col-12 text-center text-muted">
    No hay mascotas con los filtros seleccionados
  </div>
<?php endif; ?>

<?php while ($a = pg_fetch_assoc($resultado)): ?>
  <div class="col-md-3">
    <div class="card h-100 shadow-sm">

      <img src="<?= $a['imagen'] ?: 'https://via.placeholder.com/300x200' ?>"
           class="card-img-top">

      <div class="card-body">
        <p><b>Nombre:</b> <?= $a['nombre'] ?></p>
        <p><b>Edad aproximada:</b> <?= $a['edad_aprox'] ?> años</p>
        <p><b>Tamaño:</b> <?= $a['tamano'] ?></p>
        <p><b>Color:</b> <?= $a['color'] ?></p>
        <p><b>Temperamento:</b> <?= $a['temperamento'] ?></p>

        <span class="badge <?= ($a['estatus'] === 'Disponible') ? 'bg-success' : 'bg-danger' ?>">
          <?= $a['estatus'] ?>
        </span>

        <!-- BOTONES -->
        <div class="d-grid gap-2 mt-3">
          <a href="detalleMascota.php?id=<?= $a['id_animal'] ?>"
             class="btn btn-outline-dark">
            Ver más
          </a>

          <?php if ($a['estatus'] === 'Disponible'): ?>
            <a href="adoptarFormulario.php?id=<?= $a['id_animal'] ?>"
               class="btn btn-dark">
              Adoptar
            </a>
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

</body>
</html>
