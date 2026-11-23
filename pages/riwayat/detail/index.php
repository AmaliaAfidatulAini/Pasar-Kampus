<?php

$session = session_include('/login');
$userId = (int) ($session['user_id'] ?? 0);
$purchaseId = (int) ($_GET['id'] ?? 0);

if ($purchaseId < 1) {
    flash('warning', 'Transaksi tidak ditemukan.');
    redirect('/riwayat/pembelian', 302);
}

$purchase = fetch(executeQuery(
    'SELECT * FROM v_purchase_history WHERE purchase_id = ?',
    [$purchaseId]
));

if (!is_array($purchase)) {
    flash('warning', 'Transaksi tidak ditemukan.');
    redirect('/riwayat/pembelian', 302);
}

$isSeller = (int) $purchase['seller_id'] === $userId;
$isBuyer = (int) $purchase['buyer_id'] === $userId;
if (!$isSeller && !$isBuyer) {
    redirect('/403', 403);
}

$status = (string) $purchase['status'];
$meetingSet = $purchase['meeting_lat'] !== null && $purchase['meeting_lng'] !== null;
$over = $status === 'done' || $status === 'cancelled';
$cancelAllowed = !$over && ($isSeller || ($isBuyer && !$meetingSet));
$canSetMeeting = $isSeller && $status === 'appointment';
$canInvoice = $isSeller && $status === 'pickup';

$transactionCode = 'PK-' . str_pad((string) $purchaseId, 6, '0', STR_PAD_LEFT);
$productUrl = route('/barang/detail?id=' . (int) $purchase['product_id']);
$invoiceUrl = route('/invoice?id=' . (int) $purchase['invoice_id']);
$selfUrl = route('/riwayat/detail?id=' . $purchaseId);

$datetime = static fn (string $value): string => format_tanggal($value) . ', ' . date('H:i', strtotime($value));

$statusMeta = [
    'appointment' => ['label' => 'Appointment', 'icon' => 'calendar-clock', 'badge' => 'bg-warning-subtle text-warning-emphasis'],
    'pickup' => ['label' => 'Pickup', 'icon' => 'package', 'badge' => 'bg-info-subtle text-info-emphasis'],
    'done' => ['label' => 'Selesai', 'icon' => 'circle-check-big', 'badge' => 'bg-success-subtle text-success-emphasis'],
    'cancelled' => ['label' => 'Dibatalkan', 'icon' => 'x-circle', 'badge' => 'bg-secondary-subtle text-secondary-emphasis'],
];
$meta = $statusMeta[$status];

$cancelStep = $meetingSet ? 2 : 1;
$currentStep = ['appointment' => 1, 'pickup' => 2, 'done' => 3, 'cancelled' => $cancelStep][$status];

$party = $isBuyer
    ? ['role' => 'Penjual', 'name' => (string) $purchase['seller_name'], 'location' => (string) $purchase['seller_location'], 'phone' => (string) $purchase['seller_phone']]
    : ['role' => 'Pembeli', 'name' => (string) $purchase['buyer_name'], 'location' => (string) $purchase['buyer_location'], 'phone' => (string) $purchase['buyer_phone']];

$wa = preg_replace('/\D/', '', $party['phone']);
$wa = str_starts_with($wa, '0') ? '62' . substr($wa, 1) : $wa;
$waText = rawurlencode('Halo ' . $party['name'] . ', saya terkait transaksi ' . $transactionCode . ' untuk ' . $purchase['product_title'] . ' di PasarKampus.');
?>

<?php Component::flush() ?>
<link rel="stylesheet" href="<?= asset('css/lib/leaflet.css') ?>">
<link rel="stylesheet" href="<?= asset('css/riwayat.css') ?>">
<?php $styles = Component::endFlush() ?>

<?php Component::include('user/header', [
    'title' => 'Detail Transaksi',
    'styles' => $styles,
]); ?>

<main class="container py-4 py-lg-5">
    <?php Component::include('common/banner', [
        'title' => 'Detail Transaksi',
        'subtitle' => 'Pantau status pesanan dan kelola tindakanmu sebagai ' . ($isBuyer ? 'pembeli' : 'penjual') . '.',
    ]) ?>

    <div class="row g-4">
        <div class="col-12 col-lg-7">
            <section class="card rounded-3 mb-4" aria-labelledby="overviewTitle">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold text-primary mb-3" id="overviewTitle">Ringkasan Pesanan</h2>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 pk-order-table">
                            <tbody>
                                <tr>
                                    <th scope="row">ID Transaksi</th>
                                    <td><?= esc($transactionCode) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Barang</th>
                                    <td><?= esc((string) $purchase['product_title']) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Jumlah</th>
                                    <td><?= esc(format_rupiah((int) $purchase['amount'])) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Tanggal Transaksi</th>
                                    <td><?= esc(format_tanggal((string) $purchase['created_at'])) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Dibuat</th>
                                    <td><?= esc($datetime((string) $purchase['created_at'])) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Status</th>
                                    <td>
                                        <span class="badge rounded-1 d-inline-flex align-items-center gap-1 <?= esc($meta['badge']) ?>">
                                            <i data-lucide="<?= esc($meta['icon']) ?>" class="pk-icon--xs" aria-hidden="true"></i><?= esc($meta['label']) ?>
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section class="card rounded-3 mb-4" aria-labelledby="progressTitle">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold text-primary mb-4" id="progressTitle">Progres Pesanan</h2>
                    <ol class="pk-stepper">
                        <?php foreach (['Created', 'Appointment', 'Pickup', 'Done'] as $index => $stepName): ?>
                            <?php
                            if ($status === 'cancelled') {
                                $stepState = $index < $cancelStep ? 'complete' : ($index === $cancelStep ? 'cancelled' : 'pending');
                            } elseif ($status === 'done') {
                                $stepState = 'complete';
                            } else {
                                $stepState = $index < $currentStep ? 'complete' : ($index === $currentStep ? 'current' : 'pending');
                            }
                            $stepPill = ['complete' => 'Complete', 'current' => 'In-progress', 'pending' => 'Pending', 'cancelled' => 'Cancelled'][$stepState];
                            ?>
                            <li class="pk-stepper__item is-<?= esc($stepState) ?>">
                                <span class="pk-stepper__dot"><?= $index + 1 ?></span>
                                <span class="pk-stepper__label"><?= esc($stepName) ?></span>
                                <span class="pk-stepper__pill is-<?= esc($stepState) ?>"><?= esc($stepPill) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ol>

                    <?php if ($status === 'cancelled'): ?>
                        <div class="pk-cancel-note mt-4">
                            <h3 class="h6 fw-bold text-danger-emphasis mb-2 d-inline-flex align-items-center gap-2">
                                <i data-lucide="circle-x" class="pk-icon--sm" aria-hidden="true"></i>Pembatalan Transaksi
                            </h3>
                            <p class="mb-1"><span class="fw-semibold">Alasan:</span> <?= esc((string) $purchase['cancel_reason']) ?></p>
                            <p class="small text-body-secondary mb-0">Dibatalkan oleh <?= $purchase['cancelled_by'] === 'buyer' ? 'Pembeli' : 'Penjual' ?> &middot; <?= esc($datetime((string) $purchase['cancelled_at'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="card rounded-3" aria-labelledby="mapTitle">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold text-primary mb-3" id="mapTitle">Titik Temu</h2>
                    <div id="meetingBody"
                        data-editable="<?= $canSetMeeting ? '1' : '0' ?>"
                        data-lat="<?= $meetingSet ? esc((string) $purchase['meeting_lat']) : '' ?>"
                        data-lng="<?= $meetingSet ? esc((string) $purchase['meeting_lng']) : '' ?>"
                        data-default-lat="-6.8915" data-default-lng="107.6107"
                        data-geocode="<?= esc(route('/riwayat/geocode')) ?>">
                        <?php if ($meetingSet): ?>
                            <div class="pk-map" id="meetMap" role="img" aria-label="Peta lokasi titik temu"></div>
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3">
                                <p class="small text-body-secondary mb-0" id="mapHint" aria-live="polite"></p>
                                <?php if ($isBuyer): ?>
                                    <a class="btn btn-outline-primary d-inline-flex align-items-center gap-2" href="https://www.google.com/maps?q=<?= esc((string) $purchase['meeting_lat']) ?>,<?= esc((string) $purchase['meeting_lng']) ?>" target="_blank" rel="noopener">
                                        <i data-lucide="map-pin" class="pk-icon" aria-hidden="true"></i><span>Buka di Google Maps</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php elseif ($canSetMeeting): ?>
                            <form id="meetingForm" method="post" action="<?= esc(route('/riwayat/post')) ?>" novalidate>
                                <input type="hidden" name="action" value="meeting">
                                <input type="hidden" name="purchase_id" value="<?= $purchaseId ?>">
                                <input type="hidden" name="redirect" value="<?= esc($selfUrl) ?>">
                                <input type="hidden" name="meeting_lat" id="meetingLat" value="">
                                <input type="hidden" name="meeting_lng" id="meetingLng" value="">
                                <p class="small text-body-secondary mb-2">Klik pada peta untuk menandai lokasi pertemuan, cari alamat, atau gunakan lokasi saat ini.</p>
                                <div class="d-flex gap-2 mb-3">
                                    <input class="form-control" type="search" id="mapSearch" placeholder="Cari alamat atau tempat..." aria-label="Cari alamat titik temu">
                                    <button type="button" class="btn btn-outline-primary flex-shrink-0" id="mapSearchBtn" aria-label="Cari alamat" title="Cari alamat"><i data-lucide="search" class="pk-icon" aria-hidden="true"></i></button>
                                    <button type="button" class="btn btn-outline-primary flex-shrink-0" id="mapLocateBtn" aria-label="Gunakan lokasi saya" title="Gunakan lokasi saya"><i data-lucide="locate-fixed" class="pk-icon" aria-hidden="true"></i></button>
                                </div>
                                <div class="pk-map" id="meetMap" role="img" aria-label="Peta lokasi titik temu"></div>
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3">
                                    <p class="small text-body-secondary mb-0" id="mapHint" aria-live="polite"></p>
                                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2" id="meetingSubmit" disabled>
                                        <i data-lucide="map-pin" class="pk-icon" aria-hidden="true"></i><span>Simpan</span>
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="pk-meet-pending">
                                <span class="pk-meet-pending__icon"><i data-lucide="map-pin" class="pk-icon--md" aria-hidden="true"></i></span>
                                <div>
                                    <p class="fw-bold mb-1"><?= $over ? 'Titik temu tidak ditetapkan' : 'Menunggu konfirmasi titik temu dari penjual' ?></p>
                                    <p class="small text-body-secondary mb-0"><?= $over ? 'Transaksi ini tidak memiliki titik temu.' : 'Penjual belum menetapkan lokasi pertemuan. Titik temu akan tampil di sini setelah dikonfirmasi.' ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-12 col-lg-5">
            <section class="card rounded-3 mb-4" aria-labelledby="partyTitle">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold text-primary mb-3" id="partyTitle">Pihak Terkait</h2>
                    <div class="pk-party">
                        <div class="d-flex align-items-center gap-3 min-w-0">
                            <img class="pk-party__avatar" src="<?= esc(asset('img/profile-placeholder.webp')) ?>" alt="" width="52" height="52">
                            <div class="min-w-0">
                                <p class="pk-party__name mb-1 text-truncate"><?= esc($party['name']) ?></p>
                                <p class="pk-party__loc mb-0 d-inline-flex align-items-center gap-1">
                                    <i data-lucide="map-pin" class="pk-icon--xs" aria-hidden="true"></i><span><?= esc($party['location']) ?></span>
                                </p>
                            </div>
                        </div>
                        <?php if ($over): ?>
                            <span class="badge rounded-pill flex-shrink-0 <?= $status === 'done' ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' ?>">
                                <?= $status === 'done' ? 'Transaksi selesai' : 'Transaksi dibatalkan' ?>
                            </span>
                        <?php else: ?>
                            <a class="btn btn-primary d-inline-flex align-items-center gap-2 flex-shrink-0" href="https://wa.me/<?= esc($wa) ?>?text=<?= esc($waText) ?>" target="_blank" rel="noopener">
                                <i data-lucide="message-circle" class="pk-icon" aria-hidden="true"></i><span>Kontak <?= esc($party['role']) ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <section class="card rounded-3" aria-labelledby="actionsTitle">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold text-primary mb-3" id="actionsTitle">Tindakan</h2>
                    <div class="d-grid gap-2">
                        <a class="btn btn-outline-primary w-100 d-inline-flex align-items-center justify-content-center gap-2" href="<?= esc($productUrl) ?>">
                            <i data-lucide="package" class="pk-icon" aria-hidden="true"></i><span>Lihat Barang</span>
                        </a>

                        <?php if ($status === 'done'): ?>
                            <a class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-2" href="<?= esc($invoiceUrl) ?>">
                                <i data-lucide="receipt-text" class="pk-icon" aria-hidden="true"></i><span>Lihat Invoice</span>
                            </a>
                        <?php elseif ($status === 'cancelled'): ?>
                            <button type="button" class="btn btn-secondary w-100 d-inline-flex align-items-center justify-content-center gap-2" disabled>
                                <i data-lucide="x-circle" class="pk-icon" aria-hidden="true"></i><span>Transaksi Dibatalkan</span>
                            </button>
                        <?php else: ?>
                            <?php if ($canInvoice): ?>
                                <button type="button" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-2" id="invoiceBtn" data-bs-toggle="modal" data-bs-target="#invoiceModal">
                                    <i data-lucide="receipt-text" class="pk-icon" aria-hidden="true"></i><span>Buat Invoice</span>
                                </button>
                            <?php endif; ?>
                            <button type="button" class="btn btn-warning w-100 d-inline-flex align-items-center justify-content-center gap-2" id="cancelBtn" data-bs-toggle="modal" data-bs-target="#cancelModal"<?= $cancelAllowed ? '' : ' disabled' ?>>
                                <i data-lucide="x-circle" class="pk-icon" aria-hidden="true"></i><span>Batalkan Transaksi</span>
                            </button>
                            <?php if ($isBuyer): ?>
                                <p class="small text-body-secondary mb-0"><?= $meetingSet
                                    ? 'Pembatalan tidak tersedia karena penjual sudah menetapkan titik temu.'
                                    : 'Pembatalan tersedia selama titik temu belum ditentukan penjual.' ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>

<?php if ($cancelAllowed): ?>
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header p-4">
                    <h2 class="modal-title h5 fw-bold text-primary" id="cancelModalTitle">Alasan Pembatalan</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form id="cancelForm" method="post" action="<?= esc(route('/riwayat/post')) ?>" novalidate>
                    <input type="hidden" name="action" value="cancel">
                    <input type="hidden" name="purchase_id" value="<?= $purchaseId ?>">
                    <input type="hidden" name="redirect" value="<?= esc($selfUrl) ?>">
                    <div class="modal-body p-4">
                        <p class="text-body-secondary">Tuliskan alasan pembatalan transaksi <strong><?= esc($transactionCode) ?></strong>. Alasan ini disimpan pada catatan transaksi dan tidak dapat diubah.</p>
                        <label class="form-label fw-semibold" for="cancelReason">Alasan</label>
                        <textarea class="form-control" id="cancelReason" name="reason" rows="3" required placeholder="Contoh: sudah menemukan penjual lain, atau berubah pikiran."></textarea>
                        <div class="invalid-feedback" id="cancelReasonError">Alasan pembatalan wajib diisi.</div>
                    </div>
                    <div class="modal-footer p-4">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Kirim Pembatalan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if ($canInvoice): ?>
    <div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header p-4">
                    <h2 class="modal-title h5 fw-bold text-primary" id="invoiceModalTitle">Buat Invoice</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form id="invoiceForm" method="post" action="<?= esc(route('/riwayat/post')) ?>" enctype="multipart/form-data" novalidate
                    data-amount="<?= (int) $purchase['amount'] ?>"
                    data-amount-label="<?= esc(format_rupiah((int) $purchase['amount'])) ?>"
                    data-code="<?= esc($transactionCode) ?>">
                    <input type="hidden" name="action" value="invoice">
                    <input type="hidden" name="purchase_id" value="<?= $purchaseId ?>">
                    <input type="hidden" name="redirect" value="<?= esc($selfUrl) ?>">
                    <div class="modal-body p-4">
                        <p class="text-body-secondary">Transaksi <strong><?= esc($transactionCode) ?></strong> &middot; <?= esc(format_rupiah((int) $purchase['amount'])) ?></p>
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-semibold" for="invoicePay">Bayar</label>
                                <div class="input-group"><span class="input-group-text">Rp</span><input class="form-control" type="number" id="invoicePay" name="pay" min="0" step="500" required></div>
                                <div class="invalid-feedback" id="invoicePayError"></div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-semibold" for="invoiceReturn">Kembali</label>
                                <div class="input-group"><span class="input-group-text">Rp</span><input class="form-control" type="number" id="invoiceReturn" name="return" min="0" step="500" value="0" required></div>
                                <div class="invalid-feedback" id="invoiceReturnError"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="invoiceMethod">Metode Pembayaran</label>
                                <select class="form-select" id="invoiceMethod" name="method" required>
                                    <option value="">Pilih metode</option>
                                    <option value="Tunai">Tunai</option>
                                    <option value="Transfer">Transfer</option>
                                </select>
                                <div class="invalid-feedback" id="invoiceMethodError"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="invoiceProof">Bukti Bayar</label>
                                <input class="form-control" type="file" id="invoiceProof" name="proof" accept="image/*" required>
                                <div class="form-text">Satu file gambar (JPG/PNG).</div>
                                <div class="invalid-feedback" id="invoiceProofError"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer p-4">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php Component::flush() ?>
<script src="<?= asset('js/lib/leaflet.min.js') ?>"></script>
<script src="<?= asset('js/riwayat-detail.js') ?>" defer></script>
<?php $script = Component::endFlush() ?>

<?php Component::include('user/footer', ['script' => $script]); ?>
