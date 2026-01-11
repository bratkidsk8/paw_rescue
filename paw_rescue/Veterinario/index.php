<?php
session_start();

include("../conexion.php");

if (
    !isset($_SESSION["id_usuario"]) ||
    $_SESSION["rol"] !== 'Veterinario'
) {
    header("Location: ../Usuario/login.php");
    exit;
}

$sqlVet = "
SELECT id_veterinario
FROM paw_rescue.veterinario
WHERE id_usuario = $1
AND activo = true
";

$resVet = pg_query_params(
    $conexion,
    $sqlVet,
    [ (int) $_SESSION['id_usuario'] ]
);

if (!$resVet || pg_num_rows($resVet) === 0) {
    die("Veterinario no activo o no registrado");
}

$veterinario = pg_fetch_assoc($resVet);
$id_veterinario = $veterinario['id_veterinario'];

$sql = "
SELECT
    c.id_consulta,
    a.id_animal,
    a.nombre,
    a.edad_aprox,
    a.foto AS imagen,

    esp.nombre AS especie,
    r.nombre AS raza,

    col.nombre AS color,
    t.nombre AS tamano,
    temp.nombre AS temperamento,

    c.motivo,
    c.fecha,
    c.hora,
    ec.nombre AS estatus
FROM paw_rescue.consulta c
JOIN paw_rescue.animal a 
    ON c.id_animal = a.id_animal

LEFT JOIN paw_rescue.especie esp 
    ON a.id_esp = esp.id_esp

LEFT JOIN paw_rescue.raza r 
    ON a.id_raza = r.id_raza

LEFT JOIN paw_rescue.color col 
    ON a.id_color = col.id_color

LEFT JOIN paw_rescue.tam t 
    ON a.id_tam = t.id_tam

LEFT JOIN paw_rescue.temperamento temp 
    ON a.id_temp = temp.id_temp

JOIN paw_rescue.estatus_consulta ec 
    ON c.id_estatus = ec.id_estatus

WHERE c.id_veterinario = $1
ORDER BY c.fecha DESC, c.hora DESC;

";

$animales = pg_query_params($conexion, $sql, [$id_veterinario]);

if (!$animales) {
    die(pg_last_error($conexion));
}


?>



<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio | Paw Rescue</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- CSS -->
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/consulta.css">
</head>
<body class="bg-light">

<!-- Navbar -->
<?php include 'navbar.php'; ?>

<div class="container mt-4">
  <div class="row g-4">

    <?php if (pg_num_rows($animales) === 0): ?>
      <div class="col-12 text-center">
        <p class="text-muted">No hay animales registrados.</p>
      </div>
    <?php endif; ?>

    <?php while ($a = pg_fetch_assoc($animales)): ?>
<div class="col-md-12">
  <div class="card h-100 shadow-sm div_consulta">

    <!-- Imagen del animal -->
    <div class="img_cons">
      <img
        src="<?= $a['imagen'] ? htmlspecialchars($a['imagen']) : 'https://via.placeholder.com/300x200' ?>"
        class="card-img-top"
        style="height:200px; object-fit:cover;"
        alt="Imagen del animal">
    </div>

    <div class="card-body cons_cont">

      <!-- Nombre -->
      <h6 class="fw-bold">
        <?= htmlspecialchars($a['nombre']) ?>
      </h6>
      <p class="small mb-1">
        <strong>Especie:</strong>
        <?= $a['especie'] ?? 'N/D' ?>
      </p>

      <p class="small mb-1">
        <strong>Raza:</strong>
        <?= $a['raza'] ?? 'N/D' ?>
      </p>


      <!-- Datos del animal -->
      <p class="small mb-1">
        <strong>Edad:</strong>
        <?= $a['edad_aprox'] !== null ? $a['edad_aprox'].' años' : 'N/D' ?>
      </p>

      <p class="small mb-1">
        <strong>Tamaño:</strong>
        <?= $a['tamano'] ?? 'N/D' ?>
      </p>

      <p class="small mb-1">
        <strong>Color:</strong>
        <?= $a['color'] ?? 'N/D' ?>
      </p>

      <p class="small mb-1">
        <strong>Temperamento:</strong>
        <?= $a['temperamento'] ?? 'N/D' ?>
      </p>

      <!-- Datos de la consulta -->
      <hr>

      <p class="small mb-1">
        <strong>Motivo:</strong>
        <?= htmlspecialchars($a['motivo']) ?>
      </p>

      <p class="small mb-1">
        <strong>Fecha:</strong>
        <?= date('d/m/Y', strtotime($a['fecha'])) ?>
      </p>

      <p class="small mb-1">
        <strong>Hora:</strong>
        <?= substr($a['hora'], 0, 5) ?>
      </p>

      <!-- Estatus -->
      <span class="badge 
        <?= ($a['estatus'] === 'Programada' || $a['estatus'] === 'Confirmada')
            ? 'bg-success'
            : 'bg-secondary' ?>">
        <?= $a['estatus'] ?>
      </span>

      <!-- Acciones -->
      <div class="d-grid gap-2 mt-3">
        <a href="consulta.php?id=<?= $a['id_consulta'] ?>"
           class="btn btn-dark btn-sm">
          Ver consulta
        </a>
      </div>

    </div>
  </div>
</div>
<?php endwhile; ?>


  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
