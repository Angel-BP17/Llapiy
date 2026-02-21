document.addEventListener('DOMContentLoaded', () => {
    const pageData = document.getElementById('campos-page-data');
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
    const oldEditId = parseJSON(pageData.dataset.oldEditId, null);
    const oldEditName = parseJSON(pageData.dataset.oldEditName, '');
    const oldEditDataType = parseJSON(pageData.dataset.oldEditDataType, 'string');
    const oldEditLength = parseJSON(pageData.dataset.oldEditLength, null);
    const oldEditIsNullable = String(parseJSON(pageData.dataset.oldEditIsNullable, '1')) === '1';
    const oldEditAllowNegative = String(parseJSON(pageData.dataset.oldEditAllowNegative, '0')) === '1';
    const oldEditAllowZero = String(parseJSON(pageData.dataset.oldEditAllowZero, '1')) === '1';
    const oldEditEnumValues = parseJSON(pageData.dataset.oldEditEnumValues, '');

    const toBoolean = (value, fallback = false) => {
        if (value === null || typeof value === 'undefined') return fallback;
        if (typeof value === 'boolean') return value;
        if (typeof value === 'number') return value === 1;
        if (typeof value === 'string') return ['1', 'true', 'on', 'si', 'yes'].includes(value.toLowerCase());
        return fallback;
    };

    const toggleTypeOptions = (prefix, selectedType) => {
        const isNumericType = ['int', 'float', 'double'].includes(selectedType);
        const isEnumType = selectedType === 'enum';

        const numericOptions = document.getElementById(`${prefix}_numeric_options`);
        const enumOptions = document.getElementById(`${prefix}_enum_options`);

        if (numericOptions) {
            numericOptions.classList.toggle('d-none', !isNumericType);
        }
        if (enumOptions) {
            enumOptions.classList.toggle('d-none', !isEnumType);
        }
    };

    const bindTypeSelector = (prefix) => {
        const typeSelect = document.getElementById(`${prefix}_data_type`);
        if (!typeSelect) return;

        const apply = () => toggleTypeOptions(prefix, typeSelect.value || 'string');
        typeSelect.addEventListener('change', apply);
        apply();
    };

    const setEditForm = (campo) => {
        const form = document.getElementById('campoEditForm');
        const template = form?.dataset.actionTemplate;
        if (!form || !template || !campo) return;
        form.action = template.replace('CAMPO_ID', campo.id);
        document.getElementById('edit_campo_id').value = campo.id || '';
        const nameInput = document.getElementById('campo_edit_name');
        const dataTypeInput = document.getElementById('campo_edit_data_type');
        const lengthInput = document.getElementById('campo_edit_length');
        const nullableInput = document.getElementById('campo_edit_is_nullable');
        const allowNegativeInput = document.getElementById('campo_edit_allow_negative');
        const allowZeroInput = document.getElementById('campo_edit_allow_zero');
        const enumValuesInput = document.getElementById('campo_edit_enum_values');

        if (nameInput) {
            nameInput.value = campo.name || '';
        }
        if (dataTypeInput) {
            dataTypeInput.value = campo.data_type || 'string';
        }
        if (lengthInput) {
            lengthInput.value = campo.length ?? '';
        }
        if (nullableInput) {
            nullableInput.checked = toBoolean(campo.is_nullable, true);
        }
        if (allowNegativeInput) {
            allowNegativeInput.checked = toBoolean(campo.allow_negative, false);
        }
        if (allowZeroInput) {
            allowZeroInput.checked = toBoolean(campo.allow_zero, true);
        }
        if (enumValuesInput) {
            const enumValues = Array.isArray(campo.enum_values) ? campo.enum_values.join(', ') : '';
            enumValuesInput.value = enumValues;
        }

        toggleTypeOptions('campo_edit', campo.data_type || 'string');
    };

    document.querySelectorAll('.js-campo-edit').forEach((button) => {
        button.addEventListener('click', function () {
            const campo = parseJSON(this.dataset.campo, null);
            if (!campo) return;
            setEditForm(campo);
        });
    });

    bindTypeSelector('campo_create');
    bindTypeSelector('campo_edit');

    if (oldModal === 'create' && window.$) {
        const createTypeInput = document.getElementById('campo_create_data_type');
        if (createTypeInput) {
            toggleTypeOptions('campo_create', createTypeInput.value || 'string');
        }
        window.$('#campoCreateModal').modal('show');
    }

    if (oldModal === 'edit' && oldEditId) {
        const form = document.getElementById('campoEditForm');
        const template = form?.dataset.actionTemplate;
        if (form && template) {
            form.action = template.replace('CAMPO_ID', oldEditId);
        }
        document.getElementById('edit_campo_id').value = oldEditId || '';
        const nameInput = document.getElementById('campo_edit_name');
        if (nameInput) {
            nameInput.value = oldEditName || '';
        }
        const dataTypeInput = document.getElementById('campo_edit_data_type');
        if (dataTypeInput) {
            dataTypeInput.value = oldEditDataType || 'string';
        }
        const lengthInput = document.getElementById('campo_edit_length');
        if (lengthInput) {
            lengthInput.value = oldEditLength ?? '';
        }
        const nullableInput = document.getElementById('campo_edit_is_nullable');
        if (nullableInput) {
            nullableInput.checked = oldEditIsNullable;
        }
        const allowNegativeInput = document.getElementById('campo_edit_allow_negative');
        if (allowNegativeInput) {
            allowNegativeInput.checked = oldEditAllowNegative;
        }
        const allowZeroInput = document.getElementById('campo_edit_allow_zero');
        if (allowZeroInput) {
            allowZeroInput.checked = oldEditAllowZero;
        }
        const enumValuesInput = document.getElementById('campo_edit_enum_values');
        if (enumValuesInput) {
            enumValuesInput.value = oldEditEnumValues || '';
        }
        toggleTypeOptions('campo_edit', oldEditDataType || 'string');
        if (window.$) {
            window.$('#campoEditModal').modal('show');
        }
    }
});
