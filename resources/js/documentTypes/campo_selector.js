document.addEventListener("DOMContentLoaded", function () {
    // 1. Obtener datos desde el entorno (window o <script> en Blade)
    const campoTypes = window.campoTypes || []; // siempre es requerido
    const precargados = window.selectedCampoTypes || []; // solo en edit
    let selectedCampoTypes = [...precargados];

    // 2. Elementos del DOM
    const campoSearch = document.getElementById("campoSearch");
    const campoResults = document.getElementById("campoResults");
    const camposSeleccionadosUl = document.getElementById(
        "camposSeleccionados"
    );
    const camposInput = document.getElementById("camposInput");

    // 3. Filtrar por nombre
    function filterCampoTypes(query) {
        if (!query) return campoTypes;
        query = query.toLowerCase();
        return campoTypes.filter((c) => c.name.toLowerCase().includes(query));
    }

    // 4. Renderizar resultados filtrados
    function renderResults(results) {
        campoResults.innerHTML = "";

        results.forEach((campoType) => {
            const checkbox = document.createElement("input");
            checkbox.type = "checkbox";
            checkbox.value = campoType.id;
            checkbox.className = "checkbox-template";
            checkbox.dataset.name = campoType.name;
            checkbox.checked = selectedCampoTypes.includes(campoType.id);
            checkbox.addEventListener("change", updateCamposSeleccionados);

            const label = document.createElement("label");
            label.appendChild(checkbox);
            label.appendChild(document.createTextNode(" " + campoType.name));

            const div = document.createElement("div");
            div.appendChild(label);
            campoResults.appendChild(div);
        });

        updateCamposSeleccionados();
    }

    // 5. Actualizar campos seleccionados
    function updateCamposSeleccionados() {
        let seleccionados = [];
        let seleccionadosNombres = [];

        campoResults.querySelectorAll("input:checked").forEach((el) => {
            seleccionados.push(parseInt(el.value));
            seleccionadosNombres.push(el.dataset.name);
        });

        camposSeleccionadosUl.innerHTML = seleccionadosNombres
            .map((name) => `<li>${name}</li>`)
            .join("");
        camposInput.value = JSON.stringify(seleccionados);
        selectedCampoTypes = seleccionados;
    }

    // 6. Buscar al escribir
    campoSearch.addEventListener("keyup", function () {
        const filtered = filterCampoTypes(this.value);
        renderResults(filtered);
    });

    // 7. Asegurar el envío correcto
    document.querySelector("form").addEventListener("submit", function () {
        camposInput.value = JSON.stringify(selectedCampoTypes);
    });

    // 8. Mostrar todos los campos al iniciar
    renderResults(campoTypes);
});
