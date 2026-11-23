/* Gallery: one stage, a counter, and a thumbnail per attachment. */
(() => {
	const stage = document.getElementById('galleryStage');
	if (!stage) return;

	const image = document.getElementById('galleryImage');
	const count = document.getElementById('galleryCount');
	const thumbWrap = document.getElementById('galleryThumbs');
	const thumbs = Array.from(thumbWrap?.querySelectorAll('.pk-thumb') ?? []);
	const sources = thumbs.map((thumb) => thumb.querySelector('img')?.getAttribute('src') || '');
	if (sources.length === 0) return;

	const title = document.getElementById('productTitle')?.textContent?.trim() || '';
	let index = 0;

	const show = (next) => {
		index = ((next % sources.length) + sources.length) % sources.length;
		image.src = sources[index];
		image.alt = title + ' foto ' + (index + 1);
		count.textContent = (index + 1) + ' / ' + sources.length;
		thumbs.forEach((thumb, position) => {
			if (position === index) {
				thumb.setAttribute('aria-current', 'true');
			} else {
				thumb.removeAttribute('aria-current');
			}
		});
		thumbs[index].scrollIntoView({ block: 'nearest', inline: 'nearest', behavior: 'smooth' });
	};

	document.getElementById('galleryPrev')?.addEventListener('click', () => show(index - 1));
	document.getElementById('galleryNext')?.addEventListener('click', () => show(index + 1));
	thumbWrap?.addEventListener('click', (event) => {
		const thumb = event.target.closest('.pk-thumb');
		if (thumb) show(thumbs.indexOf(thumb));
	});
	stage.addEventListener('keydown', (event) => {
		if (event.key === 'ArrowLeft') {
			event.preventDefault();
			show(index - 1);
		}
		if (event.key === 'ArrowRight') {
			event.preventDefault();
			show(index + 1);
		}
	});

	show(0);
})();

/* Offer modal: live discount readout and client-side bound check.
   Bounds mirror place_offer: >= 50% of price and < price. */
(() => {
	const form = document.getElementById('offerForm');
	if (!form) return;

	const input = document.getElementById('offerInput');
	const readout = document.getElementById('offerReadout');
	const error = document.getElementById('offerError');
	const original = Number(form.dataset.original) || 0;

	const syncReadout = () => {
		const value = Number(input?.value);
		readout.textContent = original > 0 && value > 0 && value < original
			? 'hemat ' + Math.round((1 - value / original) * 100) + '% dari harga asli'
			: '';
	};

	input?.addEventListener('input', () => {
		syncReadout();
		error.hidden = true;
	});

	syncReadout();

	form.addEventListener('submit', (event) => {
		const value = Number(input?.value);
		const min = Number(input?.min) || 0;
		const max = Number(input?.max) || 0;

		if (!Number.isInteger(value) || value < min || value > max) {
			event.preventDefault();
			error.textContent = 'Tawaran harus antara Rp. ' + new Intl.NumberFormat('id-ID').format(min) + ' dan Rp. ' + new Intl.NumberFormat('id-ID').format(max) + '.';
			error.hidden = false;
		}
	});
})();
