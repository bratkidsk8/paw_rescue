<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Contacto</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <!--google fonts-->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">

        <!-- CSS -->
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="../css/contacto.css">
    </head>
    <body>

        <div class="principal">
            <div class="div_tel">
                <div class="contc">
                    CONTACTANOS
                </div>
                <img id="tel" src="../img/phone-504.svg">
            </div>
            <div class="formulario" id="formulario">
                <form>
                    <div class="mb-3">
                        <label for="formGroupExampleInput" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="formGroupExampleInput" placeholder="Placeholder">
                    </div>
                    <div class="mb-3">
                        <label for="formGroupExampleInput2" class="form-label">Apellido</label>
                        <input type="text" class="form-control" id="formGroupExampleInput2" placeholder="Placeholder">
                    </div>
                    <div class="mb-3">
                        <label for="formGroupExampleInput2" class="form-label">Email</label>
                        <input type="text" class="form-control" id="formGroupExampleInput2" placeholder="Placeholder">
                    </div>
                    <div class="mb-3">
                        <label for="formGroupExampleInput2" class="form-label">Mensaje</label>
                        <input type="text" class="form-control" id="formGroupExampleInput2" placeholder="Placeholder">
                    </div>
                    <button type="button" class="btn btn-dark" id="env">Enviar</button>
                </form>
            </div>
        </div>
    </body>
    <!-- pie pagina -->
    <footer>
        MURASAKI 2026. ©
    </footer>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Script -->
    <script src="script.js"></script>
</html>