<?php
include("../../paw_rescue/conexion.php");
pg_query($conexion, "SET search_path TO paw_rescue");
$id = (int)$_GET['id'];

$sql = "
UPDATE animal
SET id_estado = 99
WHERE id_animal = $id
";

$result = pg_query($conexion, $sql);

exit;



