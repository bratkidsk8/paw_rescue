<?php
session_start();
include("../../paw_rescue/conexion.php");

$mensaje = "";
$registro_exitoso = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre           = trim($_POST['nombre'] ?? '');
    $primer_apellido  = trim($_POST['primer_apellido'] ?? '');
    $segundo_apellido = trim($_POST['segundo_apellido'] ?? '');
    $correo           = trim($_POST['correo'] ?? '');
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
    $password_raw     = $_POST['password'] ?? '';

    /* ===== VALIDACIONES ===== */
    if (
        !$nombre ||
        !$primer_apellido ||
        !$correo ||
        !$fecha_nacimiento ||
        !$password_raw
    ) {
        $mensaje = "Todos los campos obligatorios son requeridos";
    } else {

        /*  VERIFICAR CORREO  */
        $check = pg_query_params(
            $conexion,
            "SELECT 1 FROM paw_rescue.usuario WHERE correo = $1",
            [$correo]
        );

        if (pg_num_rows($check) > 0) {
            $mensaje = "El correo ya está registrado";
        } else {

            $password = password_hash($password_raw, PASSWORD_BCRYPT);

            /*  INSERT USUARIO */
            $sql = "
            INSERT INTO paw_rescue.usuario
            (nombre, primer_apellido, segundo_apellido, correo, password, fecha_nacimiento)
            VALUES ($1, $2, $3, $4, $5, $6)
            RETURNING id_usuario
            ";

            $params = [
                $nombre,
                $primer_apellido,
                $segundo_apellido ?: null,
                $correo,
                $password,
                $fecha_nacimiento
            ];

            $result = pg_query_params($conexion, $sql, $params);

            if ($result) {

                $id_usuario = pg_fetch_result($result, 0, 'id_usuario');

                /*  ASIGNAR ROL USUARIO (id_rol = 1)  */
                pg_query_params(
                    $conexion,
                    "INSERT INTO paw_rescue.usuario_rol (id_usuario, id_rol)
                     VALUES ($1, 1)",
                    [$id_usuario]
                );

                $registro_exitoso = true;
            } else {
                $mensaje = "Error al registrar usuario";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro | Paw Rescue</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<!-- ================= NAVBAR ================= -->
<?php include 'navbar.php'; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nombre" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Primer apellido</label>
                <input type="text" class="form-control" name="primer_apellido" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Segundo apellido</label>
                <input type="text" class="form-control" name="segundo_apellido">
            </div>

            <div class="mb-3">
                <label class="form-label">Correo</label>
                <input type="email" class="form-control" name="correo" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Fecha de nacimiento</label>
                <input type="date" class="form-control" name="fecha_nacimiento" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" class="form-control" name="password" required>
            </div>

            <button type="submit" class="btn btn-dark w-100">
                Registrarse
            </button>
        </form>

    </div>
</div>

<?php if ($registro_exitoso): ?>
<script>
    alert("✅ Registro exitoso. Ahora puedes iniciar sesión.");
    window.location.href = "login.php";
</script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
