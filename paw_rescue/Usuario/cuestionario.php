<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

$sql = "
    SELECT id_cuestionario
    FROM paw_rescue.cuestionario_adopcion
    WHERE id_usuario = $1
    LIMIT 1
";

$res = pg_query_params($conexion, $sql, [$id_usuario]);

if (!$res) {
    die("Error en consulta de cuestionario: " . pg_last_error($conexion));
}

if (pg_num_rows($res) > 0) {
    header("Location: cuestionarioEnviado.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Cuestionario de Adopción</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<?php include 'navbar.php'; ?>

<section class="container my-5">

<form method="POST" action="/paw_rescue/Usuario/guardarCuestionario.php">

<!-- ================= IDENTIFICACIÓN ================= -->
<h5 class="mb-3">Identificación</h5>
<div class="mb-4">
  <input type="text" name="curp" class="form-control"
         placeholder="CURP" required>
</div>

<!-- ================= DOMICILIO ================= -->
<h5 class="mt-4 mb-3">Domicilio</h5>

<div class="row">
  <div class="col-md-3 mb-3">
    <label class="form-label">Código Postal</label>
    <input type="text" id="codigo_postal" name="codigo_postal"
           class="form-control" maxlength="5">
  </div>

  <div class="col-md-4 mb-3">
    <label class="form-label">Municipio</label>
    <input type="text" id="municipio_final" name="municipio_final"
           class="form-control" readonly>
  </div>

  <div class="col-md-5 mb-3">
    <label class="form-label">Colonia</label>
    <select id="asentamiento" name="asentamiento_id" class="form-select">
      <option value="">Selecciona colonia</option>
    </select>
  </div>
</div>

<div class="mb-4">
  <label class="form-label">Calle y número</label>
  <input type="text" name="calle" class="form-control" required>
</div>

<!-- ================= ECONOMÍA ================= -->
<h5 class="mt-4 mb-3">Situación Económica</h5>
<select name="ingresos" class="form-select mb-4" required>
  <option value="">Ingreso mensual</option>
  <option value="1">Menos de $6,000</option>
  <option value="2">$6,000 – $10,000</option>
  <option value="3">$10,000 – $20,000</option>
  <option value="4">Más de $20,000</option>
</select>


<!-- ================= TIEMPO ================= -->
<h5 class="mt-4 mb-3">Tiempo Disponible</h5>
<select name="tiempo_dedicado" class="form-select mb-4" required>
  <option value="">Tiempo diario para la mascota</option>
  <option value="1">Menos de 1 hora</option>
  <option value="2">1 a 3 horas</option>
  <option value="3">Más de 3 horas</option>
</select>


<!-- ================= PERSONALIDAD ================= -->
<h5 class="mt-4 mb-3">Personalidad</h5>
<select name="personalidad" class="form-select mb-4" required>
  <option value="">Describe tu personalidad</option>
  <option value="Tranquila">Tranquila</option>
  <option value="Activa">Activa</option>
  <option value="Muy activa">Muy activa</option>
</select>

<!-- ================= MOTIVO ================= -->
<h5 class="mt-4 mb-3">Motivo para Adoptar</h5>
<textarea name="motivo_adopcion" class="form-control mb-4"
          rows="4" placeholder="Explique por qué desea adoptar" required></textarea>

<!-- ================= CONVIVENCIA ================= -->
<h5 class="mt-4 mb-3">Convivencia en el Hogar</h5>

<div class="mb-3">
  <?php
  $opciones = ['Adultos','Niños','Adultos mayores','Otras mascotas'];
  foreach ($opciones as $o):
  ?>
  <div class="form-check">
    <input class="form-check-input"
           type="checkbox"
           name="convivientes[]"
           value="<?= $o ?>">
    <label class="form-check-label"><?= $o ?></label>
  </div>
  <?php endforeach; ?>
</div>

<select name="total_personas" class="form-select mb-3" required>
  <option value="">Número total de personas</option>
  <option value="1">1</option>
  <option value="2">2</option>
  <option value="3">3</option>
  <option value="4">4 o más</option>
</select>


<select name="acuerdo_familiar" class="form-select mb-4" required>
  <option value="">¿Todos están de acuerdo?</option>
  <option value="Si">Sí</option>
  <option value="No">No</option>
  <option value="Parcial">Parcial</option>
</select>

<h5 class="mt-4 mb-3">Preferencia de mascota</h5>
<select name="preferencia_especie" class="form-select mb-4" required>
  <option value="">¿Qué deseas adoptar?</option>
  <option value="Perro">Perro</option>
  <option value="Gato">Gato</option>
  <option value="Ambos">Ambos</option>
</select>

<h5 class="mt-4 mb-3">Tipo de vivienda</h5>
<select name="tipo_vivienda" class="form-select mb-4" required>
  <option value="">Selecciona</option>
  <option value="Departamento">Departamento</option>
  <option value="Casa chica">Casa chica</option>
  <option value="Casa amplia">Casa amplia</option>
</select>

<select name="tiene_patio" class="form-select mb-4" required>
  <option value="">¿Cuenta con patio o jardín?</option>
  <option value="Si">Sí</option>
  <option value="No">No</option>
</select>

<h5 class="mt-4 mb-3">Nivel de actividad esperado</h5>
<select name="nivel_actividad" class="form-select mb-4" required>
  <option value="">Seleccione</option>
  <option value="Tranquilo">Tranquilo</option>
  <option value="Moderado">Moderado</option>
  <option value="Activo">Activo</option>
</select>





<!-- ================= EXPERIENCIA ================= -->
<h5 class="mt-4 mb-3">Experiencia con Mascotas</h5>

<select name="experiencia_previa" class="form-select mb-3" required>
  <option value="">¿Ha tenido mascotas?</option>
  <option value="Si">Sí</option>
  <option value="No">No</option>
</select>

<select name="destino_mascota" class="form-select mb-4">
  <option value="">¿Qué pasó con su última mascota?</option>
  <option value="Vive conmigo">Vive conmigo</option>
  <option value="Falleció">Falleció</option>
  <option value="Se perdió">Se perdió</option>
  <option value="Reubicada">Fue reubicada</option>
</select>

<!-- ================= RUTINA ================= -->
<h5 class="mt-4 mb-3">Rutina</h5>

<select name="cuidador" class="form-select mb-3" required>
  <option value="">¿Quién será el responsable principal?</option>
  <option value="Yo">Yo</option>
  <option value="Familiar">Familiar</option>
  <option value="Vecino">Vecino</option>
</select>

<select name="frecuencia_viajes" class="form-select mb-4" required>
  <option value="">Frecuencia de viajes</option>
  <option value="Casi nunca">Casi nunca</option>
  <option value="1-2 al año">1–2 veces al año</option>
  <option value="Frecuente">Varias veces al año</option>
</select>

<!-- ================= RESPONSABILIDAD ================= -->
<h5 class="mt-4 mb-3">Responsabilidad</h5>

<select name="conoce_costos" class="form-select mb-3" required>
  <option value="">¿Conoce los costos veterinarios?</option>
  <option value="Si">Sí</option>
  <option value="No">No</option>
</select>

<select name="gasto_mensual" class="form-select mb-3" required>
  <option value="">Gasto mensual estimado</option>
  <option value="Menos de 500">Menos de $500</option>
  <option value="500-1000">$500 – $1000</option>
  <option value="1000-2000">$1000 – $2000</option>
  <option value="2000+">Más de $2000</option>
</select>

<select name="respuesta_enfermedad" class="form-select mb-3" required>
  <option value="">Si la mascota enferma…</option>
  <option value="Veterinario">La llevaría al veterinario</option>
  <option value="Evaluar costos">Evaluaría los costos</option>
</select>

<select name="respuesta_danos" class="form-select mb-4" required>
  <option value="">Si daña objetos…</option>
  <option value="Adiestramiento">Buscaría adiestramiento</option>
  <option value="Corregir">Intentaría corregir</option>
</select>

<select name="acepta_contrato" class="form-select mb-4" required>
  <option value="">¿Acepta firmar contrato?</option>
  <option value="Si">Sí</option>
  <option value="No">No</option>
</select>

<!-- ================= PREVENCIÓN ================= -->
<h5 class="mt-4 mb-3">Prevención y Compromiso</h5>

<textarea name="plan_emergencia" class="form-control mb-3"
          rows="3"
          placeholder="¿Qué haría en una emergencia?" required></textarea>

<textarea name="plan_largo_plazo" class="form-control mb-4"
          rows="4"
          placeholder="Plan a largo plazo para la mascota" required></textarea>

<button type="submit"
        name="enviar_cuestionario"
        class="btn btn-success px-5">
  Enviar cuestionario
</button>

</form>
</section>

<footer class="text-center py-3 bg-light">
MURASAKI © 2026
</footer>

<script src="/paw_rescue/js/cpUsuario.js"></script>
</body>
</html>
