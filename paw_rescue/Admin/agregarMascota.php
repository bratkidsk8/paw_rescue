<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include(__DIR__ . "/../conexion.php");
pg_query($conexion, "SET search_path TO paw_rescue");

$mensaje = "";

/* ===== CATÁLOGOS ===== */
$especies = pg_query($conexion, "SELECT id_esp, nombre FROM especie ORDER BY nombre");
$razas    = pg_query($conexion, "SELECT id_raza, nombre FROM raza ORDER BY nombre");
$tamanos  = pg_query($conexion, "SELECT id_tam, nombre FROM tam ORDER BY nombre");
$colores  = pg_query($conexion, "SELECT id_color, nombre FROM color ORDER BY nombre");
$ojos     = pg_query($conexion, "SELECT id_ojos, nombre FROM color_ojos ORDER BY nombre");
$temps    = pg_query($conexion, "SELECT id_temp, nombre FROM temperamento ORDER BY nombre");
$estatus  = pg_query($conexion, "SELECT id_estatus, nombre FROM estatus_adop");
$estados  = pg_query($conexion, "SELECT id_estado, nombre FROM estado_animal");
$refugios = pg_query($conexion, "SELECT id_ref, nombre FROM refugio");

/* ===== REGISTRO ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre   = $_POST["nombre"];
    $id_esp   = $_POST["id_esp"];
    $id_raza  = $_POST["id_raza"] ?: null;
    $id_tam   = $_POST["id_tam"];
    $id_color = $_POST["id_color"] ?: null;
    $id_ojos  = $_POST["id_ojos"] ?: null;
    $id_temp  = $_POST["id_temp"];
    $id_est   = $_POST["id_estatus"];
    $id_estado= $_POST["id_estado"];
    $id_ref   = $_POST["id_ref"];
    $duenos   = $_POST["duenos"]; // true / false

    /* ===== IMAGEN ===== */
    $rutaBD = null;

    if (!empty($_FILES["foto"]["name"])) {
        $carpeta = __DIR__ . "/imgMascotas/";

        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $ext = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
        $nombreFoto = time() . "_" . uniqid() . "." . $ext;

        move_uploaded_file($_FILES["foto"]["tmp_name"], $carpeta . $nombreFoto);
        $rutaBD = "Admin/imgMascotas/" . $nombreFoto;
    }

    /* ===== INSERT ANIMAL ===== */
    $sqlAnimal = "
        INSERT INTO animal (
            nombre, id_esp, id_raza, id_tam,
            id_color, id_ojos, id_temp,
            id_estatus, id_estado, id_ref,
            tuvo_duenos_anteriores, foto
        )
        VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12)
        RETURNING id_animal
    ";

    $paramsAnimal = [
        $nombre, $id_esp, $id_raza, $id_tam,
        $id_color, $id_ojos, $id_temp,
        $id_est, $id_estado, $id_ref,
        $duenos, $rutaBD
    ];

    $res = pg_query_params($conexion, $sqlAnimal, $paramsAnimal);

    if ($res) {
        $animal = pg_fetch_assoc($res);
        $idAnimal = $animal["id_animal"];

        /* ===== LISTA NEGRA (SI APLICA) ===== */
        if ($duenos === "true" && !empty($_POST["ln_curp"])) {

            $lnNombre   = $_POST["ln_nombre"];
            $lnAp1      = $_POST["ln_apellido1"];
            $lnAp2      = $_POST["ln_apellido2"];
            $lnCurp     = $_POST["ln_curp"];
            $lnMotivo   = $_POST["ln_motivo"];

            $sqlLN = "
                INSERT INTO lista_negra
                (nombre, apellido_paterno, apellido_materno, curp, motivo)
                VALUES ($1,$2,$3,$4,$5)
                RETURNING id_persona
            ";

            $resLN = pg_query_params($conexion, $sqlLN, [
                $lnNombre, $lnAp1, $lnAp2, $lnCurp, $lnMotivo
            ]);

            if ($resLN) {
                $persona = pg_fetch_assoc($resLN);
                $idPersona = $persona["id_persona"];

                pg_query_params($conexion, "
                    INSERT INTO retiro_mascota (id_animal, id_persona, motivo)
                    VALUES ($1,$2,$3)
                ", [$idAnimal, $idPersona, $lnMotivo]);
            }
        }

        $mensaje = "✅ Mascota registrada correctamente";
    } else {
        $mensaje = "❌ Error al registrar mascota";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Agregar Mascota</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg bg-white shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">🐾 Paw Rescue</a>
  </div>
</nav>

<div class="container mt-5">
<div class="card shadow p-4">

<h4 class="mb-3">Registrar nueva mascota</h4>

<?php if ($mensaje): ?>
<div class="alert alert-info"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="row g-3">

<input class="form-control" name="nombre" placeholder="Nombre de la mascota" required>

<select name="id_esp" class="form-select" required>
  <option value="">Especie</option>
  <?php while($e=pg_fetch_assoc($especies)) echo "<option value='{$e['id_esp']}'>{$e['nombre']}</option>"; ?>
</select>

<select name="id_raza" class="form-select">
  <option value="">Raza (si se conoce)</option>
  <?php while($r=pg_fetch_assoc($razas)) echo "<option value='{$r['id_raza']}'>{$r['nombre']}</option>"; ?>
</select>

<select name="id_tam" class="form-select" required>
  <option value="">Tamaño</option>
  <?php while($t=pg_fetch_assoc($tamanos)) echo "<option value='{$t['id_tam']}'>{$t['nombre']}</option>"; ?>
</select>

<select name="id_color" class="form-select">
  <option value="">Color principal</option>
  <?php while($c=pg_fetch_assoc($colores)) echo "<option value='{$c['id_color']}'>{$c['nombre']}</option>"; ?>
</select>

<select name="id_ojos" class="form-select">
  <option value="">Color de ojos</option>
  <?php while($o=pg_fetch_assoc($ojos)) echo "<option value='{$o['id_ojos']}'>{$o['nombre']}</option>"; ?>
</select>

<select name="id_temp" class="form-select" required>
  <option value="">Temperamento</option>
  <?php while($t=pg_fetch_assoc($temps)) echo "<option value='{$t['id_temp']}'>{$t['nombre']}</option>"; ?>
</select>

<select name="id_estatus" class="form-select" required>
  <option value="">Estatus de adopción</option>
  <?php while($e=pg_fetch_assoc($estatus)) echo "<option value='{$e['id_estatus']}'>{$e['nombre']}</option>"; ?>
</select>

<select name="id_estado" class="form-select" required>
  <option value="">Estado actual</option>
  <?php while($e=pg_fetch_assoc($estados)) echo "<option value='{$e['id_estado']}'>{$e['nombre']}</option>"; ?>
</select>

<select name="id_ref" class="form-select" required>
  <option value="">Refugio</option>
  <?php while($r=pg_fetch_assoc($refugios)) echo "<option value='{$r['id_ref']}'>{$r['nombre']}</option>"; ?>
</select>

<select name="duenos" id="duenos" class="form-select" required>
  <option value="">¿Tuvo dueños anteriores?</option>
  <option value="true">Sí</option>
  <option value="false">No</option>
</select>

<div id="datos_lista_negra" style="display:none;">
  <h6 class="mt-3">Datos del responsable</h6>

  <input type="text" name="ln_nombre" class="form-control mb-2" placeholder="Nombre">
  <input type="text" name="ln_apellido1" class="form-control mb-2" placeholder="Primer apellido">
  <input type="text" name="ln_apellido2" class="form-control mb-2" placeholder="Segundo apellido">
  <input type="text" name="ln_curp" class="form-control mb-2" maxlength="18" placeholder="CURP">
  <textarea name="ln_motivo" class="form-control mb-2" placeholder="Motivo del retiro"></textarea>
</div>

<input type="file" name="foto" class="form-control">

<button class="btn btn-dark mt-2">Registrar mascota</button>

</form>
</div>
</div>

<script src="../js/agregar_mascota.js"></script>
</body>
</html>
