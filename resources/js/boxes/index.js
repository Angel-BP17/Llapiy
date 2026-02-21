const initBoxesSearch = () => {
    const form = document.getElementById('box-search-form');
    const input = document.getElementById('box-search');
    const clearBtn = document.getElementById('box-search-clear');
    const emptyState = document.getElementById('boxes-dynamic-empty');

    if (!form || !input) {
        return;
    }

    const normalize = (value) => (value || '')
        .toString()
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .trim();

    const applyFilter = () => {
        const items = Array.from(document.querySelectorAll('#boxes-list .js-storage-item'));
        const term = normalize(input.value);
        let visible = 0;

        items.forEach((item) => {
            const searchableText = normalize(item.textContent);
            const matches = term === '' || searchableText.includes(term);
            item.style.display = matches ? '' : 'none';
            if (matches) {
                visible++;
            }
        });

        if (emptyState) {
            emptyState.style.display = visible === 0 ? '' : 'none';
        }
    };

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        applyFilter();
    });

    input.addEventListener('input', applyFilter);

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            input.value = '';
            applyFilter();
            input.focus();
        });
    }

    applyFilter();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBoxesSearch);
} else {
    initBoxesSearch();
}
