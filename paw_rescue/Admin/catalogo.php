<?php
include("../../paw_rescue/conexion.php");

/* ================= FILTROS ================= */
$where = " WHERE ea.descripcion = 'Disponible' ";

if (!empty($_GET['especie'])) {
  $esp = pg_escape_string($conexion, $_GET['especie']);
  $where .= " AND ta.nombre = '$esp'";
}

if (!empty($_GET['raza'])) {
  $raza = pg_escape_string($conexion, $_GET['raza']);
  $where .= " AND r.nombre ILIKE '%$raza%'";
}

if (!empty($_GET['tamanio'])) {
  $where .= " AND tm.idtamanio = " . (int)$_GET['tamanio'];
}

if (!empty($_GET['color'])) {
  $where .= " AND c.idcolor = " . (int)$_GET['color'];
}

if (!empty($_GET['temperamento'])) {
  $where .= " AND at.idtemperamento = " . (int)$_GET['temperamento'];
}

/* ================= QUERY BASE ================= */
function obtenerMascotas($conexion, $extra = "") {
  $sql = "
  SELECT DISTINCT
    a.idanimal,
    a.nombre,
    a.edad,
    a.imagen,
    ta.nombre AS especie,
    r.nombre AS raza,
    tm.descripcion AS tamanio,
    c.nombre AS color,
    ea.descripcion AS estado,
    da.nivel AS demanda,
    CASE 
      WHEN EXISTS (
        SELECT 1 FROM animal_vacuna av WHERE av.idanimal = a.idanimal
      ) THEN 'Sí'
      ELSE 'No'
    END AS vacunado
  FROM animal a
  JOIN tipo_animal ta ON a.idtipo = ta.idtipo
  JOIN raza r ON a.idraza = r.idraza
  JOIN tamanio tm ON a.idtamanio = tm.idtamanio
  JOIN color c ON a.idcolor = c.idcolor
  JOIN estado_animal ea ON a.idestado = ea.idestado
  JOIN demanda_atencion da ON a.iddemanda = da.iddemanda
  LEFT JOIN animal_temperamento at ON a.idanimal = at.idanimal
  $extra
  ORDER BY a.idanimal
  ";
  return pg_query($conexion, $sql);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Catálogo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
  <nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="index.php">
        <img src="https://cdn-icons-png.flaticon.com/512/616/616408.png" alt="logo" width="30" class="me-2">
        Marca
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="info.php">Peticiones</a></li>
          <li class="nav-item"><a class="nav-link" href="adoptar.php">Reportes</a></li>
          <li class="nav-item"><a class="nav-link" href="agregar_mascota.php">Agregar mascotas</a></li>
          <li class="nav-item"><a class="nav-link" href="reporte.php">Reportar</a></li>
          <li class="nav-item"><a class="nav-link" href="catalogo.php">Catalogo</a></li>
        </ul>
        <a href="login.php" class="btn btn-outline-dark ms-3">Login</a>
      </div>
    </div>
  </nav>

<section class="aviso">
  <h2>Mascotas disponibles</h2>
</section>

<div class="container my-5">

<!-- ================= FILTROS ================= -->
<form method="GET" class="row g-3 mb-5">

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
    <select name="tamanio" class="form-select">
      <option value="">Tamaño</option>
      <?php
      $t = pg_query($conexion, "SELECT * FROM tamanio");
      while ($row = pg_fetch_assoc($t))
        echo "<option value='{$row['idtamanio']}'>{$row['descripcion']}</option>";
      ?>
    </select>
  </div>

  <div class="col-md-2">
    <select name="color" class="form-select">
      <option value="">Color</option>
      <?php
      $c = pg_query($conexion, "SELECT * FROM color");
      while ($row = pg_fetch_assoc($c))
        echo "<option value='{$row['idcolor']}'>{$row['nombre']}</option>";
      ?>
    </select>
  </div>

  <div class="col-md-2">
  <select name="temperamento" class="form-select">
    <option value="">Temperamento</option>
    <?php
    $tp = pg_query($conexion, "SELECT idtemperamento, descripcion FROM temperamento");
    while ($row = pg_fetch_assoc($tp)) {
      echo "<option value='{$row['idtemperamento']}'>{$row['descripcion']}</option>";
    }
    ?>
  </select>
</div>


  <div class="col-md-2">
    <button class="btn btn-dark w-100">Buscar</button>
  </div>

</form>

<!-- ================= RESULTADOS ================= -->
<div class="row g-4">

<?php
$result = obtenerMascotas($conexion, $where);
while ($m = pg_fetch_assoc($result)) {
?>

<div class="col-md-3">
  <div class="card h-100 shadow-sm">
    <img src="<?= $m['imagen'] ?>" class="card-img-top">

    <div class="card-body">
      <h5><?= $m['nombre'] ?></h5>
      <p class="mb-1"><b><?= $m['especie'] ?></b> · <?= $m['raza'] ?></p>
      <p class="mb-1">Edad: <?= $m['edad'] ?> años</p>
      <p class="mb-1">Tamaño: <?= $m['tamanio'] ?></p>
      <p class="mb-1">Color: <?= $m['color'] ?></p>
      <p class="mb-1">Vacunado: <?= $m['vacunado'] ?></p>
      <p class="mb-1">Atención: <?= $m['demanda'] ?></p>

      <span class="badge bg-success"><?= $m['estado'] ?></span>

      <a href="ficha_mascota.php?id=<?= $m['idanimal'] ?>"
         class="btn btn-dark w-100 mt-3">Ficha</a>
      <div class="d-flex gap-2 mt-2">
     <a href="editar_mascota.php?id=<?= $m['idanimal'] ?>"
     class="btn btn-warning w-50">Editar</a>

     <a href="eliminar_mascota.php?id=<?= $m['idanimal'] ?>"
     class="btn btn-danger w-50"
     onclick="return confirm('¿Seguro que deseas eliminar esta mascota?');">
     Eliminar
  </a>
</div>
    </div>
  </div>
</div>

<?php } ?>

</div>
</div>

<footer class="text-center py-3">
  Paw Rescue © 2026
</footer>

</body>
</html>
