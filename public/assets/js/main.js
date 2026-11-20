document.addEventListener("DOMContentLoaded", () => {
	lucide.createIcons();
});

function toggleProfileDropdown(event) {
	if (event) event.stopPropagation();
	const dropdown = document.getElementById('lpProfileDropdown');
	const btn = document.getElementById('lpProfileBtn');
	if (!dropdown) return;

	const isShown = dropdown.classList.contains('show');
	dropdown.classList.toggle('show', !isShown);
	if (btn) btn.setAttribute('aria-expanded', isShown ? 'false' : 'true');
}

function closeProfileDropdown() {
	const dropdown = document.getElementById('lpProfileDropdown');
	const btn = document.getElementById('lpProfileBtn');
	if (dropdown) dropdown.classList.remove('show');
	if (btn) btn.setAttribute('aria-expanded', 'false');
}

document.addEventListener('click', (event) => {
	const dropdown = document.getElementById('lpProfileDropdown');
	const btn = document.getElementById('lpProfileBtn');
	if (dropdown && dropdown.classList.contains('show')) {
		if (!dropdown.contains(event.target) && (!btn || !btn.contains(event.target))) {
			closeProfileDropdown();
		}
	}
});

function openProfileModal() {
	closeProfileDropdown();
	const modal = document.getElementById('lpProfileModal');
	if (modal) modal.classList.add('show');
}

function closeProfileModal() {
	const modal = document.getElementById('lpProfileModal');
	if (modal) modal.classList.remove('show');
}

function handleProfileModalBackdrop(event) {
	if (event.target === event.currentTarget) {
		closeProfileModal();
	}
}

function handleHelpClick(event) {
	if (event) event.preventDefault();
	const faqSection = document.getElementById('faq');
	if (faqSection) {
		faqSection.scrollIntoView({ behavior: 'smooth' });
		const firstFaq = faqSection.querySelector('.lp-faq-item');
		if (firstFaq && !firstFaq.classList.contains('active')) {
			firstFaq.classList.add('active');
		}
	}
}