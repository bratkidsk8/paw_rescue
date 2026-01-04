<script>
document.addEventListener("DOMContentLoaded", () => {

  const usuarioLogueado = <?= isset($_SESSION['id_usuario']) ? 'true' : 'false' ?>;

  const btnReporte = document.getElementById("btn-reporte");
  const btnLoginReporte = document.getElementById("btn-login-reporte");

  if (btnReporte) {
    const modalReporte = new bootstrap.Modal(document.getElementById("modalReporte"));
    btnReporte.addEventListener("click", () => {
      modalReporte.show();
    });
  }

  if (btnLoginReporte) {
    const modalLogin = new bootstrap.Modal(document.getElementById("modalLogin"));
    btnLoginReporte.addEventListener("click", () => {
      modalLogin.show();
    });
  }

});
</script>
