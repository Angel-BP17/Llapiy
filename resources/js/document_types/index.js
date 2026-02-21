document.addEventListener('DOMContentLoaded', () => {
    const pageData = document.getElementById('document-types-page-data');
    if (!pageData) return;

    const parseJSON = (value, fallback) => {
        if (!value) return fallback;
        try {
            return JSON.parse(value);
        } catch {
            // Fallback for HTML-escaped JSON content in data-* attributes.
            try {
                const textArea = document.createElement('textarea');
                textArea.innerHTML = value;
                return JSON.parse(textArea.value);
            } catch {
                return fallback;
            }
        }
    };

    const parseMaybeJsonArray = (value) => {
        if (!value) return [];
        if (Array.isArray(value)) return value;
        if (typeof value === 'string') {
            try {
                const parsed = JSON.parse(value);
                return Array.isArray(parsed) ? parsed : [];
            } catch {
                return [];
            }
        }
        return [];
    };

    const escapeRegExp = (value) => value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

    const appendHighlightedText = (element, text, query) => {
        const source = String(text ?? '');
        const q = String(query ?? '').trim();
        element.textContent = '';

        if (!q) {
            element.textContent = source;
            return;
        }

        const regex = new RegExp(`(${escapeRegExp(q)})`, 'ig');
        const parts = source.split(regex);

        parts.forEach((part) => {
            if (!part) return;
            if (part.toLowerCase() === q.toLowerCase()) {
                const mark = document.createElement('mark');
                mark.className = 'dt-highlight';
                mark.textContent = part;
                element.appendChild(mark);
            } else {
                element.appendChild(document.createTextNode(part));
            }
        });
    };

    const areas = parseJSON(pageData.dataset.areas, []);
    const campoTypes = parseJSON(pageData.dataset.campoTypes, []);
    const oldModal = parseJSON(pageData.dataset.oldModal, null);
    const oldEditId = parseJSON(pageData.dataset.oldEditId, null);
    const oldEditName = parseJSON(pageData.dataset.oldEditName, '');
    const oldEditCampos = parseMaybeJsonArray(pageData.dataset.oldEditCampos);
    const oldEditGroups = parseMaybeJsonArray(pageData.dataset.oldEditGroups);
    const oldEditSubgroups = parseMaybeJsonArray(pageData.dataset.oldEditSubgroups);

    const createEls = {
        groupTree: document.getElementById('dt_create_groupTree'),
        selectedList: document.getElementById('dt_create_selectedItemsList'),
        groupInput: document.getElementById('dt_create_selectedGroupsInput'),
        subgroupInput: document.getElementById('dt_create_selectedSubgroupsInput'),
        treeSearch: document.getElementById('dt_create_treeSearch'),
        selectedCounter: document.getElementById('dt_create_selectedCounter'),
        clearSelectionBtn: document.getElementById('dt_create_clearSelectionBtn'),
        expandAllBtn: document.getElementById('dt_create_expandAllBtn'),
        collapseAllBtn: document.getElementById('dt_create_collapseAllBtn'),
        campoSearch: document.getElementById('dt_create_campoSearch'),
        campoResults: document.getElementById('dt_create_campoResults'),
        camposSeleccionados: document.getElementById('dt_create_camposSeleccionados'),
        camposInput: document.getElementById('dt_create_camposInput'),
        camposCounter: document.getElementById('dt_create_camposCounter'),
        clearCamposBtn: document.getElementById('dt_create_clearCamposBtn'),
    };

    const editEls = {
        groupTree: document.getElementById('dt_edit_groupTree'),
        selectedList: document.getElementById('dt_edit_selectedItemsList'),
        groupInput: document.getElementById('dt_edit_selectedGroupsInput'),
        subgroupInput: document.getElementById('dt_edit_selectedSubgroupsInput'),
        treeSearch: document.getElementById('dt_edit_treeSearch'),
        selectedCounter: document.getElementById('dt_edit_selectedCounter'),
        clearSelectionBtn: document.getElementById('dt_edit_clearSelectionBtn'),
        expandAllBtn: document.getElementById('dt_edit_expandAllBtn'),
        collapseAllBtn: document.getElementById('dt_edit_collapseAllBtn'),
        campoSearch: document.getElementById('dt_edit_campoSearch'),
        campoResults: document.getElementById('dt_edit_campoResults'),
        camposSeleccionados: document.getElementById('dt_edit_camposSeleccionados'),
        camposInput: document.getElementById('dt_edit_camposInput'),
        camposCounter: document.getElementById('dt_edit_camposCounter'),
        clearCamposBtn: document.getElementById('dt_edit_clearCamposBtn'),
    };

    const buildTree = (elements, preselectedGroups, preselectedSubgroups) => {
        const {
            groupTree,
            selectedList,
            groupInput,
            subgroupInput,
            treeSearch,
            selectedCounter,
            clearSelectionBtn,
            expandAllBtn,
            collapseAllBtn,
        } = elements;
        if (!groupTree || !selectedList || !groupInput || !subgroupInput) return;

        const selectedGroups = new Set([...preselectedGroups].map((id) => Number(id)));
        const selectedSubgroups = new Set([...preselectedSubgroups].map((id) => Number(id)));
        const collapsedAreas = new Set();

        const getAreaGroups = (area) => {
            const groups = [];
            (area.area_group_types || []).forEach((agt) => {
                (agt.groups || []).forEach((group) => groups.push(group));
            });
            return groups;
        };

        const collectSubgroupIds = (subgroup, bucket) => {
            bucket.push(subgroup.id);
            (subgroup.subgroups || []).forEach((child) => collectSubgroupIds(child, bucket));
        };

        const groupSubgroupIds = (group) => {
            const ids = [];
            (group.subgroups || []).forEach((sg) => collectSubgroupIds(sg, ids));
            return ids;
        };

        const subgroupMatchesSearch = (subgroup, query) => {
            if (!query) return true;
            if ((subgroup.descripcion || '').toLowerCase().includes(query)) return true;
            return (subgroup.subgroups || []).some((child) => subgroupMatchesSearch(child, query));
        };

        const toggleSubgroupRecursive = (subgroup, checked) => {
            if (checked) {
                selectedSubgroups.add(subgroup.id);
            } else {
                selectedSubgroups.delete(subgroup.id);
            }
            (subgroup.subgroups || []).forEach((child) => toggleSubgroupRecursive(child, checked));
        };

        const renderSubgroupRecursive = (subgroup, query, rawQuery, forceVisible = false) => {
            const subgroupText = (subgroup.descripcion || '').toLowerCase();
            const selfMatch = !query || subgroupText.includes(query);
            const childrenMatch = (subgroup.subgroups || []).some((child) => subgroupMatchesSearch(child, query));
            const shouldShow = forceVisible || selfMatch || childrenMatch;
            if (!shouldShow) return null;

            const wrapper = document.createElement('div');
            wrapper.className = 'mb-1';

            const row = document.createElement('div');
            row.className = 'dt-subgroup-row';

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.className = 'checkbox-subgrupo checkbox-template';
            checkbox.checked = selectedSubgroups.has(subgroup.id);
            checkbox.addEventListener('change', () => {
                toggleSubgroupRecursive(subgroup, checkbox.checked);
                renderAll();
            });

            const label = document.createElement('label');
            label.className = 'mb-0';
            label.appendChild(document.createTextNode('Subgrupo: '));
            const textSpan = document.createElement('span');
            appendHighlightedText(textSpan, subgroup.descripcion, rawQuery);
            label.appendChild(textSpan);

            row.appendChild(checkbox);
            row.appendChild(label);
            wrapper.appendChild(row);

            const visibleChildren = (subgroup.subgroups || [])
                .map((child) => renderSubgroupRecursive(child, query, rawQuery, forceVisible || selfMatch))
                .filter(Boolean);

            if (visibleChildren.length > 0) {
                const childContainer = document.createElement('div');
                childContainer.className = 'dt-subgroup-children';
                visibleChildren.forEach((childNode) => childContainer.appendChild(childNode));
                wrapper.appendChild(childContainer);
            }

            return wrapper;
        };

        const renderTree = () => {
            const rawQuery = (treeSearch?.value || '').trim();
            const query = rawQuery.toLowerCase();
            groupTree.innerHTML = '';

            let visibleAreas = 0;

            areas.forEach((area) => {
                const groups = getAreaGroups(area);
                const areaMatch = (area.descripcion || '').toLowerCase().includes(query);
                const hasGroupMatch = groups.some((group) => {
                    const groupMatch = (group.descripcion || '').toLowerCase().includes(query);
                    const subgroupMatch = (group.subgroups || []).some((sg) => subgroupMatchesSearch(sg, query));
                    return groupMatch || subgroupMatch;
                });

                if (query && !areaMatch && !hasGroupMatch) return;
                visibleAreas += 1;

                const areaCard = document.createElement('div');
                areaCard.className = 'dt-area-card';

                const areaHead = document.createElement('div');
                areaHead.className = 'dt-area-head d-flex align-items-center justify-content-between';
                const isCollapsed = query ? false : collapsedAreas.has(area.id);

                const areaLabel = document.createElement('label');
                areaLabel.className = 'mb-0 font-weight-bold d-flex align-items-center';

                const areaCheckbox = document.createElement('input');
                areaCheckbox.type = 'checkbox';
                areaCheckbox.className = 'checkbox-area checkbox-template mr-2';

                const areaGroupIds = groups.map((group) => group.id);
                const areaSubgroupIds = groups.flatMap((group) => groupSubgroupIds(group));
                const allGroupsChecked = areaGroupIds.length > 0 && areaGroupIds.every((id) => selectedGroups.has(id));
                const allSubgroupsChecked = areaSubgroupIds.every((id) => selectedSubgroups.has(id));
                const someChecked = areaGroupIds.some((id) => selectedGroups.has(id)) || areaSubgroupIds.some((id) => selectedSubgroups.has(id));
                areaCheckbox.checked = allGroupsChecked && allSubgroupsChecked;
                areaCheckbox.indeterminate = !areaCheckbox.checked && someChecked;

                areaCheckbox.addEventListener('change', () => {
                    const checked = areaCheckbox.checked;
                    groups.forEach((group) => {
                        if (checked) {
                            selectedGroups.add(group.id);
                        } else {
                            selectedGroups.delete(group.id);
                        }
                        (group.subgroups || []).forEach((sg) => toggleSubgroupRecursive(sg, checked));
                    });
                    renderAll();
                });

                areaLabel.appendChild(areaCheckbox);
                const areaText = document.createElement('span');
                appendHighlightedText(areaText, area.descripcion, rawQuery);
                areaLabel.appendChild(areaText);

                const areaCount = document.createElement('span');
                areaCount.className = 'badge badge-light';
                areaCount.textContent = `${groups.length} grupo(s)`;

                const headRight = document.createElement('div');
                headRight.className = 'd-flex align-items-center';

                const toggleAreaBtn = document.createElement('button');
                toggleAreaBtn.type = 'button';
                toggleAreaBtn.className = 'btn btn-link btn-sm p-0 mr-2';
                toggleAreaBtn.textContent = isCollapsed ? 'Expandir' : 'Contraer';
                toggleAreaBtn.addEventListener('click', () => {
                    if (collapsedAreas.has(area.id)) {
                        collapsedAreas.delete(area.id);
                    } else {
                        collapsedAreas.add(area.id);
                    }
                    renderTree();
                });

                headRight.appendChild(toggleAreaBtn);
                headRight.appendChild(areaCount);

                areaHead.appendChild(areaLabel);
                areaHead.appendChild(headRight);
                areaCard.appendChild(areaHead);

                const areaBody = document.createElement('div');
                areaBody.className = 'dt-area-body';
                if (isCollapsed) {
                    areaBody.style.display = 'none';
                }

                groups.forEach((group) => {
                    const groupMatch = (group.descripcion || '').toLowerCase().includes(query);
                    const subgroupMatch = (group.subgroups || []).some((sg) => subgroupMatchesSearch(sg, query));
                    if (query && !groupMatch && !subgroupMatch) return;

                    const groupRow = document.createElement('div');
                    groupRow.className = 'dt-group-row';

                    const groupCheckbox = document.createElement('input');
                    groupCheckbox.type = 'checkbox';
                    groupCheckbox.className = 'checkbox-group checkbox-template';
                    groupCheckbox.checked = selectedGroups.has(group.id);

                    const subIds = groupSubgroupIds(group);
                    const selectedSubCount = subIds.filter((id) => selectedSubgroups.has(id)).length;
                    if (selectedSubCount > 0 && selectedSubCount < subIds.length) {
                        groupCheckbox.indeterminate = true;
                    }

                    groupCheckbox.addEventListener('change', () => {
                        if (groupCheckbox.checked) {
                            selectedGroups.add(group.id);
                        } else {
                            selectedGroups.delete(group.id);
                        }
                        (group.subgroups || []).forEach((sg) => toggleSubgroupRecursive(sg, groupCheckbox.checked));
                        renderAll();
                    });

                    const groupLabel = document.createElement('label');
                    groupLabel.className = 'mb-0';
                    groupLabel.appendChild(document.createTextNode('Grupo: '));
                    const groupText = document.createElement('span');
                    appendHighlightedText(groupText, group.descripcion, rawQuery);
                    groupLabel.appendChild(groupText);

                    groupRow.appendChild(groupCheckbox);
                    groupRow.appendChild(groupLabel);
                    areaBody.appendChild(groupRow);

                    const subgroupNodes = (group.subgroups || [])
                        .map((sg) => renderSubgroupRecursive(sg, query, rawQuery))
                        .filter(Boolean);

                    if (subgroupNodes.length > 0) {
                        const subgroupContainer = document.createElement('div');
                        subgroupContainer.className = 'dt-subgroup-children';
                        subgroupNodes.forEach((node) => subgroupContainer.appendChild(node));
                        areaBody.appendChild(subgroupContainer);
                    }
                });

                areaCard.appendChild(areaBody);
                groupTree.appendChild(areaCard);
            });

            if (visibleAreas === 0) {
                groupTree.innerHTML = '<p class="text-muted mb-0 p-2">No hay resultados para la búsqueda actual.</p>';
            }
        };

        const renderSelectedItems = () => {
            const items = [];

            areas.forEach((area) => {
                getAreaGroups(area).forEach((group) => {
                    if (selectedGroups.has(group.id)) {
                        items.push({
                            type: 'group',
                            id: group.id,
                            label: `Grupo: ${group.descripcion}`,
                        });
                    }

                    const appendSubgroup = (subgroup) => {
                        if (selectedSubgroups.has(subgroup.id)) {
                            items.push({
                                type: 'subgroup',
                                id: subgroup.id,
                                label: `Subgrupo: ${subgroup.descripcion}`,
                            });
                        }
                        (subgroup.subgroups || []).forEach((child) => appendSubgroup(child));
                    };

                    (group.subgroups || []).forEach((sg) => appendSubgroup(sg));
                });
            });

            selectedList.innerHTML = '';
            if (items.length === 0) {
                selectedList.innerHTML = '<p class="dt-empty-selection">Aún no hay grupos o subgrupos seleccionados.</p>';
            } else {
                items.forEach((item) => {
                    const chip = document.createElement('span');
                    chip.className = 'dt-selection-chip';
                    chip.textContent = item.label;

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.setAttribute('aria-label', `Quitar ${item.label}`);
                    removeBtn.textContent = '×';
                    removeBtn.addEventListener('click', () => {
                        if (item.type === 'group') {
                            selectedGroups.delete(item.id);
                        } else {
                            selectedSubgroups.delete(item.id);
                        }
                        renderAll();
                    });

                    chip.appendChild(removeBtn);
                    selectedList.appendChild(chip);
                });
            }

            if (selectedCounter) selectedCounter.textContent = String(items.length);
            groupInput.value = JSON.stringify([...selectedGroups]);
            subgroupInput.value = JSON.stringify([...selectedSubgroups]);
        };

        const renderAll = () => {
            renderTree();
            renderSelectedItems();
        };

        if (treeSearch) {
            treeSearch.oninput = () => renderTree();
        }

        if (expandAllBtn) {
            expandAllBtn.onclick = () => {
                collapsedAreas.clear();
                renderTree();
            };
        }

        if (collapseAllBtn) {
            collapseAllBtn.onclick = () => {
                collapsedAreas.clear();
                areas.forEach((area) => collapsedAreas.add(area.id));
                renderTree();
            };
        }

        if (clearSelectionBtn) {
            clearSelectionBtn.onclick = () => {
                selectedGroups.clear();
                selectedSubgroups.clear();
                renderAll();
            };
        }

        renderAll();
    };

    const initCampoSelector = (elements, preselected = []) => {
        const { campoSearch, campoResults, camposSeleccionados, camposInput, camposCounter, clearCamposBtn } = elements;
        if (!campoSearch || !campoResults || !camposSeleccionados || !camposInput) return;

        const selected = new Set((preselected || []).map((id) => Number(id)));

        const filterCampoTypes = (query) => {
            if (!query) return campoTypes;
            const q = query.toLowerCase();
            return campoTypes.filter((c) => c.name.toLowerCase().includes(q));
        };

        const renderSelected = () => {
            camposSeleccionados.innerHTML = '';
            const selectedItems = campoTypes.filter((item) => selected.has(Number(item.id)));

            if (selectedItems.length === 0) {
                camposSeleccionados.innerHTML = '<p class="dt-empty-selection">No hay campos seleccionados.</p>';
            } else {
                selectedItems.forEach((item) => {
                    const chip = document.createElement('span');
                    chip.className = 'dt-selection-chip dt-selection-chip-green';
                    chip.textContent = item.name;

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.setAttribute('aria-label', `Quitar campo ${item.name}`);
                    removeBtn.textContent = '×';
                    removeBtn.addEventListener('click', () => {
                        selected.delete(Number(item.id));
                        renderResults(filterCampoTypes(campoSearch.value.trim()));
                    });

                    chip.appendChild(removeBtn);
                    camposSeleccionados.appendChild(chip);
                });
            }

            if (camposCounter) camposCounter.textContent = String(selectedItems.length);
            camposInput.value = JSON.stringify([...selected]);
        };

        const renderResults = (results) => {
            campoResults.innerHTML = '';
            const rawQuery = campoSearch.value.trim();

            if (!results.length) {
                campoResults.innerHTML = '<p class="text-muted mb-0 p-2">No se encontraron campos.</p>';
                renderSelected();
                return;
            }

            results.forEach((campoType) => {
                const row = document.createElement('div');
                row.className = 'dt-campo-item';

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.value = String(campoType.id);
                checkbox.className = 'checkbox-template';
                checkbox.checked = selected.has(Number(campoType.id));
                checkbox.addEventListener('change', () => {
                    if (checkbox.checked) {
                        selected.add(Number(campoType.id));
                    } else {
                        selected.delete(Number(campoType.id));
                    }
                    renderSelected();
                });

                const label = document.createElement('label');
                label.className = 'mb-0 ml-2';
                appendHighlightedText(label, campoType.name, rawQuery);

                row.appendChild(checkbox);
                row.appendChild(label);
                campoResults.appendChild(row);
            });
            renderSelected();
        };

        campoSearch.oninput = function () {
            renderResults(filterCampoTypes(this.value.trim()));
        };

        if (clearCamposBtn) {
            clearCamposBtn.onclick = () => {
                selected.clear();
                renderResults(filterCampoTypes(campoSearch.value.trim()));
            };
        }

        renderResults(campoTypes);
    };

    const openCreateModal = () => {
        if (createEls.treeSearch) createEls.treeSearch.value = '';
        if (createEls.campoSearch) createEls.campoSearch.value = '';
        buildTree(createEls, new Set(), new Set());
        initCampoSelector(createEls, []);
    };

    const openEditModal = (data) => {
        if (!data) return;
        const form = document.getElementById('documentTypeEditForm');
        const template = form?.dataset.actionTemplate;
        if (form && template) {
            form.action = template.replace('DT_ID', data.id);
        }
        document.getElementById('edit_document_type_id').value = data.id || '';
        const nameInput = document.getElementById('dt_edit_name');
        if (nameInput) nameInput.value = data.name || '';

        if (editEls.treeSearch) editEls.treeSearch.value = '';
        if (editEls.campoSearch) editEls.campoSearch.value = '';
        buildTree(editEls, new Set(data.groups || []), new Set(data.subgroups || []));
        initCampoSelector(editEls, data.campoTypes || []);
    };

    document.querySelectorAll('.js-document-type-edit').forEach((button) => {
        button.addEventListener('click', function () {
            const data = parseJSON(this.dataset.documentType, null);
            openEditModal(data);
        });
    });

    if (oldModal === 'create' && window.$) {
        openCreateModal();
        window.$('#documentTypeCreateModal').modal('show');
    }

    if (oldModal === 'edit' && oldEditId) {
        const data = {
            id: oldEditId,
            name: oldEditName,
            campoTypes: oldEditCampos,
            groups: oldEditGroups,
            subgroups: oldEditSubgroups,
        };
        openEditModal(data);
        if (window.$) {
            window.$('#documentTypeEditModal').modal('show');
        }
    }

    const createModal = document.getElementById('documentTypeCreateModal');
    const createModalTrigger = document.querySelector('[data-target="#documentTypeCreateModal"]');

    if (createModalTrigger) {
        createModalTrigger.addEventListener('click', openCreateModal);
    }

    if (window.$ && createModal) {
        window.$(createModal).on('shown.bs.modal', openCreateModal);
    } else {
        createModal?.addEventListener('shown.bs.modal', openCreateModal);
    }
});
