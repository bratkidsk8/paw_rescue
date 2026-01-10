<?php
session_start();
include("../conexion.php");

ini_set('display_errors', 1);
error_reporting(E_ALL);

/* ===== VALIDAR ADMIN ===== */
if (!isset($_SESSION['admin_id'])) {
    die("Acceso denegado");
}

/* ===== CATÁLOGOS ===== */
$tipos = pg_query($conexion, "SELECT * FROM paw_rescue.tipo_personal");
$refugios = pg_query($conexion, "SELECT * FROM paw_rescue.refugio");

/* ===== GUARDAR ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ===== VALIDACIONES ===== */
    if (
        empty($_POST['nombre']) ||
        empty($_POST['apellido1']) ||
        empty($_POST['correo']) ||
        empty($_POST['password']) ||
        empty($_POST['fecha_nac']) ||
        empty($_POST['tipo_personal']) ||
        empty($_POST['rol'])
    ) {
        die("Datos obligatorios incompletos");
    }

    pg_query($conexion, "BEGIN");

    try {

        /* 1. CREAR USUARIO */
        $sqlUsuario = "
        INSERT INTO paw_rescue.usuario
        (nombre, primer_apellido, segundo_apellido, correo, password, fecha_nacimiento)
        VALUES ($1,$2,$3,$4,$5,$6)
        RETURNING id_usuario
        ";

        $resUsuario = pg_query_params($conexion, $sqlUsuario, [
            $_POST['nombre'],
            $_POST['apellido1'],
            $_POST['apellido2'] ?? null,
            $_POST['correo'],
            password_hash($_POST['password'], PASSWORD_BCRYPT),
            $_POST['fecha_nac']
        ]);

        if (!$resUsuario) {
            throw new Exception("Error al crear usuario");
        }

        $idUsuario = pg_fetch_result($resUsuario, 0, 0);

        /* 2. ASIGNAR ROL */
        $resRol = pg_query_params(
            $conexion,
            "INSERT INTO paw_rescue.usuario_rol (id_usuario, id_rol)
             VALUES ($1,$2)",
            [$idUsuario, $_POST['rol']]
        );

        if (!$resRol) {
            throw new Exception("Error al asignar rol");
        }

        /* 3. CREAR PERSONAL */
        $sqlPersonal = "
        INSERT INTO paw_rescue.personal (id_usuario, id_tipo, telefono)
        VALUES ($1,$2,$3)
        RETURNING id_personal
        ";

        $resPersonal = pg_query_params($conexion, $sqlPersonal, [
            $idUsuario,
            $_POST['tipo_personal'],
            $_POST['telefono'] ?? null
        ]);

        if (!$resPersonal) {
            throw new Exception("Error al crear personal");
        }

        $idPersonal = pg_fetch_result($resPersonal, 0, 0);

        /* 4. REFUGIOS (CHECKBOX) */
        if (!empty($_POST['refugios']) && is_array($_POST['refugios'])) {
            foreach ($_POST['refugios'] as $idRef) {
                pg_query_params(
                    $conexion,
                    "INSERT INTO paw_rescue.personal_refugio (id_personal, id_ref)
                     VALUES ($1,$2)",
                    [$idPersonal, (int)$idRef]
                );
            }
        }

        pg_query($conexion, "COMMIT");
        header("Location: personal.php");
        exit;

    } catch (Exception $e) {
        pg_query($conexion, "ROLLBACK");
        die("❌ Error al guardar: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Agregar personal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include("navbar.php"); ?>

<div class="container mt-5">
<h2>Agregar Ayudante / Acreedor</h2>

<form method="POST" class="mt-4">

<div class="row">
<div class="col-md-4">
<label>Nombre</label>
<input type="text" name="nombre" class="form-control" required>
</div>

<div class="col-md-4">
<label>Primer apellido</label>
<input type="text" name="apellido1" class="form-control" required>
</div>

<div class="col-md-4">
<label>Segundo apellido</label>
<input type="text" name="apellido2" class="form-control">
</div>
</div>

<div class="row mt-3">
<div class="col-md-4">
<label>Correo</label>
<input type="email" name="correo" class="form-control" required>
</div>

<div class="col-md-4">
<label>Contraseña</label>
<input type="password" name="password" class="form-control" required>
</div>

<div class="col-md-4">
<label>Fecha nacimiento</label>
<input type="date" name="fecha_nac" class="form-control" required>
</div>
</div>

<div class="row mt-3">
<div class="col-md-4">
<label>Tipo</label>
<select name="tipo_personal" class="form-select" required>
<?php while ($t = pg_fetch_assoc($tipos)): ?>
<option value="<?= $t['id_tipo'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-4">
<label>Rol</label>
<select name="rol" class="form-select" required>
<option value="2">Ayudante</option>
<option value="3">Acreedor</option>
</select>
</div>

<div class="col-md-4">
<label>Teléfono</label>
<input type="text" name="telefono" class="form-control">
</div>
</div>

<!-- ===== REFUGIOS ===== -->
<div class="mt-3">
<label class="form-label fw-bold">Refugios donde trabaja</label>

<?php if (pg_num_rows($refugios) > 0): ?>
<div class="border rounded p-3">
<?php while ($r = pg_fetch_assoc($refugios)): ?>
<div class="form-check">
<input class="form-check-input" type="checkbox"
       name="refugios[]"
       value="<?= $r['id_ref'] ?>"
       id="ref<?= $r['id_ref'] ?>">
<label class="form-check-label" for="ref<?= $r['id_ref'] ?>">
<?= htmlspecialchars($r['nombre']) ?>
</label>
</div>
<?php endwhile; ?>
</div>
<?php else: ?>
<div class="alert alert-warning mt-2">
No hay refugios registrados.
</div>
<?php endif; ?>
</div>

<button type="submit" class="btn btn-success mt-4">Guardar</button>
<a href="personal.php" class="btn btn-secondary mt-4">Cancelar</a>

</form>
</div>

</body>
</html>
