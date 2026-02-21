document.addEventListener("DOMContentLoaded", function () {
    const areas = window.areas;
    if (!Array.isArray(areas)) return;
    const user = window.user || null;

    // Buscar los selects existentes en el DOM, sin depender de nombres exactos
    const areaSelect = window.selectedAreaId;
    const groupTypeSelect = window.selectedGroupType;
    const groupSelect = window.selectedGroupId;
    const subgroupSelect = window.selectedSubgroupId;
    const isSelectElement = (el) => el instanceof HTMLSelectElement;
    if (
        !isSelectElement(areaSelect) ||
        !isSelectElement(groupTypeSelect) ||
        !isSelectElement(groupSelect) ||
        !isSelectElement(subgroupSelect)
    ) {
        return;
    }

    function clearSelect(select, placeholder) {
        if (!select) return;
        select.innerHTML = `<option value="">${placeholder}</option>`;
        select.disabled = true;
    }

    function loadAreas(selectedAreaId = null) {
        if (!areaSelect) return;
        clearSelect(areaSelect, "Seleccione un área");
        areas.forEach((area) => {
            const selected = area.id == selectedAreaId ? "selected" : "";
            areaSelect.innerHTML += `<option value="${area.id}" ${selected}>${area.descripcion}</option>`;
        });
        areaSelect.disabled = false;
    }

    function loadGroupTypes(areaId, selectedGroupTypeId = null) {
        clearSelect(groupTypeSelect, "Seleccione un tipo de grupo");
        clearSelect(groupSelect, "Seleccione un grupo");
        clearSelect(subgroupSelect, "Seleccione un subgrupo");

        if (!areaId) return;

        const area = areas.find((a) => a.id == areaId);
        if (!area || !Array.isArray(area.area_group_types)) return;

        const types = area.area_group_types;
        const loaded = new Set();

        types.forEach((agt) => {
            if (!agt.group_type || loaded.has(agt.group_type.id)) return;
            const selected =
                agt.group_type.id == selectedGroupTypeId ? "selected" : "";
            groupTypeSelect.innerHTML += `<option value="${agt.group_type.id}" ${selected}>${agt.group_type.descripcion}</option>`;
            loaded.add(agt.group_type.id);
        });

        groupTypeSelect.disabled = false;
    }

    function loadGroups(areaId, groupTypeId, selectedGroupId = null) {
        clearSelect(groupSelect, "Seleccione un grupo");
        clearSelect(subgroupSelect, "Seleccione un subgrupo");

        if (!areaId || !groupTypeId) return;

        const area = areas.find((a) => a.id == areaId);
        if (!area || !Array.isArray(area.area_group_types)) return;

        area.area_group_types.forEach((agt) => {
            const matches =
                agt.group_type &&
                (agt.group_type.id == groupTypeId ||
                    agt.group_type_id == groupTypeId);
            if (!matches) return;

            (agt.groups || []).forEach((group) => {
                const selected = group.id == selectedGroupId ? "selected" : "";
                groupSelect.innerHTML += `<option value="${group.id}" ${selected}>${group.descripcion}</option>`;
            });
        });

        groupSelect.disabled = false;
    }

    function loadSubgroups(groupId, selectedSubgroupId = null) {
        clearSelect(subgroupSelect, "Seleccione un subgrupo");
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
            const selected = sg.id == selectedSubgroupId ? "selected" : "";
            subgroupSelect.innerHTML += `<option value="${sg.id}" ${selected}>${sg.descripcion}</option>`;
        });

        subgroupSelect.disabled = false;
    }

    // Detectar datos iniciales
    const selectedAreaId = user?.group?.area_group_type?.area?.id || null;
    const selectedGroupTypeId =
        user?.group?.area_group_type?.group_type?.id || null;
    const selectedGroupId = user?.group?.id || null;
    const selectedSubgroupId = user?.subgroup?.id || null;

    // Inicialización
    loadAreas(selectedAreaId);
    if (selectedAreaId) {
        loadGroupTypes(selectedAreaId, selectedGroupTypeId);
        if (selectedGroupTypeId) {
            loadGroups(selectedAreaId, selectedGroupTypeId, selectedGroupId);
            if (selectedGroupId) {
                loadSubgroups(selectedGroupId, selectedSubgroupId);
            }
        }
    }

    // Eventos
    areaSelect?.addEventListener("change", function () {
        const areaId = this.value;
        loadGroupTypes(areaId);
        clearSelect(groupSelect, "Seleccione un grupo");
        clearSelect(subgroupSelect, "Seleccione un subgrupo");
    });

    groupTypeSelect?.addEventListener("change", function () {
        const areaId = areaSelect?.value;
        const groupTypeId = this.value;
        loadGroups(areaId, groupTypeId);
        clearSelect(subgroupSelect, "Seleccione un subgrupo");
    });

    groupSelect?.addEventListener("change", function () {
        loadSubgroups(this.value);
    });
});
