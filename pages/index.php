<?php 
require_model('catalog');
$barang = get_catalog_products(['limit' => 12]);
?>

<?php Component::flush() ?>
<link rel="stylesheet" href="<?= asset("css/index.css") ?>">
<link rel="stylesheet" href="<?= asset("css/card.css") ?>">
<?php $styles = Component::endFlush() ?>

<?php Component::include("user/header", ["title" => "Beranda", "styles" => $styles]); ?>

<main class="flex-grow-1">
    <!-- ================= 1. HERO SECTION - Pusat Jual-Beli Barang Bekas ================= -->
    <section class="pk-hero py-6 border-bottom">
        <div class="container">
            <div class="row align-items-center gx-4 gy-5">
                <div class="col-lg-6">
                    <h1 class="display-5 fw-bold text-primary">
                        Pusat <u>Jual-Beli</u><br>
                        Barang Bekas
                    </h1>
                    <p class="fs-5 text-body-secondary mt-3 mb-4">
                        Temukan barang preloved yang sesuai kebutuhanmu, mulai dari perlengkapan kuliah hingga kebutuhan sehari-hari.
                    </p>
                    <a href="<?= route('/explore') ?>" class="btn btn-primary btn-lg d-inline-flex align-items-center gap-3">
                        <i data-lucide="shopping-cart" class="pk-icon pk-icon--md" aria-hidden="true"></i>
                        Mulai Belanja
                    </a>
                </div>

                <div class="col-lg-6">
                    <div class="pk-orbit">
                        <svg class="pk-orbit-rings" viewBox="0 0 520 440" fill="none" aria-hidden="true">
                            <g transform="rotate(-22 260 220)">
                                <ellipse cx="260" cy="220" rx="230" ry="110" stroke="#191A23" stroke-width="1.6"/>
                                <ellipse cx="260" cy="220" rx="192" ry="90" stroke="#191A23" stroke-width="1.6"/>
                                <ellipse cx="258" cy="222" rx="154" ry="72" stroke="#191A23" stroke-width="1.6"/>
                                <ellipse cx="254" cy="224" rx="116" ry="54" stroke="#191A23" stroke-width="1.5"/>
                            </g>
                        </svg>
                        <span class="pk-orbit-dot pk-orbit-dot--1"></span>
                        <span class="pk-orbit-dot pk-orbit-dot--lime pk-orbit-dot--2"></span>
                        <span class="pk-orbit-dot pk-orbit-dot--lime-sm pk-orbit-dot--3"></span>
                        <span class="pk-orbit-star pk-orbit-star--1"><svg viewBox="0 0 48 48" fill="#191A23"><path d="M24 0C24 13.25 13.25 24 0 24C13.25 24 24 34.75 24 48C24 34.75 34.75 24 48 24C34.75 24 24 13.25 24 0Z"/></svg></span>
                        <span class="pk-orbit-star pk-orbit-star--sm pk-orbit-star--2"><svg viewBox="0 0 24 24" fill="#191A23"><path d="M12 0C12 6.63 6.63 12 0 12C6.63 12 12 17.37 12 24C12 17.37 17.37 12 24 12C17.37 12 12 6.63 12 0Z"/></svg></span>
                        <img class="pk-orbit-cart" src="<?= asset('img/icon-inv.svg') ?>" alt="Keranjang belanja PasarKampus">
                        <span class="pk-bubble pk-bubble--dark pk-bubble--heart"><i data-lucide="heart"></i></span>
                        <span class="pk-bubble pk-bubble--lime pk-bubble--share"><i data-lucide="share-2"></i></span>
                        <span class="pk-bubble pk-bubble--dark pk-bubble--play"><i data-lucide="play"></i></span>
                        <span class="pk-bubble pk-bubble--lime pk-bubble--pin"><i data-lucide="map-pin"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= 2. BARANG TERBARU (Pilihan Barang) ================= -->
    <section class="py-6 border-bottom">
        <div class="container">
            <div class="row mb-4">
                <div class="col-lg-8">
                    <h2 class="fs-2 fw-bold text-primary mb-2">Barang Terbaru</h2>
                    <p class="text-body-secondary mb-0">Daftar item terkini yang siap kamu bawa pulang hari ini.</p>
                </div>
            </div>

            <div class="position-relative">
                <button type="button" class="pk-scroll-btn pk-scroll-btn--prev" data-pk-scroll="-1" data-pk-target="#pkBarangTerbaru" aria-label="Geser ke kiri">
                    &#10094;
                </button>

                <div id="pkBarangTerbaru" class="pk-scroll-strip">
                    <?php foreach ($barang as $item): ?>
                        <?php 
                        Component::include("common/card", [
                            "id" => $item['id'],
                            "title" => $item['title'],
                            "description" => $item['short_desc'] ?? $item['description'] ?? '',
                            "category" => $item['category'],
                            "location" => $item['location'],
                            "condition" => $item['condition'],
                            "o_price" => $item['original_price'],
                            "price" => $item['price'],
                            "foto" => $item['image'],
                        ]);
                        ?>
                    <?php endforeach; ?>
                </div>

                <button type="button" class="pk-scroll-btn pk-scroll-btn--next" data-pk-scroll="1" data-pk-target="#pkBarangTerbaru" aria-label="Geser ke kanan">
                    &#10095;
                </button>
            </div>
        </div>
    </section>

    <!-- ================= 3. BERI BARANG KESEMPATAN KEDUA ================= -->
    <section id="tentang-kami" class="py-6 border-bottom bg-body-tertiary">
        <div class="container">
            <div class="row align-items-center gx-4 gy-5">
                <div class="col-lg-6">
                    <div class="pk-orbit pk-orbit--box">
                        <svg class="pk-orbit-rings" viewBox="0 0 520 440" fill="none" aria-hidden="true">
                            <g transform="rotate(18 260 220)">
                                <ellipse cx="260" cy="220" rx="225" ry="126" stroke="#191A23" stroke-width="1.6"/>
                                <ellipse cx="260" cy="220" rx="198" ry="108" stroke="#191A23" stroke-width="1.6"/>
                                <ellipse cx="260" cy="220" rx="171" ry="90" stroke="#191A23" stroke-width="1.6"/>
                                <ellipse cx="260" cy="220" rx="144" ry="72" stroke="#191A23" stroke-width="1.5"/>
                            </g>
                        </svg>
                        <span class="pk-orbit-dot pk-orbit-dot--1"></span>
                        <span class="pk-orbit-dot pk-orbit-dot--lime pk-orbit-dot--2"></span>
                        <span class="pk-orbit-dot pk-orbit-dot--lime-sm pk-orbit-dot--3"></span>
                        <span class="pk-orbit-star pk-orbit-star--1"><svg viewBox="0 0 48 48" fill="#191A23"><path d="M24 0C24 13.25 13.25 24 0 24C13.25 24 24 34.75 24 48C24 34.75 34.75 24 48 24C34.75 24 24 13.25 24 0Z"/></svg></span>
                        <span class="pk-orbit-star pk-orbit-star--sm pk-orbit-star--2"><svg viewBox="0 0 24 24" fill="#191A23"><path d="M12 0C12 6.63 6.63 12 0 12C6.63 12 12 17.37 12 24C12 17.37 17.37 12 24 12C17.37 12 12 6.63 12 0Z"/></svg></span>
                        <img class="pk-orbit-cart pk-orbit-cart--box" src="<?= asset('img/box.png') ?>" alt="Kotak paket berisi barang preloved yang siap diberikan kepada pemilik baru">
                        <span class="pk-bubble pk-bubble--dark pk-bubble--heart"><i data-lucide="heart"></i></span>
                        <span class="pk-bubble pk-bubble--lime pk-bubble--tag"><i data-lucide="tag"></i></span>
                        <span class="pk-bubble pk-bubble--lime pk-bubble--pin"><i data-lucide="map-pin"></i></span>
                        <span class="pk-bubble pk-bubble--dark pk-bubble--user"><i data-lucide="user-round"></i></span>
                    </div>
                </div>

                <div class="col-lg-6">
                    <h2 class="fs-2 fw-bold text-primary mb-3">Beri Barang Kesempatan Kedua</h2>
                    <p class="text-body-secondary mb-3">
                        Barang yang sudah tidak kamu gunakan mungkin masih dibutuhkan orang lain. Bagi mahasiswa, barang preloved menjadi alternatif untuk memenuhi kebutuhan dengan harga lebih terjangkau.
                    </p>
                    <p class="text-body-secondary mb-0">
                        Namun, penawarannya masih tersebar di berbagai tempat. Mulai dari toko thrift, grup chat, hingga story media sosial. PasarKampus menyatukan penawaran tersebut dalam satu platform jual-beli, tempat kamu bisa mencari maupun menawarkan barang preloved dengan lebih mudah.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= 4. CARA MENGGUNAKAN PLATFORM INI ================= -->
    <section class="py-6 border-bottom">
        <div class="container">
            <div class="row gx-4 gy-5">
                <div class="col-lg-4">
                    <span class="pk-feature-icon d-inline-flex align-items-center justify-content-center bg-primary-subtle rounded-1 text-primary mb-3">
                        <i data-lucide="list-checks" class="pk-icon pk-icon--lg"></i>
                    </span>
                    <h2 class="fs-2 fw-bold text-primary">Cara Menggunakan Platform Ini</h2>
                    <p class="text-body-secondary mb-0">
                        Ikuti beberapa langkah sederhana ini untuk memulai transaksi di PasarKampus.
                    </p>
                </div>

                <div class="col-lg-8 d-grid gap-3">
                    <div class="pk-step">
                        <span class="pk-step-number">1</span>
                        <div>
                            <h3 class="h6 fw-bold mb-1">Masuk/Buat Akun</h3>
                            <p class="small text-body-secondary mb-0">Buat akun atau masuk jika sudah terdaftar.</p>
                        </div>
                    </div>

                    <div class="pk-step">
                        <span class="pk-step-number">2</span>
                        <div>
                            <h3 class="h6 fw-bold mb-1">Cari atau Posting Barang</h3>
                            <p class="small text-body-secondary mb-0">Jelajahi katalog barang atau posting barang bekas yang ingin kamu jual.</p>
                        </div>
                    </div>

                    <div class="pk-step">
                        <span class="pk-step-number">3</span>
                        <div>
                            <h3 class="h6 fw-bold mb-1">Ajukan Pembelian</h3>
                            <p class="small text-body-secondary mb-0">Lakukan negosiasi dengan penjual/pembeli.</p>
                        </div>
                    </div>

                    <div class="pk-step">
                        <span class="pk-step-number">4</span>
                        <div>
                            <h3 class="h6 fw-bold mb-1">Konfirmasi dan Serah Terima</h3>
                            <p class="small text-body-secondary mb-0">Lanjutkan dengan mengatur jadwal serah terima barang.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= 5. KENAPA PASAR KAMPUS ================= -->
    <section class="py-6 border-bottom bg-body-tertiary">
        <div class="container">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <h2 class="fs-2 fw-bold text-primary mb-2">Kenapa Pasar Kampus?</h2>
                    <p class="text-body-secondary mb-0">
                        PasarKampus menyatukan kebutuhan pembeli dan penjual dalam satu platform yang praktis, dengan penawaran yang lebih mudah dijelajahi dan peluang transaksi langsung di area terdekat.
                    </p>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-3 g-4">
                <div class="col">
                    <div class="pk-feature">
                        <span class="pk-feature-icon d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary mb-3">
                            <i data-lucide="user-round" class="pk-icon pk-icon--lg"></i>
                        </span>
                        <h3 class="h5 fw-bold mb-2">Satu Akun untuk Semua</h3>
                        <p class="small text-body-secondary mb-0">Tidak perlu akun khusus penjual. Gunakan satu akun untuk mencari barang yang kamu inginkan maupun menawarkan barang yang sudah tidak terpakai.</p>
                    </div>
                </div>

                <div class="col">
                    <div class="pk-feature">
                        <span class="pk-feature-icon d-inline-flex align-items-center justify-content-center rounded-3 bg-danger-subtle text-danger mb-3">
                            <i data-lucide="map-pin" class="pk-icon pk-icon--lg"></i>
                        </span>
                        <h3 class="h5 fw-bold mb-2">Temukan Penjual di Sekitarmu</h3>
                        <p class="small text-body-secondary mb-0">Lihat informasi lokasi penjual pada penawaran untuk membantu menemukan barang di sekitar dan merencanakan serah terima secara langsung.</p>
                    </div>
                </div>

                <div class="col">
                    <div class="pk-feature">
                        <span class="pk-feature-icon d-inline-flex align-items-center justify-content-center rounded-3 bg-success-subtle text-success mb-3">
                            <i data-lucide="layout-grid" class="pk-icon pk-icon--lg"></i>
                        </span>
                        <h3 class="h5 fw-bold mb-2">Semua Penawaran dalam Satu Katalog</h3>
                        <p class="small text-body-secondary mb-0">Jelajahi penawaran yang tersusun dalam satu katalog. Informasi barang lebih mudah ditemukan tanpa bergantung pada pesan grup atau story media sosial.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= 6. FAQ SECTION ================= -->
    <section id="faq" class="py-6 border-bottom">
        <div class="container">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <h2 class="fs-2 fw-bold text-primary mb-2">FAQ</h2>
                    <p class="text-body-secondary mb-0">
                        Pertanyaan yang sering diajukan mengenai layanan PasarKampus
                    </p>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="pkFaq">
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#pkFaq1" aria-expanded="true" aria-controls="pkFaq1">
                                    Mengapa harga barang di PasarKampus bisa lebih terjangkau?
                                </button>
                            </h3>
                            <div id="pkFaq1" class="accordion-collapse collapse show" data-bs-parent="#pkFaq">
                                <div class="accordion-body">
                                    <p class="mb-0">
                                        Karena barang yang dijual berasal langsung dari sesama mahasiswa atau pengguna tangan pertama yang ingin melepas barang preloved mereka tanpa perantara komersial atau biaya sewa toko, sehingga harga jauh lebih ekonomis dan bersahabat bagi kantong mahasiswa.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pkFaq2" aria-expanded="false" aria-controls="pkFaq2">
                                    Bagaimana PasarKampus menjamin keamanan transaksi dari penipuan?
                                </button>
                            </h3>
                            <div id="pkFaq2" class="accordion-collapse collapse" data-bs-parent="#pkFaq">
                                <div class="accordion-body">
                                    <p class="mb-0">
                                        PasarKampus menerapkan sistem verifikasi identitas kampus untuk setiap pengguna terdaftar. Metode transaksi diutamakan melalui Meetup / COD di area publik kampus, sehingga pembeli dapat memeriksa keaslian dan kondisi fisik barang terlebih dahulu sebelum membayarkan uang kepada penjual.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pkFaq3" aria-expanded="false" aria-controls="pkFaq3">
                                    Apakah aplikasi ini terbatas untuk mahasiswa dari satu kampus saja?
                                </button>
                            </h3>
                            <div id="pkFaq3" class="accordion-collapse collapse" data-bs-parent="#pkFaq">
                                <div class="accordion-body">
                                    <p class="mb-0">
                                        Tidak. PasarKampus dapat digunakan oleh mahasiswa dari berbagai universitas dan politeknik. Anda dapat memfilter katalog barang berdasarkan kampus tertentu maupun wilayah kecamatan domisili kos Anda untuk menemukan rekan transaksi yang paling dekat.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pkFaq4" aria-expanded="false" aria-controls="pkFaq4">
                                    Barang apa saja yang bisa diperjualbelikan di platform ini?
                                </button>
                            </h3>
                            <div id="pkFaq4" class="accordion-collapse collapse" data-bs-parent="#pkFaq">
                                <div class="accordion-body">
                                    <p class="mb-0">
                                        Segala kebutuhan perkuliahan dan perlengkapan harian mahasiswa, termasuk: buku diktat & referensi mata kuliah, modul praktikum, kalkulator & alat tulis, perangkat elektronik (laptop, tablet, monitor), perabot perlengkapan kost (meja lipat, rak buku, dispenser), serta pakaian perkuliahan yang bersih dan layak pakai.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php Component::include("user/footer"); ?>
