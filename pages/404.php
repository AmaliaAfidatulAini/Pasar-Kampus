<?php Component::include("user/header", ["title" => "Halaman Tidak Ditemukan"]) ?>

<main class="d-flex flex-grow-1 align-items-center justify-content-center text-center bg-white py-5 px-3">
    <section class="container" aria-labelledby="error-title">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 d-flex flex-column align-items-center">
                <div class="w-100 mx-auto mb-2 pk-error-illustration" aria-hidden="true">
                    <svg viewBox="0 0 240 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M45 144c-13-18-15-44-4-63 11-20 32-26 47-18 9-25 35-39 57-30 19 7 27 24 28 42 20-4 38 8 43 26 7 25-9 47-30 54-25 9-52-1-72 7-23 9-52 3-69-18Z" fill="#EEF2FF" />
                        <path d="M79 73h57l24 23v61H79V73Z" fill="#fff" stroke="#1D356D" stroke-width="4" stroke-linejoin="round" />
                        <path d="M136 73v25h24M99 120h40M99 137h26" stroke="#1D356D" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="178" cy="54" r="24" fill="#B9FF66" />
                        <circle cx="178" cy="54" r="11" stroke="#1D356D" stroke-width="4" />
                        <path d="m186 62 10 10" stroke="#1D356D" stroke-width="4" stroke-linecap="round" />
                        <circle cx="52" cy="106" r="5" fill="#1D356D" />
                        <circle cx="191" cy="137" r="5" fill="#1D356D" />
                    </svg>
                </div>
                <p class="display-1 fw-bold text-primary lh-1 mb-0">404</p>
                <h1 id="error-title" class="fs-2 fw-bold text-primary mt-3 mb-0">Halaman ini tidak ditemukan</h1>
                <p class="text-body-secondary mt-3 mb-0">Tautannya mungkin sudah berubah atau halaman yang kamu cari tidak tersedia. Coba cari barang dari katalog atau kembali ke beranda.</p>
                <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
                    <a class="btn btn-primary" href="<?= route('/') ?>">Kembali ke Beranda</a>
                    <a class="btn btn-outline-primary" href="<?= route('/explore') ?>">Jelajahi Katalog</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php Component::include("user/footer") ?>