<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$idUsuario = $_SESSION['id_usuario'];

/* ===============================
   VALIDAR CUESTIONARIO
================================= */
$sqlCuest = "SELECT 1 FROM paw_rescue.cuestionario_adopcion WHERE id_usuario = $1";
$resCuest = pg_query_params($conexion, $sqlCuest, [$idUsuario]);

if (pg_num_rows($resCuest) === 0) {
    echo "<div class='container mt-5 alert alert-warning'>
            ⚠️ Debes contestar el cuestionario para continuar.
          </div>";
    exit;
}

/* ===============================
   PROCESO / ADOPCIÓN
================================= */
$sqlProceso = "
    SELECT
        s.id_solicitud,
        s.id_animal,
        a.nombre AS mascota,
        a.foto,
        e.nombre AS estatus,
        s.fecha_solicitud
    FROM paw_rescue.solicitud_adopcion s
    JOIN paw_rescue.animal a ON a.id_animal = s.id_animal
    JOIN paw_rescue.estatus_proceso_adopcion e ON e.id_estatus = s.id_estatus
    WHERE s.id_usuario = $1
    ORDER BY s.fecha_solicitud DESC
    LIMIT 1
";
$resProceso = pg_query_params($conexion, $sqlProceso, [$idUsuario]);
$proceso = pg_fetch_assoc($resProceso);

$hayProceso   = (bool)$proceso;
$adopcionOk   = $hayProceso && $proceso['estatus'] === 'Aprobada';

/* ===============================
   COMPATIBLES SOLO SI NO HAY PROCESO
================================= */
$resCompatibles = null;

if (!$hayProceso) {
    $sqlCompatibles = "
        SELECT
            c.id_animal,
            c.nivel_compatibilidad,
            a.nombre,
            tm.nombre AS tamano,
            t.nombre AS temperamento,
            a.foto
        FROM paw_rescue.compatibilidad_adopcion c
        JOIN paw_rescue.animal a ON a.id_animal = c.id_animal
        JOIN paw_rescue.tam tm ON tm.id_tam = a.id_tam
        JOIN paw_rescue.temperamento t ON t.id_temp = a.id_temp
        WHERE c.id_usuario = $1
          AND c.nivel_compatibilidad >= 70
          AND a.id_estatus = (
              SELECT id_estatus FROM paw_rescue.estatus_adop WHERE nombre = 'Disponible'
          )
        ORDER BY c.nivel_compatibilidad DESC
    ";
    $resCompatibles = pg_query_params($conexion, $sqlCompatibles, [$idUsuario]);
}

    $sqlSeguimiento = "
    SELECT
        tc.nombre AS tipo_cita,
        ec.nombre AS estatus_cita,
        sa.fecha,
        sa.hora,
        r.nombre AS refugio,
        r.calle,
        r.codigo_postal,
        sa.es_candidato,
        sa.fecha_inicio_prueba,
        sa.fecha_fin_prueba,
        sa.aprobada
    FROM paw_rescue.solicitud_adopcion s
    JOIN paw_rescue.seguimiento_adopcion sa ON sa.id_solicitud = s.id_solicitud
    JOIN paw_rescue.tipo_cita tc ON tc.id_tipo = sa.id_tipo_cita
    JOIN paw_rescue.estatus_cita ec ON ec.id_estatus = sa.id_estatus_cita
    JOIN paw_rescue.animal a ON a.id_animal = s.id_animal
    JOIN paw_rescue.refugio r ON r.id_ref = a.id_ref
    WHERE s.id_usuario = $1
    ORDER BY sa.fecha DESC
    ";

    $resSeguimiento = pg_query_params($conexion, $sqlSeguimiento, [$idUsuario]);

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Proceso de adopción</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">

<h2 class="mb-4">🐾 Mi proceso de adopción</h2>

<?php if ($hayProceso): ?>

<!-- ===============================
     TABLA 1 · ESTADO GENERAL
================================= -->
<h4 class="mt-4">📋 Estado del proceso</h4>

<table class="table table-bordered">
<thead class="table-light">
<tr>
  <th>Mascota</th>
  <th>Estatus actual</th>
  <th>Fecha inicio</th>
</tr>
</thead>
<tbody>
<tr>
  <td><?= htmlspecialchars($proceso['mascota']) ?></td>
  <td><?= htmlspecialchars($proceso['estatus']) ?></td>
  <td><?= $proceso['fecha_solicitud'] ?></td>
</tr>
</tbody>
</table>

<!-- ===============================
     TABLA 2 · CITAS Y VISITAS
================================= -->
<h4 class="mt-5">📅 Entrevistas y visitas</h4>

<?php if ($resSeguimiento && pg_num_rows($resSeguimiento) > 0): ?>
<table class="table table-bordered">
<thead class="table-light">
<tr>
  <th>Tipo</th>
  <th>Fecha</th>
  <th>Hora</th>
  <th>Albergue</th>
  <th>Dirección</th>
  <th>Estatus cita</th>
</tr>
</thead>
<tbody>

<?php
pg_result_seek($resSeguimiento, 0);
while ($row = pg_fetch_assoc($resSeguimiento)):
?>
<tr>
  <td><?= htmlspecialchars($row['tipo_cita']) ?></td>
  <td><?= $row['fecha'] ?></td>
  <td><?= substr($row['hora'], 0, 5) ?></td>
  <td><?= htmlspecialchars($row['refugio']) ?></td>
  <td><?= htmlspecialchars($row['calle']) ?>, CP <?= $row['codigo_postal'] ?></td>
  <td><?= htmlspecialchars($row['estatus_cita']) ?></td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
<?php else: ?>
<div class="alert alert-info">No hay citas registradas.</div>
<?php endif; ?>

<!-- ===============================
     TABLA 3 · PRUEBA DE ADOPCIÓN
================================= -->
<h4 class="mt-5">🐕 Periodo de prueba (15 días)</h4>

<table class="table table-bordered">
<thead class="table-light">
<tr>
  <th>Candidato</th>
  <th>Inicio</th>
  <th>Fin</th>
  <th>Resultado</th>
</tr>
</thead>
<tbody>

<?php
pg_result_seek($resSeguimiento, 0);
$pruebaMostrada = false;

while ($row = pg_fetch_assoc($resSeguimiento)):
  if ($row['fecha_inicio_prueba']):
    $pruebaMostrada = true;
?>
<tr>
  <td>
    <?= $row['es_candidato'] === 't'
      ? '<span class="badge bg-success">Sí</span>'
      : '<span class="badge bg-danger">No</span>' ?>
  </td>
  <td><?= $row['fecha_inicio_prueba'] ?></td>
  <td><?= $row['fecha_fin_prueba'] ?></td>
  <td>
    <?php
      if ($row['aprobada'] === 't') {
        echo '<span class="badge bg-success">Aprobada</span>';
      } elseif ($row['aprobada'] === 'f') {
        echo '<span class="badge bg-danger">No aprobada</span>';
      } else {
        echo 'Pendiente';
      }
    ?>
  </td>
</tr>
<?php
  endif;
endwhile;

if (!$pruebaMostrada):
?>
<tr>
  <td colspan="4" class="text-center">Aún no inicia el periodo de prueba</td>
</tr>
<?php endif; ?>

</tbody>
</table>

<!-- ===============================
     TABLA 4 · ADOPCIÓN FINAL
================================= -->
<?php if ($adopcionOk): ?>
<h4 class="mt-5">🎉 Adopción finalizada</h4>

<table class="table table-success table-bordered">
<thead>
<tr>
  <th>Mascota</th>
  <th>Resultado</th>
  <th>Fecha</th>
</tr>
</thead>
<tbody>
<tr>
  <td><?= htmlspecialchars($proceso['mascota']) ?></td>
  <td>Adopción aprobada</td>
  <td><?= $proceso['fecha_solicitud'] ?></td>
</tr>
</tbody>
</table>
<?php endif; ?>

<?php else: ?>

<!-- ===============================
     TABLA 5 · COMPATIBLES
================================= -->
<h4 class="mt-4">🐶 Mascotas compatibles</h4>

<table class="table table-bordered">
<thead class="table-light">
<tr>
  <th>Nombre</th>
  <th>Tamaño</th>
  <th>Temperamento</th>
  <th>Compatibilidad</th>
  <th>Acción</th>
</tr>
</thead>
<tbody>

<?php if ($resCompatibles && pg_num_rows($resCompatibles) > 0): ?>
<?php while ($m = pg_fetch_assoc($resCompatibles)): ?>
<tr>
  <td><?= htmlspecialchars($m['nombre']) ?></td>
  <td><?= $m['tamano'] ?></td>
  <td><?= $m['temperamento'] ?></td>
  <td><?= $m['nivel_compatibilidad'] ?>%</td>
  <td>
    <form method="POST" action="iniciarAdopcion.php">
      <input type="hidden" name="id_animal" value="<?= $m['id_animal'] ?>">
      <button class="btn btn-sm btn-primary">Iniciar</button>
    </form>
  </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
  <td colspan="5" class="text-center">No hay mascotas compatibles</td>
</tr>
<?php endif; ?>

</tbody>
</table>

<?php endif; ?>

</div>

</body>
</html>
