<?php
include("../../paw_rescue/conexion.php");
pg_query($conexion, "SET search_path TO paw_rescue");

/* ================= FILTROS ================= */
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

/* ================= CONSULTA ================= */
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
            SELECT 1 FROM hist_vac hv
            WHERE hv.id_animal = a.id_animal
        ) THEN 'Sí'
        ELSE 'No'
    END AS vacunado
FROM animal a
JOIN especie e ON a.id_esp = e.id_esp
LEFT JOIN raza r ON a.id_raza = r.id_raza
LEFT JOIN tam t ON a.id_tam = t.id_tam
LEFT JOIN color c ON a.id_color = c.id_color
LEFT JOIN temperamento temp ON a.id_temp = temp.id_temp
JOIN estado_animal ea ON a.id_estado = ea.id_estado
LEFT JOIN img_animal_principal img ON img.id_animal = a.id_animal
$where
ORDER BY a.id_animal
";

$result = pg_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Catálogo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm">
<div class="container-fluid">
<a class="navbar-brand fw-bold" href="index.php">Paw Rescue</a>
<div class="collapse navbar-collapse justify-content-end">
<ul class="navbar-nav">
<li class="nav-item"><a class="nav-link" href="catalogo.php">Catálogo</a></li>
<li class="nav-item"><a class="nav-link" href="agregar_mascota.php">Agregar</a></li>
</ul>
</div>
</div>
</nav>

<!-- FILTROS -->
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
$t = pg_query($conexion, "SELECT id_tam, nombre FROM tam");
while ($row = pg_fetch_assoc($t)) {
    echo "<option value='{$row['id_tam']}'>{$row['nombre']}</option>";
}
?>
</select>
</div>

<div class="col-md-2">
<select name="color" class="form-select">
<option value="">Color</option>
<?php
$c = pg_query($conexion, "SELECT id_color, nombre FROM color");
while ($row = pg_fetch_assoc($c)) {
    echo "<option value='{$row['id_color']}'>{$row['nombre']}</option>";
}
?>
</select>
</div>

<div class="col-md-2">
<select name="temperamento" class="form-select">
<option value="">Temperamento</option>
<?php
$temp = pg_query($conexion, "SELECT id_temp, nombre FROM temperamento");
while ($row = pg_fetch_assoc($temp)) {
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

<!-- RESULTADOS -->
<div class="container">
<div class="row g-4">

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

<a href="ficha.php?id=<?= $m['id_animal'] ?>" class="btn btn-dark w-100 mt-2">Ficha</a>

<div class="d-flex gap-2 mt-2">
<a href="editar_mascota.php?id=<?= $m['id_animal'] ?>" class="btn btn-warning w-50">Editar</a>
<a href="eliminarMascota.php?id=<?= $m['id_animal'] ?>" 
   class="btn btn-danger w-50"
   onclick="return confirm('¿Eliminar mascota?')">Eliminar</a>
</div>

</div>
</div>
</div>
<?php } ?>

</div>
</div>

</body>
</html>
