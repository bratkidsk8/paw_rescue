<?php
session_start();
include("../conexion.php");

/* ================= VALIDAR SESIÓN ================= */
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

/* ================= VALIDAR ENVÍO ================= */
if (!isset($_POST['enviar_cuestionario'])) {
    header("Location: cuestionario.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

/* ================= PROCESAR CONVIVIENTES ================= */
$convivientes = $_POST['convivientes'] ?? [];
$convivientes_txt = implode(', ', $convivientes);

/* ================= INSERT ================= */
$sql = "
INSERT INTO paw_rescue.cuestionario_adopcion (
  id_usuario,
  curp,
  codigo_postal,
  asentamiento_id,
  calle,
  ingresos,
  tiempo_dedicado,
  personalidad,
  motivo_adopcion,
  convivientes,
  total_personas,
  acuerdo_familiar,

  preferencia_especie,
  tipo_vivienda,
  tiene_patio,
  nivel_actividad,

  experiencia_previa,
  destino_mascota,
  cuidador,
  frecuencia_viajes,
  conoce_costos,
  gasto_mensual,
  respuesta_enfermedad,
  respuesta_danos,
  acepta_contrato,
  plan_emergencia,
  plan_largo_plazo
) VALUES (
  $1,$2,$3,$4,$5,
  $6,$7,$8,$9,
  $10,$11,$12,
  $13,$14,$15,$16,
  $17,$18,$19,$20,
  $21,$22,$23,$24,
  $25,$26,$27
)
";

$params = [
  $id_usuario,
  $_POST['curp'],
  $_POST['codigo_postal'] ?: null,
  $_POST['asentamiento_id'] ?: null,
  $_POST['calle'],
  (int)$_POST['ingresos'],
  (int)$_POST['tiempo_dedicado'],
  $_POST['personalidad'],
  $_POST['motivo_adopcion'],
  $convivientes_txt,
  $_POST['total_personas'],
  $_POST['acuerdo_familiar'],

  $_POST['preferencia_especie'],
  $_POST['tipo_vivienda'],
  $_POST['tiene_patio'],
  $_POST['nivel_actividad'],

  $_POST['experiencia_previa'],
  $_POST['destino_mascota'] ?: null,
  $_POST['cuidador'],
  $_POST['frecuencia_viajes'],
  $_POST['conoce_costos'],
  (int)$_POST['gasto_mensual'],
  $_POST['respuesta_enfermedad'],
  $_POST['respuesta_danos'],
  $_POST['acepta_contrato'],
  $_POST['plan_emergencia'],
  $_POST['plan_largo_plazo']
];

$res = pg_query_params($conexion, $sql, $params);

if (!$res) {
    die("❌ Error BD: " . pg_last_error($conexion));
}

/* ================= REDIRECCIÓN ================= */
header("Location: gracias.php");
exit;
