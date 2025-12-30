<?php
$conexion = pg_connect(
  "host=localhost port=5432 dbname=paw_rescue user=murasaki password=projpaw1"
);

if (!$conexion) {
  die("❌ NO CONECTA");
}

$result = pg_query($conexion, "SELECT current_database(), current_schema()");
$row = pg_fetch_assoc($result);

echo "BD: " . $row['current_database'] . "<br>";
echo "SCHEMA: " . $row['current_schema'] . "<br>";
?>
