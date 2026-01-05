<?php
include(__DIR__ . "/../conexion.php");
pg_query($conexion, "SET search_path TO paw_rescue");

$id_esp = $_GET['id_esp'] ?? null;

if (!$id_esp) {
    echo json_encode([]);
    exit;
}

$res = pg_query_params($conexion, "
    SELECT id_raza, nombre
    FROM raza
    WHERE id_esp = $1
    ORDER BY nombre
", [$id_esp]);

$razas = [];
while ($row = pg_fetch_assoc($res)) {
    $razas[] = $row;
}

header('Content-Type: application/json');
echo json_encode($razas);
