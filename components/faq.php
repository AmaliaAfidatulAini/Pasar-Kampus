<!-- FAQ Section -->
<section id="faq" class="lp-section lp-section-alt">
    <div class="lp-container">
        <div class="lp-section-header">
            <span class="lp-badge lp-badge-navy">FAQ</span>
            <h2 class="lp-section-title">Pertanyaan yang Sering Diajukan</h2>
            <p class="lp-section-desc">
                Semua jawaban lengkap mengenai alur transaksi, keamanan, dan penggunaan PasarKampus.
            </p>
        </div>

        <div class="lp-faq-container">
            <!-- FAQ 1 -->
            <div class="lp-faq-item active">
                <button class="lp-faq-trigger" onclick="toggleFaq(this)">
                    <span>Mengapa harga barang di PasarKampus bisa lebih terjangkau?</span>
                    <div class="lp-faq-icon">+</div>
                </button>
                <div class="lp-faq-body">
                    <p>
                        Barang yang dijual di PasarKampus berasal langsung dari sesama mahasiswa (peer-to-peer) tanpa adanya pemotongan komisi perantara yang membebani. Selain itu, transaksi dilakukan dengan sistem serah terima langsung (COD) di area kampus, sehingga pembeli terbebas dari beban ongkos kirim ekspedisi.
                    </p>
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="lp-faq-item">
                <button class="lp-faq-trigger" onclick="toggleFaq(this)">
                    <span>Bagaimana PasarKampus menjamin keamanan transaksi dari penipuan?</span>
                    <div class="lp-faq-icon">+</div>
                </button>
                <div class="lp-faq-body">
                    <p>
                        PasarKampus menerapkan sistem verifikasi identitas kampus untuk setiap pengguna terdaftar. Metode transaksi diutamakan melalui Meetup / COD di area publik kampus (seperti perpustakaan, fakultas, atau kantin utama), sehingga pembeli dapat memeriksa keaslian dan kondisi fisik barang terlebih dahulu sebelum membayarkan uang kepada penjual.
                    </p>
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="lp-faq-item">
                <button class="lp-faq-trigger" onclick="toggleFaq(this)">
                    <span>Apakah aplikasi ini terbatas untuk mahasiswa dari satu kampus saja?</span>
                    <div class="lp-faq-icon">+</div>
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
                    <span>Bagaimana jika barang yang saya terima ternyata rusak atau cacat?</span>
                    <div class="lp-faq-icon">+</div>
                </button>
                <div class="lp-faq-body">
                    <p>
                        Dengan sistem COD kampus, Anda berhak memeriksa kondisi fisik barang terlebih dahulu sebelum transaksi diselesaikan. Jika tidak sesuai dengan deskripsi penjual, Anda dapat langsung membatalkan transaksi di tempat tanpa dikenakan penalti biaya apapun.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

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
</script>
