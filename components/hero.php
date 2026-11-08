<!-- Hero Section -->
<section class="lp-hero-section">
    <div class="lp-container lp-hero-grid">
        <!-- Hero Left Content -->
        <div class="lp-hero-left">
            <span class="lp-badge lp-badge-amber">
                Marketplace Eksklusif Mahasiswa
            </span>
            <h1 class="lp-hero-title">
                Pusat Transaksi Mahasiswa
            </h1>
            <p class="lp-hero-desc">
                Pasar Kampus hadir sebagai solusi marketplace eksklusif bagi mahasiswa. Temukan barang kebutuhan kampus dengan mudah, harga terjangkau, dan transaksi langsung yang aman antar sesama mahasiswa.
            </p>
            <a href="<?= route('/barang') ?>" class="lp-btn lp-btn-primary lp-btn-lg">
                Mulai belanja &rarr;
            </a>

            <!-- Trust indicators -->
            <div class="lp-hero-trust">
                <div class="lp-trust-item">
                    <span class="lp-trust-dot"></span>
                    Bebas Ongkir (COD Kampus)
                </div>
                <div class="lp-trust-item">
                    <span class="lp-trust-dot"></span>
                    Terverifikasi Identitas Kampus
                </div>
                <div class="lp-trust-item">
                    <span class="lp-trust-dot"></span>
                    Harga Ramah Mahasiswa
                </div>
            </div>
        </div>

        <!-- Hero Right Illustration: Orbital Modern (Keranjang Belanja 3D & Floating Badges) -->
        <div class="lp-hero-visual-wrap">
            <div class="lp-hero-orbit-stage">
                <!-- SVG Orbital Ellipses -->
                <svg class="lp-orbit-svg" viewBox="0 0 520 440" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g transform="rotate(-22 260 220)">
                        <ellipse cx="260" cy="220" rx="230" ry="110" stroke="#191A23" stroke-width="1.6" />
                        <ellipse cx="260" cy="220" rx="192" ry="90" stroke="#191A23" stroke-width="1.6" />
                        <ellipse cx="258" cy="222" rx="154" ry="72" stroke="#191A23" stroke-width="1.6" />
                        <ellipse cx="254" cy="224" rx="116" ry="54" stroke="#191A23" stroke-width="1.5" />
                    </g>
                </svg>

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
                    <img src="<?= route('/images/cart-transparent.png') ?>" 
                         onerror="this.onerror=null; this.src='<?= route('/images/04573efb612afe5bde19c3cfa3a9c746.jpg') ?>';" 
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
            </div>
        </div>
    </div>
</section>
