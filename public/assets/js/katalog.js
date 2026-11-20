(() => {
	const form = document.getElementById('catalogFilterForm');
	form?.addEventListener('submit', () => {
		form.querySelectorAll('input, select').forEach((field) => {
			const isDefaultValue = field.name === 'sort' && field.value === 'newest';
			if (field.value.trim() === '' || isDefaultValue) {
				field.disabled = true;
			}
		});
	});

	const layout = document.getElementById('catalogLayout');
	const sidebar = document.getElementById('catalogSidebar');
	const toggle = document.getElementById('catalogFilterToggle');
	const desktop = window.matchMedia('(min-width: 992px)');

	const syncExpanded = (expanded) => toggle?.setAttribute('aria-expanded', String(expanded));

	toggle?.addEventListener('click', () => {
		if (desktop.matches) {
			syncExpanded(!layout.classList.toggle('is-sidebar-hidden'));
			return;
		}
		bootstrap.Offcanvas.getOrCreateInstance(sidebar).toggle();
	});

	sidebar?.addEventListener('shown.bs.offcanvas', () => syncExpanded(true));
	sidebar?.addEventListener('hidden.bs.offcanvas', () => syncExpanded(false));

	syncExpanded(desktop.matches ? !layout.classList.contains('is-sidebar-hidden') : sidebar?.classList.contains('show') === true);
})();
