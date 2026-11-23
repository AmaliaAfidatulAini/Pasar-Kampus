<?php

$session = session_include('/login');
$userId = (int) ($session['user_id'] ?? 0);

// GET-driven filtering over the offers on the seller's listings.
$getString = static function (string $key): string {
    $value = $_GET[$key] ?? '';
    return is_string($value) ? trim($value) : '';
};

$q = $getString('q');
$statusInput = $getString('status');
$page = max(1, (int) ($_GET['page'] ?? 1));

$statusOptions = ['baru' => 'Tawaran baru', 'menunggu' => 'Menunggu pembeli', 'ditutup' => 'Ditutup'];
$status = array_key_exists($statusInput, $statusOptions) ? $statusInput : '';

/** A `YYYY-MM-DD` GET value, or null when absent/malformed. */
$dateInput = static function (string $key): ?string {
    $value = $_GET[$key] ?? '';
    if (!is_string($value) || preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($value)) !== 1) {
        return null;
    }
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', trim($value));

    return $date instanceof DateTimeImmutable && $date->format('Y-m-d') === trim($value)
        ? $date->format('Y-m-d')
        : null;
};

$start = $dateInput('start');
$end = $dateInput('end');
if ($start !== null && $end !== null && $start > $end) {
    [$start, $end] = [$end, $start];
}

$perPage = 5;
$statusFilter = $status !== '' ? $status : null;

$totalRow = executeQuery(
    'SELECT count_offers(?, ?, ?, ?, ?, ?) AS total',
    [$userId, 'seller', $q, $statusFilter, $start, $end],
    'fetch'
);
$total = is_array($totalRow) ? (int) $totalRow['total'] : 0;
$totalPages = max(1, (int) ceil($total / $perPage));

$queryBase = [];
if ($q !== '') {
    $queryBase['q'] = $q;
}
if ($status !== '') {
    $queryBase['status'] = $status;
}
if ($start !== null) {
    $queryBase['start'] = $start;
}
if ($end !== null) {
    $queryBase['end'] = $end;
}

if ($page > $totalPages) {
    $redirectParams = $queryBase;
    if ($totalPages > 1) {
        $redirectParams['page'] = $totalPages;
    }
    redirect('/tawaran/penjualan' . ($redirectParams !== [] ? '?' . http_build_query($redirectParams) : ''), 302);
}

$items = fetchAll(executeQuery(
    'SELECT * FROM offer_rows(?, ?, ?, ?, ?, ?, ?, ?)',
    [$userId, 'seller', $q, $statusFilter, $start, $end, $perPage, ($page - 1) * $perPage]
));

$tawaranUrl = static function (array $overrides = []) use ($queryBase): string {
    $params = array_merge($queryBase, $overrides);
    $params = array_filter($params, static fn ($value): bool => $value !== null && $value !== '');
    $query = http_build_query($params);

    return route('/tawaran/penjualan' . ($query !== '' ? '?' . $query : ''));
};

$startItem = $total > 0 ? (($page - 1) * $perPage) + 1 : 0;
$endItem = min($page * $perPage, $total);

if ($totalPages <= 7) {
    $pageNumbers = range(1, $totalPages);
} else {
    $pageNumbers = [1];
    $windowStart = max(2, $page - 1);
    $windowEnd = min($totalPages - 1, $page + 1);
    if ($windowStart > 2) {
        $pageNumbers[] = null;
    }
    for ($p = $windowStart; $p <= $windowEnd; $p++) {
        $pageNumbers[] = $p;
    }
    if ($windowEnd < $totalPages - 1) {
        $pageNumbers[] = null;
    }
    $pageNumbers[] = $totalPages;
}

$cardClass = [
    'baru' => 'card rounded-3 border-warning bg-warning bg-opacity-10',
    'menunggu' => 'card rounded-3 bg-transparent',
    'ditutup' => 'card rounded-3 border-0 bg-body-tertiary',
];
$badge = [
    'baru' => ['class' => 'border border-warning bg-warning-subtle text-warning-emphasis', 'icon' => 'inbox', 'label' => 'Tawaran baru'],
    'menunggu' => ['class' => 'bg-primary-subtle text-primary-emphasis', 'icon' => 'clock-3', 'label' => 'Menunggu pembeli'],
    'ditutup' => ['class' => 'bg-secondary-subtle text-secondary-emphasis', 'icon' => 'lock', 'label' => 'Ditutup'],
];
?>

<?php Component::flush() ?>
<link rel="stylesheet" href="<?= asset('css/katalog.css') ?>">
<?php $styles = Component::endFlush() ?>

<?php Component::include('user/header', [
    'title' => 'Tawaran Masuk',
    'styles' => $styles,
]); ?>

<main class="container py-4 py-lg-5" id="tawaranPage">

    <?php Component::include('common/banner', [
        'title' => 'Tawaran Masuk',
        'subtitle' => 'Semua tawaran pembeli untuk barang yang kamu jual, lengkap dengan statusnya.',
    ]) ?>

    <div class="pk-catalog-layout" id="catalogLayout">
        <aside class="pk-catalog-sidebar">
            <div class="offcanvas-lg offcanvas-start" tabindex="-1" id="catalogSidebar" aria-labelledby="tawaranSidebarLabel">
                <div class="offcanvas-header">
                    <h2 class="offcanvas-title h5 mb-0" id="tawaranSidebarLabel">Filter Tawaran</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#catalogSidebar" aria-label="Tutup filter"></button>
                </div>
                <div class="offcanvas-body">
                    <form id="catalogFilterForm" method="GET" action="<?= esc(route('/tawaran/penjualan')) ?>" class="d-flex flex-column gap-4">
                        <div>
                            <label class="form-label small fw-semibold" for="tawaranStatus">Status</label>
                            <select class="form-select" id="tawaranStatus" name="status">
                                <option value="">Semua</option>
                                <?php foreach ($statusOptions as $value => $label): ?>
                                    <option value="<?= esc($value) ?>" <?= $status === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="form-label small fw-semibold" for="tawaranQuery">Cari barang</label>
                            <div class="pk-field-search">
                                <i data-lucide="search" class="pk-icon" aria-hidden="true"></i>
                                <input class="form-control" type="search" id="tawaranQuery" name="q" value="<?= esc($q) ?>" placeholder="Nama barang...">
                            </div>
                        </div>

                        <div>
                            <label class="form-label small fw-semibold" for="tawaranStart">Tanggal Mulai</label>
                            <input class="form-control" type="date" id="tawaranStart" name="start" value="<?= esc((string) $start) ?>">
                        </div>
                        
                        <div>
                            <label class="form-label small fw-semibold" for="tawaranEnd">Tanggal Selesai</label>
                            <input class="form-control" type="date" id="tawaranEnd" name="end" value="<?= esc((string) $end) ?>">
                        </div>

                        <div class="d-flex gap-2">
                            <a class="btn btn-outline-secondary flex-fill" href="<?= esc(route('/tawaran/penjualan')) ?>">Reset</a>
                            <button type="submit" class="btn btn-primary flex-fill">Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </aside>

        <div class="pk-catalog-main">
            <?php if ($queryBase !== []): ?>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <?php if ($q !== ''): ?>
                        <a class="btn btn-sm btn-outline-primary rounded-pill" href="<?= esc($tawaranUrl(['q' => null])) ?>" title="Hapus kata kunci">
                            Kata kunci: <?= esc($q) ?> <span aria-hidden="true">&times;</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($status !== ''): ?>
                        <a class="btn btn-sm btn-outline-primary rounded-pill" href="<?= esc($tawaranUrl(['status' => null])) ?>" title="Hapus status">
                            <?= esc($statusOptions[$status]) ?> <span aria-hidden="true">&times;</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($start !== null || $end !== null): ?>
                        <a class="btn btn-sm btn-outline-primary rounded-pill" href="<?= esc($tawaranUrl(['start' => null, 'end' => null])) ?>" title="Hapus rentang tanggal">
                            <?= esc(($start ?? 'Awal') . ' – ' . ($end ?? 'Sekarang')) ?> <span aria-hidden="true">&times;</span>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="row align-items-center g-2 mb-3">
                <div class="col">
                    <p class="text-body-secondary small mb-0">
                        <?php if ($total > 0): ?>
                            Menampilkan <?= (int) $startItem ?>&ndash;<?= (int) $endItem ?> dari <?= (int) $total ?> tawaran
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn d-inline-flex align-items-center gap-2 px-3" id="catalogFilterToggle" aria-controls="catalogSidebar" aria-expanded="true">
                        <i data-lucide="sliders-horizontal" class="pk-icon" aria-hidden="true"></i>
                        <span>Filter</span>
                    </button>
                </div>
            </div>

            <?php if ($items !== []): ?>
                <div class="d-grid gap-3" id="tawaranCardList">
                    <?php foreach ($items as $item): ?>
                        <?php $state = (string) $item['state']; ?>
                        <article class="<?= esc($cardClass[$state]) ?>" data-offer-card
                            data-offer-id="<?= (int) $item['offer_id'] ?>"
                            data-product="<?= esc((string) $item['product_title']) ?>"
                            data-price="<?= (int) $item['asking_price'] ?>"
                            data-offer="<?= (int) $item['offer_price'] ?>"
                            data-buyer="<?= esc((string) $item['buyer_name']) ?>">
                            <div class="card-body d-flex flex-wrap flex-sm-nowrap align-items-center gap-3 p-3">
                                <img src="<?= esc((string) $item['thumbnail']) ?>"
                                    alt="<?= esc((string) $item['product_title']) ?>" width="64" height="64" loading="lazy"
                                    class="rounded-2 object-fit-cover flex-shrink-0 <?= $state === 'ditutup' ? 'opacity-75' : 'bg-body-tertiary' ?>">
                                <div class="flex-grow-1 min-w-0">
                                    <h2 class="h6 fw-bold mb-1 <?= $state === 'ditutup' ? 'text-body-secondary' : 'text-body-emphasis' ?>"><?= esc((string) $item['product_title']) ?></h2>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="small text-body-secondary"><?= esc((string) $item['buyer_name']) ?> &middot; <?= esc(format_tanggal((string) $item['created_at'])) ?></span>
                                        <span class="badge rounded-1 <?= esc($badge[$state]['class']) ?> d-inline-flex align-items-center gap-1" data-role="badge">
                                            <i data-lucide="<?= esc($badge[$state]['icon']) ?>" class="pk-icon--xs" aria-hidden="true"></i><?= esc($badge[$state]['label']) ?>
                                        </span>
                                    </div>
                                </div>
                                <?php if ($state === 'baru'): ?>
                                    <div class="d-flex align-items-center gap-3 flex-shrink-0 ms-auto" data-role="action">
                                        <span class="small fw-semibold text-warning-emphasis d-inline-flex align-items-center gap-1">
                                            <i data-lucide="triangle-alert" class="pk-icon" aria-hidden="true"></i>Perlu Tindakan
                                        </span>
                                        <button type="button" class="btn btn-sm btn-warning flex-shrink-0"
                                            data-review data-bs-toggle="modal" data-bs-target="#reviewModal"
                                            aria-label="Tinjau tawaran <?= esc((string) $item['product_title']) ?> dari <?= esc((string) $item['buyer_name']) ?>">Tinjau</button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5 px-3 border rounded-3 pk-border-dashed">
                    <i data-lucide="handshake" class="pk-icon--xl text-body-tertiary d-block mx-auto mb-3" aria-hidden="true"></i>
                    <h2 class="h5 fw-bold text-primary mb-2">Belum ada tawaran masuk</h2>
                    <p class="text-body-secondary mb-3">Tawaran dari pembeli untuk barang yang kamu jual akan muncul di sini beserta statusnya.</p>
                    <a class="btn btn-primary d-inline-flex align-items-center gap-2" href="<?= esc(route('/barang')) ?>">Buka barang saya</a>
                </div>
            <?php endif; ?>

            <?php if ($totalPages > 1): ?>
                <nav aria-label="Navigasi halaman tawaran" class="mt-4">
                    <ul class="pagination justify-content-end mb-0">
                        <li class="page-item<?= $page > 1 ? '' : ' disabled' ?>">
                            <a class="page-link" href="<?= esc($tawaranUrl(['page' => $page - 1])) ?>" aria-label="Halaman sebelumnya">&lt;</a>
                        </li>
                        <?php foreach ($pageNumbers as $pageNumber): ?>
                            <?php if ($pageNumber === null): ?>
                                <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                            <?php elseif ($pageNumber === $page): ?>
                                <li class="page-item active" aria-current="page"><span class="page-link"><?= (int) $pageNumber ?></span></li>
                            <?php else: ?>
                                <li class="page-item"><a class="page-link" href="<?= esc($tawaranUrl(['page' => $pageNumber])) ?>"><?= (int) $pageNumber ?></a></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <li class="page-item<?= $page < $totalPages ? '' : ' disabled' ?>">
                            <a class="page-link" href="<?= esc($tawaranUrl(['page' => $page + 1])) ?>" aria-label="Halaman berikutnya">&gt;</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

</main>

<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header p-4">
                <h2 class="modal-title h5 fw-bold text-primary" id="reviewModalTitle">Tinjau Tawaran</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="reviewForm" method="post" action="<?= esc(route('/tawaran/post')) ?>">
                <input type="hidden" name="action" value="terima">
                <input type="hidden" name="offer_id" id="reviewOfferId" value="">
                <input type="hidden" name="redirect" value="<?= esc(current_request_path()) ?>">
                <div class="modal-body p-4">
                    <div class="pk-modal__summary">
                        <span class="d-flex flex-column">
                            <span data-field="product">-</span>
                            <span class="small fw-normal text-body-secondary">dari <span data-field="buyer">-</span></span>
                        </span>
                        <span class="pk-modal__summary-price pk-modal__summary-price--offer" data-field="offer">-</span>
                    </div>

                    <div class="pk-compare">
                        <div class="pk-compare__row">
                            <span>Harga jual</span>
                            <strong data-field="price">-</strong>
                        </div>
                        <div class="pk-compare__row pk-compare__row--offer">
                            <span>Tawaran pembeli</span>
                            <strong data-field="offer">-</strong>
                        </div>
                        <div class="pk-compare__row pk-compare__row--total">
                            <span>Selisih</span>
                            <strong data-field="gap">-</strong>
                        </div>
                    </div>

                    <div class="pk-gap-note">
                        <span class="pk-gap-note__value" data-field="pct">-</span>
                        <span class="pk-gap-note__label">di bawah harga jual</span>
                    </div>
                </div>
                <div class="modal-footer p-4">
                    <button type="button" class="btn btn-outline-secondary" id="bandingOpen">Ajukan Banding</button>
                    <button type="submit" class="btn pk-btn-accept"
                        data-confirm="Apakah anda yakin untuk menerima tawaran ini?"
                        data-confirm-title="Terima Tawaran"
                        data-confirm-ok="Ya, terima">Terima</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="bandingModal" tabindex="-1" aria-labelledby="bandingModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header p-4">
                <h2 class="modal-title h5 fw-bold text-primary" id="bandingModalTitle">Ajukan Banding</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="bandingForm" method="post" action="<?= esc(route('/tawaran/post')) ?>" novalidate>
                <input type="hidden" name="action" value="banding">
                <input type="hidden" name="offer_id" id="bandingOfferId" value="">
                <input type="hidden" name="redirect" value="<?= esc(current_request_path()) ?>">
                <div class="modal-body p-4">
                    <p class="fw-semibold mb-2" data-field="product">-</p>

                    <div class="pk-compare mb-3">
                        <div class="pk-compare__row">
                            <span>Harga jual</span>
                            <strong data-field="price">-</strong>
                        </div>
                        <div class="pk-compare__row pk-compare__row--offer">
                            <span>Tawaran pembeli</span>
                            <strong data-field="offer">-</strong>
                        </div>
                    </div>
                    <label class="form-label fw-semibold" for="bandingInput">Harga banding</label>
                    <div class="input-group pk-amount">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="bandingInput" name="price" inputmode="numeric" step="500" required>
                    </div>
                    <div class="pk-readout" id="bandingReadout" aria-live="polite"></div>
                    <p class="text-secondary mb-2 mt-3">Catatan:</p>
                    <ul class="text-secondary mb-0">
                        <li id="bandingBounds"></li>
                        <li>Pembeli bisa menerima bandingmu atau membiarkannya.</li>
                    </ul>
                    <div class="invalid-feedback d-block pk-offer-error" id="bandingError" hidden></div>
                </div>
                <div class="modal-footer p-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"
                        data-confirm="Apakah anda yakin untuk mengirim banding ini?"
                        data-confirm-title="Kirim Banding"
                        data-confirm-ok="Ya, kirim">Kirim Banding</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php Component::flush() ?>
<script src="<?= asset('js/katalog.js') ?>" defer></script>
<script src="<?= asset('js/tawaran.js') ?>" defer></script>
<?php $script = Component::endFlush() ?>

<?php Component::include('user/footer', compact('script')); ?>
