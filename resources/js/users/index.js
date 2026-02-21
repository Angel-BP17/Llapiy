document.addEventListener('DOMContentLoaded', () => {
    const pageData = document.getElementById('users-page-data');
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

    const userAreas = parseJSON(pageData.dataset.areas, []);
    const oldModal = parseJSON(pageData.dataset.oldModal, null);
    const oldEditUserId = parseJSON(pageData.dataset.oldEditUserId, null);
    const oldEditRoles = parseJSON(pageData.dataset.oldEditRoles, []);
    const oldCreate = parseJSON(pageData.dataset.oldCreate, {});
    const oldEdit = parseJSON(pageData.dataset.oldEdit, {});
    const roleLabels = parseJSON(pageData.dataset.roleLabels, {});
    const defaultAvatar = pageData.dataset.defaultAvatar || '';

    const createSelects = {
        area: document.getElementById('create_area_id'),
        groupType: document.getElementById('create_group_type_id'),
        group: document.getElementById('create_group_id'),
        subgroup: document.getElementById('create_subgroup_id'),
    };

    const editSelects = {
        area: document.getElementById('edit_area'),
        groupType: document.getElementById('edit_group_type'),
        group: document.getElementById('edit_group'),
        subgroup: document.getElementById('edit_subgroup'),
    };

    const createDefaults = {
        areaId: oldCreate.area_id || null,
        groupTypeId: oldCreate.group_type_id || null,
        groupId: oldCreate.group_id || null,
        subgroupId: oldCreate.subgroup_id || null,
    };

    const editDefaults = {
        areaId: oldEdit.area || null,
        groupTypeId: oldEdit.groupType || null,
        groupId: oldEdit.group || null,
        subgroupId: oldEdit.subgroup || null,
    };

    let useOldEditValues = oldModal === 'edit';

    function clearSelect(select, placeholder) {
        if (!select) return;
        select.innerHTML = `<option value="">${placeholder}</option>`;
        select.disabled = true;
    }

    function loadAreas(select, selectedAreaId = null) {
        if (!select) return;
        clearSelect(select, 'Seleccione un área');
        userAreas.forEach((area) => {
            const selected = area.id == selectedAreaId ? 'selected' : '';
            select.innerHTML += `<option value="${area.id}" ${selected}>${area.descripcion}</option>`;
        });
        select.disabled = false;
    }

    function loadGroupTypes(areaId, selects, selectedGroupTypeId = null) {
        clearSelect(selects.groupType, 'Seleccione un tipo de grupo');
        clearSelect(selects.group, 'Seleccione un grupo');
        clearSelect(selects.subgroup, 'Seleccione un subgrupo');

        if (!areaId) return;

        const area = userAreas.find((a) => a.id == areaId);
        if (!area || !Array.isArray(area.area_group_types)) return;

        const loaded = new Set();
        area.area_group_types.forEach((agt) => {
            if (!agt.group_type || loaded.has(agt.group_type.id)) return;
            const selected = agt.group_type.id == selectedGroupTypeId ? 'selected' : '';
            selects.groupType.innerHTML +=
                `<option value="${agt.group_type.id}" ${selected}>${agt.group_type.descripcion}</option>`;
            loaded.add(agt.group_type.id);
        });

        selects.groupType.disabled = false;
    }

    function loadGroups(areaId, groupTypeId, selects, selectedGroupId = null) {
        clearSelect(selects.group, 'Seleccione un grupo');
        clearSelect(selects.subgroup, 'Seleccione un subgrupo');

        if (!areaId || !groupTypeId) return;

        const area = userAreas.find((a) => a.id == areaId);
        if (!area || !Array.isArray(area.area_group_types)) return;

        area.area_group_types.forEach((agt) => {
            const matches =
                agt.group_type &&
                (agt.group_type.id == groupTypeId || agt.group_type_id == groupTypeId);
            if (!matches) return;

            (agt.groups || []).forEach((group) => {
                const selected = group.id == selectedGroupId ? 'selected' : '';
                selects.group.innerHTML +=
                    `<option value="${group.id}" ${selected}>${group.descripcion}</option>`;
            });
        });

        selects.group.disabled = false;
    }

    function loadSubgroups(groupId, selects, selectedSubgroupId = null) {
        clearSelect(selects.subgroup, 'Seleccione un subgrupo');
        if (!groupId) return;

        let subgroups = [];
        userAreas.forEach((area) => {
            (area.area_group_types || []).forEach((agt) => {
                (agt.groups || []).forEach((group) => {
                    if (group.id == groupId && Array.isArray(group.subgroups)) {
                        subgroups = group.subgroups;
                    }
                });
            });
        });

        subgroups.forEach((sg) => {
            const selected = sg.id == selectedSubgroupId ? 'selected' : '';
            selects.subgroup.innerHTML +=
                `<option value="${sg.id}" ${selected}>${sg.descripcion}</option>`;
        });

        selects.subgroup.disabled = false;
    }

    function initSelectors(selects, defaults) {
        loadAreas(selects.area, defaults.areaId || null);
        if (defaults.areaId) {
            loadGroupTypes(defaults.areaId, selects, defaults.groupTypeId || null);
            if (defaults.groupTypeId) {
                loadGroups(defaults.areaId, defaults.groupTypeId, selects, defaults.groupId || null);
                if (defaults.groupId) {
                    loadSubgroups(defaults.groupId, selects, defaults.subgroupId || null);
                }
            }
        }

        selects.area?.addEventListener('change', function () {
            loadGroupTypes(this.value, selects);
            clearSelect(selects.group, 'Seleccione un grupo');
            clearSelect(selects.subgroup, 'Seleccione un subgrupo');
        });

        selects.groupType?.addEventListener('change', function () {
            const areaId = selects.area?.value;
            loadGroups(areaId, this.value, selects);
            clearSelect(selects.subgroup, 'Seleccione un subgrupo');
        });

        selects.group?.addEventListener('change', function () {
            loadSubgroups(this.value, selects);
        });
    }

    initSelectors(createSelects, createDefaults);

    document.getElementById('create_foto_perfil')?.addEventListener('change', function () {
        if (this.files?.[0]) {
            document.getElementById('create_preview').src = window.URL.createObjectURL(this.files[0]);
        }
    });

    document.getElementById('edit_foto_perfil')?.addEventListener('change', function () {
        if (this.files?.[0]) {
            document.getElementById('edit_preview').src = window.URL.createObjectURL(this.files[0]);
        }
    });

    function setEditRoles(selectedRoles) {
        const container = document.getElementById('edit_roles_container');
        if (!container) return;
        const values = Array.isArray(selectedRoles) ? selectedRoles : [];
        container.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
            checkbox.checked = values.includes(checkbox.value);
        });
    }

    function setEditForm(user) {
        const form = document.getElementById('userEditForm');
        const template = form?.dataset.actionTemplate;
        if (form && template) {
            form.action = template.replace('USER_ID', user.id);
        }
        document.getElementById('edit_user_id').value = user.id || '';
        if (!useOldEditValues) {
            document.getElementById('edit_name').value = user.name || '';
            document.getElementById('edit_last_name').value = user.last_name || '';
            document.getElementById('edit_dni').value = user.dni || '';
            document.getElementById('edit_user_name').value = user.user_name || '';
            document.getElementById('edit_email').value = user.email || '';
            document.getElementById('edit_preview').src = user.foto || defaultAvatar;
            setEditRoles(user.roles || []);

            const defaults = {
                areaId: user.group?.area_group_type?.area?.id || null,
                groupTypeId: user.group?.area_group_type?.group_type?.id || null,
                groupId: user.group?.id || null,
                subgroupId: user.subgroup?.id || null,
            };
            initSelectors(editSelects, defaults);
        } else {
            document.getElementById('edit_preview').src = user.foto || defaultAvatar;
            setEditRoles(oldEditRoles || []);
            initSelectors(editSelects, editDefaults);
            useOldEditValues = false;
        }
    }

    function setShowModal(user) {
        document.getElementById('show_avatar').src = user.foto || defaultAvatar;
        document.getElementById('show_name').textContent =
            `${user.name || ''} ${user.last_name || ''}`.trim() || '—';
        document.getElementById('show_username').textContent = user.user_name
            ? `@${user.user_name}`
            : '—';
        document.getElementById('show_email').textContent = user.email || '—';
        document.getElementById('show_dni').textContent = user.dni || '—';
        document.getElementById('show_area').textContent =
            user.group?.area_group_type?.area?.descripcion || 'Sin área';
        document.getElementById('show_group').textContent = user.group?.descripcion || 'Sin grupo';
        document.getElementById('show_subgroup').textContent =
            user.subgroup?.descripcion || 'Sin subgrupo';

        const rolesContainer = document.getElementById('show_roles');
        rolesContainer.innerHTML = '';
        const roles = user.roles || [];
        if (!roles.length) {
            rolesContainer.innerHTML = '<span class="text-muted">Sin roles</span>';
            return;
        }
        roles.forEach((role) => {
            const badge = document.createElement('span');
            badge.className = 'badge bg-secondary mr-1 mb-1';
            badge.textContent = roleLabels[role] || role;
            rolesContainer.appendChild(badge);
        });
    }

    document.querySelectorAll('.js-user-edit').forEach((button) => {
        button.addEventListener('click', function () {
            const user = parseJSON(this.dataset.user, null);
            if (!user) return;
            setEditForm(user);
        });
    });

    document.querySelectorAll('.js-user-show').forEach((button) => {
        button.addEventListener('click', function () {
            const user = parseJSON(this.dataset.user, null);
            if (!user) return;
            setShowModal(user);
        });
    });

    if (oldModal === 'create' && window.$) {
        window.$('#userCreateModal').modal('show');
    }

    if (oldModal === 'edit' && oldEditUserId) {
        const editBtn = [...document.querySelectorAll('.js-user-edit')].find((button) => {
            const user = parseJSON(button.dataset.user, null);
            return user ? String(user.id) === String(oldEditUserId) : false;
        });
        if (editBtn) {
            const user = parseJSON(editBtn.dataset.user, null);
            if (!user) return;
            setEditForm(user);
            if (window.$) {
                window.$('#userEditModal').modal('show');
            }
        }
    }
});
