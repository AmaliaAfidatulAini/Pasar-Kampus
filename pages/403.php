<?php Component::include("section/header", ["title" => "Akses Ditolak"]) ?>

<main class="lp-error-page lp-error-forbidden">
    <section class="lp-error-content" aria-labelledby="error-title">
        <div class="lp-error-illustration" aria-hidden="true">
            <svg viewBox="0 0 240 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M45 144c-13-18-15-44-4-63 11-20 32-26 47-18 9-25 35-39 57-30 19 7 27 24 28 42 20-4 38 8 43 26 7 25-9 47-30 54-25 9-52-1-72 7-23 9-52 3-69-18Z" fill="#EEF2FF"/>
                <path d="M84 89V70a36 36 0 0 1 72 0v19" stroke="#1D356D" stroke-width="8" stroke-linecap="round"/>
                <rect x="70" y="87" width="100" height="76" rx="14" fill="#fff" stroke="#1D356D" stroke-width="4"/>
                <circle cx="120" cy="119" r="9" fill="#B9FF66" stroke="#1D356D" stroke-width="3"/>
                <path d="M120 128v14" stroke="#1D356D" stroke-width="4" stroke-linecap="round"/>
                <circle cx="182" cy="58" r="20" fill="#B9FF66"/>
                <path d="m176 52 12 12m0-12-12 12" stroke="#1D356D" stroke-width="3.5" stroke-linecap="round"/>
                <circle cx="51" cy="104" r="5" fill="#1D356D"/>
                <circle cx="189" cy="139" r="5" fill="#1D356D"/>
            </svg>
        </div>
        <p class="lp-error-code">403</p>
        <h1 id="error-title">Kamu belum bisa mengakses halaman ini</h1>
        <p class="lp-error-description">Halaman ini memerlukan izin atau akun yang sesuai. Masuk ke akunmu, atau kembali ke beranda untuk melanjutkan menjelajah.</p>
        <div class="lp-error-actions">
            <a class="lp-btn lp-btn-outline" href="<?= route('/') ?>">Kembali ke Beranda</a>
        </div>
    </section>
</main>

<?php Component::include("section/footer") ?>