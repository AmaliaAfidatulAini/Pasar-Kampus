(() => {
	const body = document.getElementById('meetingBody');
	const swal = window.Swal.mixin({
		heightAuto: false,
		confirmButtonColor: '#1D356D',
		cancelButtonColor: '#64748B',
		cancelButtonText: 'Batal',
		reverseButtons: true,
		focusCancel: true,
	});

	const rupiah = (value) => 'Rp. ' + new Intl.NumberFormat('id-ID').format(value);

	/* ---- Map ---- */
	const mapEl = document.getElementById('meetMap');
	if (body && mapEl) {
		const editable = body.dataset.editable === '1';
		const hint = document.getElementById('mapHint');
		const latInput = document.getElementById('meetingLat');
		const lngInput = document.getElementById('meetingLng');
		const submitBtn = document.getElementById('meetingSubmit');
		const defaultLat = Number(body.dataset.defaultLat);
		const defaultLng = Number(body.dataset.defaultLng);
		const initialLat = body.dataset.lat === '' ? null : Number(body.dataset.lat);
		const initialLng = body.dataset.lng === '' ? null : Number(body.dataset.lng);
		let marker = null;

		const setHint = (text) => {
			if (hint) hint.textContent = text;
		};

		if (!window.L) {
			setHint('Peta tidak dapat dimuat.');
		} else {
			const hasPoint = initialLat !== null && initialLng !== null;
			const center = hasPoint ? [initialLat, initialLng] : [defaultLat, defaultLng];
			const map = window.L.map(mapEl, { scrollWheelZoom: false }).setView(center, hasPoint ? 16 : 14);

			window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				maxZoom: 19,
				attribution: '&copy; OpenStreetMap',
			}).addTo(map);

			const setPoint = (lat, lng, recenter) => {
				if (!marker) {
					marker = window.L.marker([lat, lng], { draggable: editable }).addTo(map);
					if (editable) {
						marker.on('dragend', () => {
							const point = marker.getLatLng();
							setPoint(point.lat, point.lng, false);
						});
					}
				} else {
					marker.setLatLng([lat, lng]);
				}
				if (recenter) map.setView([lat, lng], 16);
				if (editable) {
					if (latInput) latInput.value = String(lat);
					if (lngInput) lngInput.value = String(lng);
					if (submitBtn) submitBtn.disabled = false;
				}
				setHint('Titik temu: ' + lat.toFixed(5) + ', ' + lng.toFixed(5));
			};

			if (hasPoint) {
				setPoint(initialLat, initialLng, true);
			} else {
				setHint('Titik temu belum ditentukan. Klik peta atau cari alamat untuk menandainya.');
			}

			if (editable) {
				map.on('click', (event) => setPoint(event.latlng.lat, event.latlng.lng, false));

				/* ---- Current location ---- */
				document.getElementById('mapLocateBtn')?.addEventListener('click', () => {
					const onError = () => {
						swal.fire({
							icon: 'error',
							title: 'Lokasi tidak tersedia',
							text: 'Izinkan akses lokasi pada browser lalu coba lagi.',
							confirmButtonText: 'Mengerti',
						});
					};
					if (!navigator.geolocation) {
						onError();
						return;
					}
					navigator.geolocation.getCurrentPosition(
						(position) => {
							setPoint(position.coords.latitude, position.coords.longitude, true);
							setHint('Lokasi saat ini ditetapkan.');
						},
						onError,
						{ enableHighAccuracy: true, timeout: 8000 },
					);
				});

				/* ---- Address search ---- */
				const searchInput = document.getElementById('mapSearch');

				const runSearch = async () => {
					if (!searchInput) return;
					const query = searchInput.value.trim();
					if (query === '') {
						searchInput.focus();
						return;
					}
					setHint('Mencari alamat...');
					try {
						const response = await fetch(body.dataset.geocode + '?q=' + encodeURIComponent(query));
						if (!response.ok) throw new Error('geocode failed');
						const data = await response.json();
						if (!data || typeof data.lat !== 'number' || typeof data.lng !== 'number') {
							throw new Error('no result');
						}
						setPoint(data.lat, data.lng, true);
						setHint('Titik temu ditetapkan pada ' + data.label + '.');
					} catch (error) {
						setPoint(defaultLat, defaultLng, true);
						setHint('Alamat tidak dapat dicari. Titik dipindahkan ke lokasi contoh; klik peta untuk menyesuaikan.');
					}
				};

				document.getElementById('mapSearchBtn')?.addEventListener('click', runSearch);
				searchInput?.addEventListener('keydown', (event) => {
					if (event.key === 'Enter') {
						event.preventDefault();
						runSearch();
					}
				});
			}
		}
	}

	/* ---- Meeting point form ---- */
	const meetingForm = document.getElementById('meetingForm');
	meetingForm?.addEventListener('submit', (event) => {
		event.preventDefault();
		const latInput = document.getElementById('meetingLat');
		const lngInput = document.getElementById('meetingLng');
		if (!latInput || latInput.value === '' || !lngInput || lngInput.value === '') {
			swal.fire({
				icon: 'error',
				title: 'Titik temu belum ditentukan',
				text: 'Tandai lokasi pada peta, cari alamat, atau gunakan lokasi saat ini.',
				confirmButtonText: 'Mengerti',
			});
			return;
		}

		const lat = Number(latInput.value);
		const lng = Number(lngInput.value);
		swal.fire({
			icon: 'question',
			title: 'Simpan Titik Temu',
			text: 'Titik temu akan ditetapkan pada koordinat ' + lat.toFixed(5) + ', ' + lng.toFixed(5) + '. Lanjutkan?',
			showCancelButton: true,
			confirmButtonText: 'Ya, simpan',
		}).then((result) => {
			if (result.isConfirmed) HTMLFormElement.prototype.submit.call(meetingForm);
		});
	});

	/* ---- Cancel form ---- */
	const cancelForm = document.getElementById('cancelForm');
	cancelForm?.addEventListener('submit', (event) => {
		event.preventDefault();
		const reasonInput = document.getElementById('cancelReason');
		const reasonError = document.getElementById('cancelReasonError');
		const reason = reasonInput ? reasonInput.value.trim() : '';

		if (reason === '') {
			reasonError?.classList.add('d-block');
			reasonInput?.focus();
			return;
		}
		reasonError?.classList.remove('d-block');

		swal.fire({
			icon: 'question',
			title: 'Kirim Pembatalan',
			text: 'Transaksi akan dibatalkan dengan alasan: "' + reason + '".',
			showCancelButton: true,
			confirmButtonText: 'Ya, batalkan',
		}).then((result) => {
			if (result.isConfirmed) HTMLFormElement.prototype.submit.call(cancelForm);
		});
	});

	/* ---- Invoice form ---- */
	const invoiceForm = document.getElementById('invoiceForm');
	invoiceForm?.addEventListener('submit', (event) => {
		event.preventDefault();

		const errorSlots = ['invoicePayError', 'invoiceReturnError', 'invoiceMethodError', 'invoiceProofError'];
		const showError = (id, message) => {
			const slot = document.getElementById(id);
			if (!slot) return;
			slot.textContent = message;
			slot.classList.add('d-block');
		};
		errorSlots.forEach((id) => {
			const slot = document.getElementById(id);
			if (!slot) return;
			slot.textContent = '';
			slot.classList.remove('d-block');
		});

		const payInput = document.getElementById('invoicePay');
		const returnInput = document.getElementById('invoiceReturn');
		const methodInput = document.getElementById('invoiceMethod');
		const proofInput = document.getElementById('invoiceProof');

		const payRaw = payInput ? payInput.value.trim() : '';
		const returnRaw = returnInput ? returnInput.value.trim() : '';
		const pay = Number(payRaw);
		const change = Number(returnRaw);
		const method = methodInput ? methodInput.value : '';
		const amount = Number(invoiceForm.dataset.amount);
		const payValid = payRaw !== '' && Number.isInteger(pay) && pay >= 0;
		const returnValid = returnRaw !== '' && Number.isInteger(change) && change >= 0;
		let ok = true;

		if (!payValid) {
			showError('invoicePayError', 'Bayar harus angka minimal 0.');
			ok = false;
		}
		if (!returnValid) {
			showError('invoiceReturnError', 'Kembali harus angka minimal 0.');
			ok = false;
		}
		if (payValid && returnValid && pay - change !== amount) {
			showError('invoicePayError', 'Bayar dikurangi kembalian harus sama dengan ' + invoiceForm.dataset.amountLabel + '.');
			ok = false;
		}
		if (method !== 'Tunai' && method !== 'Transfer') {
			showError('invoiceMethodError', 'Pilih metode pembayaran.');
			ok = false;
		}
		if (returnValid && method === 'Transfer' && change !== 0) {
			showError('invoiceReturnError', 'Transfer tidak memiliki kembalian.');
			ok = false;
		}

		const files = proofInput ? proofInput.files : null;
		if (!files || files.length !== 1 || !String(files[0].type).startsWith('image/')) {
			showError('invoiceProofError', 'Unggah satu file gambar bukti bayar.');
			ok = false;
		}
		if (!ok) return;

		const modal = window.bootstrap.Modal.getOrCreateInstance(document.getElementById('invoiceModal'));
		modal.hide();

		swal.fire({
			icon: 'question',
			title: 'Simpan Invoice',
			text: 'Simpan invoice untuk transaksi ' + invoiceForm.dataset.code + '? Bayar ' + rupiah(pay)
				+ ', kembali ' + rupiah(change) + ', metode ' + method + '.',
			showCancelButton: true,
			confirmButtonText: 'Ya, simpan',
		}).then((result) => {
			if (result.isConfirmed) {
				HTMLFormElement.prototype.submit.call(invoiceForm);
				return;
			}
			modal.show();
		});
	});
})();
