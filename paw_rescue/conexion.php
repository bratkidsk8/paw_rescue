<?php
try {
    $conexion = new PDO(
        "pgsql:host=127.0.0.1;port=5432;dbname=paw_rescue",
        "murasaki",
        "projpaw1"
    );

    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conexión exitosa desde XAMPP";
} catch (PDOException $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}
?>
