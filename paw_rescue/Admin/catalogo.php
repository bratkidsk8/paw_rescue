<?php
include("../../paw_rescue/conexion.php");

/* ===============================
   FILTROS
================================ */
$where = " WHERE 1=1 ";

if (!empty($_GET['especie'])) {
    $esp = pg_escape_string($conexion, $_GET['especie']);
    $where .= " AND e.nombre = '$esp'";
}

if (!empty($_GET['raza'])) {
    $raza = pg_escape_string($conexion, $_GET['raza']);
    $where .= " AND r.nombre ILIKE '%$raza%'";
}

if (!empty($_GET['tam'])) {
    $where .= " AND a.id_tam = " . (int)$_GET['tam'];
}

if (!empty($_GET['color'])) {
    $where .= " AND a.id_color = " . (int)$_GET['color'];
}

if (!empty($_GET['temperamento'])) {
    $where .= " AND a.id_temp = " . (int)$_GET['temperamento'];
}

/* ===============================
   CONSULTA PRINCIPAL
================================ */
$sql = "
SELECT DISTINCT
    a.id_animal,
    a.nombre,
    a.edad_aprox,
    e.nombre AS especie,
    r.nombre AS raza,
    t.nombre AS tam,
    c.nombre AS color,
    temp.nombre AS temperamento,
    ea.nombre AS estado,
    COALESCE(img.url, 'https://via.placeholder.com/300') AS imagen,
    CASE 
        WHEN EXISTS (
            SELECT 1 
            FROM paw_rescue.hist_vac hv
            WHERE hv.id_animal = a.id_animal
        ) THEN 'Sí'
        ELSE 'No'
    END AS vacunado
FROM paw_rescue.animal a
JOIN paw_rescue.especie e ON a.id_esp = e.id_esp
LEFT JOIN paw_rescue.raza r ON a.id_raza = r.id_raza
LEFT JOIN paw_rescue.tam t ON a.id_tam = t.id_tam
LEFT JOIN paw_rescue.color c ON a.id_color = c.id_color
LEFT JOIN paw_rescue.temperamento temp ON a.id_temp = temp.id_temp
JOIN paw_rescue.estado_animal ea ON a.id_estado = ea.id_estado
LEFT JOIN paw_rescue.img_animal_principal img ON img.id_animal = a.id_animal
$where
ORDER BY a.id_animal
";

$result = pg_query($conexion, $sql);
if (!$result) {
    die("Error en la consulta: " . pg_last_error($conexion));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Catálogo - Paw Rescue</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<!-- ===============================
     NAVBAR
================================ -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">Paw Rescue</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="catalogo.php">Catálogo</a></li>
        <li class="nav-item"><a class="nav-link" href="catalogo.php?especie=Perro">Perros</a></li>
        <li class="nav-item"><a class="nav-link" href="catalogo.php?especie=Gato">Gatos</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- ===============================
     FILTROS
================================ -->
<div class="container my-4">
<form method="GET" class="row g-3">

  <div class="col-md-2">
    <select name="especie" class="form-select">
      <option value="">Especie</option>
      <option value="Perro">Perro</option>
      <option value="Gato">Gato</option>
    </select>
  </div>

  <div class="col-md-2">
    <input type="text" name="raza" class="form-control" placeholder="Raza">
  </div>

  <div class="col-md-2">
    <select name="tam" class="form-select">
      <option value="">Tamaño</option>
      <?php
      $q = pg_query($conexion, "SELECT id_tam, nombre FROM paw_rescue.tam");
      while ($row = pg_fetch_assoc($q)) {
          echo "<option value='{$row['id_tam']}'>{$row['nombre']}</option>";
      }
      ?>
    </select>
  </div>

  <div class="col-md-2">
    <select name="color" class="form-select">
      <option value="">Color</option>
      <?php
      $q = pg_query($conexion, "SELECT id_color, nombre FROM paw_rescue.color");
      while ($row = pg_fetch_assoc($q)) {
          echo "<option value='{$row['id_color']}'>{$row['nombre']}</option>";
      }
      ?>
    </select>
  </div>

  <div class="col-md-2">
    <select name="temperamento" class="form-select">
      <option value="">Temperamento</option>
      <?php
      $q = pg_query($conexion, "SELECT id_temp, nombre FROM paw_rescue.temperamento");
      while ($row = pg_fetch_assoc($q)) {
          echo "<option value='{$row['id_temp']}'>{$row['nombre']}</option>";
      }
      ?>
    </select>
  </div>

  <div class="col-md-2">
    <button class="btn btn-dark w-100">Buscar</button>
  </div>

</form>
</div>

<!-- ===============================
     RESULTADOS
================================ -->
<div class="container">
<div class="row g-4">

<?php if (pg_num_rows($result) == 0): ?>
  <div class="col-12 text-center text-muted">
    No se encontraron resultados
  </div>
<?php endif; ?>

<?php while ($m = pg_fetch_assoc($result)) { ?>
<div class="col-md-3">
  <div class="card h-100 shadow-sm">

    <img src="<?= $m['imagen'] ?>" class="card-img-top">

    <div class="card-body">
      <h5><?= $m['nombre'] ?></h5>
      <p><?= $m['especie'] ?> · <?= $m['raza'] ?></p>
      <p>Edad: <?= $m['edad_aprox'] ?> años</p>
      <p>Tamaño: <?= $m['tam'] ?></p>
      <p>Color: <?= $m['color'] ?></p>
      <p>Temperamento: <?= $m['temperamento'] ?></p>
      <p>Vacunado: <?= $m['vacunado'] ?></p>

      <span class="badge bg-success"><?= $m['estado'] ?></span>

      <a href="ficha.php?id=<?= $m['id_animal'] ?>" class="btn btn-dark w-100 mt-2">
        Ver ficha
      </a>
    </div>

  </div>
</div>
<?php } ?>

</div>
</div>

<footer class="text-center py-4">
  Paw Rescue © 2026
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
