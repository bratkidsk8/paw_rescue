<?php
include("../conexion.php");

/* ================= VALIDAR ID ================= */
if (!isset($_GET['id'])) {
    die("Solicitud no válida");
}

$idSolicitud = (int) $_GET['id'];

/* ================= OBTENER SOLICITUD ================= */
$sqlSolicitud = "
SELECT s.id_solicitud, s.id_estatus, m.nombre AS mascota
FROM solicitud_adopcion s
JOIN mascota m ON s.id_mascota = m.id_mascota
WHERE s.id_solicitud = $1
";

$resSolicitud = pg_query_params($conexion, $sqlSolicitud, [$idSolicitud]);

if (!$resSolicitud || pg_num_rows($resSolicitud) === 0) {
    die("Solicitud no encontrada");
}

$sol = pg_fetch_assoc($resSolicitud);

/* ================= VALIDAR FASE ================= */
if ((int)$sol['id_estatus'] !== 4) {
    die("Esta solicitud no se encuentra en la fase de firma");
}

/* ================= GUARDAR CITA ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fecha = $_POST['fecha'];
    $hora  = $_POST['hora'];
    $obs   = trim($_POST['observaciones']);

    $sqlInsert = "
    INSERT INTO cita_adopcion
    (id_solicitud, tipo_cita, fecha, hora, estatus_cita, observaciones)
    VALUES ($1, 'Firma de adopción', $2, $3, 'Programada', $4)
    ";

    $resInsert = pg_query_params($conexion, $sqlInsert, [
        $idSolicitud,
        $fecha,
        $hora,
        $obs
    ]);

    if ($resInsert) {
        header("Location: verSolicitud.php?id=$idSolicitud");
        exit;
    } else {
        $error = "Error al programar la cita";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Programar firma</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container mt-5">

  <div class="card">
    <div class="card-body">
      <h4>✍️ Programar firma de adopción</h4>
      <p><b>Mascota:</b> <?= htmlspecialchars($sol['mascota']) ?></p>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
      <?php endif; ?>

      <form method="POST">

        <div class="mb-3">
          <label class="form-label">Fecha</label>
          <input type="date" name="fecha" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Hora</label>
          <input type="time" name="hora" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Observaciones</label>
          <textarea name="observaciones" class="form-control"
            placeholder="Ej. Traer identificación oficial"></textarea>
        </div>

        <button class="btn btn-success">
          Guardar cita de firma
        </button>

        <a href="verSolicitud.php?id=<?= $idSolicitud ?>" class="btn btn-secondary">
          Cancelar
        </a>

      </form>
    </div>
  </div>

</div>
</body>
</html>
