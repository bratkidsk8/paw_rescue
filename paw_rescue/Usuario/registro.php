<?php
ob_start(); // Inicia el buffer de salida para permitir redirecciones header() sin errores
session_start();
include("../../paw_rescue/conexion.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre   = trim($_POST["nombre"]);
    $apellido = trim($_POST["apellido"]);
    $correo   = trim($_POST["correo"]);
    $fecha_nacimiento = $_POST["fecha_nacimiento"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $nombreCompleto = $nombre . " " . $apellido;

    try {
        $conn->beginTransaction();

        // 1. Verificar si el correo ya existe
        $stmt = $conn->prepare("SELECT 1 FROM paw_rescue.usuario WHERE correo = :correo");
        $stmt->execute([":correo" => $correo]);

        if ($stmt->fetch()) {
            throw new Exception("El correo ya está registrado");
        }

        // 2. Generar nuevo ID (Si tu BD no es AUTO_INCREMENT)
        $stmt = $conn->query("SELECT COALESCE(MAX(id_usuario),0) + 1 FROM paw_rescue.usuario");
        $id_usuario = $stmt->fetchColumn();

        // 3. Insertar usuario
        $stmt = $conn->prepare(
            "INSERT INTO paw_rescue.usuario (id_usuario, nombre, correo, password, fecha_nacimiento)
            VALUES (:id, :nombre, :correo, :password, :fecha)"
        );

        $stmt->execute([
            ":id"       => $id_usuario,
            ":nombre"   => $nombreCompleto,
            ":correo"   => $correo,
            ":password" => $password,
            ":fecha"    => $fecha_nacimiento
        ]);

        // 4. Asignar rol adoptante (id_rol = 1)
        $stmt = $conn->prepare("INSERT INTO paw_rescue.usuario_rol (id_usuario, id_rol) VALUES (:u, 1)");
        $stmt->execute([":u" => $id_usuario]);

        // 5. Insertar como adoptante
        $stmt = $conn->prepare("INSERT INTO paw_rescue.adoptante (id_usuario) VALUES (:u)");
        $stmt->execute([":u" => $id_usuario]);

        $conn->commit();

        // Guardar mensaje y redirigir
        $_SESSION['registro_exitoso'] = "Cuenta creada correctamente. Inicia sesión.";
        
        // Redirección forzada
        header("Location: login.php");
        exit(); // Detiene la ejecución del script

    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro | Paw Rescue</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>

<nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php">
            <img src="https://cdn-icons-png.flaticon.com/512/616/616409.png"
                 alt="logo" width="30" class="me-2">
            Paw Rescue
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="info.php">Acerca de</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="adoptar.php"
                       role="button" data-bs-toggle="dropdown">
                        Adoptar
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="adoptar.php">Ver mascotas</a></li>
                        <li><a class="dropdown-item" href="cuestionario.php">Cuestionario</a></li>
                        <li><a class="dropdown-item" href="prueba.php">Prueba de adopción</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="donar.php">Donaciones</a></li>
                <li class="nav-item"><a class="nav-link" href="reporte.php">Reportar</a></li>
                <li class="nav-item"><a class="nav-link" href="contacto.php">Contacto</a></li>
            </ul>
            <a href="login.php" class="btn btn-outline-dark ms-3">Login</a>
        </div>
    </div>
</nav>

<div class="container mt-5 mb-5" style="max-width: 500px;">
    <h2 class="text-center mb-4">Registro</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Apellido</label>
            <input type="text" name="apellido" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Correo</label>
            <input type="email" name="correo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Fecha de nacimiento</label>
            <input type="date" name="fecha_nacimiento" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-dark w-100">
            Registrarse
        </button>
    </form>
</div>

<footer class="text-center text-muted mb-3">
    MURASAKI 2026. ©
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>