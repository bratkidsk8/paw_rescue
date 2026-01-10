<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    die("Sesión no válida");
}

$idUsuario = $_SESSION['id_usuario'];
$idSolicitud = $_GET['id_solicitud'] ?? null;


$sql = "
SELECT *
FROM paw_rescue.cuestionario_adopcion
WHERE id_usuario = $1
";

$res = pg_query_params($conexion, $sql, [$idUsuario]);

if (!$res || pg_num_rows($res) === 0) {
    die("El usuario no ha llenado el cuestionario");
}

$q = pg_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Cuestionario de adopción</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container mt-4">

<h3>📄 Cuestionario de adopción</h3>

<table class="table table-bordered">
<tr><th>CURP</th><td><?= $q['curp'] ?></td></tr>
<tr><th>Tipo de vivienda</th><td><?= $q['tipo_vivienda'] ?></td></tr>
<tr><th>¿Tiene patio?</th><td><?= $q['tiene_patio'] ?></td></tr>
<tr><th>Ingresos</th><td><?= $q['ingresos'] ?></td></tr>
<tr><th>Tiempo dedicado</th><td><?= $q['tiempo_dedicado'] ?></td></tr>
<tr><th>Personalidad</th><td><?= $q['personalidad'] ?></td></tr>
<tr><th>Motivo adopción</th><td><?= $q['motivo_adopcion'] ?></td></tr>
<tr><th>Convivientes</th><td><?= $q['convivientes'] ?></td></tr>
<tr><th>Total personas</th><td><?= $q['total_personas'] ?></td></tr>
<tr><th>Experiencia previa</th><td><?= $q['experiencia_previa'] ?></td></tr>
<tr><th>Plan de emergencia</th><td><?= $q['plan_emergencia'] ?></td></tr>
<tr><th>Plan largo plazo</th><td><?= $q['plan_largo_plazo'] ?></td></tr>
</table>

<?php if ($idSolicitud): ?>
<a href="verSolicitud.php?id=<?= $idSolicitud ?>" class="btn btn-secondary">
Volver a la solicitud
</a>
<?php endif; ?>

</div>
</body>
</html>
