document.addEventListener("DOMContentLoaded", () => {
	const toggle = document.getElementById("password-toggle");
	if (!toggle) return;

	toggle.addEventListener("click", () => {
		const field = toggle.closest("[data-password-wrap]").querySelector("input");
		const eyeIcon = toggle.querySelector("img");

		if (!field || !eyeIcon) return;

		if (field.type === "password") {
			field.type = "text";
			eyeIcon.src = eyeIcon.src.replace("eye-closed", "eye-open");
			eyeIcon.alt = "Sembunyikan password";
		} else {
			field.type = "password";
			eyeIcon.src = eyeIcon.src.replace("eye-open", "eye-closed");
			eyeIcon.alt = "Lihat password";
		}
	});
});
