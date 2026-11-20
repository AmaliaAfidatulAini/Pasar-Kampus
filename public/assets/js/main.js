document.addEventListener('DOMContentLoaded', () => {
	lucide.createIcons()
});

/* Navbar search: show the clear button only while the field has text. */
const searchClearSync = (input) => {
	const button = document.querySelector(`[data-pk-search-clear="#${input.id}"]`);
	if (!button) return;
	button.classList.toggle('d-none', input.value === '');
};

/* Confirmation dialogs. SweetAlert2 is loaded in user/footer.php; the native
   confirm() is the fallback for pages that render without that footer. */
const confirmAction = async (message, title = 'Konfirmasi') => {
	if (!window.Swal) return window.confirm(message);
	const result = await window.Swal.fire({
		title,
		text: message,
		icon: 'question',
		showCancelButton: true,
		confirmButtonText: 'Ya',
		cancelButtonText: 'Batal',
		confirmButtonColor: '#1D356D',
		cancelButtonColor: '#64748B',
		reverseButtons: true,
		focusCancel: true,
	});
	return result.isConfirmed;
};

document.addEventListener('input', (event) => {
	if (event.target.matches('#pkHeaderSearch')) searchClearSync(event.target);
});

document.addEventListener('DOMContentLoaded', () => {
	const input = document.getElementById('pkHeaderSearch');
	if (input) searchClearSync(input);
});

document.addEventListener('click', (event) => {
	const scrollBtn = event.target.closest('[data-pk-scroll]');
	if (scrollBtn) {
		const strip = document.querySelector(scrollBtn.dataset.pkTarget);
		if (!strip) return;
		const card = strip.firstElementChild;
		const step = card ? card.getBoundingClientRect().width + 20 : 280;
		strip.scrollBy({ left: Number(scrollBtn.dataset.pkScroll) * step, behavior: 'smooth' });
		return;
	}
	const alertLink = event.target.closest('[data-pk-alert]');
	if (alertLink) {
		event.preventDefault();
		alert(alertLink.dataset.pkAlert);
		return;
	}
	const confirmLink = event.target.closest('[data-pk-confirm]');
	if (confirmLink) {
		event.preventDefault();
		confirmAction(confirmLink.dataset.pkConfirm, confirmLink.dataset.pkConfirmTitle || 'Konfirmasi').then((confirmed) => {
			if (confirmed) window.location.href = confirmLink.href;
		});
		return;
	}
});

document.addEventListener('click', (event) => {
	const clearBtn = event.target.closest('[data-pk-search-clear]');
	if (!clearBtn) return;
	const input = document.querySelector(clearBtn.dataset.pkSearchClear);
	if (!input) return;
	input.value = '';
	input.focus();
	clearBtn.classList.add('d-none');
});
