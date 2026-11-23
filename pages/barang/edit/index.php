<?php

session_include('/login');

$categories = fetchColumn(executeQuery('SELECT category_name FROM categories ORDER BY category_name ASC'));

$editProductRow = executeQuery(
    'SELECT * FROM v_product_display WHERE id = ?',
    [(int) ($_GET['id'] ?? 0)],
    'fetch'
);
$editProduct = is_array($editProductRow) ? $editProductRow : null;
$formValues = [
    'namaBarang' => (string) ($editProduct['title'] ?? ''),
    'kategori' => (string) ($editProduct['category'] ?? ''),
    'harga' => $editProduct ? (string) (int) $editProduct['price'] : '',
    'hargaAsli' => $editProduct ? (string) (int) $editProduct['original_price'] : '',
    'kondisi' => (string) ($editProduct['condition_label'] ?? 'Baik'),
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

Component::flush();
?>
<link rel="stylesheet" href="<?= asset('css/lib/quill.snow.css') ?>">
<link rel="stylesheet" href="<?= asset('css/barang-form.css') ?>">
<?php $styles = Component::endFlush(); ?>
<?php Component::include('user/header', [
    'title' => 'Edit Barang',
    'styles' => $styles,
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

    <?php Component::include('barang/form', [
        'action' => route('/barang/edit/post'),
        'target' => 'edit',
        'productId' => $productId,
        'submitLabel' => 'Simpan perubahan',
        'formValues' => $formValues,
        'categories' => $categories,
        'coverImage' => $coverImage,
        'thumbImages' => $thumbImages,
        'productTags' => $productTags,
    ]); ?>
</main>

<?php Component::flush(); ?>
<script src="<?= asset('js/lib/quill.min.js') ?>" defer></script>
<script src="<?= asset('js/barang.js') ?>" defer></script>
<?php $script = Component::endFlush(); ?>
<?php Component::include('user/footer', ['script' => $script]); ?>
