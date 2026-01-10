<?php
session_start();
include("../conexion.php");


if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$idUsuario = $_SESSION['id_usuario'];

/*
   VALIDAR CUESTIONARIO
 */
$sqlCuest = "
SELECT 1
FROM paw_rescue.cuestionario_adopcion
WHERE id_usuario = $1
";
$resCuest = pg_query_params($conexion, $sqlCuest, [$idUsuario]);

if (pg_num_rows($resCuest) === 0) {
    echo "
    <div class='container mt-5 alert alert-warning'>
         Debes contestar el cuestionario para continuar.
    </div>";
    exit;
}

/* VERIFICAR SI YA ADOPTÓ */
$sqlAdoptado = "
SELECT
    a.nombre AS mascota,
    ad.fecha
FROM paw_rescue.adopcion ad
JOIN paw_rescue.animal a ON a.id_animal = ad.id_animal
WHERE ad.id_usuario = $1
ORDER BY ad.fecha DESC
LIMIT 1
";
$resAdoptado = pg_query_params($conexion, $sqlAdoptado, [$idUsuario]);
$adopcion = pg_fetch_assoc($resAdoptado);
$yaAdopto = (bool)$adopcion;

/*
   OBTENER SOLICITUD ACTIVA  */
$hayProceso = false;
$proceso = null;

if (!$yaAdopto) {
    $sqlProceso = "
    SELECT
        s.id_solicitud,
        a.nombre AS mascota,
        ep.nombre AS estatus,
        s.fecha_solicitud
    FROM paw_rescue.solicitud_adopcion s
    JOIN paw_rescue.animal a ON a.id_animal = s.id_animal
    JOIN paw_rescue.estatus_proceso_adopcion ep ON ep.id_estatus = s.id_estatus
    WHERE s.id_usuario = $1
    ORDER BY s.fecha_solicitud DESC
    LIMIT 1
    ";
    $resProceso = pg_query_params($conexion, $sqlProceso, [$idUsuario]);
    $proceso = pg_fetch_assoc($resProceso);
    $hayProceso = (bool)$proceso;
}

/*
   PERIODO DE PRUEBA
 */
$resPrueba = null;

if ($hayProceso) {
    $sqlPrueba = "
    SELECT
        tc.nombre AS tipo_cita,
        ca.fecha,
        ca.hora
    FROM paw_rescue.cita_adopcion ca
    JOIN paw_rescue.tipo_cita tc ON tc.id_tipo = ca.id_tipo
    WHERE ca.id_solicitud = $1
      AND tc.nombre IN (
        'Inicio periodo de prueba',
        'Fin periodo de prueba'
      )
    ORDER BY ca.fecha
    ";
    $resPrueba = pg_query_params($conexion, $sqlPrueba, [$proceso['id_solicitud']]);
}

/*----- COMPATIBLES (SI NO HAY NADA)---- */
$resCompatibles = null;

if (!$yaAdopto && !$hayProceso) {
    $sqlCompatibles = "
    SELECT
        c.id_animal,
        c.nivel_compatibilidad,
        a.nombre,
        tm.nombre AS tamano,
        t.nombre AS temperamento
    FROM paw_rescue.compatibilidad_adopcion c
    JOIN paw_rescue.animal a ON a.id_animal = c.id_animal
    JOIN paw_rescue.tam tm ON tm.id_tam = a.id_tam
    JOIN paw_rescue.temperamento t ON t.id_temp = a.id_temp
    WHERE c.id_usuario = $1
      AND c.nivel_compatibilidad >= 70
    ORDER BY c.nivel_compatibilidad DESC
    ";
    $resCompatibles = pg_query_params($conexion, $sqlCompatibles, [$idUsuario]);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mi proceso de adopción</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include("navbar.php"); ?>

<div class="container mt-5">
<h2>🐾 Mi proceso de adopción</h2>

<!------ADOPCIÓN FINALIZADA ------ -->
<?php if ($yaAdopto): ?>

<div class="alert alert-success mt-4">
    🎉 <strong>¡Adopción completada!</strong><br>
    Has adoptado a <strong><?= htmlspecialchars($adopcion['mascota']) ?></strong><br>
    Fecha de adopción: <?= $adopcion['fecha'] ?>
</div>

<!-- ------ PROCESO ACTIVO ------ -->
<?php elseif ($hayProceso): ?>

<h4 class="mt-4">📋 Estado del proceso</h4>
<table class="table table-bordered">
<tr>
    <th>Mascota</th>
    <th>Estatus</th>
    <th>Inicio</th>
</tr>
<tr>
    <td><?= htmlspecialchars($proceso['mascota']) ?></td>
    <td><?= htmlspecialchars($proceso['estatus']) ?></td>
    <td><?= $proceso['fecha_solicitud'] ?></td>
</tr>
</table>

<h4 class="mt-4">🐕 Periodo de prueba</h4>

<?php if ($resPrueba && pg_num_rows($resPrueba) > 0): ?>

<table class="table table-bordered">
<tr>
    <th>Evento</th>
    <th>Fecha</th>
    <th>Hora</th>
</tr>

<?php while ($p = pg_fetch_assoc($resPrueba)): ?>
<tr>
    <td><?= $p['tipo_cita'] ?></td>
    <td><?= $p['fecha'] ?></td>
    <td><?= substr($p['hora'], 0, 5) ?></td>
</tr>
<?php endwhile; ?>
</table>

<div class="alert alert-info">
Durante el periodo de prueba el refugio puede realizar visitas de seguimiento.
</div>

<?php else: ?>

<div class="alert alert-secondary">
⏳ El periodo de prueba aún no ha sido programado.
</div>

<?php endif; ?>

<!-- ================= COMPATIBLES ================= -->
<?php else: ?>

<h4 class="mt-4">Mascotas compatibles</h4>

<table class="table table-bordered">
<tr>
    <th>Nombre</th>
    <th>Tamaño</th>
    <th>Temperamento</th>
    <th>Compatibilidad</th>
    <th></th>
</tr>

<?php if ($resCompatibles && pg_num_rows($resCompatibles) > 0): ?>
<?php while ($m = pg_fetch_assoc($resCompatibles)): ?>
<tr>
    <td><?= $m['nombre'] ?></td>
    <td><?= $m['tamano'] ?></td>
    <td><?= $m['temperamento'] ?></td>
    <td><?= $m['nivel_compatibilidad'] ?>%</td>
    <td>
        <form method="POST" action="iniciarAdopcion.php">
            <input type="hidden" name="id_animal" value="<?= $m['id_animal'] ?>">
            <button class="btn btn-primary btn-sm">Iniciar</button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="5" class="text-center">No hay mascotas compatibles</td>
</tr>
<?php endif; ?>

</table>

<?php endif; ?>

</div>
</body>
</html>
