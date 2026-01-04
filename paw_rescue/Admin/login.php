<?php
session_start();

include(__DIR__ . "/../conexion.php");

$mensaje = "";

/* ===== SI YA HAY SESIÓN ===== */
if (isset($_SESSION["admin_id"])) {
    header("Location: dashboard_admin.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $clave    = trim($_POST["clave"] ?? '');
    $password = $_POST["password"] ?? '';

    if ($clave === '' || $password === '') {
        $mensaje = "Clave y contraseña obligatorias";
    } else {

        $sql = "
            SELECT id_admin, nombre, password
            FROM paw_rescue.admin
            WHERE clave = $1
        ";

        $result = pg_query_params($conexion, $sql, [$clave]);

        if (!$result || pg_num_rows($result) === 0) {
            $mensaje = "Clave o contraseña incorrectas";
        } else {

            $admin = pg_fetch_assoc($result);

            if (!password_verify($password, $admin["password"])) {
                $mensaje = "Clave o contraseña incorrectas";
            } else {

                /* ===== CREAR SESIÓN ADMIN ===== */
                $_SESSION["admin_id"]     = $admin["id_admin"];
                $_SESSION["admin_nombre"] = $admin["nombre"];
                $_SESSION["admin_login"]  = true;

                header("Location: index.php");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Admin | Paw Rescue</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="../index.php">
            🐾 Paw Rescue
        </a>
    </div>
</nav>

<div class="container mt-5">
    <div class="card shadow p-4 mx-auto" style="max-width: 420px;">

        <h4 class="text-center mb-3">Acceso Administrador</h4>

        <?php if ($mensaje): ?>
            <div class="alert alert-danger text-center">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Clave</label>
                <input type="text" name="clave" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-dark w-100">
                Iniciar sesión
            </button>

        </form>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
