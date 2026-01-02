<?php
$conexion = pg_connect("
host=localhost
dbname=paw_rescue
user=murasaki
password=projpaw1
");

if (!$conexion) {
    die("Error de conexión");
}
?>
