document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    const search = document.querySelector('[data-table-search]');
    const rows = [...document.querySelectorAll('[data-table-row]')];
    const count = document.querySelector('[data-table-count]');
    const empty = document.querySelector('[data-table-empty]');

    if (!search || rows.length === 0) {
        return;
    }

    const updateTable = () => {
        const term = search.value.trim().toLowerCase();
        let visibleRows = 0;

        rows.forEach((row) => {
            const matches = row.textContent.toLowerCase().includes(term);
            row.hidden = !matches;
            visibleRows += matches ? 1 : 0;
        });

        if (count) {
            count.textContent = `${visibleRows} ${visibleRows === 1 ? 'record' : 'records'}`;
        }

        if (empty) {
            empty.hidden = visibleRows !== 0;
        }
    };

    search.addEventListener('input', updateTable);
});
