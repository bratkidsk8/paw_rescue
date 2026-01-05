<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include(__DIR__ . "/../conexion.php");
pg_query($conexion, "SET search_path TO paw_rescue");

$mensaje = "";

/* ================= CATÁLOGOS ================= */
$especies = pg_query($conexion, "SELECT id_esp, nombre FROM especie ORDER BY nombre");
$tamanos  = pg_query($conexion, "SELECT id_tam, nombre FROM tam ORDER BY nombre");
$colores  = pg_query($conexion, "SELECT id_color, nombre FROM color ORDER BY nombre");
$ojos     = pg_query($conexion, "SELECT id_ojos, nombre FROM color_ojos ORDER BY nombre");
$temps    = pg_query($conexion, "SELECT id_temp, nombre FROM temperamento ORDER BY nombre");
$estatus  = pg_query($conexion, "SELECT id_estatus, nombre FROM estatus_adop ORDER BY nombre");
$estados  = pg_query($conexion, "SELECT id_estado, nombre FROM estado_animal ORDER BY nombre");
$refugios = pg_query($conexion, "SELECT id_ref, nombre FROM refugio ORDER BY nombre");

/* ================= REGISTRO ================= */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $origen = $_POST['origen'] ?? 'calle';
    $tuvo_duenos = ($origen === 'retiro') ? 't' : 'f'; // PostgreSQL-safe

    pg_query($conexion, "BEGIN");

    try {
        /* ==== FOTO ==== */
        $rutaBD = null;
        if (!empty($_FILES["foto"]["name"])) {
            $carpeta = __DIR__ . "/imgMascotas/";
            if (!is_dir($carpeta)) {
                mkdir($carpeta, 0777, true);
            }

            $ext = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
            $nombreFoto = time() . "_" . uniqid() . "." . $ext;

            if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $carpeta . $nombreFoto)) {
                throw new Exception("Error al subir la imagen");
            }

            $rutaBD = "/paw_rescue/Admin/imgMascotas/" . $nombreFoto;
        }

        /* ==== ANIMAL ==== */
        $resAnimal = pg_query_params($conexion, "
            INSERT INTO animal (
                nombre, id_esp, id_raza, id_tam,
                id_color, id_ojos, id_temp,
                id_estatus, id_estado, id_ref,
                edad_aprox, tuvo_duenos_anteriores, foto
            )
            VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13)
            RETURNING id_animal
        ", [
            $_POST['nombre'],
            $_POST['id_esp'],
            $_POST['id_raza'] ?: null,
            $_POST['id_tam'],
            $_POST['id_color'] ?: null,
            $_POST['id_ojos'] ?: null,
            $_POST['id_temp'],
            $_POST['id_estatus'],
            $_POST['id_estado'],
            $_POST['id_ref'],
            $_POST['edad_aprox'] ?: null,
            $tuvo_duenos,   // 't' o 'f'
            $rutaBD
        ]);

        if (!$resAnimal) {
            throw new Exception(pg_last_error($conexion));
        }

        $animal = pg_fetch_assoc($resAnimal);
        $idAnimal = $animal['id_animal'];

        /* ==== RESCATE ==== */
        pg_query_params($conexion, "
            INSERT INTO rescate (id_animal, fecha, lugar, condiciones)
            VALUES ($1,$2,$3,$4)
        ", [
            $idAnimal,
            $_POST['fecha_rescate'],
            $_POST['lugar_rescate'],
            $_POST['condiciones'] ?: null
        ]);

        /* ==== HISTORIAL ESTADO ==== */
        pg_query_params($conexion, "
        INSERT INTO rescate (id_animal, fecha, lugar)
        VALUES ($1,$2,$3)", 
        [
          $idAnimal,
          $_POST['fecha_rescate'],
          $_POST['lugar_rescate']
          ]);


        /* ==== LISTA NEGRA (OPCIONAL) ==== */
        if ($origen === 'retiro' && !empty($_POST['ln_curp'])) {

            $resPersona = pg_query_params($conexion, "
                INSERT INTO lista_negra
                (nombre, primer_apellido, segundo_apellido, curp, motivo)
                VALUES ($1,$2,$3,$4,$5)
                RETURNING id_persona
            ", [
                $_POST['ln_nombre'] ?: null,
                $_POST['ln_apellido1'] ?: null,
                $_POST['ln_apellido2'] ?: null,
                $_POST['ln_curp'],
                $_POST['ln_motivo'] ?: 'Retiro de mascota'
            ]);

            if (!$resPersona) {
                throw new Exception(pg_last_error($conexion));
            }

            $persona = pg_fetch_assoc($resPersona);

            pg_query_params($conexion, "
                INSERT INTO retiro_mascota (id_animal, id_persona, motivo)
                VALUES ($1,$2,$3)
            ", [
                $idAnimal,
                $persona['id_persona'],
                $_POST['ln_motivo'] ?: 'Retiro de mascota'
            ]);
        }

        pg_query($conexion, "COMMIT");
        $mensaje = "Mascota registrada correctamente";

    } catch (Exception $e) {
        pg_query($conexion, "ROLLBACK");
        $mensaje = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registrar Mascota</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<?php include("navbar.php"); ?>

<div class="container mt-4 mb-5">
<div class="card shadow p-4">

<h4>Registrar nueva mascota</h4>

<?php if ($mensaje): ?>
<div class="alert alert-info"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="row g-3">

<input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
<input type="number" name="edad_aprox" class="form-control" placeholder="Edad aproximada">

<label class="form-label">Foto</label>
<input type="file" name="foto" class="form-control">

<select name="id_esp" id="id_esp" class="form-select" required>
<option value="">Especie</option>
<?php while ($e = pg_fetch_assoc($especies)) echo "<option value='{$e['id_esp']}'>{$e['nombre']}</option>"; ?>
</select>

<select name="id_raza" id="id_raza" class="form-select" disabled>
<option value="">Seleccione una especie</option>
</select>

<select name="id_tam" class="form-select" required>
<option value="">Tamaño</option>
<?php while ($t = pg_fetch_assoc($tamanos)) echo "<option value='{$t['id_tam']}'>{$t['nombre']}</option>"; ?>
</select>

<select name="id_color" class="form-select">
<option value="">Color</option>
<?php while ($c = pg_fetch_assoc($colores)) echo "<option value='{$c['id_color']}'>{$c['nombre']}</option>"; ?>
</select>

<select name="id_ojos" class="form-select">
<option value="">Color de ojos</option>
<?php while ($o = pg_fetch_assoc($ojos)) echo "<option value='{$o['id_ojos']}'>{$o['nombre']}</option>"; ?>
</select>

<select name="id_temp" class="form-select" required>
<option value="">Temperamento</option>
<?php while ($t = pg_fetch_assoc($temps)) echo "<option value='{$t['id_temp']}'>{$t['nombre']}</option>"; ?>
</select>

<select name="id_estatus" class="form-select" required>
<option value="">Estatus adopción</option>
<?php while ($e = pg_fetch_assoc($estatus)) echo "<option value='{$e['id_estatus']}'>{$e['nombre']}</option>"; ?>
</select>

<select name="id_estado" class="form-select" required>
<option value="">Estado de la mascota</option>
<?php while ($e = pg_fetch_assoc($estados)) echo "<option value='{$e['id_estado']}'>{$e['nombre']}</option>"; ?>
</select>

<select name="id_ref" class="form-select" required>
<option value="">Refugio</option>
<?php while ($r = pg_fetch_assoc($refugios)) echo "<option value='{$r['id_ref']}'>{$r['nombre']}</option>"; ?>
</select>

<select name="origen" id="origen" class="form-select" required>
<option value="">Origen</option>
<option value="calle">Rescate en la calle</option>
<option value="retiro">Retirada a una persona</option>
</select>

<label class="form-label">Día de rescate</label>
<input type="date" name="fecha_rescate" class="form-control" required>

<input type="text" name="lugar_rescate" class="form-control" placeholder="Lugar del rescate" required>
<div id="datos_lista_negra" class="border p-3" style="display:none;">
<h6>Datos de la persona (Lista negra)</h6>
<input type="text" name="ln_nombre" class="form-control mb-2" placeholder="Nombre">
<input type="text" name="ln_apellido1" class="form-control mb-2" placeholder="Primer apellido">
<input type="text" name="ln_apellido2" class="form-control mb-2" placeholder="Segundo apellido">
<input type="text" name="ln_curp" class="form-control mb-2" placeholder="CURP">
<textarea name="ln_motivo" class="form-control" placeholder="Motivo"></textarea>
</div>

<button class="btn btn-dark mt-3">Registrar mascota</button>
</form>

</div>
</div>

<script src="/paw_rescue/js/agregar_mascota.js"></script>
</body>
</html>
