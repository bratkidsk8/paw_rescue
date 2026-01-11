<?php
session_start();
include("../conexion.php");

/* ================= SEGURIDAD ================= */
if (
    !isset($_SESSION["id_usuario"]) ||
    $_SESSION["rol"] !== 'Veterinario'
) {
    header("Location: ../Usuario/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Consulta no especificada");
}

$id_consulta = (int) $_GET['id'];

/* ================= OBTENER VETERINARIO ================= */
$sqlVet = "
SELECT id_veterinario
FROM paw_rescue.veterinario
WHERE id_usuario = $1
AND activo = true
";

$resVet = pg_query_params($conexion, $sqlVet, [$_SESSION['id_usuario']]);

if (pg_num_rows($resVet) === 0) {
    die("Veterinario no válido");
}

$id_veterinario = (int) pg_fetch_result($resVet, 0, 'id_veterinario');

/* ================= CONSULTA PRINCIPAL ================= */
$sqlConsulta = "
SELECT
    c.id_consulta,
    c.fecha,
    c.hora,
    c.motivo,
    c.observaciones,
    ec.nombre AS estatus,

    a.id_animal,
    a.nombre AS animal,
    a.edad_aprox,
    a.foto,

    esp.nombre AS especie,
    r.nombre AS raza
FROM paw_rescue.consulta c
JOIN paw_rescue.animal a ON c.id_animal = a.id_animal
JOIN paw_rescue.especie esp ON a.id_esp = esp.id_esp
JOIN paw_rescue.raza r ON a.id_raza = r.id_raza
JOIN paw_rescue.estatus_consulta ec ON c.id_estatus = ec.id_estatus
WHERE c.id_consulta = $1
AND c.id_veterinario = $2
";

$resConsulta = pg_query_params(
    $conexion,
    $sqlConsulta,
    [$id_consulta, $id_veterinario]
);

if (pg_num_rows($resConsulta) === 0) {
    die("Consulta no encontrada");
}

/* ================= DATOS BASE ================= */
$consulta  = pg_fetch_assoc($resConsulta);
$id_animal = (int) $consulta['id_animal'];

/* ================= SALUD GENERAL ================= */
$sqlSalud = "
SELECT
    CASE
        WHEN COUNT(*) FILTER (
            WHERE est.nombre IN ('Activa', 'En tratamiento')
        ) = 0
        THEN true
        ELSE false
    END AS sano
FROM paw_rescue.enf_animal ea
JOIN paw_rescue.estado_enfermedad est
    ON ea.id_estado = est.id_estado
WHERE ea.id_animal = $1
";

$resSalud = pg_query_params($conexion, $sqlSalud, [$id_animal]);
$salud    = pg_fetch_assoc($resSalud);
$estaSano = ($salud['sano'] === 't');

/* ================= VACUNAS ================= */
$sqlVacunas = "
SELECT
    v.nombre AS vacuna,
    e.fecha_aplicacion,
    e.fecha_vencimiento,
    CASE
        WHEN e.fecha_aplicacion IS NULL THEN 'No aplicada'
        WHEN e.fecha_vencimiento < CURRENT_DATE THEN 'Vencida'
        ELSE 'Vigente'
    END AS estado
FROM paw_rescue.esquema_vacunacion e
JOIN paw_rescue.vacuna v ON e.id_vacuna = v.id_vacuna
WHERE e.id_animal = $1
ORDER BY v.nombre
";

$resVacunas = pg_query_params($conexion, $sqlVacunas, [$id_animal]);

$sqlEstadoVacunas = "
SELECT
    CASE
        WHEN COUNT(*) FILTER (
            WHERE fecha_aplicacion IS NULL
               OR fecha_vencimiento < CURRENT_DATE
        ) = 0
        THEN true
        ELSE false
    END AS al_corriente
FROM paw_rescue.esquema_vacunacion
WHERE id_animal = $1
";

$resEstadoVacunas = pg_query_params($conexion, $sqlEstadoVacunas, [$id_animal]);
$estadoVacunas    = pg_fetch_assoc($resEstadoVacunas);
$alCorriente      = ($estadoVacunas['al_corriente'] === 't');

/* ================= ENFERMEDADES ================= */
$sqlEnfermedades = "
SELECT
    enf.nombreenfer AS enfermedad,
    ea.fecha,
    est.nombre AS estado,
    ea.observaciones
FROM paw_rescue.enf_animal ea
JOIN paw_rescue.enfermedad enf
    ON ea.id_enf = enf.id_enf
JOIN paw_rescue.estado_enfermedad est
    ON ea.id_estado = est.id_estado
WHERE ea.id_animal = $1
ORDER BY ea.fecha DESC;

";

$resEnfermedades = pg_query_params(
    $conexion,
    $sqlEnfermedades,
    [$id_animal]
);


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Consulta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/consulta.css">
</head>
<body class="bg-light">

<?php include 'navbar.php'; ?>

<div class="container mt-4">

<!-- ================= ANIMAL ================= -->
<div class="card mb-4 shadow-sm">
  <div class="row g-0">
    <div class="col-md-4">
      <img src="<?= htmlspecialchars($consulta['foto']) ?>"
           class="img-fluid rounded-start"
           alt="Animal">
    </div>
    <div class="col-md-8">
      <div class="card-body">
        <h4 class="card-title"><?= htmlspecialchars($consulta['animal']) ?></h4>
        <p class="mb-1"><strong>Especie:</strong> <?= $consulta['especie'] ?></p>
        <p class="mb-1"><strong>Raza:</strong> <?= $consulta['raza'] ?></p>
        <p class="mb-1"><strong>Edad:</strong> <?= $consulta['edad_aprox'] ?> años</p>
      </div>
    </div>
  </div>
</div>

<!-- ================= CONSULTA ================= -->
<div class="card mb-4 shadow-sm">
  <div class="card-body">
    <h5 class="card-title">Consulta</h5>

    <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($consulta['fecha'])) ?></p>
    <p><strong>Hora:</strong> <?= substr($consulta['hora'], 0, 5) ?></p>
    <p><strong>Motivo:</strong> <?= htmlspecialchars($consulta['motivo']) ?></p>

    <p><strong>Estatus:</strong>
      <span class="badge bg-info"><?= $consulta['estatus'] ?></span>
    </p>

    <p><strong>Observaciones:</strong><br>
      <?= $consulta['observaciones'] ?: '<span class="text-muted">Sin observaciones</span>' ?>
    </p>
  </div>
</div>

<!-- ================= VACUNACIÓN ================= -->
<div class="card mt-4 shadow-sm">
  <div class="card-body">

    <h5 class="fw-bold mb-3">Esquema de Vacunación</h5>

    <!-- Estado general -->
    <p>
      <strong>Estado general:</strong>
      <span class="badge <?= $alCorriente ? 'bg-success' : 'bg-danger' ?>">
        <?= $alCorriente ? 'Al corriente' : 'No al corriente' ?>
      </span>
    </p>

    <hr>

    <!-- Tabla de vacunas -->
    <table class="table table-sm table-bordered align-middle">
      <thead class="table-light">
        <tr>
          <th>Vacuna</th>
          <th>Aplicación</th>
          <th>Vencimiento</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>

        <?php while ($v = pg_fetch_assoc($resVacunas)): ?>
        <tr>
          <td><?= htmlspecialchars($v['vacuna']) ?></td>

          <td>
            <?= $v['fecha_aplicacion']
              ? date('d/m/Y', strtotime($v['fecha_aplicacion']))
              : '—' ?>
          </td>

          <td>
            <?= $v['fecha_vencimiento']
              ? date('d/m/Y', strtotime($v['fecha_vencimiento']))
              : '—' ?>
          </td>

          <td>
            <span class="badge
              <?php
                echo match ($v['estado']) {
                  'Vigente' => 'bg-success',
                  'Vencida' => 'bg-danger',
                  default   => 'bg-secondary'
                };
              ?>">
              <?= $v['estado'] ?>
            </span>
          </td>
        </tr>
        <?php endwhile; ?>

      </tbody>
    </table>

  </div>
</div>

<!-- ================= ENFERMEDADES ================= -->
<?php if ($resEnfermedades && pg_num_rows($resEnfermedades) > 0): ?>
<div class="table-responsive">
  <table class="table table-sm table-bordered align-middle">
    <thead class="table-light">
      <tr>
        <th>Enfermedad</th>
        <th>Fecha</th>
        <th>Estado</th>
        <th>Observaciones</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($e = pg_fetch_assoc($resEnfermedades)): ?>
        <tr>
          <td><?= htmlspecialchars($e['enfermedad']) ?></td>
          <td><?= $e['fecha'] ? date('d/m/Y', strtotime($e['fecha'])) : '—' ?></td>
          <?php
            // Cambio de match a switch por compatibilidad
            switch ($e['estado']) {
              case 'Curada': $badge='bg-success'; break;
              case 'En tratamiento': $badge='bg-warning text-dark'; break;
              case 'Activa': $badge='bg-danger'; break;
              default: $badge='bg-secondary';
            }
          ?>
          <td><span class="badge <?= $badge ?>"><?= $e['estado'] ?></span></td>
          <td><?= $e['observaciones'] ? htmlspecialchars($e['observaciones']) : '<span class="text-muted">Sin observaciones</span>' ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php else: ?>
<p class="text-muted">No hay enfermedades registradas.</p>
<?php endif; ?>


<!-- ================= ACCIONES ================= -->
<div class="mt-5 mb-4 d-flex gap-2">
<a href="index.php" class="btn btn-secondary">
    Volver
</a>

<a href="consulta_editar.php?id=<?= isset($consulta['id_consulta']) ? $consulta['id_consulta'] : 0 ?>"
    class="btn btn-warning">
    Modificar consulta
</a>
<a href="enfermedad.php?id=<?= $consulta['id_animal'] ?>"
    class="btn btn-warning">
    Agregar enfermedad
</a>
<a href="vacuna.php?id=<?= $consulta['id_animal'] ?>"
    class="btn btn-warning">
    Agregar Vacuna
</a>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
