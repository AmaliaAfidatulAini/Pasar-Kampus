
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();

    const adminLayout = document.querySelector('.admin-layout');
    const adminSidebar = document.getElementById('adminSidebar');
    const adminSidebarOverlay = document.querySelector('.admin-sidebar-overlay');
    const adminSidebarButton = document.querySelector('.admin-mobile-toggle');
    const adminMobileSidebarQuery = window.matchMedia('(max-width: 768px)');

    if (!adminLayout || !adminSidebar || !adminSidebarOverlay || !adminSidebarButton) return;

    const syncAdminSidebarControls = () => {
        if (adminMobileSidebarQuery.matches) {
            adminLayout.classList.remove('sidebar-collapsed');
            adminSidebar.classList.remove('show-mobile');
            adminSidebarOverlay.classList.remove('show-mobile');
            adminSidebarButton.classList.remove('is-open');
            adminSidebarButton.setAttribute('aria-expanded', 'false');
            adminSidebarButton.setAttribute('aria-label', 'Buka navigasi admin');
            return;
        }

        adminSidebar.classList.remove('show-mobile');
        adminSidebarOverlay.classList.remove('show-mobile');
        const expanded = !adminLayout.classList.contains('sidebar-collapsed');
        adminSidebarButton.classList.toggle('is-open', expanded);
        adminSidebarButton.setAttribute('aria-expanded', String(expanded));
        adminSidebarButton.setAttribute('aria-label', expanded ? 'Ciutkan navigasi admin' : 'Perluas navigasi admin');
    };

    window.toggleMobileSidebar = forceOpen => {
        if (adminMobileSidebarQuery.matches) {
            const open = typeof forceOpen === 'boolean' ? forceOpen : !adminSidebar.classList.contains('show-mobile');
            adminSidebar.classList.toggle('show-mobile', open);
            adminSidebarOverlay.classList.toggle('show-mobile', open);
            adminSidebarButton.classList.toggle('is-open', open);
            adminSidebarButton.setAttribute('aria-expanded', String(open));
            adminSidebarButton.setAttribute('aria-label', open ? 'Tutup navigasi admin' : 'Buka navigasi admin');
            return;
        }

        const expanded = typeof forceOpen === 'boolean' ? forceOpen : adminLayout.classList.contains('sidebar-collapsed');
        adminLayout.classList.toggle('sidebar-collapsed', !expanded);
        adminSidebarButton.classList.toggle('is-open', expanded);
        adminSidebarButton.setAttribute('aria-expanded', String(expanded));
        adminSidebarButton.setAttribute('aria-label', expanded ? 'Ciutkan navigasi admin' : 'Perluas navigasi admin');
        try {
            localStorage.setItem('admin-sidebar-collapsed', String(!expanded));
        } catch (error) {}
    };

    adminMobileSidebarQuery.addEventListener('change', syncAdminSidebarControls);
    syncAdminSidebarControls();
    requestAnimationFrame(() => requestAnimationFrame(() => adminLayout.classList.remove('sidebar-state-preload')));
});

const esc = value => String(value ?? '').replace(/[&<>"']/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char]);
function openModal(id) {
    document.getElementById(id)?.classList.add('open');
}

function closeModal(id) {
    document.getElementById(id)?.classList.remove('open');
}


window.addEventListener('click', event => {
    if (event.target.classList?.contains('admin-modal-backdrop')) event.target.classList.remove('open');
});
window.addEventListener('keydown', event => {
    if (event.key === 'Escape') document.querySelectorAll('.admin-modal-backdrop.open').forEach(modal => modal.classList.remove('open'));
});
