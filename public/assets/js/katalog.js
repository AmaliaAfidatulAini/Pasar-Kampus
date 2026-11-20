(() => {
	const sidebar = document.getElementById('catalogSidebar');
	const backdrop = document.getElementById('catalogSidebarBackdrop');
	const toggle = document.getElementById('catalogFilterToggle');
	const closeButton = document.getElementById('catalogSidebarClose');
	const form = document.getElementById('catalogFilterForm');

	const openSidebar = () => {
		if (!sidebar) return;
		sidebar.classList.add('active');
		backdrop?.classList.add('active');
		document.body.style.overflow = 'hidden';
	};

	const closeSidebar = () => {
		if (!sidebar) return;
		sidebar.classList.remove('active');
		backdrop?.classList.remove('active');
		document.body.style.overflow = '';
	};

	toggle?.addEventListener('click', openSidebar);
	closeButton?.addEventListener('click', closeSidebar);
	backdrop?.addEventListener('click', closeSidebar);

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape') {
			closeSidebar();
		}
	});

	window.addEventListener('resize', () => {
		if (window.innerWidth >= 992) {
			closeSidebar();
		}
	});

form?.addEventListener('submit', () => {
	form.querySelectorAll('input, select').forEach((field) => {
		const isDefaultValue = field.name === 'sort' && field.value === 'newest';
		if (field.value.trim() === '' || isDefaultValue) {
			field.disabled = true;
		}
	});
});
})();
