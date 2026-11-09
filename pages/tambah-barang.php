<?php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PasarKampus - Tambah Barang Baru</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --pk-navy: #1D356D;
            --pk-amber: #F59E0B;
            --pk-blue: #2563EB;
            --pk-bg: #F8FAFC;
            --pk-border: #E2E8F0;
        }
        body {
            background-color: var(--pk-bg);
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 0;
            color: #1E293B;
        }
        .pk-navbar {
            background: linear-gradient(180deg, #1D4ED8 0%, #2355B8 60%, #1E40AF 100%);
            height: 64px;
            display: flex;
            align-items: center;
            padding: 0 28px;
            color: #FFF;
        }
        .pk-nav-inner {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .pk-nav-logo { font-size: 1.35rem; font-weight: 800; color: #FFF; text-decoration: none; }
        .pk-container {
            max-width: 1000px;
            margin: 32px auto 60px;
            padding: 0 20px;
        }
        .pk-header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
        }
        .pk-page-title { font-size: 1.6rem; font-weight: 800; color: var(--pk-navy); margin: 0 0 6px 0; }
        .pk-page-sub { color: #64748B; margin: 0; font-size: 0.92rem; }
        .pk-back-link { color: var(--pk-navy); text-decoration: none; font-weight: 700; }
        .pk-form-card {
            background: #FFF;
            border-radius: 20px;
            padding: 36px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            border: 1px solid #EDF2F7;
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 40px;
        }
        
        /* Layout Foto Upload Preview */
        .photo-upload-section { display: flex; flex-direction: column; gap: 14px; }
        .main-photo-slot {
            width: 100%;
            height: 240px;
            border: 2px dashed #CBD5E1;
            border-radius: 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            background: #F8FAFC;
            position: relative;
            overflow: hidden;
            transition: border-color 0.2s;
        }
        .main-photo-slot:hover { border-color: var(--pk-blue); }
        .main-photo-slot img { width: 100%; height: 100%; object-fit: cover; position: absolute; }
        .thumb-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; }
        .thumb-slot {
            height: 54px;
            border: 1px dashed #CBD5E1;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            color: #94A3B8;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            background: #F8FAFC;
        }
        .thumb-slot img { width: 100%; height: 100%; object-fit: cover; position: absolute; }
        
        /* Form Inputs */
        .form-grid { display: flex; flex-direction: column; gap: 18px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group label { font-weight: 700; font-size: 0.9rem; color: #334155; }
        .pk-input, .pk-select, .pk-textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #CBD5E1;
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.9rem;
            box-sizing: border-box;
            outline: none;
        }
        .pk-input:focus, .pk-select:focus, .pk-textarea:focus { border-color: var(--pk-blue); }
        .radio-group { display: flex; gap: 16px; }
        .radio-label { display: flex; align-items: center; gap: 6px; font-size: 0.88rem; cursor: pointer; }
        
        /* Tags Container */
        .tags-wrapper { display: flex; flex-direction: column; gap: 10px; }
        .selected-tags-box {
            min-height: 44px;
            border: 1px solid #CBD5E1;
            border-radius: 10px;
            padding: 6px 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
        }
        .tag-chip {
            background: #EFF6FF;
            color: var(--pk-blue);
            border: 1px solid #BFDBFE;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }
        .tag-chip .remove-btn { cursor: pointer; color: #EF4444; font-weight: 700; }
        .custom-tag-input {
            border: none;
            outline: none;
            font-size: 0.85rem;
            flex: 1;
            min-width: 120px;
        }
        .recom-tags-box { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }
        .recom-tag-chip {
            background: #F1F5F9;
            color: #475569;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
        }
        .recom-tag-chip:hover { background: #E2E8F0; }
        
        /* Footer Action */
        .form-actions {
            grid-column: 1 / -1;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 10px;
        }
        .btn-submit {
            background: var(--pk-navy);
            color: #FFF;
            border: none;
            padding: 12px 28px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
        }
        .btn-cancel {
            background: #E2E8F0;
            color: #475569;
            border: none;
            padding: 12px 24px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
    </style>
</head>
<body>

    <header class="pk-navbar">
        <div class="pk-nav-inner">
            <a href="dashboard-penjual.php" class="pk-nav-logo">PasarKampus</a>
        </div>
    </header>

    <div class="pk-container">
        <div class="pk-header-row">
            <div>
                <h1 class="pk-page-title">Tambah barang baru</h1>
                <p class="pk-page-sub">Isi detail barang yang ingin kamu jual di lingkungan kampus.</p>
            </div>
            <a href="dashboard-penjual.php" class="pk-back-link">&larr; Kembali</a>
        </div>

        <form id="tambahBarangForm" onsubmit="handleSaveBarang(event)">
            <div class="pk-form-card">
                
                <!-- Unggah Foto Upload UI -->
                <div class="photo-upload-section">
                    <label style="font-weight: 700; font-size: 0.9rem;">Unggah Foto Produk</label>
                    
                    <!-- Cover Utama -->
                    <div class="main-photo-slot" onclick="document.getElementById('imgCover').click()">
                        <span id="coverPlaceholder">📷 Foto Utama (Katalog)</span>
                        <img id="coverPreview" src="" style="display:none;">
                        <input type="file" id="imgCover" accept="image/*" style="display:none" onchange="previewImage(this, 'coverPreview', 'coverPlaceholder')">
                    </div>

                    <!-- 5 Thumbnail Gallery -->
                    <div class="thumb-grid">
                        <?php for($i=1; $i<=5; $i++): ?>
                        <div class="thumb-slot" onclick="document.getElementById('imgThumb<?= $i ?>').click()">
                            <span id="thumbPlaceholder<?= $i ?>">+<?= $i ?></span>
                            <img id="thumbPreview<?= $i ?>" src="" style="display:none;">
                            <input type="file" id="imgThumb<?= $i ?>" accept="image/*" style="display:none" onchange="previewImage(this, 'thumbPreview<?= $i ?>', 'thumbPlaceholder<?= $i ?>')">
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Form Fields -->
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nama barang</label>
                        <input type="text" id="namaBarang" class="pk-input" placeholder="Contoh: Tumbler Stainless 500ml" required>
                    </div>

                    <div class="form-group">
                        <label>Kategori</label>
                        <select id="kategori" class="pk-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Buku">Buku</option>
                            <option value="Elektronik">Elektronik</option>
                            <option value="Pakaian">Pakaian</option>
                            <option value="Perlengkapan">Perlengkapan Kos</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Harga (Rp)</label>
                        <input type="number" id="harga" class="pk-input" placeholder="Rp. 50000" required>
                    </div>

                    <div class="form-group">
                        <label>Kondisi barang</label>
                        <div class="radio-group">
                            <label class="radio-label"><input type="radio" name="kondisi" value="Sangat Bagus" checked> Sangat Bagus</label>
                            <label class="radio-label"><input type="radio" name="kondisi" value="Baik"> Baik</label>
                            <label class="radio-label"><input type="radio" name="kondisi" value="Cukup"> Cukup</label>
                            <label class="radio-label"><input type="radio" name="kondisi" value="Bekas"> Bekas</label>
                        </div>
                    </div>

                    <!-- Tag Interaktif -->
                    <div class="form-group">
                        <label>Tag</label>
                        <div class="tags-wrapper">
                            <div class="selected-tags-box" id="selectedTagsBox">
                                <!-- Tags terpilih dimuat dinamis -->
                                <input type="text" id="customTagInput" class="custom-tag-input" placeholder="Ketik tag & tekan Enter..." onkeydown="handleCustomTag(event)">
                            </div>
                            <div class="recom-tags-box" id="recomTagsBox">
                                <span style="font-size: 0.78rem; color: #94A3B8;">Rekomendasi:</span>
                                <!-- Tag rekomendasi dimuat dinamis -->
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi barang</label>
                        <textarea id="deskripsi" class="pk-textarea" rows="3" placeholder="Jelaskan kondisi atau kelebihan barang..." required></textarea>
                    </div>
                </div>

                <!-- Footer CTA Bar -->
                <div class="form-actions">
                    <a href="dashboard-penjual.php" class="btn-cancel">Batal</a>
                    <button type="submit" class="btn-submit">Simpan & Tayangkan</button>
                </div>

            </div>
        </form>
    </div>

    <script>
        // Managing Interactive Tags
        let selectedTags = [];
        let recommendedTags = ["#Baru", "#Gokil", "#SiapTempur", "#NegoAfgan", "#BukuKuliah"];

        function renderTags() {
            const selectedBox = document.getElementById('selectedTagsBox');
            const recomBox = document.getElementById('recomTagsBox');
            const customInput = document.getElementById('customTagInput');

            // Render selected tags
            selectedBox.querySelectorAll('.tag-chip').forEach(el => el.remove());
            selectedTags.forEach(tag => {
                const chip = document.createElement('span');
                chip.className = 'tag-chip';
                chip.innerHTML = `${tag} <span class="remove-btn" onclick="removeTag('${tag}')">&times;</span>`;
                selectedBox.insertBefore(chip, customInput);
            });

            // Render recommended tags
            recomBox.innerHTML = `<span style="font-size: 0.78rem; color: #94A3B8;">Rekomendasi:</span>`;
            recommendedTags.forEach(tag => {
                const chip = document.createElement('span');
                chip.className = 'recom-tag-chip';
                chip.innerText = `${tag} +`;
                chip.onclick = () => selectTag(tag);
                recomBox.appendChild(chip);
            });
        }

        function selectTag(tag) {
            if (!selectedTags.includes(tag)) {
                selectedTags.push(tag);
                recommendedTags = recommendedTags.filter(t => t !== tag);
                renderTags();
            }
        }

        function removeTag(tag) {
            selectedTags = selectedTags.filter(t => t !== tag);
            if (!recommendedTags.includes(tag) && tag.startsWith('#')) {
                recommendedTags.push(tag);
            }
            renderTags();
        }

        function handleCustomTag(e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                let val = e.target.value.trim();
                if (val) {
                    if (!val.startsWith('#')) val = '#' + val;
                    if (!selectedTags.includes(val)) {
                        selectedTags.push(val);
                        e.target.value = '';
                        renderTags();
                    }
                }
            }
        }

        function previewImage(input, previewId, placeholderId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).src = e.target.result;
                    document.getElementById(previewId).style.display = 'block';
                    document.getElementById(placeholderId).style.display = 'none';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function handleSaveBarang(e) {
            e.preventDefault();
            const stored = JSON.parse(localStorage.getItem('pk_penjual_barang') || '[]');
            
            const newBarang = {
                id: Date.now(),
                namaBarang: document.getElementById('namaBarang').value,
                kategori: document.getElementById('kategori').value,
                harga: parseInt(document.getElementById('harga').value),
                status: "Tersedia",
                pengajuanCount: 0,
                pengajuanList: [],
                gambar: document.getElementById('coverPreview').src || 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=300',
                tags: selectedTags,
                tglInput: new Date().toLocaleDateString('id-ID')
            };

            // Simpan data di urutan teratas (paling depan)
            stored.unshift(newBarang);
            localStorage.setItem('pk_penjual_barang', JSON.stringify(stored));
            
            // Redirect kembali ke dashboard utama penjual
            window.location.href = 'dashboard-penjual.php';
        }

        document.addEventListener('DOMContentLoaded', renderTags);
    </script>
</body>
</html>