document.addEventListener("DOMContentLoaded", function () {
    const areas = window.areas;

    const areaSelect = document.getElementById("area_id");
    const groupSelect = document.getElementById("group_id");
    const subgroupSelect = document.getElementById("subgroup_id");

    const selectedAreaId = window.selectedAreaId || null;
    const selectedGroupId = window.selectedGroupId || null;
    const selectedSubgroupId = window.selectedSubgroupId || null;

    function clearSelect(selectEl, placeholderText) {
        selectEl.innerHTML = `<option value="">${placeholderText}</option>`;
        selectEl.disabled = true;
    }

    function loadGroups(areaId, selectedGroupId = null) {
        clearSelect(groupSelect, "-- Seleccionar Grupo --");
        clearSelect(subgroupSelect, "-- Seleccionar Subgrupo --");

        if (!areaId) return;

        const area = areas.find((a) => a.id == areaId);
        if (!area || !Array.isArray(area.area_group_types)) return;

        const addedGroupIds = new Set();

        area.area_group_types.forEach((agt) => {
            (agt.groups || []).forEach((group) => {
                if (addedGroupIds.has(group.id)) return;

                const opt = document.createElement("option");
                opt.value = group.id;
                opt.textContent = group.descripcion;

                if (selectedGroupId && group.id == selectedGroupId) {
                    opt.selected = true;
                }

                groupSelect.appendChild(opt);
                addedGroupIds.add(group.id);
            });
        });

        groupSelect.disabled = false;

        if (selectedGroupId) {
            loadSubgroups(selectedGroupId, selectedSubgroupId);
        }
    }

    function loadSubgroups(groupId, selectedSubgroupId = null) {
        clearSelect(subgroupSelect, "-- Seleccionar Subgrupo --");
        if (!groupId) return;

        let subgroups = [];

        areas.forEach((area) => {
            (area.area_group_types || []).forEach((agt) => {
                (agt.groups || []).forEach((group) => {
                    if (group.id == groupId && Array.isArray(group.subgroups)) {
                        subgroups = group.subgroups;
                    }
                });
            });
        });

        subgroups.forEach((sg) => {
            const opt = document.createElement("option");
            opt.value = sg.id;
            opt.textContent = sg.descripcion;

            if (selectedSubgroupId && sg.id == selectedSubgroupId) {
                opt.selected = true;
            }

            subgroupSelect.appendChild(opt);
        });
        subgroupSelect.disabled = false;
    }

    // Eventos
    areaSelect.addEventListener("change", function () {
        loadGroups(this.value);
    });

    groupSelect.addEventListener("change", function () {
        loadSubgroups(this.value);
    });

    // Restaurar selección anterior
    if (selectedAreaId) {
        areaSelect.value = selectedAreaId;
        loadGroups(selectedAreaId, selectedGroupId);
    } else {
        groupSelect.disabled = true;
        subgroupSelect.disabled = true;
    }
});
