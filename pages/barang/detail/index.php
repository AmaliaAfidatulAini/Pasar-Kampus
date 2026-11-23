<?php
$session = get_session();
$isLoggedIn = !empty($session);

$productIdInput = $_GET['id'] ?? '';
if (!is_string($productIdInput) || trim($productIdInput) === '') {
    redirect('/explore', 302);
}

$productId = (int) $productIdInput;
$product = executeQuery('SELECT * FROM v_product_display WHERE id = ?', [$productId], 'fetch');
if (!$product) {
    flash('warning', 'Barang tidak ditemukan atau telah dihapus!');
    redirect('/explore', 302);
}

$dbProductId = (int) $product['id'];
$buyerId = $session['user_id'] ?? null;
$isOwner = $isLoggedIn && $buyerId !== null && (int) $product['user_id'] === (int) $buyerId;
$isAvailable = $product['lifecycle'] === 'Belum Terjual';

$viewer = fetch(executeQuery('SELECT * FROM product_viewer_state(?, ?)', [$dbProductId, (int) ($buyerId ?? 0)]));
$viewerOffer = is_array($viewer) && $viewer['offer_id'] !== null ? $viewer : null;
$viewerPurchaseId = is_array($viewer) && $viewer['purchase_id'] !== null ? (int) $viewer['purchase_id'] : 0;

$state = 'guest';
if ($isLoggedIn) {
    if ($isOwner) {
        $state = 'owner';
    } elseif ($viewerPurchaseId > 0) {
        $state = 'purchase';
    } elseif (!$isAvailable) {
        $state = 'unavailable';
    } elseif ($viewerOffer !== null && $viewerOffer['offer_status'] === 'OPEN' && (int) $viewerOffer['offer_stage'] === 2) {
        $state = 'final';
    } elseif ($viewerOffer !== null && $viewerOffer['offer_status'] === 'OPEN' && (int) $viewerOffer['offer_stage'] === 1) {
        $state = 'offer';
    } else {
        $state = 'default';
    }
}

$wa = preg_replace('/\D/', '', (string) $product['seller_phone']);
$wa = str_starts_with($wa, '0') ? '62' . substr($wa, 1) : $wa;

$price = (int) $product['price'];
$originalPrice = (int) $product['original_price'];
$minOffer = (int) ceil($price * 0.5);
$maxOffer = $price - 1;
$suggestedOffer = (int) (round($price * 0.75 / 500) * 500);

$conditionLevel = (int) $product['condition'];
$conditionLevel = $conditionLevel >= 1 && $conditionLevel <= 4 ? $conditionLevel : 3;

$gallery = json_decode((string) $product['gallery'], true) ?: [(string) $product['image']];
$tags = json_decode((string) $product['tags'], true) ?: [];
$mainImage = $gallery[0];

$sellerHeader = fetch(executeQuery(
    'SELECT avatar_path FROM v_user_header WHERE user_id = ?',
    [(int) $product['user_id']]
));
$sellerAvatarPath = trim((string) ($sellerHeader['avatar_path'] ?? ''));
$sellerAvatarUrl = $sellerAvatarPath === ''
    ? asset('img/profile-placeholder.webp')
    : (preg_match('~^(https?:)?//|^/~', $sellerAvatarPath) === 1 ? $sellerAvatarPath : upload($sellerAvatarPath));

$preferredRow = executeQuery('SELECT user_preferred_category_ids(?) AS ids', [(int) ($buyerId ?? 0)], 'fetch');
$preferredIds = is_array($preferredRow) ? (string) $preferredRow['ids'] : '[]';

$recommendations = fetchAll(executeQuery(
    'SELECT * FROM search_catalog(p_exclude_id => ?, p_exclude_user_id => ?,
        p_prefer_category_ids => CAST(? AS jsonb), p_seed => ?, p_limit => 8)',
    [$dbProductId, (int) ($buyerId ?? 0), $preferredIds, (string) random_int(100000, 999999)]
));

$conditionBadge = [1 => 'text-bg-secondary', 2 => 'text-bg-primary', 3 => 'text-bg-success', 4 => 'text-bg-warning'];
$conditionByLabel = ['Bekas' => 1, 'Cukup' => 2, 'Baik' => 3, 'Sangat Bagus' => 4];
?>

<?php Component::flush() ?>
<link rel="stylesheet" href="<?= asset('css/detail.css') ?>">
<?php $styles = Component::endFlush() ?>

<?php Component::include('user/header', [
    'title' => 'Detail Barang',
    'styles' => $styles,
]); ?>

<main class="container pt-4 pb-5" id="detailPage">
    <?php Component::flush() ?>
    <a class="btn btn-outline-secondary d-inline-flex align-items-center px-3 gap-2" href="<?= esc(route('/barang')) ?>">
        <i data-lucide="arrow-left" class="pk-icon" aria-hidden="true"></i>
        <span>Kembali</span>
    </a>
    <?php $extra = Component::endFlush() ?>

    <?php Component::include('common/banner', [
        'title' => 'Detail barang',
        'subtitle' => 'Lihat deskripsi barang secara lebih detail',
        'extra' => $extra,
    ]) ?>

    <section class="pk-panel">
        <div class="pk-detail">
            <div class="pk-gallery">
                <div class="pk-gallery__stage" id="galleryStage" tabindex="0" aria-label="Galeri foto">
                    <img id="galleryImage" src="<?= esc($mainImage) ?>" alt="<?= esc($product['title'] . ' foto 1') ?>" width="900" height="675">
                    <button type="button" class="pk-gallery__nav pk-gallery__nav--prev" id="galleryPrev" aria-label="Foto sebelumnya">
                        <i data-lucide="chevron-left" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="pk-gallery__nav pk-gallery__nav--next" id="galleryNext" aria-label="Foto berikutnya">
                        <i data-lucide="chevron-right" aria-hidden="true"></i>
                    </button>
                    <span class="pk-gallery__count" id="galleryCount" aria-live="polite">1 / <?= count($gallery) ?></span>
                </div>
                <div class="pk-thumbs" id="galleryThumbs" role="group" aria-label="Pilih foto">
                    <?php foreach ($gallery as $index => $imgUrl): ?>
                        <button type="button" class="pk-thumb" data-index="<?= (int) $index ?>" aria-label="Lihat foto <?= (int) $index + 1 ?>"<?= $index === 0 ? ' aria-current="true"' : '' ?>>
                            <img src="<?= esc($imgUrl) ?>" alt="" loading="lazy">
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="pk-info">
                <h2 class="pk-info__title" id="productTitle"><?= esc($product['title']) ?></h2>

                <div class="d-flex flex-wrap gap-2 mt-3">
                    <span class="badge text-bg-primary"><?= esc($product['category']) ?></span>
                    <?php foreach ($tags as $tag): ?>
                        <span class="badge bg-warning-subtle text-warning-emphasis"><?= esc($tag) ?></span>
                    <?php endforeach; ?>
                </div>

                <div class="pk-seller">
                    <span class="pk-seller__avatar" aria-hidden="true">
                        <img src="<?= esc($sellerAvatarUrl) ?>" alt="">
                    </span>
                    <div class="pk-seller__text">
                        <p class="pk-seller__name">oleh <strong><?= esc($product['seller_name']) ?></strong></p>
                        <p class="pk-seller__loc">
                            <i data-lucide="map-pin" aria-hidden="true"></i>
                            <span><?= esc($product['location']) ?></span>
                        </p>
                    </div>
                    <button type="button" class="pk-seller__report" data-bs-toggle="modal" data-bs-target="#reportModal" aria-label="Laporkan barang ini" title="Laporkan barang">
                        <i data-lucide="flag" aria-hidden="true"></i>
                    </button>
                </div>

                <div class="pk-specs-wrap">
                    <table class="table table-sm align-middle pk-specs mb-0">
                        <tbody>
                            <tr>
                                <th scope="row">Kondisi</th>
                                <td><span class="pk-cond pk-cond--<?= $conditionLevel ?>"><?= esc($product['condition_label']) ?></span></td>
                            </tr>
                            <tr><th scope="row">Lokasi</th><td><?= esc($product['location']) ?></td></tr>
                            <tr><th scope="row">Metode</th><td><?= esc($product['method']) ?></td></tr>
                            <tr><th scope="row">Tanggal Dibuat</th><td><?= esc(format_tanggal((string) $product['created_at'])) ?></td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="pk-price">
                    <?php if ($originalPrice > $price): ?>
                        <s class="pk-price__was"><?= esc(format_rupiah($originalPrice)) ?></s>
                    <?php endif; ?>
                    <span class="pk-price__now"><?= esc(format_rupiah($price)) ?></span>
                </div>

                <div class="pk-state" data-state-panel="<?= esc($state) ?>">
                    <?php if ($state === 'guest'): ?>
                        <div class="row g-2 mt-3">
                            <div class="col-12 d-grid">
                                <a class="btn btn-primary btn-lg d-inline-flex align-items-center justify-content-center gap-2" href="<?= esc(route('/login')) ?>">
                                    <i data-lucide="shopping-bag" class="pk-icon" aria-hidden="true"></i><span>Ajukan Pembelian</span>
                                </a>
                            </div>
                        </div>
                    <?php elseif ($state === 'owner'): ?>
                        
                    <?php elseif ($state === 'unavailable'): ?>
                        <div class="row g-2 mt-3">
                            <div class="col-12 d-grid">
                                <button type="button" class="btn btn-secondary btn-lg" disabled>Status: <?= esc((string) $product['lifecycle_label']) ?></button>
                            </div>
                        </div>
                    <?php elseif ($state === 'purchase'): ?>
                        <div class="alert alert-success small d-flex align-items-center gap-2 mb-0 mt-3 py-2 px-3" role="status">
                            <i data-lucide="circle-check-big" class="pk-icon--sm flex-shrink-0" aria-hidden="true"></i>
                            <span>Pembelian diajukan <strong><?= esc(format_rupiah((int) ($viewer['purchase_amount'] ?? 0))) ?></strong> &middot; menunggu konfirmasi penjual.</span>
                        </div>
                        <div class="row g-2 mt-3">
                            <div class="col-12 col-sm-6 d-grid">
                                <a class="btn btn-primary btn-lg d-inline-flex align-items-center justify-content-center gap-2" href="https://wa.me/<?= esc($wa) ?>?text=<?= esc(rawurlencode('Halo, saya sudah mengajukan pembelian untuk ' . (string) $product['title'] . ' di PasarKampus.')) ?>" target="_blank" rel="noopener">
                                    <i data-lucide="message-circle" class="pk-icon" aria-hidden="true"></i><span>Kontak Penjual</span>
                                </a>
                            </div>
                            <div class="col-12 col-sm-6 d-grid">
                                <a class="btn btn-lg btn-outline-primary d-inline-flex align-items-center justify-content-center gap-2" href="<?= esc(route('/riwayat/detail?id=' . $viewerPurchaseId)) ?>">
                                    <i data-lucide="receipt-text" class="pk-icon" aria-hidden="true"></i><span>Detail Pembelian</span>
                                </a>
                            </div>
                        </div>
                    <?php elseif ($state === 'final'): ?>
                        <div class="alert alert-warning small d-flex align-items-center gap-2 mb-0 mt-3 py-2 px-3" role="status">
                            <i data-lucide="undo-2" class="pk-icon--sm flex-shrink-0" aria-hidden="true"></i>
                            <span>Penjual menawar balik <strong><?= esc(format_rupiah((int) $viewerOffer['offer_price'])) ?></strong>. Segera terima sebelum barang dibeli pengguna lain!</span>
                        </div>
                        <form class="row g-2 mt-3" method="post" action="<?= esc(route('/tawaran/post')) ?>">
                            <input type="hidden" name="action" value="terima">
                            <input type="hidden" name="offer_id" value="<?= (int) $viewerOffer['offer_id'] ?>">
                            <input type="hidden" name="redirect" value="<?= esc(current_request_path()) ?>">
                            <div class="col-12 d-grid">
                                <button type="submit" class="btn btn-lg pk-btn-accept d-inline-flex align-items-center justify-content-center gap-2"
                                    data-confirm="Apakah anda yakin untuk menerima tawaran ini?"
                                    data-confirm-title="Terima Tawaran"
                                    data-confirm-ok="Ya, terima">
                                    <i data-lucide="circle-check-big" class="pk-icon" aria-hidden="true"></i><span>Terima Tawaran &middot; <?= esc(format_rupiah((int) $viewerOffer['offer_price'])) ?></span>
                                </button>
                            </div>
                        </form>
                    <?php elseif ($state === 'offer'): ?>
                        <div class="alert alert-info small d-flex align-items-center gap-2 mb-0 mt-3 py-2 px-3" role="status">
                            <i data-lucide="clock-3" class="pk-icon flex-shrink-0" aria-hidden="true"></i>
                            <span>
                                Kamu menawarkan harga <strong><?= esc(format_rupiah((int) $viewerOffer['offer_price'])) ?></strong> ke penjual. <br />
                                Tunggu review dari penjual atau langsung beli dengan harga asli.
                            </span>
                        </div>
                        <div class="row g-2 mt-3">
                            <div class="col-12 col-sm-6 d-grid">
                                <button type="button" class="btn btn-primary btn-lg d-inline-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#buyModal">
                                    <i data-lucide="shopping-bag" class="pk-icon" aria-hidden="true"></i><span>Beli Sekarang</span>
                                </button>
                            </div>
                            <div class="col-12 col-sm-6 d-grid">
                                <button type="button" class="btn btn-secondary btn-lg d-inline-flex align-items-center justify-content-center gap-2" disabled>
                                    <i data-lucide="clock-3" class="pk-icon" aria-hidden="true"></i><span>Tawaran Diajukan</span>
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="row g-2 mt-3">
                            <div class="col-12 col-sm-6 d-grid">
                                <button type="button" class="btn btn-primary btn-lg d-inline-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#buyModal">
                                    <i data-lucide="shopping-bag" class="pk-icon" aria-hidden="true"></i><span>Beli Sekarang</span>
                                </button>
                            </div>
                            <div class="col-12 col-sm-6 d-grid">
                                <button type="button" class="btn btn-lg pk-btn-nego d-inline-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#offerModal">
                                    <i data-lucide="handshake" class="pk-icon" aria-hidden="true"></i><span>Nego</span>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="pk-panel mt-4">
        <h2 class="pk-eyebrow">Deskripsi</h2>
        <?php
        // Rows written before rich text are plain text: keep their line breaks
        // and escaping. Anything containing markup is re-sanitized at the sink so
        // a legacy row can never inject a tag or script.
        $descriptionDisplay = (string) $product['description_display'];
        $descriptionHtml = str_contains($descriptionDisplay, '<')
            ? sanitize_rich_text($descriptionDisplay)
            : nl2br(esc($descriptionDisplay));
        ?>
        <div class="pk-desc"><?= $descriptionHtml ?></div>
    </section>

    <section class="pk-rec" aria-labelledby="rekomendasiTitle">
        <h2 class="pk-rec__title" id="rekomendasiTitle">Kamu mungkin juga suka</h2>
        <div class="pk-rec__grid">
            <?php foreach ($recommendations as $item): ?>
                <?php
                $recLevel = $conditionByLabel[(string) $item['condition']] ?? 3;
                $recPrice = (int) $item['price'];
                $recOriginal = (int) $item['original_price'];
                ?>
                <a class="pk-rec__card" href="<?= esc(route('/barang/detail?id=' . (int) $item['id'])) ?>">
                    <div class="pk-rec__media">
                        <img src="<?= esc((string) $item['image']) ?>" alt="" loading="lazy">
                        <span class="badge <?= esc($conditionBadge[$recLevel]) ?> position-absolute top-0 start-0 m-2"><?= esc((string) $item['condition']) ?></span>
                    </div>
                    <div class="pk-rec__body">
                        <p class="pk-rec__name"><?= esc((string) $item['title']) ?></p>
                        <p class="pk-rec__loc"><i data-lucide="map-pin" aria-hidden="true"></i><span><?= esc((string) $item['location']) ?></span></p>
                        <p class="pk-rec__price"><?= esc(format_rupiah($recPrice)) ?><?php if ($recOriginal > $recPrice): ?> <s><?= esc(format_rupiah($recOriginal)) ?></s><?php endif; ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<div class="modal fade" id="buyModal" tabindex="-1" aria-labelledby="buyModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header p-4">
                <h2 class="modal-title h5 fw-bold text-primary" id="buyModalTitle">Ajukan Pembelian COD</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="buyForm" method="post" action="<?= esc(route('/tawaran/post')) ?>">
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="beli">
                    <input type="hidden" name="product_id" value="<?= esc((string) $product['id']) ?>">
                    <input type="hidden" name="redirect" value="<?= esc(current_request_path()) ?>">
                    <p class="text-body-secondary mb-0">
                        Anda mengajukan pembelian untuk
                        <strong class="text-body-emphasis"><?= esc($product['title']) ?></strong>
                        seharga
                        <strong class="text-body-emphasis"><?= esc(format_rupiah($price)) ?></strong>
                        dengan metode COD di area kampus.
                    </p>
                </div>
                <div class="modal-footer p-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Ajukan Pembelian</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="offerModal" tabindex="-1" aria-labelledby="offerModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header p-4">
                <h2 class="modal-title h5 fw-bold text-primary" id="offerModalTitle">Ajukan Tawaran</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="offerForm" method="post" action="<?= esc(route('/tawaran/post')) ?>" data-original="<?= $originalPrice ?>" novalidate>
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="nego">
                    <input type="hidden" name="product_id" value="<?= esc((string) $product['id']) ?>">
                    <input type="hidden" name="redirect" value="<?= esc(current_request_path()) ?>">
                    <div class="pk-modal__summary">
                        <span><?= esc($product['title']) ?></span>
                        <span class="pk-modal__summary-price"><?= esc(format_rupiah($price)) ?></span>
                    </div>
                    <label class="form-label fw-semibold" for="offerInput">Tawaran kamu</label>
                    <div class="input-group pk-amount">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="offerInput" name="price" inputmode="numeric" step="500"
                            min="<?= $minOffer ?>" max="<?= $maxOffer ?>" value="<?= $suggestedOffer ?>" required>
                    </div>
                    <div class="pk-readout" id="offerReadout" aria-live="polite"></div>
                    <p class="text-secondary mb-2">Catatan:</p>
                    <ul class="text-secondary mb-0">
                        <li id="offerBounds">Batas tawaran <?= esc(format_rupiah($minOffer)) ?> &ndash; <?= esc(format_rupiah($maxOffer)) ?>.</li>
                        <li>Penjual bisa menerima atau menawar balik tawaranmu.</li>
                    </ul>
                    <div class="invalid-feedback d-block pk-offer-error" id="offerError" hidden></div>
                </div>
                <div class="modal-footer p-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Tawaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="modal-header p-4">
                <h2 class="modal-title h5 fw-bold text-primary" id="reportModalTitle">Laporkan Barang</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="reportForm" method="post" action="<?= esc(route('/user/report/post')) ?>" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="submit-report">
                    <input type="hidden" name="product_id" value="<?= esc((string) $product['id']) ?>">

                    <div class="mb-3">
                        <span class="form-label d-block fw-semibold">Jenis</span>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis" id="jenisBarang" value="Barang" checked>
                                <label class="form-check-label" for="jenisBarang">Barang</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis" id="jenisPengguna" value="Pengguna">
                                <label class="form-check-label" for="jenisPengguna">Pengguna</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="aduanKategori">Kategori</label>
                        <select class="form-select" name="kategori" id="aduanKategori" required>
                            <option value="" disabled selected>Pilih kategori aduan</option>
                            <option value="Penipuan">Penipuan</option>
                            <option value="Konten tidak pantas">Konten tidak pantas</option>
                            <option value="Barang palsu / rusak">Barang palsu / rusak</option>
                            <option value="Ghosting / Tidak merespon">Ghosting / Tidak merespon</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="aduanDeskripsi">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" id="aduanDeskripsi" rows="3" placeholder="Tuliskan keterangan detail laporan Anda..." required></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold" for="aduanLampiran">Lampiran</label>
                        <input class="form-control" type="file" name="lampiran[]" id="aduanLampiran" accept="image/*" multiple>
                        <div class="form-text">Opsional, maksimal 3 foto.</div>
                    </div>
                </div>
                <div class="modal-footer p-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php Component::flush() ?>
<script src="<?= asset('js/barang-detail.js') ?>" defer></script>
<script src="<?= asset('js/tawaran.js') ?>" defer></script>
<?php $script = Component::endFlush() ?>

<?php Component::include('user/footer', ['script' => $script]); ?>
