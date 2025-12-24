<?php
include("paw_rescue/conexion.php");

if ($conexion) {
    echo "✅ Conexión exitosa a la base de datos";
} else {
    echo "❌ No hay conexión con la base de datos";
}
?>
