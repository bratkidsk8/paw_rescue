<?php
include("../conexion.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* ================== VALIDAR ID ================== */
if (!isset($_GET['id'])) {
    die("Solicitud no válida");
}
$idSolicitud = (int)$_GET['id'];

/* ================== DATOS PRINCIPALES ================== */
$sql = "
SELECT
    s.id_solicitud,
    s.id_estatus,
    ep.nombre AS estatus_proceso,

    u.nombre,
    u.primer_apellido,
    u.segundo_apellido,

    a.nombre AS mascota
FROM paw_rescue.solicitud_adopcion s
JOIN paw_rescue.usuario u ON s.id_usuario = u.id_usuario
JOIN paw_rescue.animal a ON s.id_animal = a.id_animal
JOIN paw_rescue.estatus_proceso_adopcion ep ON ep.id_estatus = s.id_estatus
WHERE s.id_solicitud = $1
";

$res = pg_query_params($conexion, $sql, [$idSolicitud]);
if (!$res || pg_num_rows($res) === 0) {
    die("Solicitud no encontrada");
}
$data = pg_fetch_assoc($res);
$idEstatus = (int)$data['id_estatus'];

/* ================== CITAS ================== */
$sqlCitas = "
SELECT
    ca.id_cita,
    ca.fecha,
    ca.hora,
    tc.nombre AS tipo_cita,
    ec.nombre AS estatus_cita
FROM paw_rescue.cita_adopcion ca
JOIN paw_rescue.tipo_cita tc ON tc.id_tipo = ca.id_tipo
JOIN paw_rescue.estatus_cita ec ON ec.id_estatus = ca.id_estatus
WHERE ca.id_solicitud = $1
ORDER BY ca.fecha, ca.hora
";

$resCitas = pg_query_params($conexion, $sqlCitas, [$idSolicitud]);

pg_result_seek($resCitas, 0);

$existeFirma = false;
$firmaRealizada = false;

while ($c = pg_fetch_assoc($resCitas)) {
    if ($c['tipo_cita'] === 'Firmar adopcion') {
        $existeFirma = true;
        if ($c['estatus_cita'] === 'Realizada') {
            $firmaRealizada = true;
        }
    }
}

?>



<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Detalle solicitud</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<?php include("navbar.php"); ?>

<div class="container mt-4">

<h3>Detalle de solicitud</h3>

<div class="card mb-3">
<div class="card-body">
<p><b>Solicitante:</b>
<?= $data['nombre']." ".$data['primer_apellido']." ".$data['segundo_apellido']; ?>
</p>
<p><b>Mascota:</b> <?= $data['mascota'] ?></p>
<p><b>Estatus:</b>
<span class="badge bg-secondary"><?= $data['estatus_proceso'] ?></span>
</p>
</div>
</div>

<!-- ======================================================
FASE 1 - EVALUACIÓN INICIAL
====================================================== -->
<?php if ($idEstatus === 1): ?>
<div class="card mb-3">
<div class="card-body">
<h5>📝 Evaluación inicial</h5>

<form method="POST" action="evaluarSolicitud.php">
<input type="hidden" name="id_solicitud" value="<?= $idSolicitud ?>">

<div class="form-check">
<input type="radio" name="resultado" value="apto" required>
<label>Candidato apto</label>
</div>

<div class="form-check">
<input type="radio" name="resultado" value="no_apto">
<label>No apto</label>
</div>

<textarea name="observaciones" class="form-control mt-2" required></textarea>

<button class="btn btn-success mt-2">Guardar</button>
</form>
</div>
</div>
<?php endif; ?>

<!-- ======================================================
FASE 2 - VISITAS
====================================================== -->
<?php if (in_array($idEstatus, [2,4])): ?>

<div class="card mb-3">
<div class="card-body">
<h5>🏠 Visitas / Entrevistas</h5>

<?php
$visitasPendientes = false;
if ($resCitas && pg_num_rows($resCitas) > 0):
?>
<table class="table table-bordered">
<thead>
<tr>
<th>Tipo</th><th>Fecha</th><th>Hora</th><th>Estatus</th><th>Acción</th>
</tr>
</thead>
<tbody>

<?php while ($c = pg_fetch_assoc($resCitas)):
if (stripos($c['tipo_cita'], 'firma') !== false) continue;

if ($c['estatus_cita'] !== 'Realizada') {
    $visitasPendientes = true;
}
?>
<tr>
<td><?= $c['tipo_cita'] ?></td>
<td><?= $c['fecha'] ?></td>
<td><?= $c['hora'] ?></td>
<td><?= $c['estatus_cita'] ?></td>
<td>
<?php if ($c['estatus_cita'] !== 'Realizada'): ?>
<form method="POST" action="marcarCitaRealizada.php">
<input type="hidden" name="id_cita" value="<?= $c['id_cita'] ?>">
<input type="hidden" name="id_solicitud" value="<?= $idSolicitud ?>">
<button class="btn btn-sm btn-outline-success">Marcar realizada</button>
</form>
<?php else: ?>
✔
<?php endif; ?>
</td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
<?php else: ?>
<p class="text-warning">No hay visitas programadas.</p>
<?php endif; ?>

<a href="programaCita.php?id=<?= $idSolicitud ?>" class="btn btn-outline-primary">
Programar visita
</a>
</div>
</div>

<?php if (!$visitasPendientes && $idEstatus === 4): ?>
<div class="card mb-3">
<div class="card-body">
<h5>📝 Evaluación de visita</h5>

<form method="POST" action="evaluarVisita.php">
<input type="hidden" name="id_solicitud" value="<?= $idSolicitud ?>">

<input type="radio" name="resultado" value="apto" required>
Apto, pasar a periodo de prueba<br>

<input type="radio" name="resultado" value="no_apto">
No apto<br><br>

<textarea name="observaciones" class="form-control" required></textarea>

<button class="btn btn-success mt-2">Guardar</button>
</form>
</div>
</div>
<?php endif; ?>

<?php endif; ?>

<?php if ($idEstatus === 5): ?>
<!-- ======================================================
FASE 3 - PERIODO DE PRUEBA
====================================================== -->
<div class="card mb-3">
<div class="card-body">
<h5>🐾 Evaluación del periodo de prueba</h5>

<form method="POST" action="evaluarPeriodoPrueba.php">
<input type="hidden" name="id_solicitud" value="<?= $idSolicitud ?>">

<div class="form-check">
<input class="form-check-input" type="radio" name="resultado" value="apto" required>
<label class="form-check-label">Se adaptó correctamente</label>
</div>

<div class="form-check">
<input class="form-check-input" type="radio" name="resultado" value="no_apto">
<label class="form-check-label">No se adaptó</label>
</div>

<textarea name="observaciones" class="form-control mt-2" required></textarea>

<button class="btn btn-success mt-3">Guardar resultado</button>
</form>
</div>
</div>

<?php elseif ($idEstatus === 6): ?>

  <?php elseif ($idEstatus === 6): ?>
<div class="card mb-3">
<div class="card-body">
<h5>✍️ Firma de adopción</h5>

<?php if (!$existeFirma): ?>

    <!-- NO EXISTE CITA -->
    <p class="text-warning">No hay cita de firma programada.</p>
    <a href="programarFirma.php?id=<?= $idSolicitud ?>" class="btn btn-primary">
        Programar cita de firma
    </a>

<?php else: ?>

    <?php
    pg_result_seek($resCitas, 0);
    while ($c = pg_fetch_assoc($resCitas)):
        if ($c['tipo_cita'] !== 'Firmar adopcion') continue;
    ?>
    <table class="table table-bordered">
    <tr>
        <th>Fecha</th>
        <th>Hora</th>
        <th>Estatus</th>
        <th>Acción</th>
    </tr>
    <tr>
        <td><?= $c['fecha'] ?></td>
        <td><?= $c['hora'] ?></td>
        <td><?= $c['estatus_cita'] ?></td>
        <td>
            <?php if ($c['estatus_cita'] !== 'Realizada'): ?>
            <form method="POST" action="marcarFirmaRealizada.php">
                <input type="hidden" name="id_cita" value="<?= $c['id_cita'] ?>">
                <input type="hidden" name="id_solicitud" value="<?= $idSolicitud ?>">
                <button class="btn btn-success btn-sm">
                    Marcar como firmada
                </button>
            </form>
            <?php else: ?>
                ✔ Firmada
            <?php endif; ?>
        </td>
    </tr>
    </table>
    <?php endwhile; ?>

<?php endif; ?>

</div>
</div>
<?php endif; ?>
