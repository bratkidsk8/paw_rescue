<?php
$conn = pg_connect(
  "host=localhost port=5432 dbname=paw_rescue user=murasaki password=projpaw1"
);

if (!$conn) {
    die("❌ Error de conexión");
}

echo "✅ Conectado correctamente";
?>
