<?php
session_start();
include("../conexion.php");

/* ================= SEGURIDAD ================= */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== 'Veterinario') {
    header("Location: ../Usuario/login.php");
    exit;
}

/* ================= VALIDAR ANIMAL ================= */
if (!isset($_GET['id'])) die("Animal no especificado");
$id_animal = (int) $_GET['id'];

/* ================= OBTENER VETERINARIO ================= */
$sqlVet = "SELECT id_veterinario FROM paw_rescue.veterinario WHERE id_usuario = $1 AND activo = true";
$resVet = pg_query_params($conexion, $sqlVet, [$_SESSION['id_usuario']]);
if (pg_num_rows($resVet) === 0) die("Veterinario no válido");
$id_veterinario = (int) pg_fetch_result($resVet, 0, 'id_veterinario');

/* ================= OBTENER LISTA DE VACUNAS Y ESTADOS ================= */
$resVacunas = pg_query($conexion, "SELECT id_vacuna, nombre FROM paw_rescue.vacuna ORDER BY nombre");
if (!$resVacunas) die("Error al obtener vacunas: " . pg_last_error($conexion));

$resEstados = pg_query($conexion, "SELECT id_estado, nombre FROM paw_rescue.estado_vacunacion ORDER BY nombre");
if (!$resEstados) die("Error al obtener estados: " . pg_last_error($conexion));

$listaVacunas = pg_fetch_all($resVacunas) ?: [];
$listaEstados = pg_fetch_all($resEstados) ?: [];

/* ================= PROCESAR FORMULARIO ================= */
$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_vacuna = (int) $_POST['id_vacuna'];
    $id_estado = (int) $_POST['id_estado'];
    $fecha_aplicacion = $_POST['fecha_aplicacion'] ?: date('Y-m-d');

    // Calcular fecha de vencimiento: +1 año desde fecha_aplicacion
    $fecha_vencimiento = date('Y-m-d', strtotime($fecha_aplicacion . ' +1 year'));

    $sqlInsert = "
        INSERT INTO paw_rescue.esquema_vacunacion
        (id_animal, id_vacuna, id_estado, fecha_aplicacion, fecha_vencimiento)
        VALUES ($1, $2, $3, $4, $5)
    ";

    $resInsert = pg_query_params($conexion, $sqlInsert, [$id_animal, $id_vacuna, $id_estado, $fecha_aplicacion, $fecha_vencimiento]);

    if ($resInsert) {
        $mensaje = '<div class="alert alert-success">Vacuna agregada correctamente.</div>';
    } else {
        $mensaje = '<div class="alert alert-danger">Error al agregar la vacuna: ' . pg_last_error($conexion) . '</div>';
    }
}

/* ================= OBTENER NOMBRE DEL ANIMAL ================= */
$sqlAnimal = "SELECT nombre FROM paw_rescue.animal WHERE id_animal = $1";
$resAnimal = pg_query_params($conexion, $sqlAnimal, [$id_animal]);
$animalNombre = pg_fetch_result($resAnimal, 0, 'nombre');
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Agregar Vacuna - <?= htmlspecialchars($animalNombre) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h3>Agregar Vacuna a <?= htmlspecialchars($animalNombre) ?></h3>

    <?= $mensaje ?>

    <form method="POST" class="mt-3">
        <div class="mb-3">
            <label for="id_vacuna" class="form-label">Vacuna</label>
            <select name="id_vacuna" id="id_vacuna" class="form-select" required>
                <option value="">-- Selecciona vacuna --</option>
                <?php foreach ($listaVacunas as $v): ?>
                    <option value="<?= $v['id_vacuna'] ?>"><?= htmlspecialchars($v['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="id_estado" class="form-label">Estado</label>
            <select name="id_estado" id="id_estado" class="form-select" required>
                <option value="">-- Selecciona estado --</option>
                <?php foreach ($listaEstados as $s): ?>
                    <option value="<?= $s['id_estado'] ?>"><?= htmlspecialchars($s['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="fecha_aplicacion" class="form-label">Fecha de Aplicación</label>
            <input type="date" name="fecha_aplicacion" id="fecha_aplicacion" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Agregar vacuna</button>
        <a href="consulta.php?id=<?= $id_animal ?>" class="btn btn-secondary">Volver</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
