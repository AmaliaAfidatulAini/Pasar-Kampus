<?php
// COMPONENTS PROPS //
$script = $script ?? "";
// END OF COMPONENTS PROPS //
?>

    <footer class="pk-footer mt-auto">
        <div class="container">
            <div class="row g-4 pb-4 mb-4 border-bottom border-light border-opacity-10">
                <div class="col-12 col-lg-4">
                    <a href="<?= route('/') ?>" class="pk-footer-brand d-inline-flex align-items-center gap-2 mb-3">
                        <img src="<?= asset('img/icon.svg') ?>" alt="" width="30" height="34">
                        <span class="fs-3 fw-bold">PasarKampus</span>
                    </a>
                    <p class="small text-white-50 mb-0">
                        Platform marketplace eksklusif mahasiswa untuk sirkulasi barang perkuliahan yang aman, hemat, dan berkelanjutan.
                    </p>
                </div>

                <div class="col-12 col-lg-8">
                    <div class="row g-4">
                        <div class="col-6 col-md-4">
                            <h2 class="pk-footer-title">PasarKampus</h2>
                            <ul class="list-unstyled d-grid gap-2 mb-0">
                                <li><a href="#tentang-kami">Tentang Kami</a></li>
                                <li><a href="<?= route('/explore') ?>">Katalog Barang</a></li>
                                <li><a href="<?= route('/register') ?>">Daftar Akun Mahasiswa</a></li>
                                <li><a href="<?= route('/barang') ?>">Barang Saya</a></li>
                            </ul>
                        </div>

                        <div class="col-6 col-md-4">
                            <h2 class="pk-footer-title">Bantuan</h2>
                            <ul class="list-unstyled d-grid gap-2 mb-0">
                                <li><a href="#faq">Pusat Bantuan</a></li>
                                <li><a href="#faq">FAQ</a></li>
                                <li><a href="mailto:support@pasarkampus.id">Kontak Kami</a></li>
                            </ul>
                        </div>

                        <div class="col-6 col-md-4">
                            <h2 class="pk-footer-title">Dukungan</h2>
                            <ul class="list-unstyled d-grid gap-2 mb-0">
                                <li><a href="#" data-pk-alert="Kebijakan Privasi: Data kampus dan identitas mahasiswa dilindungi.">Kebijakan</a></li>
                                <li><a href="#" data-pk-alert="Panduan COD: Selalu lakukan serah terima di area kampus yang ramai pada jam operasional kampus.">Panduan Keamanan COD</a></li>
                                <li><a href="#" data-pk-alert="Syarat &amp; Ketentuan PasarKampus: Hanya untuk barang legal &amp; tidak melanggar hukum.">Syarat &amp; Ketentuan</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="small text-white-50 text-start">
                <p>
                    &copy; 2026 PasarKampus
                </p>
            </div>
        </div>
    </footer>

    <script src="<?= asset('js/lib/popper.min.js') ?>"></script>
    <script src="<?= asset('js/lib/bootstrap.min.js') ?>"></script>
    <script src="<?= asset('js/lib/lucide.min.js') ?>"></script>
    <script src="<?= asset('js/lib/sweetalert2.min.js') ?>"></script>
    <script src="<?= asset('js/main.js') ?>"></script>

    <?= $script ?>
</body>
</html>
