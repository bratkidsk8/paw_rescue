document.addEventListener("DOMContentLoaded", () => {

  const origen = document.getElementById("origen");
  const bloqueLN = document.getElementById("datos_lista_negra");

  origen.addEventListener("change", () => {
    if (origen.value === "retiro") {
      bloqueLN.style.display = "block";
    } else {
      bloqueLN.style.display = "none";
      bloqueLN.querySelectorAll("input, textarea").forEach(el => el.value = "");
    }
  });

  /* ===== ESPECIE → RAZA ===== */
  const especie = document.getElementById("id_esp");
  const raza = document.getElementById("id_raza");

  especie.addEventListener("change", () => {
    raza.innerHTML = `<option>Cargando...</option>`;
    raza.disabled = true;

    if (!especie.value) return;

    fetch(`tipoRaza.php?id_esp=${especie.value}`)
      .then(res => res.json())
      .then(data => {
        raza.innerHTML = `<option value="">Raza</option>`;
        data.forEach(r => {
          raza.innerHTML += `<option value="${r.id_raza}">${r.nombre}</option>`;
        });
        raza.disabled = false;
      });
  });

});
