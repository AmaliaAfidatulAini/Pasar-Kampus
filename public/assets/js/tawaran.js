/* Seller offer inbox: fill the review/banding modals from the clicked card. */
(() => {
	const reviewModalEl = document.getElementById('reviewModal');
	if (!reviewModalEl) return;

	const bandingModalEl = document.getElementById('bandingModal');
	const reviewModal = bootstrap.Modal.getOrCreateInstance(reviewModalEl);
	const bandingModal = bandingModalEl ? bootstrap.Modal.getOrCreateInstance(bandingModalEl) : null;

	const rupiah = (value) => 'Rp. ' + new Intl.NumberFormat('id-ID').format(value);

	const setFields = (root, data) => {
		root.querySelectorAll('[data-field]').forEach((node) => {
			if (node.dataset.field in data) node.textContent = data[node.dataset.field];
		});
	};

	let activeCard = null;

	document.addEventListener('click', (event) => {
		const trigger = event.target.closest('[data-review]');
		if (!trigger) return;
		const card = trigger.closest('[data-offer-card]');
		if (!card) return;

		activeCard = card;
		const price = Number(card.dataset.price) || 0;
		const offer = Number(card.dataset.offer) || 0;

		setFields(reviewModalEl, {
			product: card.dataset.product || '',
			buyer: card.dataset.buyer || '',
			price: rupiah(price),
			offer: rupiah(offer),
			gap: rupiah(Math.max(price - offer, 0)),
			pct: price > 0 ? Math.round((1 - offer / price) * 100) + '%' : '-',
		});

		document.getElementById('reviewOfferId').value = card.dataset.offerId || '';
	});

	document.getElementById('bandingOpen')?.addEventListener('click', () => {
		if (!activeCard || !bandingModalEl || !bandingModal) return;

		const price = Number(activeCard.dataset.price) || 0;
		const offer = Number(activeCard.dataset.offer) || 0;
		const input = document.getElementById('bandingInput');

		setFields(bandingModalEl, {
			product: activeCard.dataset.product || '',
			price: rupiah(price),
			offer: rupiah(offer),
		});

		document.getElementById('bandingOfferId').value = activeCard.dataset.offerId || '';
		document.getElementById('bandingError').hidden = true;

		if (input) {
			input.min = String(offer + 1);
			input.max = String(price);
			input.value = String(Math.round((offer + price) / 2 / 500) * 500);
			input.dispatchEvent(new Event('input', { bubbles: true }));
		}

		const bounds = document.getElementById('bandingBounds');
		if (bounds) {
			bounds.textContent = 'Banding harus lebih tinggi dari ' + rupiah(offer) + ' dan maksimal ' + rupiah(price) + '.';
		}

		reviewModalEl.addEventListener('hidden.bs.modal', () => bandingModal.show(), { once: true });
		reviewModal.hide();
	});

	document.getElementById('bandingInput')?.addEventListener('input', (event) => {
		const readout = document.getElementById('bandingReadout');
		const price = Number(activeCard?.dataset.price) || 0;
		const value = Number(event.target.value);
		readout.textContent = price > 0 && value > 0
			? Math.round((1 - value / price) * 100) + '% di bawah harga jual'
			: '';
	});
})();

/* Confirm before submitting: hide the modal first, then ask.
   Bootstrap's focus trap steals focus from a dialog rendered in body. */
(() => {
	document.addEventListener('submit', async (event) => {
		const form = event.target;
		const submitter = event.submitter;
		if (!(form instanceof HTMLFormElement) || !submitter?.dataset.confirm) return;

		event.preventDefault();
		const modalEl = form.closest('.modal');
		const modal = modalEl ? bootstrap.Modal.getOrCreateInstance(modalEl) : null;
		if (modal) modal.hide();

		const result = window.Swal
			? await window.Swal.fire({
				icon: 'question',
				title: submitter.dataset.confirmTitle || 'Konfirmasi',
				text: submitter.dataset.confirm,
				confirmButtonText: submitter.dataset.confirmOk || 'Ya',
				cancelButtonText: 'Batal',
				showCancelButton: true,
				confirmButtonColor: '#1D356D',
				cancelButtonColor: '#64748B',
				reverseButtons: true,
				focusCancel: true,
				heightAuto: false,
			})
			: { isConfirmed: window.confirm(submitter.dataset.confirm) };

		if (result.isConfirmed) {
			form.submit();
			return;
		}
		if (!modal) return;
		modalEl.addEventListener('shown.bs.modal', () => submitter.focus(), { once: true });
		modal.show();
	});
})();
