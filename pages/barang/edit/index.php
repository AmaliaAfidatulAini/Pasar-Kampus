<?php

session_include('/login');

$categories = fetchColumn(executeQuery('SELECT category_name FROM categories ORDER BY category_name ASC'));

require_model('catalog');
$editProduct = get_catalog_product_by_id((int) ($_GET['id'] ?? 0));
$conditionLabels = [1 => 'Bekas', 2 => 'Cukup', 3 => 'Baik', 4 => 'Sangat Bagus'];
$formValues = [
    'namaBarang' => (string) ($editProduct['title'] ?? ''),
    'kategori' => (string) ($editProduct['category'] ?? ''),
    'harga' => $editProduct ? (string) (int) $editProduct['price'] : '',
    'kondisi' => $conditionLabels[(int) ($editProduct['condition'] ?? 3)] ?? 'Baik',
    'deskripsi' => (string) ($editProduct['description'] ?? ''),
];

$productId = (int) ($editProduct['id'] ?? 0);
$coverImage = '';
$thumbImages = [];
foreach (fetchAll(executeQuery(
    'SELECT attachment_type, file_path FROM attachments WHERE product_id = ? ORDER BY attachment_type ASC, attachment_id ASC',
    [$productId]
)) as $attachment) {
    $filePath = trim((string) ($attachment['file_path'] ?? ''));
    if ($filePath === '') {
        continue;
    }
    if ((int) $attachment['attachment_type'] === 1) {
        $coverImage = $coverImage !== '' ? $coverImage : $filePath;
    } elseif ((int) $attachment['attachment_type'] === 2) {
        $thumbImages[] = $filePath;
    }
}
$thumbImages = array_slice($thumbImages, 0, 5);
$productTags = fetchColumn(executeQuery(
    'SELECT t.name_tag FROM tags t JOIN products_tags pt ON pt.products_tagsid_tag = t.id_tag WHERE pt.productsproduct_id = ? ORDER BY t.name_tag ASC',
    [$productId]
));
?>
<?php Component::include('user/header', [
    'title' => 'Edit Barang',
]); ?>

<main class="container py-4 py-lg-5">
    <?php Component::flush() ?>
    <a class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3" href="<?= esc(route('/barang')) ?>">
        <i data-lucide="arrow-left" class="pk-icon" aria-hidden="true"></i>
        <span>Kembali</span>
    </a>
    <?php $extra = Component::endFlush() ?>

    <?php Component::include("common/banner", [
        "title" => 'Edit barang',
        "subtitle" => "Isi detail barang yang ingin kamu jual di lingkungan kampus",
        "extra" => $extra
    ]) ?>

    <form id="barangForm" class="card" method="post" action="<?= esc(route('/barang/edit/post')) ?>" enctype="multipart/form-data">
        <input type="hidden" name="target" value="edit">
        <input type="hidden" name="id" value="<?= (int) ($editProduct['id'] ?? 0) ?>">
        <div class="card-body p-3">
            <div class="row g-4">
                <div class="col-12 col-lg-4">
                    <div class="d-grid gap-2">
                        <span class="form-label small fw-semibold mb-0">Unggah Foto Produk</span>
                        <button type="button" class="ratio ratio-1x1 border rounded-3 pk-border-dashed bg-body-tertiary overflow-hidden position-relative" data-file-target="imgCover">
                            <span class="d-flex align-items-center justify-content-center text-body-secondary small<?= $coverImage !== '' ? ' d-none' : '' ?>" data-placeholder>📷 Foto Utama (Katalog)</span>
                            <img class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover<?= $coverImage !== '' ? '' : ' d-none' ?>" data-preview alt=""<?= $coverImage !== '' ? ' src="' . esc($coverImage) . '"' : '' ?>>
                        </button>
                        <input type="file" id="imgCover" name="imgCover" accept="image/*" class="d-none" data-preview-input="imgCover">
                        <div class="row row-cols-5 g-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php $thumbImage = $thumbImages[$i - 1] ?? ''; ?>
                                <div class="col">
                                    <button type="button" class="ratio ratio-1x1 border rounded-2 pk-border-dashed bg-body-tertiary overflow-hidden position-relative" data-file-target="imgThumb<?= $i ?>">
                                        <span class="d-flex align-items-center justify-content-center text-body-secondary small<?= $thumbImage !== '' ? ' d-none' : '' ?>" data-placeholder>+<?= $i ?></span>
                                        <img class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover<?= $thumbImage !== '' ? '' : ' d-none' ?>" data-preview alt=""<?= $thumbImage !== '' ? ' src="' . esc($thumbImage) . '"' : '' ?>>
                                    </button>
                                    <input type="file" id="imgThumb<?= $i ?>" name="imgThumb<?= $i ?>" accept="image/*" class="d-none" data-preview-input="imgThumb<?= $i ?>">
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-8">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small" for="namaBarang">Nama barang</label>
                            <input class="form-control" type="text" id="namaBarang" name="namaBarang" placeholder="Contoh: Tumbler Stainless 500ml" value="<?= esc($formValues['namaBarang']) ?>" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small" for="kategori">Kategori</label>
                            <select class="form-select" id="kategori" name="kategori" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($categories as $option): ?>
                                    <option value="<?= esc($option) ?>" <?= $formValues['kategori'] === $option ? 'selected' : '' ?>><?= esc($option) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small" for="harga">Harga (Rp)</label>
                            <input class="form-control" type="number" id="harga" name="harga" min="1" step="1" placeholder="50000" value="<?= esc($formValues['harga']) ?>" required>
                        </div>
                        <fieldset class="col-12">
                            <legend class="form-label fw-semibold small">Kondisi barang</legend>
                            <div class="d-flex flex-wrap gap-3">
                                <?php foreach (['Sangat Bagus', 'Baik', 'Cukup', 'Bekas'] as $option): ?>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="radio" name="kondisi" id="kondisi-<?= esc(str_replace(' ', '-', $option)) ?>" value="<?= esc($option) ?>" <?= $formValues['kondisi'] === $option ? 'checked' : '' ?>>
                                        <label class="form-check-label small" for="kondisi-<?= esc(str_replace(' ', '-', $option)) ?>"><?= esc($option) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </fieldset>
                        <div class="col-12">
                            <label class="form-label fw-semibold small" for="customTagInput">Tag</label>
                            <div class="border rounded-3 px-3 py-2 d-flex flex-wrap align-items-center gap-2" id="selectedTagsBox">
                                <input class="form-control form-control-sm border-0 shadow-none p-0 flex-grow-1" style="min-width: 8rem;" type="text" id="customTagInput" placeholder="Ketik tag & tekan Enter...">
                            </div>
                            <input type="hidden" name="tags" id="tagsInput" value="<?= esc(json_encode($productTags)) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small" for="deskripsi">Deskripsi barang</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" placeholder="Jelaskan kondisi atau kelebihan barang..." required><?= esc($formValues['deskripsi']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer p-3 d-flex flex-wrap justify-content-end gap-2">
            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2" id="btnSubmitBarang">
                <i data-lucide="save" class="pk-icon" aria-hidden="true"></i>
                Simpan
            </button>
        </div>
    </form>
</main>

<?php Component::flush() ?>
<script src="<?= asset('js/barang.js') ?>" defer></script>
<?php $script = Component::endFlush() ?>

<?php Component::include('user/footer', ['script' => $script]); ?>
