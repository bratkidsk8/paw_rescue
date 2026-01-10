<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$idUsuario = $_SESSION['id_usuario'];
$idAnimal  = $_POST['id_animal'];

/* ESTATUS MASCOTA */
$sql = "
SELECT ea.nombre AS estatus
FROM paw_rescue.animal a
JOIN paw_rescue.estatus_adop ea ON ea.id_estatus = a.id_estatus
WHERE a.id_animal = $1
";

$res = pg_query_params($conexion, $sql, [$idAnimal]);
$animal = pg_fetch_assoc($res);

if (!$animal) {
    die("Mascota no encontrada");
}

/* ===============================
   CASOS
================================= */
if ($animal['estatus'] === 'Adoptado') {
    echo "
    <div class='alert alert-danger mt-5'>
        ❌ Esta mascota ya fue adoptada.
    </div>";
    exit;
}

if ($animal['estatus'] === 'En proceso') {
    echo "
    <div class='alert alert-warning mt-5'>
        🐾 Esta mascota se encuentra en proceso de adopción.<br>
        Puedes:
        <ul>
            <li>Esperar a que finalice el proceso</li>
            <li>Elegir otra mascota disponible</li>
        </ul>
        <a href='mascotas.php' class='btn btn-primary mt-2'>Ver otras mascotas</a>
    </div>";
    exit;
}

$sqlInsert = "
INSERT INTO paw_rescue.solicitud_adopcion (
    id_usuario,
    id_animal,
    id_estatus
) VALUES (
    $1,
    $2,
    (SELECT id_estatus FROM paw_rescue.estatus_proceso_adopcion WHERE nombre = 'En revisión')
)
";

pg_query_params($conexion, $sqlInsert, [$idUsuario, $idAnimal]);

header("Location: procesoAdopcion.php");
exit;
