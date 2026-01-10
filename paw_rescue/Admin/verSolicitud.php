<?php
include("../conexion.php");
ini_set('display_errors', 1);
error_reporting(E_ALL);

/* ===== VALIDAR ID ===== */
if (!isset($_GET['id'])) {
    die("Solicitud no válida");
}
$idSolicitud = (int)$_GET['id'];

/* ===== DATOS PRINCIPALES ===== */
$sql = "
SELECT s.id_solicitud, s.id_estatus, ep.nombre AS estatus_proceso,
       u.nombre, u.primer_apellido, u.segundo_apellido,
       a.nombre AS mascota
FROM paw_rescue.solicitud_adopcion s
JOIN paw_rescue.usuario u ON s.id_usuario = u.id_usuario
JOIN paw_rescue.animal a ON s.id_animal = a.id_animal
JOIN paw_rescue.estatus_proceso_adopcion ep ON ep.id_estatus = s.id_estatus
WHERE s.id_solicitud = $1
";
$res = pg_query_params($conexion, $sql, [$idSolicitud]);
$data = pg_fetch_assoc($res);
$idEstatus = (int)$data['id_estatus'];

/* ===== CITAS ===== */
$sqlCitas = "
SELECT ca.id_cita, ca.fecha, ca.hora,
       tc.nombre AS tipo_cita, ec.nombre AS estatus_cita
FROM paw_rescue.cita_adopcion ca
JOIN paw_rescue.tipo_cita tc ON tc.id_tipo = ca.id_tipo
JOIN paw_rescue.estatus_cita ec ON ec.id_estatus = ca.id_estatus
WHERE ca.id_solicitud = $1
ORDER BY ca.fecha
";
$resCitas = pg_query_params($conexion, $sqlCitas, [$idSolicitud]);

$visitas = [];
$firma = [];
$inicioPrueba = null;
$finPrueba = null;
$visitaSeguimiento = null;

while ($c = pg_fetch_assoc($resCitas)) {
    $tipo = strtolower($c['tipo_cita']);

    if (strpos($tipo, 'firma') !== false) {
        $firma[] = $c;
    } elseif (strpos($tipo, 'inicio') !== false) {
        $inicioPrueba = $c;
    } elseif (strpos($tipo, 'fin') !== false) {
        $finPrueba = $c;
    } elseif (strpos($tipo, 'seguimiento') !== false) {
        $visitaSeguimiento = $c;
    } else {
        $visitas[] = $c;
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

<h3>Solicitud de adopción</h3>

<div class="card mb-3">
<div class="card-body">
<b>Solicitante:</b> <?= $data['nombre']." ".$data['primer_apellido']." ".$data['segundo_apellido'] ?><br>
<b>Mascota:</b> <?= $data['mascota'] ?><br>
<b>Estatus:</b> <?= $data['estatus_proceso'] ?><br><br>

<a href="verCuestionario.php?id=<?= $idSolicitud ?>" class="btn btn-outline-secondary btn-sm">
📄 Ver cuestionario
</a>
</div>
</div>

<!-- ================= FASE 1 ================= -->
<div class="card mb-4 border-primary">
<div class="card-header fw-bold">FASE 1 · Evaluación inicial</div>
<div class="card-body">

<?php if ($idEstatus == 1): ?>
<form method="POST" action="evaluarSolicitud.php">
<input type="hidden" name="id_solicitud" value="<?= $idSolicitud ?>">
<input type="radio" name="resultado" value="apto" required> Apto<br>
<input type="radio" name="resultado" value="no_apto"> No apto<br>
<textarea name="observaciones" class="form-control mt-2" required></textarea>
<button class="btn btn-success mt-3">Guardar y avanzar</button>
</form>
<?php else: ?>
<p class="text-muted">✔ Evaluación completada</p>
<?php endif; ?>

</div>
</div>

<!-- ================= FASE 2 ================= -->
<!-- ================= FASE 2 ================= -->
<div class="card mb-4 border-info">
<div class="card-header fw-bold">FASE 2 · Visitas</div>
<div class="card-body">

<?php if (count($visitas) == 0): ?>
    <p>No hay visitas.</p>
<?php else: ?>

<table class="table table-bordered align-middle">
<thead>
<tr>
    <th>Tipo</th>
    <th>Fecha</th>
    <th>Hora</th>
    <th>Estatus</th>
    <th>Acción</th>
</tr>
</thead>
<tbody>

<?php foreach ($visitas as $v): ?>
<tr>
    <td><?= htmlspecialchars($v['tipo_cita']) ?></td>
    <td><?= $v['fecha'] ?></td>
    <td><?= $v['hora'] ?></td>
    <td><?= htmlspecialchars($v['estatus_cita']) ?></td>
    <td>

    <?php if ($v['estatus_cita'] !== 'Realizada'): ?>

        <!-- ASISTIÓ -->
        <form method="POST" action="marcarCitaRealizada.php" class="d-inline">
            <input type="hidden" name="id_cita" value="<?= $v['id_cita'] ?>">
            <input type="hidden" name="id_solicitud" value="<?= $idSolicitud ?>">
            <button class="btn btn-sm btn-success">
                 Asistió
            </button>
        </form>

        <!-- NO ASISTIÓ -->
        <form method="POST" action="marcarNoAsistio.php" class="d-inline">
            <input type="hidden" name="id_cita" value="<?= $v['id_cita'] ?>">
            <input type="hidden" name="id_solicitud" value="<?= $idSolicitud ?>">
            <button class="btn btn-sm btn-danger"
                onclick="return confirm('¿El solicitante NO asistió? Esto cancelará la solicitud.')">
                 No asistió
            </button>
        </form>

    <?php else: ?>
        ✔
    <?php endif; ?>

    </td>
</tr>
<?php endforeach; ?>

</tbody>
</table>
<?php endif; ?>

<a href="programaCita.php?id=<?= $idSolicitud ?>" class="btn btn-outline-primary mt-2">
 Programar visita
</a>

<form method="POST" action="avanzarPeriodoPrueba.php" class="mt-3">
    <input type="hidden" name="id_solicitud" value="<?= $idSolicitud ?>">
    <button class="btn btn-warning">
         Avanzar a periodo de prueba
    </button>
</form>

</div>
</div>


<!-- ================= FASE 3 · PERIODO DE PRUEBA ================= -->
<div class="card mb-4 border-warning">
<div class="card-header fw-bold">FASE 3 · Periodo de prueba</div>
<div class="card-body">

<table class="table table-bordered text-center">
<thead class="table-light">
<tr>
<th>Evento</th>
<th>Fecha</th>
<th>Hora</th>
</tr>
</thead>
<tbody>

<tr>
<td><b>Inicio del periodo</b></td>
<td><?= $inicioPrueba ? $inicioPrueba['fecha'] : 'No programado' ?></td>
<td><?= $inicioPrueba ? $inicioPrueba['hora'] : '—' ?></td>
</tr>

<tr>
<td><b>Fin del periodo</b></td>
<td><?= $finPrueba ? $finPrueba['fecha'] : 'No programado' ?></td>
<td><?= $finPrueba ? $finPrueba['hora'] : '—' ?></td>
</tr>

<tr>
<td><b>Visita de revisión</b></td>
<td><?= $visitaSeguimiento ? $visitaSeguimiento['fecha'] : 'No programada' ?></td>
<td><?= $visitaSeguimiento ? $visitaSeguimiento['hora'] : '—' ?></td>
</tr>

</tbody>
</table>

<?php if (!$inicioPrueba || !$finPrueba): ?>
<a href="programarPrueba.php?id=<?= $idSolicitud ?>" class="btn btn-primary">
📅 Programar periodo de prueba
</a>
<hr>
<?php endif; ?>

<h6 class="fw-bold mt-3">Resultado del periodo de prueba</h6>

<form method="POST" action="evaluarPeriodoPrueba.php">
<input type="hidden" name="id_solicitud" value="<?= $idSolicitud ?>">

<div class="form-check">
<input class="form-check-input" type="radio" name="resultado" value="apto" required>
<label class="form-check-label">
✅ La mascota se adaptó al entorno
</label>
</div>

<div class="form-check">
<input class="form-check-input" type="radio" name="resultado" value="no_apto">
<label class="form-check-label">
❌ La mascota NO se adaptó (detener adopción)
</label>
</div>

<textarea name="observaciones" class="form-control mt-3"
placeholder="Observaciones del periodo de prueba" required></textarea>

<button class="btn btn-success mt-3">
💾 Guardar resultado
</button>
</form>

<hr>

<form method="POST" action="finalizarAdopcion.php">
    <input type="hidden" name="id_solicitud" value="<?= $idSolicitud ?>">
    <button class="btn btn-success w-100">
        🐾 Confirmar adopción y firmar
    </button>
</form>


</div>
</div>
