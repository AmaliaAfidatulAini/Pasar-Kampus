/**
 * Component Loader Script
 * Memuat file HTML modular menggunakan atribut: data-include="path/file.html"
 */
async function loadComponents() {
    const elements = document.querySelectorAll('[data-include]');
    
    const promises = Array.from(elements).map(async (el) => {
        const file = el.getAttribute('data-include');
        if (!file) return;

        try {
            const response = await fetch(file);
            if (!response.ok) {
                throw new Error(`HTTP status ${response.status}`);
            }
            const htmlText = await response.text();
            el.outerHTML = htmlText;
        } catch (err) {
            console.error(`Gagal memuat komponen [${file}]:`, err);
            el.innerHTML = `<div style="padding:10px; color:#ef4444; font-size:12px; background:#fee2e2; border-radius:6px;">⚠️ Gagal memuat komponen: <code>${file}</code>. Pastikan membuka website via Web Server (misal: Live Server di VS Code).</div>`;
        }
    });

    await Promise.all(promises);
    document.dispatchEvent(new CustomEvent('componentsLoaded'));
}

// Inisialisasi setelah DOM siap
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadComponents);
} else {
    loadComponents();
}
