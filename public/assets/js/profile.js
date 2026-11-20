(() => {
	const modalElement = document.getElementById('avatarModal');
	const image = document.getElementById('avatarCropperImage');
	const stage = document.getElementById('avatarStage');
	const drop = document.getElementById('avatarDrop');
	const fileInput = document.getElementById('avatarFileInput');
	const resetButton = document.getElementById('avatarReset');
	const saveButton = document.getElementById('avatarSave');
	const removeButton = document.getElementById('avatarRemove');
	if (!modalElement || !stage || !image || !drop || !fileInput || !saveButton || !removeButton) return;

	const endpoint = modalElement.dataset.endpoint;
	const savedSrc = (modalElement.dataset.avatar || '').trim();
	const CROP_SIZE = 512;
	const WEBP_QUALITY = 0.85;
	let cropper = null;
	let objectUrl = null;

	const template = `
		<cropper-canvas background>
			<cropper-image rotatable scalable skewable translatable initial-fit="cover" min-fit="cover" max-inset="0"></cropper-image>
			<cropper-shade hidden></cropper-shade>
			<cropper-handle action="move" plain></cropper-handle>
			<cropper-selection aspect-ratio="1" movable resizable min-inset="0">
				<cropper-grid role="grid" bordered covered></cropper-grid>
				<cropper-crosshair centered></cropper-crosshair>
				<cropper-handle action="move" theme-color="rgba(255, 255, 255, 0.35)"></cropper-handle>
				<cropper-handle action="n-resize"></cropper-handle>
				<cropper-handle action="e-resize"></cropper-handle>
				<cropper-handle action="s-resize"></cropper-handle>
				<cropper-handle action="w-resize"></cropper-handle>
				<cropper-handle action="ne-resize"></cropper-handle>
				<cropper-handle action="nw-resize"></cropper-handle>
				<cropper-handle action="se-resize"></cropper-handle>
				<cropper-handle action="sw-resize"></cropper-handle>
			</cropper-selection>
		</cropper-canvas>`;

	const syncFooter = () => {
		// Hapus only makes sense for a photo saved in the database.
		removeButton.hidden = savedSrc === '';
	};

	const setBusy = (busy) => {
		saveButton.disabled = busy || cropper === null;
		removeButton.disabled = busy;
		resetButton.disabled = busy;
	};

	const post = async (formData) => {
		// The handler answers 303; following it would consume the one-shot flash,
		// so keep the redirect manual and let the reload render it.
		await fetch(endpoint, { method: 'POST', body: formData, redirect: 'manual' });
		window.location.reload();
	};

	const showEmpty = () => {
		if (cropper) {
			cropper.destroy();
			cropper = null;
		}
		if (objectUrl) {
			URL.revokeObjectURL(objectUrl);
			objectUrl = null;
		}
		image.removeAttribute('src');
		fileInput.value = '';
		drop.hidden = false;
		drop.classList.remove('is-dragover');
		resetButton.hidden = true;
		saveButton.disabled = true;
		syncFooter();
	};

	// A coverage-based box would size itself to the image, which is larger than the
	// canvas once the image covers it, so the box is placed explicitly instead.
	const centerSelection = async () => {
		if (!cropper) return;
		const cropperImage = cropper.getCropperImage();
		const selection = cropper.getCropperSelection();
		if (!cropperImage || !selection) return;
		await cropperImage.$ready().catch(() => {});
		const canvas = cropperImage.parentElement;
		if (!canvas) return;
		const size = Math.round(Math.min(canvas.clientWidth, canvas.clientHeight) * 0.8);
		selection.$change(
			Math.round((canvas.clientWidth - size) / 2),
			Math.round((canvas.clientHeight - size) / 2),
			size,
			size
		);
	};

	const showCrop = async (src) => {
		if (cropper) {
			cropper.destroy();
			cropper = null;
		}
		image.src = src;
		await image.decode().catch(() => {});
		cropper = Cropper.createCropper(image, { template });
		await centerSelection();
		drop.hidden = true;
		resetButton.hidden = false;
		saveButton.disabled = false;
		syncFooter();
	};

	const loadFile = async (file) => {
		if (!file || !file.type.startsWith('image/')) return;
		if (objectUrl) URL.revokeObjectURL(objectUrl);
		objectUrl = URL.createObjectURL(file);
		await showCrop(objectUrl);
	};

	modalElement.addEventListener('shown.bs.modal', () => {
		if (savedSrc !== '') {
			showCrop(savedSrc);
		} else {
			showEmpty();
		}
	});

	modalElement.addEventListener('hidden.bs.modal', () => {
		showEmpty();
	});

	fileInput.addEventListener('change', () => {
		loadFile(fileInput.files && fileInput.files[0]);
	});

	resetButton.addEventListener('click', showEmpty);

	let dragDepth = 0;
	stage.addEventListener('dragenter', (event) => {
		event.preventDefault();
		dragDepth += 1;
		drop.hidden = false;
		drop.classList.add('is-dragover');
	});
	stage.addEventListener('dragover', (event) => {
		event.preventDefault();
		if (event.dataTransfer) event.dataTransfer.dropEffect = 'copy';
	});
	stage.addEventListener('dragleave', () => {
		dragDepth = Math.max(0, dragDepth - 1);
		if (dragDepth > 0) return;
		drop.classList.remove('is-dragover');
		// A saved or staged crop keeps the dropzone hidden until the next drag.
		if (cropper) drop.hidden = true;
	});
	stage.addEventListener('drop', (event) => {
		event.preventDefault();
		dragDepth = 0;
		drop.classList.remove('is-dragover');
		loadFile(event.dataTransfer && event.dataTransfer.files[0]);
	});

	saveButton.addEventListener('click', async () => {
		if (!cropper) return;
		const selection = cropper.getCropperSelection();
		if (!selection) return;
		setBusy(true);
		try {
			const canvas = await selection.$toCanvas({ width: CROP_SIZE, height: CROP_SIZE });
			const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/webp', WEBP_QUALITY));
			if (!blob) throw new Error('Gagal membuat gambar.');
			const data = new FormData();
			data.append('target', 'avatar');
			data.append('avatar', blob, 'avatar.webp');
			await post(data);
		} catch (error) {
			console.error('Avatar crop failed:', error);
			alert('Gagal memproses foto profil. Coba foto lain.');
			setBusy(false);
		}
	});

	if (removeButton) {
		removeButton.addEventListener('click', async () => {
			setBusy(true);
			const data = new FormData();
			data.append('target', 'avatar');
			data.append('remove', '1');
			await post(data);
		});
	}
})();
