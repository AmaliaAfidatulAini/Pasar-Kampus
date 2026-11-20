(() => {
	const form = document.getElementById('barangForm');

	if (form) {
		const selectedBox = document.getElementById('selectedTagsBox');
		const tagInput = document.getElementById('customTagInput');
		const tagsInput = document.getElementById('tagsInput');
		const submitButton = document.getElementById('btnSubmitBarang');
		const initialTags = (() => {
			try {
				const parsed = JSON.parse(tagsInput?.value || '[]');
				return Array.isArray(parsed) ? parsed.filter((tag) => typeof tag === 'string') : [];
			} catch {
				return [];
			}
		})();
		let selectedTags = initialTags;

		const renderTags = () => {
			selectedBox.querySelectorAll('[data-tag-chip]').forEach((node) => node.remove());
			selectedTags.forEach((tag) => {
				const node = document.createElement('span');
				node.className = 'badge rounded-pill text-bg-primary d-inline-flex align-items-center gap-1';
				node.dataset.tagChip = tag;
				node.append(tag);
				const remove = document.createElement('button');
				remove.type = 'button';
				remove.className = 'btn-close btn-close-white';
				remove.style.fontSize = '.5rem';
				remove.setAttribute('aria-label', `Hapus tag ${tag}`);
				remove.addEventListener('click', () => {
					selectedTags = selectedTags.filter((item) => item !== tag);
					renderTags();
				});
				node.append(remove);
				selectedBox.insertBefore(node, tagInput);
			});

			tagsInput.value = JSON.stringify(selectedTags);
		};

		tagInput?.addEventListener('keydown', (event) => {
			if (event.key !== 'Enter' && event.key !== ',') return;
			event.preventDefault();
			const value = tagInput.value.trim();
			if (!value) return;
			const tag = value.startsWith('#') ? value : `#${value}`;
			if (!selectedTags.includes(tag)) {
				selectedTags.push(tag);
				renderTags();
			}
			tagInput.value = '';
		});

		document.querySelectorAll('[data-file-target]').forEach((slot) => {
			slot.addEventListener('click', () => document.getElementById(slot.dataset.fileTarget)?.click());
		});

		document.querySelectorAll('[data-preview-input]').forEach((input) => {
			input.addEventListener('change', () => {
				const file = input.files && input.files[0];
				if (!file) return;
				const slot = input.closest('.col, .d-grid')?.querySelector(`[data-file-target="${input.id}"]`)
					|| document.querySelector(`[data-file-target="${input.id}"]`);
				if (!slot) return;
				const preview = slot.querySelector('[data-preview]');
				const placeholder = slot.querySelector('[data-placeholder]');
				const reader = new FileReader();
				reader.onload = (event) => {
					preview.src = event.target.result;
					preview.classList.remove('d-none');
					placeholder?.classList.add('d-none');
				};
				reader.readAsDataURL(file);
			});
		});

		form.addEventListener('submit', async (event) => {
			event.preventDefault();
			const confirmed = await confirmAction('Pastikan data barang sudah benar sebelum disimpan.', 'Simpan barang?');
			if (!confirmed) return;
			if (submitButton) {
				submitButton.disabled = true;
				submitButton.textContent = 'Menyimpan...';
			}
			form.submit();
		});

		renderTags();
	}

	document.querySelectorAll('form[data-confirm-hapus]').forEach((form) => {
		form.addEventListener('submit', async (event) => {
			event.preventDefault();
			const name = form.dataset.confirmHapus || 'barang ini';
			const confirmed = await confirmAction(`Barang "${name}" akan dihapus permanen. Lanjutkan?`, 'Hapus barang?');
			if (confirmed) form.submit();
		});
	});
})();
