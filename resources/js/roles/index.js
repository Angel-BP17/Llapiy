document.addEventListener('DOMContentLoaded', () => {
    const pageData = document.getElementById('roles-page-data');
    if (!pageData) return;

    const parseJSON = (value, fallback) => {
        if (!value) return fallback;
        try {
            return JSON.parse(value);
        } catch {
            try {
                const textArea = document.createElement('textarea');
                textArea.innerHTML = value;
                return JSON.parse(textArea.value);
            } catch {
                return fallback;
            }
        }
    };

    const oldModal = parseJSON(pageData.dataset.oldModal, null);
    const oldEditRoleId = parseJSON(pageData.dataset.oldEditRoleId, null);
    const oldEditPermissions = parseJSON(pageData.dataset.oldEditPermissions, []);
    const oldEditName = parseJSON(pageData.dataset.oldEditName, '');

    const escapeRegExp = (value) => value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

    const highlightNode = (element, text, query) => {
        const source = String(text ?? '');
        const q = String(query ?? '').trim();
        element.textContent = '';

        if (!q) {
            element.textContent = source;
            return;
        }

        const regex = new RegExp(`(${escapeRegExp(q)})`, 'ig');
        source.split(regex).forEach((part) => {
            if (!part) return;
            if (part.toLowerCase() === q.toLowerCase()) {
                const mark = document.createElement('mark');
                mark.className = 'permission-highlight';
                mark.textContent = part;
                element.appendChild(mark);
            } else {
                element.appendChild(document.createTextNode(part));
            }
        });
    };

    const syncGroupState = (group) => {
        const moduleCheckbox = group.querySelector('.permission-module-checkbox');
        const actionCheckboxes = Array.from(group.querySelectorAll('.permission-action-checkbox'));

        if (!moduleCheckbox || actionCheckboxes.length === 0) return;

        const checkedCount = actionCheckboxes.filter((checkbox) => checkbox.checked).length;

        if (checkedCount === 0) {
            moduleCheckbox.checked = false;
            moduleCheckbox.indeterminate = false;
            return;
        }

        if (checkedCount === actionCheckboxes.length) {
            moduleCheckbox.checked = true;
            moduleCheckbox.indeterminate = false;
            return;
        }

        moduleCheckbox.checked = false;
        moduleCheckbox.indeterminate = true;
    };

    const setGroupCollapsed = (group, collapsed) => {
        const toggleBtn = group.querySelector('.permission-toggle-btn');
        const targetId = toggleBtn?.dataset.target;
        if (!toggleBtn || !targetId) return;

        const itemsContainer = group.querySelector(`#${targetId}`);
        if (!itemsContainer) return;

        itemsContainer.classList.toggle('is-collapsed', collapsed);
        toggleBtn.textContent = collapsed ? 'Expandir' : 'Contraer';
    };

    const initPermissionUX = (formEl, config) => {
        if (!formEl || !config) return;

        const searchInput = document.getElementById(config.searchInputId);
        const selectedContainer = document.getElementById(config.selectedContainerId);
        const countEl = document.getElementById(config.countId);
        const clearBtn = document.getElementById(config.clearBtnId);
        const selectAllBtn = document.getElementById(config.selectAllBtnId);
        const expandAllBtn = document.getElementById(config.expandAllBtnId);
        const collapseAllBtn = document.getElementById(config.collapseAllBtnId);

        if (!searchInput || !selectedContainer || !countEl || !clearBtn || !selectAllBtn || !expandAllBtn || !collapseAllBtn) {
            return;
        }

        const groups = () => Array.from(formEl.querySelectorAll('.permission-group'));

        const allPermissionCheckboxes = () =>
            Array.from(formEl.querySelectorAll('input[name="permissions[]"]'));

        const labelForCheckbox = (checkbox) => {
            const label = checkbox.closest('label');
            if (!label) return checkbox.value;
            const textNode = label.querySelector('.permission-action-label, .permission-module-label');
            return (textNode?.textContent || checkbox.value).trim();
        };

        const selectedCheckboxes = () => allPermissionCheckboxes().filter((checkbox) => checkbox.checked);

        const removePermission = (value) => {
            allPermissionCheckboxes().forEach((checkbox) => {
                if (checkbox.value === value) {
                    checkbox.checked = false;
                }
            });
            groups().forEach(syncGroupState);
            renderSummary();
        };

        const renderSummary = () => {
            const selected = selectedCheckboxes();
            selectedContainer.innerHTML = '';

            if (selected.length === 0) {
                selectedContainer.innerHTML = '<p class="permissions-empty">No hay permisos seleccionados.</p>';
            } else {
                selected.forEach((checkbox) => {
                    const chip = document.createElement('span');
                    chip.className = 'permission-chip';
                    chip.textContent = labelForCheckbox(checkbox);

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.setAttribute('aria-label', `Quitar permiso ${checkbox.value}`);
                    removeBtn.textContent = 'x';
                    removeBtn.addEventListener('click', () => removePermission(checkbox.value));

                    chip.appendChild(removeBtn);
                    selectedContainer.appendChild(chip);
                });
            }

            countEl.textContent = String(selected.length);
        };

        const filterGroups = () => {
            const query = searchInput.value.trim().toLowerCase();

            groups().forEach((group) => {
                const moduleLabelEl = group.querySelector('.permission-module-label');
                const actionLabelEls = Array.from(group.querySelectorAll('.permission-action-label'));

                const moduleText = moduleLabelEl?.textContent?.trim() || '';
                const actionTexts = actionLabelEls.map((el) => el.textContent.trim());

                const matches =
                    !query ||
                    moduleText.toLowerCase().includes(query) ||
                    actionTexts.some((text) => text.toLowerCase().includes(query));

                group.style.display = matches ? '' : 'none';

                if (moduleLabelEl) {
                    highlightNode(moduleLabelEl, moduleText, query);
                }
                actionLabelEls.forEach((labelEl, idx) => {
                    highlightNode(labelEl, actionTexts[idx], query);
                });
            });
        };

        selectAllBtn.onclick = () => {
            allPermissionCheckboxes().forEach((checkbox) => {
                checkbox.checked = true;
            });
            groups().forEach(syncGroupState);
            renderSummary();
        };

        clearBtn.onclick = () => {
            allPermissionCheckboxes().forEach((checkbox) => {
                checkbox.checked = false;
            });
            groups().forEach(syncGroupState);
            renderSummary();
        };

        expandAllBtn.onclick = () => {
            groups().forEach((group) => setGroupCollapsed(group, false));
        };

        collapseAllBtn.onclick = () => {
            groups().forEach((group) => setGroupCollapsed(group, true));
        };

        searchInput.oninput = filterGroups;

        groups().forEach((group) => {
            const moduleCheckbox = group.querySelector('.permission-module-checkbox');
            const actionCheckboxes = Array.from(group.querySelectorAll('.permission-action-checkbox'));
            const toggleBtn = group.querySelector('.permission-toggle-btn');

            if (moduleCheckbox && moduleCheckbox.dataset.bound !== '1') {
                moduleCheckbox.addEventListener('change', () => {
                    actionCheckboxes.forEach((checkbox) => {
                        checkbox.checked = moduleCheckbox.checked;
                    });
                    moduleCheckbox.indeterminate = false;
                    renderSummary();
                });
                moduleCheckbox.dataset.bound = '1';
            }

            actionCheckboxes.forEach((checkbox) => {
                if (checkbox.dataset.bound === '1') return;
                checkbox.addEventListener('change', () => {
                    syncGroupState(group);
                    renderSummary();
                });
                checkbox.dataset.bound = '1';
            });

            if (toggleBtn && toggleBtn.dataset.bound !== '1') {
                toggleBtn.addEventListener('click', () => {
                    const targetId = toggleBtn.dataset.target;
                    const itemsContainer = group.querySelector(`#${targetId}`);
                    if (!itemsContainer) return;
                    const collapsed = !itemsContainer.classList.contains('is-collapsed');
                    setGroupCollapsed(group, collapsed);
                });
                toggleBtn.dataset.bound = '1';
            }
        });

        groups().forEach(syncGroupState);
        filterGroups();
        renderSummary();
    };

    const setPermissions = (rootEl, permissions) => {
        if (!rootEl) return;

        const selected = new Set(Array.isArray(permissions) ? permissions : []);

        rootEl.querySelectorAll('input[name="permissions[]"]').forEach((checkbox) => {
            checkbox.checked = selected.has(checkbox.value);
        });

        rootEl.querySelectorAll('.permission-group').forEach(syncGroupState);
    };

    const setEditForm = (role) => {
        const form = document.getElementById('roleEditForm');
        if (!form || !role) return;

        const template = form.dataset.actionTemplate;
        if (template) {
            form.action = template.replace('ROLE_ID', role.id);
        }

        const editRoleId = document.getElementById('edit_role_id');
        if (editRoleId) editRoleId.value = role.id || '';

        const nameInput = document.getElementById('role_edit_name');
        if (nameInput) nameInput.value = role.name || '';

        setPermissions(form, role.permissions || []);
    };

    const openEditWithOldValues = () => {
        const form = document.getElementById('roleEditForm');
        if (!form || !oldEditRoleId) return;

        const template = form.dataset.actionTemplate;
        if (template) {
            form.action = template.replace('ROLE_ID', oldEditRoleId);
        }

        const editRoleId = document.getElementById('edit_role_id');
        if (editRoleId) editRoleId.value = oldEditRoleId || '';

        const nameInput = document.getElementById('role_edit_name');
        if (nameInput) nameInput.value = oldEditName || '';

        setPermissions(form, oldEditPermissions || []);
    };

    const createForm = document.querySelector('#roleCreateModal form');
    const editForm = document.getElementById('roleEditForm');

    if (createForm) {
        initPermissionUX(createForm, {
            searchInputId: 'role_create_perm_search',
            selectedContainerId: 'role_create_perm_selected',
            countId: 'role_create_perm_count',
            clearBtnId: 'role_create_perm_clear',
            selectAllBtnId: 'role_create_perm_select_all',
            expandAllBtnId: 'role_create_perm_expand_all',
            collapseAllBtnId: 'role_create_perm_collapse_all',
        });
    }

    if (editForm) {
        initPermissionUX(editForm, {
            searchInputId: 'role_edit_perm_search',
            selectedContainerId: 'role_edit_perm_selected',
            countId: 'role_edit_perm_count',
            clearBtnId: 'role_edit_perm_clear',
            selectAllBtnId: 'role_edit_perm_select_all',
            expandAllBtnId: 'role_edit_perm_expand_all',
            collapseAllBtnId: 'role_edit_perm_collapse_all',
        });
    }

    document.querySelectorAll('.js-role-edit').forEach((button) => {
        button.addEventListener('click', function () {
            const role = parseJSON(this.dataset.role, null);
            if (!role) return;
            setEditForm(role);
        });
    });

    if (oldModal === 'create' && window.$) {
        window.$('#roleCreateModal').modal('show');
    }

    if (oldModal === 'edit' && oldEditRoleId) {
        openEditWithOldValues();
        if (window.$) {
            window.$('#roleEditModal').modal('show');
        }
    }
});
