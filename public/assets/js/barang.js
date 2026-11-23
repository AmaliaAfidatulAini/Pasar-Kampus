(() => {
	const priceInput = document.getElementById('harga');
	const originalInput = document.getElementById('hargaAsli');

	if (priceInput && originalInput) {
		let touched = originalInput.value !== '';
		const syncOriginal = () => {
			const price = Number(priceInput.value);
			if (!(price > 0)) return;
			originalInput.min = String(price);
			if (!touched || Number(originalInput.value) < price) {
				originalInput.value = String(price);
			}
		};
		originalInput.addEventListener('input', () => { touched = true; });
		priceInput.addEventListener('input', syncOriginal);
		syncOriginal();
	}

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
				node.className = 'badge rounded-pill text-bg-primary d-inline-flex align-items-center gap-1 mt-2';
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

		// Image slots accept a photo dragged from the desktop: the dropped file is
		// put into the slot's file input so the change handler above renders it.
		const isImageFile = (file) => file instanceof File && file.type.startsWith('image/');

		document.querySelectorAll('[data-file-target]').forEach((slot) => {
			let dragDepth = 0;

			slot.addEventListener('dragenter', (event) => {
				event.preventDefault();
				dragDepth += 1;
				slot.classList.add('is-dragover');
			});
			slot.addEventListener('dragover', (event) => {
				event.preventDefault();
				if (event.dataTransfer) event.dataTransfer.dropEffect = 'copy';
			});
			slot.addEventListener('dragleave', () => {
				dragDepth = Math.max(0, dragDepth - 1);
				if (dragDepth === 0) slot.classList.remove('is-dragover');
			});
			slot.addEventListener('drop', (event) => {
				event.preventDefault();
				dragDepth = 0;
				slot.classList.remove('is-dragover');
				const input = document.getElementById(slot.dataset.fileTarget);
				const file = event.dataTransfer?.files?.[0];
				if (!input || !isImageFile(file)) return;
				const transfer = new DataTransfer();
				transfer.items.add(file);
				input.files = transfer.files;
				input.dispatchEvent(new Event('change'));
			});
		});

		// A file dropped outside a slot would navigate the tab to the file and lose
		// the filled form. Text drags (the Quill editor) keep native behaviour.
		const carriesFiles = (event) => Boolean(event.dataTransfer && Array.from(event.dataTransfer.types).includes('Files'));
		document.addEventListener('dragover', (event) => {
			if (carriesFiles(event)) event.preventDefault();
		});
		document.addEventListener('drop', (event) => {
			if (carriesFiles(event)) event.preventDefault();
		});

		const editorEl = document.getElementById('deskripsiEditor');
		const descInput = document.getElementById('deskripsi');
		let quill = null;
		let syncDescription = null;
		if (editorEl && descInput && typeof window.Quill === 'function') {
			quill = new window.Quill(editorEl, {
				theme: 'snow',
				modules: {
					toolbar: [
						[{ header: [2, 3, false] }],
						['bold', 'italic', 'underline', 'strike'],
						[{ list: 'ordered' }, { list: 'bullet' }],
						['clean'],
					],
				},
			});
			if (descInput.value.trim() !== '') {
				quill.clipboard.dangerouslyPasteHTML(descInput.value);
			}
			// Quill stores the list type on <li data-list> inside an <ol>. The
			// server sanitizer drops attributes, so turn an all-bullet list into
			// a real <ul> here; otherwise it renders (and re-opens) as numbered.
			syncDescription = () => {
				const clone = quill.root.cloneNode(true);
				clone.querySelectorAll('ol').forEach((list) => {
					const items = [...list.children].filter((item) => item.tagName === 'LI');
					if (items.length === 0 || items.some((item) => item.getAttribute('data-list') !== 'bullet')) return;
					const replacement = document.createElement('ul');
					while (list.firstChild) replacement.append(list.firstChild);
					list.replaceWith(replacement);
				});
				descInput.value = clone.innerHTML;
			};
			quill.on('text-change', syncDescription);
			syncDescription();
		}

		form.addEventListener('submit', async (event) => {
			event.preventDefault();
			if (syncDescription) syncDescription();
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
