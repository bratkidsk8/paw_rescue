<?php
session_start();
include("../conexion.php");

/* SEGURIDAD */
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

/* VETERINARIO */
$sqlVet = "
SELECT id_veterinario
FROM paw_rescue.veterinario
WHERE id_usuario = $1 AND activo = true
";
$resVet = pg_query_params($conexion, $sqlVet, [$_SESSION['id_usuario']]);
$id_veterinario = pg_fetch_result($resVet, 0, 'id_veterinario');

/* OBTENER CONSULTA */
$sql = "
SELECT
    id_consulta,
    fecha,
    hora,
    motivo,
    observaciones,
    id_estatus
FROM paw_rescue.consulta
WHERE id_consulta = $1
AND id_veterinario = $2
";

$res = pg_query_params($conexion, $sql, [$id_consulta, $id_veterinario]);

if (pg_num_rows($res) === 0) {
    die("Consulta no encontrada");
}

$consulta = pg_fetch_assoc($res);

/* ACTUALIZAR */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $motivo = $_POST['motivo'];
    $observaciones = $_POST['observaciones'];
    $id_estatus = (int) $_POST['id_estatus'];

    $sqlUpdate = "
    UPDATE paw_rescue.consulta
    SET motivo = $1,
        observaciones = $2,
        id_estatus = $3
    WHERE id_consulta = $4
    AND id_veterinario = $5
    ";

    pg_query_params($conexion, $sqlUpdate, [
        $motivo,
        $observaciones,
        $id_estatus,
        $id_consulta,
        $id_veterinario
    ]);

    header("Location: consulta.php?id=$id_consulta");
    exit;
}

/* ESTATUS */
$estatus = pg_query($conexion, "
SELECT id_estatus, nombre
FROM paw_rescue.estatus_consulta
ORDER BY nombre
");

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Consulta</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include 'navbar.php'; ?>

<div class="container mt-4">

<div class="card shadow-sm">
  <div class="card-body">

    <h5 class="fw-bold mb-3">Modificar Consulta</h5>

    <form method="POST">

      <div class="mb-3">
        <label class="form-label">Motivo</label>
        <input type="text" name="motivo" class="form-control"
               value="<?= htmlspecialchars($consulta['motivo']) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Observaciones</label>
        <textarea name="observaciones" class="form-control" rows="4"><?= htmlspecialchars($consulta['observaciones']) ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Estatus</label>
        <select name="id_estatus" class="form-select" required>
          <?php while ($e = pg_fetch_assoc($estatus)): ?>
            <option value="<?= $e['id_estatus'] ?>"
              <?= $e['id_estatus'] == $consulta['id_estatus'] ? 'selected' : '' ?>>
              <?= $e['nombre'] ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-success">
          Guardar cambios
        </button>

        <a href="consulta_detalle.php?id=<?= $id_consulta ?>"
           class="btn btn-secondary">
          Cancelar
        </a>
      </div>

    </form>

  </div>
</div>

</div>

</body>
</html>
