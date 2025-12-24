<?php
$host = "localhost";
$usuario = "root";
$password = "";
$bd = "paw_rescue";


$conexion = new mysqli($host, $usuario, $password, $bd);

/* Validar conexión */
if ($conexion->connect_error) {
    // Mientras no exista la BD, no rompemos el sistema
    $conexion = null;
}
?>
