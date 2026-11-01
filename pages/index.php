<?php include_component("header", ["title" => "Beranda"]); ?>

<main>
    <!-- ================= 1. HERO SECTION - Pusat Jual-Beli Barang Bekas ================= -->
    <section class="lp-hero-section">
        <div class="lp-container lp-hero-grid">
            <!-- Hero Left Content -->
            <div class="lp-hero-left">
                <h1 class="lp-hero-title">
                    Pusat Jual-Beli Barang Bekas
                </h1>
                <p class="lp-hero-desc">
                    Temukan barang preloved yang sesuai kebutuhanmu, mulai dari perlengkapan kuliah hingga kebutuhan sehari-hari.
                </p>
                <a href="<?= route('/barang') ?>" class="lp-btn lp-btn-primary lp-btn-lg">
                    Mulai Belanja &rarr;
                </a>
            </div>

            <!-- Hero Right Illustration: Orbital Modern (Keranjang Belanja 3D & Floating Badges) -->
            <div class="lp-hero-visual-wrap">
                <div class="lp-hero-orbit-stage">
                    <!-- SVG Orbital Ellipses -->
                    <svg class="lp-orbit-svg" viewBox="0 0 520 440" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Orbit Rings tilted -22 degrees -->
                        <g transform="rotate(-22 260 220)">
                            <ellipse cx="260" cy="220" rx="230" ry="110" stroke="#191A23" stroke-width="1.6" />
                            <ellipse cx="260" cy="220" rx="192" ry="90" stroke="#191A23" stroke-width="1.6" />
                            <ellipse cx="258" cy="222" rx="154" ry="72" stroke="#191A23" stroke-width="1.6" />
                            <ellipse cx="254" cy="224" rx="116" ry="54" stroke="#191A23" stroke-width="1.5" />
                        </g>
                    </svg>

                    <!-- Decorative Ornaments: Dots & Sparkles -->
                    <div class="lp-orbit-decor lp-dot-dark" style="top: 15%; left: 8%;"></div>
                    <div class="lp-orbit-decor lp-dot-lime" style="top: 22%; left: 33%;"></div>
                    <div class="lp-orbit-decor lp-dot-lime-sm" style="bottom: 12%; right: 26%;"></div>

                    <div class="lp-orbit-decor lp-star-large" style="bottom: 18%; left: 10%;">
                        <svg viewBox="0 0 48 48" fill="#191A23" xmlns="http://www.w3.org/2000/svg">
                            <path d="M24 0 C24 13.25 13.25 24 0 24 C13.25 24 24 34.75 24 48 C24 34.75 34.75 24 48 24 C34.75 24 24 13.25 24 0 Z"/>
                        </svg>
                    </div>

                    <div class="lp-orbit-decor lp-star-small" style="bottom: 8%; right: 38%;">
                        <svg viewBox="0 0 24 24" fill="#191A23" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 0 C12 6.63 6.63 12 0 12 C6.63 12 12 17.37 12 24 C12 17.37 17.37 12 24 12 C17.37 12 12 6.63 12 0 Z"/>
                        </svg>
                    </div>

                    <!-- Center 3D Shopping Cart Image -->
                    <div class="lp-orbit-cart-holder" title="PasarKampus - Pusat Belanja Kebutuhan Mahasiswa">
                        <img src="<?= asset("img/cart-transparent.png") ?>"
                                alt="Keranjang Belanja PasarKampus" 
                                class="lp-orbit-cart-img">
                    </div>

                    <!-- Floating Badges -->
                    <div class="lp-badge-bubble lp-bubble-heart" title="Favorit Mahasiswa">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                    </div>

                    <div class="lp-badge-bubble lp-bubble-share" title="Bagikan & Terhubung">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="18" cy="5" r="3" fill="currentColor" stroke="none"/>
                            <circle cx="6" cy="12" r="3" fill="currentColor" stroke="none"/>
                            <circle cx="18" cy="19" r="3" fill="currentColor" stroke="none"/>
                            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                        </svg>
                    </div>

                    <div class="lp-badge-bubble lp-bubble-play" title="Video Review Produk">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>

                    <div class="lp-badge-bubble lp-bubble-pin" title="Titik COD Kampus">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= 2. BARANG TERBARU (Pilihan Barang) ================= -->
    <section class="lp-section">
        <div class="lp-container">
            <div class="lp-section-header left-aligned">
                <h2 class="lp-section-title" style="margin-top: 0;">Barang Terbaru</h2>
                <p class="lp-section-desc">Daftar item terkini yang siap kamu bawa pulang hari ini.</p>
            </div>

            <!-- Horizontal Slider Wrapper -->
            <div class="lp-preloved-wrapper">
                <!-- Nav Prev Button -->
                <button type="button" class="lp-carousel-nav-btn lp-carousel-prev" onclick="scrollBarangTerbaru(-1)" aria-label="Geser ke kiri">
                    &#10094;
                </button>

                <!-- Scrollable Row -->
                <div id="barangTerbaruSlider" class="lp-preloved-scroll">
                    <!-- Product 1: Keyboard Logiac -->
                    <div class="product-card">
                        <div>
                            <div class="product-img-wrapper">
                                <img src="https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=600" alt="Keyboard Logiac">
                                <span class="badge-condition">Layak pakai</span>
                                <span class="badge-category">Elektronik</span>
                            </div>
                            <div class="product-body">
                                <div class="product-location">📍 Kecamatan Lowokwaru</div>
                                <h3 class="product-title">Keyboard Logiac - Silver</h3>
                                <p class="product-desc">Keyboard wireless dengan balutan warna silver elegan dan desain slim yang hemat...</p>
                            </div>
                        </div>
                        <div class="product-footer">
                            <div class="product-price-box">
                                <span class="orig-price">Rp. 150.000</span>
                                <div class="cur-price">Rp. 90.000</div>
                            </div>
                            <a href="<?= route('/barang') ?>" class="btn-product-detail">👁 Detail</a>
                        </div>
                    </div>

                    <!-- Product 2: Lampu Belajar -->
                    <div class="product-card">
                        <div>
                            <div class="product-img-wrapper">
                                <img src="https://images.unsplash.com/photo-1507473885765-e6ed057ab6fe?auto=format&fit=crop&q=80&w=600" alt="Lampu Belajar">
                                <span class="badge-condition">Sangat Baik</span>
                                <span class="badge-category">Perlengkapan</span>
                            </div>
                            <div class="product-body">
                                <div class="product-location">📍 Kecamatan Sukun</div>
                                <h3 class="product-title">Lampu Belajar Minimalis LED</h3>
                                <p class="product-desc">Lampu meja 3 tingkat kecerahan, baterai awet cocok untuk belajar malam hari di...</p>
                            </div>
                        </div>
                        <div class="product-footer">
                            <div class="product-price-box">
                                <span class="orig-price">Rp. 85.000</span>
                                <div class="cur-price">Rp. 45.000</div>
                            </div>
                            <a href="<?= route('/barang') ?>" class="btn-product-detail">👁 Detail</a>
                        </div>
                    </div>

                    <!-- Product 3: Buku Kalkulus -->
                    <div class="product-card">
                        <div>
                            <div class="product-img-wrapper">
                                <img src="https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&q=80&w=600" alt="Buku Kalkulus">
                                <span class="badge-condition">Layak pakai</span>
                                <span class="badge-category">Buku Kuliah</span>
                            </div>
                            <div class="product-body">
                                <div class="product-location">📍 Kecamatan Klojen</div>
                                <h3 class="product-title">Buku Kalkulus Purcel Edisi 9</h3>
                                <p class="product-desc">Buku teks lengkap, halaman utuh tanpa robek, ada sedikit catatan pensil yang bisa...</p>
                            </div>
                        </div>
                        <div class="product-footer">
                            <div class="product-price-box">
                                <span class="orig-price">Rp. 120.000</span>
                                <div class="cur-price">Rp. 65.000</div>
                            </div>
                            <a href="<?= route('/barang') ?>" class="btn-product-detail">👁 Detail</a>
                        </div>
                    </div>

                    <!-- Product 4: Kursi Belajar -->
                    <div class="product-card">
                        <div>
                            <div class="product-img-wrapper">
                                <img src="https://images.unsplash.com/photo-1580480055273-228ff5388ef8?auto=format&fit=crop&q=80&w=600" alt="Kursi Belajar">
                                <span class="badge-condition">Seperti Baru</span>
                                <span class="badge-category">Furnitur</span>
                            </div>
                            <div class="product-body">
                                <div class="product-location">📍 Kecamatan Lowokwaru</div>
                                <h3 class="product-title">Kursi Belajar Ergonomis</h3>
                                <p class="product-desc">Busa tebal empuk, hidrolik naik turun bekerja sempurna. Dijual karena mau pindah kost.</p>
                            </div>
                        </div>
                        <div class="product-footer">
                            <div class="product-price-box">
                                <span class="orig-price">Rp. 350.000</span>
                                <div class="cur-price">Rp. 180.000</div>
                            </div>
                            <a href="<?= route('/barang') ?>" class="btn-product-detail">👁 Detail</a>
                        </div>
                    </div>

                    <!-- Product 5: Monitor -->
                    <div class="product-card">
                        <div>
                            <div class="product-img-wrapper">
                                <img src="https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?auto=format&fit=crop&q=80&w=600" alt="Monitor IPS">
                                <span class="badge-condition">Like New</span>
                                <span class="badge-category">Elektronik</span>
                            </div>
                            <div class="product-body">
                                <div class="product-location">📍 Kecamatan Blimbing</div>
                                <h3 class="product-title">Monitor IPS LG 24MK600 75Hz</h3>
                                <p class="product-desc">Pemakaian 6 bulan untuk skripsi. Layar bening tanpa dead pixel, bonus HDMI...</p>
                            </div>
                        </div>
                        <div class="product-footer">
                            <div class="product-price-box">
                                <span class="orig-price">Rp. 1.600.000</span>
                                <div class="cur-price">Rp. 850.000</div>
                            </div>
                            <a href="<?= route('/barang') ?>" class="btn-product-detail">👁 Detail</a>
                        </div>
                    </div>

                    <!-- Product 6: Gitar Akustik -->
                    <div class="product-card">
                        <div>
                            <div class="product-img-wrapper">
                                <img src="https://images.unsplash.com/photo-1510915361894-db8b60106cb1?auto=format&fit=crop&q=80&w=600" alt="Gitar Akustik">
                                <span class="badge-condition">95% Mulus</span>
                                <span class="badge-category">Lainnya</span>
                            </div>
                            <div class="product-body">
                                <div class="product-location">📍 Kecamatan Sukun</div>
                                <h3 class="product-title">Gitar Akustik Yamaha F310</h3>
                                <p class="product-desc">Senar baru diganti, neck lurus tidak melengkung, suara akustik jernih...</p>
                            </div>
                        </div>
                        <div class="product-footer">
                            <div class="product-price-box">
                                <span class="orig-price">Rp. 1.200.000</span>
                                <div class="cur-price">Rp. 520.000</div>
                            </div>
                            <a href="<?= route('/barang') ?>" class="btn-product-detail">👁 Detail</a>
                        </div>
                    </div>
                </div>

                <!-- Nav Next Button -->
                <button type="button" class="lp-carousel-nav-btn lp-carousel-next" onclick="scrollBarangTerbaru(1)" aria-label="Geser ke kanan">
                    &#10095;
                </button>
            </div>
        </div>
    </section>

    <!-- ================= 3. BERI BARANG KESEMPATAN KEDUA ================= -->
    <section id="tentang-kami" class="lp-section lp-section-alt">
        <div class="lp-container">
            <div class="lp-kesempatan-grid">
                <!-- Left: Cart Illustration -->
                <div class="lp-kesempatan-visual">
                    <div class="lp-hero-orbit-stage">
                        <!-- SVG Orbital Ellipses -->
                        <svg class="lp-orbit-svg" viewBox="0 0 520 440" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Orbit Rings tilted -22 degrees -->
                            <g transform="rotate(-22 260 220)">
                                <ellipse cx="260" cy="220" rx="230" ry="110" stroke="#191A23" stroke-width="1.6" />
                                <ellipse cx="260" cy="220" rx="192" ry="90" stroke="#191A23" stroke-width="1.6" />
                                <ellipse cx="258" cy="222" rx="154" ry="72" stroke="#191A23" stroke-width="1.6" />
                                <ellipse cx="254" cy="224" rx="116" ry="54" stroke="#191A23" stroke-width="1.5" />
                            </g>
                        </svg>

                        <!-- Decorative Ornaments -->
                        <div class="lp-orbit-decor lp-dot-dark" style="top: 15%; left: 8%;"></div>
                        <div class="lp-orbit-decor lp-dot-lime" style="top: 22%; left: 33%;"></div>
                        <div class="lp-orbit-decor lp-dot-lime-sm" style="bottom: 12%; right: 26%;"></div>

                        <div class="lp-orbit-decor lp-star-large" style="bottom: 18%; left: 10%;">
                            <svg viewBox="0 0 48 48" fill="#191A23" xmlns="http://www.w3.org/2000/svg">
                                <path d="M24 0 C24 13.25 13.25 24 0 24 C13.25 24 24 34.75 24 48 C24 34.75 34.75 24 48 24 C34.75 24 24 13.25 24 0 Z"/>
                            </svg>
                        </div>

                        <div class="lp-orbit-decor lp-star-small" style="bottom: 8%; right: 38%;">
                            <svg viewBox="0 0 24 24" fill="#191A23" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 0 C12 6.63 6.63 12 0 12 C6.63 12 12 17.37 12 24 C12 17.37 17.37 12 24 12 C17.37 12 12 6.63 12 0 Z"/>
                            </svg>
                        </div>

                        <!-- Center 3D Shopping Cart Image -->
                        <div class="lp-orbit-cart-holder" title="PasarKampus - Pusat Belanja Kebutuhan Mahasiswa">
                            <img src="<?= asset("img/cart-transparent.png") ?>" 
                                    alt="Keranjang Belanja PasarKampus" 
                                    class="lp-orbit-cart-img">
                        </div>

                        <!-- Floating Badges -->
                        <div class="lp-badge-bubble lp-bubble-heart" title="Favorit Mahasiswa">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        </div>

                        <div class="lp-badge-bubble lp-bubble-share" title="Bagikan & Terhubung">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="18" cy="5" r="3" fill="currentColor" stroke="none"/>
                                <circle cx="6" cy="12" r="3" fill="currentColor" stroke="none"/>
                                <circle cx="18" cy="19" r="3" fill="currentColor" stroke="none"/>
                                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                            </svg>
                        </div>

                        <div class="lp-badge-bubble lp-bubble-play" title="Video Review Produk">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </div>

                        <div class="lp-badge-bubble lp-bubble-pin" title="Titik COD Kampus">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Right: Text Content -->
                <div class="lp-kesempatan-content">
                    <h2 class="lp-section-title" style="text-align: left; margin-top: 0;">Beri Barang Kesempatan Kedua</h2>
                    <p class="lp-section-desc" style="text-align: left;">
                        Barang yang sudah tidak kamu gunakan mungkin masih dibutuhkan orang lain. Bagi mahasiswa, barang preloved menjadi alternatif untuk memenuhi kebutuhan dengan harga lebih terjangkau.
                    </p>
                    <p class="lp-section-desc" style="text-align: left; margin-top: 16px;">
                        Namun, penawarannya masih tersebar di berbagai tempat. Mulai dari toko thrift, grup chat, hingga story media sosial. PasarKampus menyatukan penawaran tersebut dalam satu platform jual-beli, tempat kamu bisa mencari maupun menawarkan barang preloved dengan lebih mudah.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= 4. CARA MENGGUNAKAN PLATFORM INI ================= -->
    <section class="lp-section">
        <div class="lp-container">
            <div class="lp-cara-grid">
                <!-- Left: Title and description -->
                <div class="lp-cara-left">
                    <div class="lp-cara-icon-box">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#1D356D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 11l3 3L22 4"/>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                        </svg>
                    </div>
                    <h2 class="lp-section-title" style="text-align: left; margin-top: 12px;">Cara Menggunakan Platform Ini</h2>
                    <p class="lp-section-desc" style="text-align: left;">
                        Ikuti beberapa langkah sederhana ini untuk memulai transaksi di PasarKampus.
                    </p>
                </div>

                <!-- Right: Steps -->
                <div class="lp-cara-right">
                    <div class="lp-step-card">
                        <div class="lp-step-number">1</div>
                        <div class="lp-step-content">
                            <h4>Masuk/Buat Akun</h4>
                            <p>Buat akun atau masuk jika sudah terdaftar.</p>
                        </div>
                    </div>

                    <div class="lp-step-card">
                        <div class="lp-step-number">2</div>
                        <div class="lp-step-content">
                            <h4>Cari atau Posting Barang</h4>
                            <p>Jelajahi katalog barang atau posting barang bekas yang ingin kamu jual.</p>
                        </div>
                    </div>

                    <div class="lp-step-card">
                        <div class="lp-step-number">3</div>
                        <div class="lp-step-content">
                            <h4>Ajukan Pembelian</h4>
                            <p>Lakukan negosiasi dengan penjual/pembeli.</p>
                        </div>
                    </div>

                    <div class="lp-step-card">
                        <div class="lp-step-number">4</div>
                        <div class="lp-step-content">
                            <h4>Konfirmasi dan Serah Terima</h4>
                            <p>Lanjutkan dengan mengatur jadwal serah terima barang.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= 5. KENAPA PASAR KAMPUS ================= -->
    <section class="lp-section lp-section-alt">
        <div class="lp-container">
            <div class="lp-section-header">
                <h2 class="lp-section-title" style="margin-top: 0;">Kenapa Pasar Kampus?</h2>
                <p class="lp-section-desc">
                    PasarKampus menyatukan kebutuhan pembeli dan penjual dalam satu platform yang praktis, dengan penawaran yang lebih mudah dijelajahi dan peluang transaksi langsung di area terdekat.
                </p>
            </div>

            <div class="lp-kenapa-grid">
                <!-- Card 1: Satu Akun untuk Semua -->
                <div class="lp-kenapa-card">
                    <div class="lp-kenapa-icon" style="color: #1D356D;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <h3>Satu Akun untuk Semua</h3>
                    <p>Tidak perlu akun khusus penjual. Gunakan satu akun untuk mencari barang yang kamu inginkan maupun menawarkan barang yang sudah tidak terpakai.</p>
                </div>

                <!-- Card 2: Temukan Penjual di Sekitarmu -->
                <div class="lp-kenapa-card">
                    <div class="lp-kenapa-icon" style="color: #EF4444;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                    </div>
                    <h3>Temukan Penjual di Sekitarmu</h3>
                    <p>Lihat informasi lokasi penjual pada penawaran untuk membantu menemukan barang di sekitar dan merencanakan serah terima secara langsung.</p>
                </div>

                <!-- Card 3: Semua Penawaran dalam Satu Katalog -->
                <div class="lp-kenapa-card">
                    <div class="lp-kenapa-icon" style="color: #22C55E;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                            <line x1="8" y1="21" x2="16" y2="21"/>
                            <line x1="12" y1="17" x2="12" y2="21"/>
                        </svg>
                    </div>
                    <h3>Semua Penawaran dalam Satu Katalog</h3>
                    <p>Jelajahi penawaran yang tersusun dalam satu katalog. Informasi barang lebih mudah ditemukan tanpa bergantung pada pesan grup atau story media sosial.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= 6. FAQ SECTION ================= -->
    <section id="faq" class="lp-section">
        <div class="lp-container">
            <div class="lp-section-header">
                <h2 class="lp-section-title" style="margin-top: 0;">FAQ</h2>
                <p class="lp-section-desc">
                    Pertanyaan yang sering diajukan mengenai layanan PasarKampus
                </p>
            </div>

            <div class="lp-faq-container">
                <!-- FAQ 1 -->
                <div class="lp-faq-item active">
                    <button class="lp-faq-trigger" onclick="toggleFaq(this)">
                        <span>Mengapa harga barang di PasarKampus bisa lebih terjangkau?</span>
                        <div class="lp-faq-icon-v2">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </div>
                    </button>
                    <div class="lp-faq-body">
                        <p>
                            Karena barang yang dijual berasal langsung dari sesama mahasiswa atau pengguna tangan pertama yang ingin melepas barang preloved mereka tanpa perantara komersial atau biaya sewa toko, sehingga harga jauh lebih ekonomis dan bersahabat bagi kantong mahasiswa.
                        </p>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="lp-faq-item">
                    <button class="lp-faq-trigger" onclick="toggleFaq(this)">
                        <span>Bagaimana PasarKampus menjamin keamanan transaksi dari penipuan?</span>
                        <div class="lp-faq-icon-v2">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </div>
                    </button>
                    <div class="lp-faq-body">
                        <p>
                            PasarKampus menerapkan sistem verifikasi identitas kampus untuk setiap pengguna terdaftar. Metode transaksi diutamakan melalui Meetup / COD di area publik kampus, sehingga pembeli dapat memeriksa keaslian dan kondisi fisik barang terlebih dahulu sebelum membayarkan uang kepada penjual.
                        </p>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="lp-faq-item">
                    <button class="lp-faq-trigger" onclick="toggleFaq(this)">
                        <span>Apakah aplikasi ini terbatas untuk mahasiswa dari satu kampus saja?</span>
                        <div class="lp-faq-icon-v2">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </div>
                    </button>
                    <div class="lp-faq-body">
                        <p>
                            Tidak. PasarKampus dapat digunakan oleh mahasiswa dari berbagai universitas dan politeknik. Anda dapat memfilter katalog barang berdasarkan kampus tertentu maupun wilayah kecamatan domisili kos Anda untuk menemukan rekan transaksi yang paling dekat.
                        </p>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="lp-faq-item">
                    <button class="lp-faq-trigger" onclick="toggleFaq(this)">
                        <span>Barang apa saja yang bisa diperjualbelikan di platform ini?</span>
                        <div class="lp-faq-icon-v2">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </div>
                    </button>
                    <div class="lp-faq-body">
                        <p>
                            Segala kebutuhan perkuliahan dan perlengkapan harian mahasiswa, termasuk: buku diktat & referensi mata kuliah, modul praktikum, kalkulator & alat tulis, perangkat elektronik (laptop, tablet, monitor), perabot perlengkapan kost (meja lipat, rak buku, dispenser), serta pakaian perkuliahan yang bersih dan layak pakai.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php Flush::start() ?>
<script>
    function toggleFaq(button) {
        const item = button.closest('.lp-faq-item');
        const isActive = item.classList.contains('active');
        
        document.querySelectorAll('.lp-faq-item').forEach(el => {
            el.classList.remove('active');
        });
        
        if (!isActive) {
            item.classList.add('active');
        }
    }

    function scrollBarangTerbaru(direction) {
        const container = document.getElementById('barangTerbaruSlider');
        if (container) {
            const card = container.querySelector('.product-card');
            const scrollStep = card ? card.offsetWidth + 20 : 280;
            container.scrollBy({ left: direction * scrollStep, behavior: 'smooth' });
        }
    }
</script>
<?php $script = Flush::end() ?>

<?php include_component("footer", compact("script")); ?>
