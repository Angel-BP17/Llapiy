document.addEventListener('DOMContentLoaded', () => {
    const pageData = document.getElementById('blocks-page-data');
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

    const setEditForm = (block) => {
        const form = document.getElementById('blockEditForm');
        const template = form?.dataset.actionTemplate;
        if (form && template) {
            form.action = template.replace('BLOCK_ID', block.id);
        }
        document.getElementById('edit_block_id').value = block.id || '';
        document.getElementById('block_edit_n_bloque').value = block.n_bloque || '';
        document.getElementById('block_edit_asunto').value = block.asunto || '';
        document.getElementById('block_edit_folios').value = block.folios || '';
        document.getElementById('block_edit_fecha').value = block.fecha || '';
        document.getElementById('block_edit_rango_inicial').value = block.rango_inicial || '';
        document.getElementById('block_edit_rango_final').value = block.rango_final || '';
        const link = document.getElementById('block_edit_file_link');
        if (link) {
            link.href = block.root_url || '#';
        }
    };

    const setShowModal = (block) => {
        const user = block.user?.name ? `${block.user.name} ${block.user.last_name || ''}`.trim() : '—';
        document.getElementById('block_show_user').textContent = user || '—';
        document.getElementById('block_show_numero').textContent = block.n_bloque || '—';
        document.getElementById('block_show_asunto').textContent = block.asunto || '—';
        document.getElementById('block_show_folios').textContent = block.folios || '—';
        document.getElementById('block_show_section').textContent = block.box?.section || '—';
        document.getElementById('block_show_andamio').textContent = block.box?.andamio || '—';
        document.getElementById('block_show_box').textContent = block.box?.box || '—';
        document.getElementById('block_show_area').textContent = block.area || '—';
        document.getElementById('block_show_group').textContent = block.group || '—';
        document.getElementById('block_show_subgroup').textContent = block.subgroup || '—';
        const iframe = document.getElementById('block_show_iframe');
        if (iframe) iframe.src = block.root_url || '';
    };

    document.querySelectorAll('.js-block-edit').forEach((button) => {
        button.addEventListener('click', function () {
            const block = parseJSON(this.dataset.block, null);
            if (!block) return;
            setEditForm(block);
        });
    });

    document.querySelectorAll('.js-block-show').forEach((button) => {
        button.addEventListener('click', function () {
            const block = parseJSON(this.dataset.block, null);
            if (!block) return;
            setShowModal(block);
        });
    });

    if (oldModal === 'create' && window.$) {
        window.$('#blockCreateModal').modal('show');
    }

    if (oldModal === 'edit' && oldEditId) {
        const editBtn = [...document.querySelectorAll('.js-block-edit')].find((button) => {
            const block = parseJSON(button.dataset.block, null);
            return block ? String(block.id) === String(oldEditId) : false;
        });
        if (editBtn) {
            const block = parseJSON(editBtn.dataset.block, null);
            if (!block) return;
            setEditForm(block);
            if (window.$) {
                window.$('#blockEditModal').modal('show');
            }
        }
    }

});
