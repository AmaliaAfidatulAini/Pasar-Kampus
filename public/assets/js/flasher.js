(() => {
	// Single source of truth for the toast lifetime (ms).
	const LIFETIME = 6000;

	// The progress bar animates over this duration; keep it in sync with LIFETIME.
	document.documentElement.style.setProperty("--lp-flash-lifetime", `${LIFETIME}ms`);

	document.addEventListener("DOMContentLoaded", () => {
		const root = document.querySelector("[data-flasher]");
		if (!root) return;

		const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");

		const dismiss = (toast) => {
			if (toast.dataset.state === "leaving") return;
			toast.dataset.state = "leaving";
			clearTimeout(toast.flashTimer);

			const remove = () => {
				toast.remove();
				if (!root.querySelector(".lp-flash")) root.remove();
			};
			toast.addEventListener("transitionend", remove, { once: true });
			window.setTimeout(remove, 400);
		};

		root.querySelectorAll(".lp-flash").forEach((toast) => {
			toast.querySelector("[data-flash-close]")?.addEventListener("click", () => dismiss(toast));

			const progress = toast.querySelector(".lp-flash-progress");

			if (reducedMotion.matches || !progress) {
				toast.flashTimer = window.setTimeout(() => dismiss(toast), LIFETIME);
				return;
			}

			// The bar's animation is the clock; hover/focus pauses it in CSS.
			progress.addEventListener("animationend", () => dismiss(toast), { once: true });
		});

		document.addEventListener("keydown", (event) => {
			if (event.key !== "Escape") return;
			root.querySelectorAll(".lp-flash").forEach(dismiss);
		});
	});
})();