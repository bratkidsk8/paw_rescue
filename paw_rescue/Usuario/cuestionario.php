<?php
session_start();

/* ========= PROTECCIÓN =========
   Solo usuarios logueados */
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cuestionario de Adopción</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<!-- NAVBAR (RESPETADO) -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">
      🐾 Paw Rescue
    </a>

    <div class="collapse navbar-collapse justify-content-end">
      <a href="logout.php" class="btn btn-outline-danger">Cerrar sesión</a>
    </div>
  </div>
</nav>

<!-- CONTENIDO -->
<section class="container my-5">
    <h2 class="text-center mb-3">📋 Cuestionario de Adopción</h2>
    <p class="text-center mb-4">
        Este cuestionario nos ayuda a evaluar si el hogar es adecuado para una mascota.
    </p>

    <form action="guardar_cuestionario.php" method="POST" class="shadow p-4 rounded bg-light">

        <!-- CURP -->
        <h5>Identificación</h5>
        <div class="mb-3">
            <label class="form-label">CURP</label>
            <input type="text" name="curp" class="form-control" maxlength="18" required>
        </div>

        <!-- VIVIENDA -->
        <h5 class="mt-4">Vivienda</h5>

        <div class="mb-3">
            <label class="form-label">Tipo de vivienda</label>
            <select name="tipo_vivienda" class="form-select" required>
                <option value="">Selecciona</option>
                <option value="1">Casa</option>
                <option value="2">Departamento</option>
                <option value="3">Otro</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">¿La vivienda es rentada?</label>
            <select name="permiso_renta" class="form-select">
                <option value="">No aplica</option>
                <option value="1">Sí</option>
                <option value="0">No</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">¿Cuenta con comprobante de domicilio?</label>
            <select name="comprobante_domicilio" class="form-select" required>
                <option value="1">Sí</option>
                <option value="0">No</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">¿Cuenta con espacio adecuado para la mascota?</label>
            <select name="espacio_adecuado" class="form-select" required>
                <option value="1">Sí</option>
                <option value="0">No</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">¿La vivienda tiene protecciones (bardas, rejas)?</label>
            <select name="protecciones" class="form-select" required>
                <option value="1">Sí</option>
                <option value="0">No</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">¿Hay niños en el hogar?</label>
            <select name="convivencia_ninos" class="form-select" required>
                <option value="1">Sí</option>
                <option value="0">No</option>
            </select>
        </div>

        <!-- COMPROMISO -->
        <h5 class="mt-4">Compromiso</h5>

        <div class="mb-3">
            <label class="form-label">¿Acepta visitas de supervisión?</label>
            <select name="acepta_visitas" class="form-select" required>
                <option value="1">Sí</option>
                <option value="0">No</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">¿Acepta la esterilización?</label>
            <select name="acepta_esterilizacion" class="form-select" required>
                <option value="1">Sí</option>
                <option value="0">No</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">¿Compromiso a largo plazo (10–15 años)?</label>
            <select name="compromiso_largo_plazo" class="form-select" required>
                <option value="1">Sí</option>
                <option value="0">No</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">¿Puede cubrir gastos veterinarios?</label>
            <select name="gastos_veterinarios" class="form-select" required>
                <option value="1">Sí</option>
                <option value="0">No</option>
            </select>
        </div>

        <!-- MOTIVO -->
        <h5 class="mt-4">Motivo de adopción</h5>
        <div class="mb-3">
            <select name="motivo" class="form-select">
                <option value="">Selecciona</option>
                <option value="1">Compañía</option>
                <option value="2">Cuidado familiar</option>
                <option value="3">Protección</option>
                <option value="4">Otro</option>
            </select>
        </div>

        <!-- OBSERVACIONES -->
        <div class="mb-3">
            <label class="form-label">Observaciones</label>
            <textarea name="observaciones" class="form-control" rows="3"></textarea>
        </div>

        <!-- BOTÓN -->
        <div class="text-center">
            <button type="submit" class="btn btn-success px-5">
                Enviar cuestionario
            </button>
        </div>

    </form>
</section>

<footer class="text-center py-3 bg-light">
    MURASAKI 2026 ©
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
