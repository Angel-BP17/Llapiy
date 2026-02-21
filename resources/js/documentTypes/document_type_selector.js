document.addEventListener("DOMContentLoaded", function () {
    const areas = window.areas;
    if (!Array.isArray(areas)) return;

    const selectedGroups = new Set(window.preselectedGroups || []);
    const selectedSubgroups = new Set(window.preselectedSubgroups || []);

    const treeContainer = document.getElementById("groupTree");
    const selectedList = document.getElementById("selectedItemsList");
    const groupInput = document.getElementById("selectedGroupsInput");
    const subgroupInput = document.getElementById("selectedSubgroupsInput");
    if (!treeContainer || !selectedList || !groupInput || !subgroupInput) return;

    function renderTree() {
        treeContainer.innerHTML = "";
        treeContainer.classList.add("bg-white");

        areas.forEach((area) => {
            const areaWrapper = document.createElement("div");
            areaWrapper.classList.add("mb-2");

            const areaCheckbox = document.createElement("input");
            areaCheckbox.type = "checkbox";
            areaCheckbox.classList.add("checkbox-area", "checkbox-template");
            areaCheckbox.dataset.areaId = area.id;
            areaCheckbox.addEventListener("change", () => {
                const checked = areaCheckbox.checked;
                (area.area_group_types || []).forEach((agt) => {
                    (agt.groups || []).forEach((group) => {
                        checked
                            ? selectedGroups.add(group.id)
                            : selectedGroups.delete(group.id);
                        (group.subgroups || []).forEach((sg) =>
                            toggleSubgroupRecursive(sg, checked)
                        );
                    });
                });
                updateDisplay();
                renderTree(); // re-render para reflejar los checkboxes
            });

            const label = document.createElement("label");
            label.classList.add("font-weight-bold");
            label.appendChild(areaCheckbox);
            label.append(" " + area.descripcion);
            areaWrapper.appendChild(label);

            (area.area_group_types || []).forEach((agt) => {
                (agt.groups || []).forEach((group) => {
                    const groupDiv = document.createElement("div");
                    groupDiv.classList.add("ml-3");

                    const groupCheckbox = document.createElement("input");
                    groupCheckbox.type = "checkbox";
                    groupCheckbox.classList.add(
                        "checkbox-group",
                        "checkbox-template"
                    );
                    groupCheckbox.dataset.groupId = group.id;
                    groupCheckbox.checked = selectedGroups.has(group.id);
                    groupCheckbox.addEventListener("change", () => {
                        if (groupCheckbox.checked) {
                            selectedGroups.add(group.id);
                        } else {
                            selectedGroups.delete(group.id);
                        }
                        (group.subgroups || []).forEach((sg) =>
                            toggleSubgroupRecursive(sg, groupCheckbox.checked)
                        );
                        updateDisplay();
                    });

                    const groupLabel = document.createElement("label");
                    groupLabel.appendChild(groupCheckbox);
                    groupLabel.append(" Grupo: " + group.descripcion);
                    groupDiv.appendChild(groupLabel);

                    // Recursivamente renderizar subgrupos
                    if (group.subgroups && group.subgroups.length > 0) {
                        const subgroupList = document.createElement("ul");
                        subgroupList.classList.add("ml-4");
                        group.subgroups.forEach((sg) => {
                            const sgLi = renderSubgroupRecursive(sg);
                            subgroupList.appendChild(sgLi);
                        });
                        groupDiv.appendChild(subgroupList);
                    }

                    areaWrapper.appendChild(groupDiv);
                });
            });

            treeContainer.appendChild(areaWrapper);
        });
    }

    function renderSubgroupRecursive(subgroup) {
        const li = document.createElement("li");
        li.classList.add("subgrupo-item");

        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.classList.add("checkbox-subgrupo", "checkbox-template");
        checkbox.value = subgroup.id;
        checkbox.checked = selectedSubgroups.has(subgroup.id);
        checkbox.addEventListener("change", () => {
            toggleSubgroupRecursive(subgroup, checkbox.checked);
            updateDisplay();
            renderTree(); // Para reflejar el cambio recursivamente
        });

        const label = document.createElement("label");
        label.appendChild(checkbox);
        label.append(" Subgrupo: " + subgroup.descripcion);

        li.appendChild(label);

        if (subgroup.subgroups && subgroup.subgroups.length > 0) {
            const ul = document.createElement("ul");
            ul.classList.add("ml-4");
            subgroup.subgroups.forEach((child) => {
                ul.appendChild(renderSubgroupRecursive(child));
            });
            li.appendChild(ul);
        }

        return li;
    }

    function toggleSubgroupRecursive(subgroup, checked) {
        if (checked) {
            selectedSubgroups.add(subgroup.id);
        } else {
            selectedSubgroups.delete(subgroup.id);
        }

        if (subgroup.subgroups && subgroup.subgroups.length > 0) {
            subgroup.subgroups.forEach((child) => {
                toggleSubgroupRecursive(child, checked);
            });
        }
    }

    function updateDisplay() {
        if (!selectedList) return;

        selectedList.innerHTML = "";

        areas.forEach((area) => {
            (area.area_group_types || []).forEach((agt) => {
                (agt.groups || []).forEach((group) => {
                    if (selectedGroups.has(group.id)) {
                        const li = document.createElement("li");
                        li.textContent = "Grupo: " + group.descripcion;
                        selectedList.appendChild(li);
                    }
                    (group.subgroups || []).forEach((sg) => {
                        appendSubgroupNamesRecursive(sg);
                    });
                });
            });
        });

        groupInput.value = JSON.stringify([...selectedGroups]);
        subgroupInput.value = JSON.stringify([...selectedSubgroups]);
    }

    function appendSubgroupNamesRecursive(subgroup) {
        if (selectedSubgroups.has(subgroup.id)) {
            const li = document.createElement("li");
            li.textContent = "Subgrupo: " + subgroup.descripcion;
            selectedList.appendChild(li);
        }

        if (subgroup.subgroups && subgroup.subgroups.length > 0) {
            subgroup.subgroups.forEach((child) => {
                appendSubgroupNamesRecursive(child);
            });
        }
    }

    renderTree();
    updateDisplay();
});
