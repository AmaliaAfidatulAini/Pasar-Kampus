<?php Component::include("section/header", ["title" => "Halaman Tidak Ditemukan"]) ?>

<main class="lp-error-page lp-error-not-found">
    <section class="lp-error-content" aria-labelledby="error-title">
        <div class="lp-error-illustration" aria-hidden="true">
            <svg viewBox="0 0 240 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M45 144c-13-18-15-44-4-63 11-20 32-26 47-18 9-25 35-39 57-30 19 7 27 24 28 42 20-4 38 8 43 26 7 25-9 47-30 54-25 9-52-1-72 7-23 9-52 3-69-18Z" fill="#EEF2FF"/>
                <path d="M79 73h57l24 23v61H79V73Z" fill="#fff" stroke="#1D356D" stroke-width="4" stroke-linejoin="round"/>
                <path d="M136 73v25h24M99 120h40M99 137h26" stroke="#1D356D" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="178" cy="54" r="24" fill="#B9FF66"/>
                <circle cx="178" cy="54" r="11" stroke="#1D356D" stroke-width="4"/>
                <path d="m186 62 10 10" stroke="#1D356D" stroke-width="4" stroke-linecap="round"/>
                <circle cx="52" cy="106" r="5" fill="#1D356D"/>
                <circle cx="191" cy="137" r="5" fill="#1D356D"/>
            </svg>
        </div>
        <p class="lp-error-code">404</p>
        <h1 id="error-title">Halaman ini tidak ditemukan</h1>
        <p class="lp-error-description">Tautannya mungkin sudah berubah atau halaman yang kamu cari tidak tersedia. Coba cari barang dari katalog atau kembali ke beranda.</p>
        <div class="lp-error-actions">
            <a class="lp-btn lp-btn-primary" href="<?= route('/') ?>">Kembali ke Beranda</a>
            <a class="lp-btn lp-btn-outline" href="<?= route('/barang') ?>">Jelajahi Katalog</a>
        </div>
    </section>
</main>

<?php Component::include("section/footer") ?>