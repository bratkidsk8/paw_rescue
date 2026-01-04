function toggleListaNegra(valor) {
    const bloque = document.getElementById("datos_lista_negra");
    if (!bloque) return;

    if (valor === "SI") {
        bloque.style.display = "block";
    } else {
        bloque.style.display = "none";
    }
}
