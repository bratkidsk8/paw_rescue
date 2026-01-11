<?php
session_start();
include("../conexion.php");

/* ================= SEGURIDAD ================= */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== 'Veterinario') {
    header("Location: ../Usuario/login.php");
    exit;
}

/* ================= VALIDAR ANIMAL ================= */
if (!isset($_GET['id'])) {
    die("Animal no especificado");
}

$id_animal = (int) $_GET['id'];

/* ================= OBTENER VETERINARIO ================= */


/* ================= OBTENER LISTA DE ENFERMEDADES Y ESTADOS ================= */
$enfermedades = pg_query($conexion, "SELECT id_enf, nombreenfer FROM paw_rescue.enfermedad ORDER BY nombreenfer");
$estados      = pg_query($conexion, "SELECT id_estado, nombre FROM paw_rescue.estado_enfermedad ORDER BY nombre");

/* ================= PROCESAR FORMULARIO ================= */
$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_enf    = (int) $_POST['id_enf'];
    $id_estado = (int) $_POST['id_estado'];
    $fecha     = $_POST['fecha'] ?: date('Y-m-d');
    $observ    = trim($_POST['observaciones']);

    $sqlInsert = "
        INSERT INTO paw_rescue.enf_animal
        (id_animal, id_enf, id_estado, fecha, observaciones)
        VALUES ($1, $2, $3, $4, $5)
    ";

    $resInsert = pg_query_params($conexion, $sqlInsert, [$id_animal, $id_enf, $id_estado, $fecha, $observ]);

    if ($resInsert) {
        $mensaje = '<div class="alert alert-success">Enfermedad agregada correctamente.</div>';
    } else {
        $mensaje = '<div class="alert alert-danger">Error al agregar la enfermedad.</div>';
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
<title>Agregar Enfermedad - <?= htmlspecialchars($animalNombre) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h3>Agregar Enfermedad a <?= htmlspecialchars($animalNombre) ?></h3>

    <?= $mensaje ?>

    <form method="POST" class="mt-3">
        <div class="mb-3">
            <label for="id_enf" class="form-label">Enfermedad</label>
            <select name="id_enf" id="id_enf" class="form-select" required>
                <option value="">-- Selecciona enfermedad --</option>
                <?php while ($e = pg_fetch_assoc($enfermedades)): ?>
                    <option value="<?= $e['id_enf'] ?>"><?= htmlspecialchars($e['nombreenfer']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="id_estado" class="form-label">Estado de la enfermedad</label>
            <select name="id_estado" id="id_estado" class="form-select" required>
                <option value="">-- Selecciona estado --</option>
                <?php while ($s = pg_fetch_assoc($estados)): ?>
                    <option value="<?= $s['id_estado'] ?>"><?= htmlspecialchars($s['nombre']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" name="fecha" id="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="mb-3">
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea name="observaciones" id="observaciones" rows="3" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Agregar enfermedad</button>
        <a href="consulta.php?id=<?= $id_animal ?>" class="btn btn-secondary">Volver</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
