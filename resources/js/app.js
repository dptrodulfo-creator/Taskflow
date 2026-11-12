

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

const sidebar = document.querySelector('.sidebar');
const sidebarToggleButtons = sidebar?.querySelectorAll('[data-sidebar-toggle]');

sidebarToggleButtons?.forEach((button) => {
    button.addEventListener('click', () => {
        const isCollapsed = sidebar.classList.toggle('sidebar-collapsed');

        sidebarToggleButtons.forEach((toggleButton) => {
            toggleButton.setAttribute('aria-expanded', String(!isCollapsed));
            toggleButton.setAttribute(
                'aria-label',
                toggleButton === sidebarToggleButtons[0]
                    ? (isCollapsed ? 'Open navigation menu' : 'Close navigation menu')
                    : (isCollapsed ? 'Expand sidebar' : 'Collapse sidebar'),
            );
        });
    });
});

const shareButton = document.querySelector('[data-share-dashboard]');
const shareStatus = document.querySelector('[data-share-status]');

shareButton?.addEventListener('click', async () => {
    const shareData = { title: document.title, url: window.location.href };

    try {
        if (typeof navigator.share === 'function') {
            await navigator.share(shareData);
            return;
        }

        if (navigator.clipboard?.writeText) {
            await navigator.clipboard.writeText(shareData.url);
            shareStatus.textContent = 'Dashboard link copied.';
            return;
        }

        window.prompt('Copy this link to share your dashboard:', shareData.url);
    } catch (error) {
        if (error?.name !== 'AbortError') {
            shareStatus.textContent = 'The dashboard link could not be shared.';
        }
    }
});
