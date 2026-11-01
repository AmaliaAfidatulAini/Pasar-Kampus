<!-- Categories Section -->
<section id="kategori" class="section section-bg-white">
    <div class="container">
        <div class="section-header">
            <h2>Kategori Barang Preloved</h2>
            <p>Pilih kategori barang yang paling sesuai dengan kebutuhan kuliah dan harianmu</p>
        </div>

        <div class="categories-grid">
            <!-- 1. Buku -->
            <a href="<?= route('/dashboard.php?category=buku') ?>" class="cat-card">
                <div class="cat-icon-wrapper cat-icon-buku">📚</div>
                <h4>Buku</h4>
                <span>Diktat, Modul, Novel</span>
            </a>

            <!-- 2. Elektronik -->
            <a href="<?= route('/dashboard.php?category=elektronik') ?>" class="cat-card">
                <div class="cat-icon-wrapper cat-icon-elektronik">💻</div>
                <h4>Elektronik</h4>
                <span>Laptop, HP, Monitor</span>
            </a>

            <!-- 3. ATK -->
            <a href="<?= route('/dashboard.php?category=atk') ?>" class="cat-card">
                <div class="cat-icon-wrapper cat-icon-atk">✏️</div>
                <h4>ATK</h4>
                <span>Kalkulator & Binder</span>
            </a>

            <!-- 4. Perlengkapan Kost -->
            <a href="<?= route('/dashboard.php?category=kost') ?>" class="cat-card">
                <div class="cat-icon-wrapper cat-icon-kost">🛏️</div>
                <h4>Perlengkapan Kost</h4>
                <span>Kasur, Lemari, dll</span>
            </a>

            <!-- 5. Pakaian -->
            <a href="<?= route('/dashboard.php?category=pakaian') ?>" class="cat-card">
                <div class="cat-icon-wrapper cat-icon-pakaian">👕</div>
                <h4>Pakaian</h4>
                <span>Jaket, Kemeja, Sepatu</span>
            </a>

            <!-- 6. Lainnya -->
            <a href="<?= route('/dashboard.php?category=lainnya') ?>" class="cat-card">
                <div class="cat-icon-wrapper cat-icon-lainnya">📦</div>
                <h4>Lainnya</h4>
                <span>Hobi & Aneka Barang</span>
            </a>
        </div>
    </div>
</section>
