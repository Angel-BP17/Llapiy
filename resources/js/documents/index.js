document.addEventListener('DOMContentLoaded', () => {
    const pageData = document.getElementById('documents-page-data');
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

    const createDocumentTypes = parseJSON(pageData.dataset.createDocumentTypes, []);
    const allDocumentTypes = parseJSON(pageData.dataset.allDocumentTypes, []);
    const oldModal = parseJSON(pageData.dataset.oldModal, null);
    const oldEditId = parseJSON(pageData.dataset.oldEditId, null);

    const createTypeSelect = document.getElementById('document_create_type');
    const createCamposContainer = document.getElementById('document_create_campos_container');

    const editTypeSelect = document.getElementById('document_edit_type');
    const editCamposContainer = document.getElementById('document_edit_campos_container');

    const escapeHtml = (value) => String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const toBoolean = (value, fallback = false) => {
        if (value === null || typeof value === 'undefined') return fallback;
        if (typeof value === 'boolean') return value;
        if (typeof value === 'number') return value === 1;
        if (typeof value === 'string') return ['1', 'true', 'on', 'si', 'yes'].includes(value.toLowerCase());
        return fallback;
    };

    const buildCampoInput = (campo, index, value) => {
        const fieldType = campo.data_type || 'string';
        const isNullable = toBoolean(campo.is_nullable, true);
        const allowNegative = toBoolean(campo.allow_negative, false);
        const allowZero = toBoolean(campo.allow_zero, true);
        const length = Number(campo.length) > 0 ? Number(campo.length) : null;
        const isRequired = isNullable ? '' : 'required';
        const safeValue = value ?? '';

        if (fieldType === 'text') {
            const maxLength = length ? `maxlength="${length}"` : '';
            return `
                <textarea name="campos[${index}][dato]" class="form-control" rows="3" ${isRequired} ${maxLength}>${escapeHtml(safeValue)}</textarea>
            `;
        }

        if (fieldType === 'boolean') {
            const selected = String(safeValue).toLowerCase();
            return `
                <select name="campos[${index}][dato]" class="form-control" ${isRequired}>
                    <option value="">Seleccione</option>
                    <option value="1" ${selected === '1' || selected === 'true' ? 'selected' : ''}>Si</option>
                    <option value="0" ${selected === '0' || selected === 'false' ? 'selected' : ''}>No</option>
                </select>
            `;
        }

        if (fieldType === 'enum') {
            const options = Array.isArray(campo.enum_values) ? campo.enum_values : [];
            const optionsHtml = options
                .map((option) => {
                    const optionValue = String(option);
                    return `<option value="${escapeHtml(optionValue)}" ${String(safeValue) === optionValue ? 'selected' : ''}>${escapeHtml(optionValue)}</option>`;
                })
                .join('');

            return `
                <select name="campos[${index}][dato]" class="form-control" ${isRequired}>
                    <option value="">Seleccione</option>
                    ${optionsHtml}
                </select>
            `;
        }

        if (['int', 'float', 'double'].includes(fieldType)) {
            const min = !allowNegative ? (allowZero ? 0 : 1) : null;
            const minAttr = min !== null ? `min="${min}"` : '';
            const stepAttr = fieldType === 'int' ? 'step="1"' : 'step="any"';
            return `
                <input type="number" name="campos[${index}][dato]" class="form-control"
                    value="${escapeHtml(safeValue)}" ${isRequired} ${minAttr} ${stepAttr}>
            `;
        }

        const maxLength = length ? `maxlength="${length}"` : '';
        const charLimit = fieldType === 'char' && !length ? 'maxlength="1"' : '';
        return `
            <input type="text" name="campos[${index}][dato]" class="form-control"
                value="${escapeHtml(safeValue)}" ${isRequired} ${maxLength} ${charLimit}>
        `;
    };

    const renderCampos = (container, documentType, existingCampos = []) => {
        if (!container) return;
        container.innerHTML = '';
        if (!documentType || !documentType.campo_types || documentType.campo_types.length === 0) {
            container.innerHTML = `
                <div class="col">
                    <p class="text-center text-muted">Este tipo de documento no tiene campos adicionales.</p>
                </div>
            `;
            return;
        }
        documentType.campo_types.forEach((campo, index) => {
            const existing = existingCampos.find((c) => c.campo_type_id == campo.id);
            const value = existing ? existing.dato : '';
            const inputHtml = buildCampoInput(campo, index, value);
            const html = `
                <div class="col-sm-4 form-group mb-3">
                    <label class="form-control-label">${campo.name}</label>
                    ${inputHtml}
                    <input type="hidden" name="campos[${index}][id]" value="${campo.id}">
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        });
    };

    const bindCreate = () => {
        if (!createTypeSelect) return;
        createTypeSelect.addEventListener('change', function () {
            const selected = createDocumentTypes.find((t) => t.id == this.value);
            if (!selected) {
                createCamposContainer.innerHTML = `
                    <div class="col">
                        <p class="text-center text-muted">Seleccione un tipo de documento</p>
                    </div>
                `;
                return;
            }
            renderCampos(createCamposContainer, selected, []);
        });
    };

    const setEditForm = (documentData) => {
        const form = document.getElementById('documentEditForm');
        const template = form?.dataset.actionTemplate;
        if (form && template) {
            form.action = template.replace('DOC_ID', documentData.id);
        }
        document.getElementById('edit_document_id').value = documentData.id || '';
        document.getElementById('document_edit_n_documento').value = documentData.n_documento || '';
        document.getElementById('document_edit_asunto').value = documentData.asunto || '';
        document.getElementById('document_edit_folios').value = documentData.folios || '';
        document.getElementById('document_edit_fecha').value = documentData.fecha || '';
        const link = document.getElementById('document_edit_file_link');
        if (link) link.href = documentData.root_url || '#';

        if (editTypeSelect) {
            editTypeSelect.value = documentData.document_type_id || '';
        }
        const selectedType = allDocumentTypes.find((t) => t.id == documentData.document_type_id);
        renderCampos(editCamposContainer, selectedType, documentData.campos || []);
    };

    const setShowModal = (documentData) => {
        document.getElementById('document_show_numero').textContent = documentData.n_documento || '—';
        document.getElementById('document_show_asunto').textContent = documentData.asunto || '—';
        document.getElementById('document_show_folios').textContent = documentData.folios || '—';
        document.getElementById('document_show_tipo').textContent = documentData.document_type_name || '—';
        document.getElementById('document_show_fecha').textContent = documentData.fecha || '—';
        const camposList = document.getElementById('document_show_campos');
        camposList.innerHTML = '';
        if (!documentData.campos || !documentData.campos.length) {
            camposList.innerHTML = '<li class="text-muted">Sin campos adicionales</li>';
        } else {
            documentData.campos.forEach((campo) => {
                const li = document.createElement('li');
                li.textContent = `${campo.name}: ${campo.dato ?? '—'}`;
                camposList.appendChild(li);
            });
        }
        const iframe = document.getElementById('document_show_iframe');
        if (iframe) iframe.src = documentData.root_url || '';
    };

    document.querySelectorAll('.js-document-edit').forEach((button) => {
        button.addEventListener('click', function () {
            const doc = parseJSON(this.dataset.document, null);
            if (!doc) return;
            setEditForm(doc);
        });
    });

    document.querySelectorAll('.js-document-show').forEach((button) => {
        button.addEventListener('click', function () {
            const doc = parseJSON(this.dataset.document, null);
            if (!doc) return;
            setShowModal(doc);
        });
    });

    bindCreate();

    if (oldModal === 'create' && window.$) {
        window.$('#documentCreateModal').modal('show');
    }

    if (oldModal === 'edit' && oldEditId) {
        const editBtn = [...document.querySelectorAll('.js-document-edit')].find((button) => {
            const doc = parseJSON(button.dataset.document, null);
            return doc ? String(doc.id) === String(oldEditId) : false;
        });
        if (editBtn) {
            const doc = parseJSON(editBtn.dataset.document, null);
            if (!doc) return;
            setEditForm(doc);
            if (window.$) {
                window.$('#documentEditModal').modal('show');
            }
        }
    }

});
