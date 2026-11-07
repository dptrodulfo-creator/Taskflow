

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const globalSearchDialog = document.querySelector('#global-search-dialog');
const globalSearchInput = globalSearchDialog?.querySelector('input[name="search"]');

document.addEventListener('keydown', (event) => {
    if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k' && globalSearchDialog) {
        event.preventDefault();

        if (!globalSearchDialog.open) {
            globalSearchDialog.showModal();
        }

        globalSearchInput?.focus();
    }
});

globalSearchDialog?.querySelector('[data-close-global-search]')?.addEventListener('click', () => {
    globalSearchDialog.close();
});
