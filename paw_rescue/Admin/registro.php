<?php
include("../paw_rescue/conexion.php");

$nombre   = $_POST['nombre'] . " " . $_POST['apellido'];
$correo   = $_POST['correo'];
$fecha    = $_POST['fecha_nacimiento'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);

$sql = "
INSERT INTO paw_rescue.usuario
(nombre, correo, password, fecha_nacimiento)
VALUES ($1, $2, $3, $4)
";

$params = [$nombre, $correo, $password, $fecha];

$result = pg_query_params($conexion, $sql, $params);

if ($result) {
    header("Location: login.html?registro=ok");
} else {
    echo "Error al registrar usuario";
}
?>



<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registro</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <!--google fonts-->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">

        <!-- CSS -->
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="../css/login.css">
    </head>
    <body>

        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg bg-white shadow-sm">
            <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php">
                <img src="https://cdn-icons-png.flaticon.com/512/616/616408.png" alt="logo" width="30" class="me-2">
                Marca
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="info.php">Peticiones</a></li>
                <li class="nav-item"><a class="nav-link" href="adoptar.php">Reportes</a></li>
                <li class="nav-item"><a class="nav-link" href="agregar_mascota.php">Agregar mascotas</a></li>
                <li class="nav-item"><a class="nav-link" href="reporte.php">Reportar</a></li>
                <li class="nav-item"><a class="nav-link" href="adoptar.php">Catalogo</a></li>
                </ul>
                <a href="login.html" class="btn btn-outline-dark ms-3">Login</a>
            </div>
            </div>
        </nav>

        <div class="principal">
            <div class="contc">
                    Registro
            </div>
        <form action="registrar_usuario.php" method="POST">

    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" class="form-control" name="nombre" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Apellido</label>
        <input type="text" class="form-control" name="apellido" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Teléfono</label>
        <input type="text" class="form-control" name="telefono">
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

    <button type="submit" class="btn btn-dark">Registrarse</button>
</form>

    </body>
    <!-- pie pagina -->
    <footer>
        MURASAKI 2026. ©
    </footer>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Script -->
    <script src="script.js"></script>
</html>