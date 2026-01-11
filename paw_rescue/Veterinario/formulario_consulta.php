<?php
session_start();
include("../conexion.php");

/* ================= SEGURIDAD ================= */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== 'Veterinario') {
    header("Location: ../Usuario/login.php");
    exit;
}

/* ================= OBTENER VETERINARIO ================= */
$sqlVet = "SELECT id_veterinario FROM paw_rescue.veterinario WHERE id_usuario = $1 AND activo = true";
$resVet = pg_query_params($conexion, $sqlVet, [$_SESSION['id_usuario']]);
if (pg_num_rows($resVet) === 0) die("Veterinario no válido");
$id_veterinario = (int) pg_fetch_result($resVet, 0, 'id_veterinario');

/* ================= OBTENER ANIMALE================= */
$sqlAnimales = "SELECT id_animal, nombre FROM paw_rescue.animal ORDER BY nombre";
$resAnimales = pg_query($conexion, $sqlAnimales);
if (!$resAnimales) die("Error al obtener animales: " . pg_last_error($conexion));
$listaAnimales = pg_fetch_all($resAnimales) ?: [];


/* ================= OBTENER ESTATUS DE CONSULTA ================= */
$sqlEstatus = "SELECT id_estatus, nombre FROM paw_rescue.estatus_consulta ORDER BY nombre";
$resEstatus = pg_query($conexion, $sqlEstatus);
$listaEstatus = pg_fetch_all($resEstatus) ?: [];

/* ================= PROCESAR FORMULARIO ================= */
$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_animal = (int) $_POST['id_animal'];
    $fecha = $_POST['fecha'] ?: date('Y-m-d');
    $hora = $_POST['hora'] ?: date('H:i');
    $motivo = trim($_POST['motivo']);
    $observaciones = trim($_POST['observaciones']);
    $id_estatus = (int) $_POST['id_estatus'];

    $sqlInsert = "
        INSERT INTO paw_rescue.consulta
        (id_veterinario, id_animal, fecha, hora, motivo, observaciones, id_estatus)
        VALUES ($1, $2, $3, $4, $5, $6, $7)
    ";

    $resInsert = pg_query_params($conexion, $sqlInsert, [$id_veterinario, $id_animal, $fecha, $hora, $motivo, $observaciones, $id_estatus]);

    if ($resInsert) {
        $mensaje = '<div class="alert alert-success">Cita agregada correctamente.</div>';
    } else {
        $mensaje = '<div class="alert alert-danger">Error al agregar la cita: ' . pg_last_error($conexion) . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Agregar Cita</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h3>Agregar Cita</h3>

    <?= $mensaje ?>

    <form method="POST" class="mt-3">

        <div class="mb-3">
            <label for="id_animal" class="form-label">Animal</label>
            <select name="id_animal" id="id_animal" class="form-select" required>
                <option value="">-- Selecciona animal --</option>
                <?php foreach ($listaAnimales as $a): ?>
                    <option value="<?= $a['id_animal'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" name="fecha" id="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="mb-3">
            <label for="hora" class="form-label">Hora</label>
            <input type="time" name="hora" id="hora" class="form-control" value="<?= date('H:i') ?>" required>
        </div>

        <div class="mb-3">
            <label for="motivo" class="form-label">Motivo</label>
            <input type="text" name="motivo" id="motivo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea name="observaciones" id="observaciones" rows="3" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label for="id_estatus" class="form-label">Estatus</label>
            <select name="id_estatus" id="id_estatus" class="form-select" required>
                <option value="">-- Selecciona estatus --</option>
                <?php foreach ($listaEstatus as $e): ?>
                    <option value="<?= $e['id_estatus'] ?>"><?= htmlspecialchars($e['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Agregar Cita</button>
        <a href="index.php" class="btn btn-secondary">Volver</a>

    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
