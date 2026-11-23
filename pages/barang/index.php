<?php

$session = session_include('/login');
$userId = (int) ($session['user_id'] ?? 0);

// GET-driven filtering over the seller's own listings: same shape as the catalog page.
$getString = static function (string $key): string {
    $value = $_GET[$key] ?? '';
    return is_string($value) ? trim($value) : '';
};

$q = $getString('q');
$categoryInput = $_GET['category'] ?? [];
$categoryInput = is_array($categoryInput) ? $categoryInput : [$categoryInput];
$statusInput = $getString('status');
$page = max(1, (int) ($_GET['page'] ?? 1));

$categories = executeQuery('SELECT name FROM v_categories', [], 'fetchAll');
$categoryOptions = array_map(static fn (array $category): string => (string) $category['name'], $categories);
$statusOptions = ['Dijual', 'Pemesanan', 'Pengambilan', 'Terjual'];

$categoryFilter = array_values(array_unique(array_filter(
    array_map(static fn ($value): string => is_string($value) ? trim($value) : '', $categoryInput),
    static fn (string $value): bool => in_array($value, $categoryOptions, true)
)));

if (!in_array($statusInput, $statusOptions, true)) {
    $statusInput = '';
}

$perPage = 10;
$categoryJson = json_encode(array_values($categoryFilter), JSON_UNESCAPED_UNICODE);
$statusFilter = $statusInput !== '' ? $statusInput : null;

$totalRow = executeQuery(
    'SELECT count_owner_products(?, ?, CAST(? AS jsonb), ?) AS total',
    [$userId, $q, $categoryJson, $statusFilter],
    'fetch'
);
$total = is_array($totalRow) ? (int) $totalRow['total'] : 0;
$totalPages = max(1, (int) ceil($total / $perPage));

$queryBase = [];
if ($q !== '') {
    $queryBase['q'] = $q;
}
if ($categoryFilter !== []) {
    $queryBase['category'] = $categoryFilter;
}
if ($statusInput !== '') {
    $queryBase['status'] = $statusInput;
}

if ($page > $totalPages) {
    $redirectParams = $queryBase;
    if ($totalPages > 1) {
        $redirectParams['page'] = $totalPages;
    }
    redirect('/barang' . ($redirectParams !== [] ? '?' . http_build_query($redirectParams) : ''), 302);
}

$items = fetchAll(executeQuery(
    'SELECT * FROM owner_products(?, ?, CAST(? AS jsonb), ?, ?, ?)',
    [$userId, $q, $categoryJson, $statusFilter, $perPage, ($page - 1) * $perPage]
));

$barangUrl = static function (array $overrides = []) use ($queryBase): string {
    $params = array_merge($queryBase, $overrides);
    $params = array_filter($params, static fn ($value): bool => $value !== null && $value !== '');
    $query = http_build_query($params);

    return route('/barang' . ($query !== '' ? '?' . $query : ''));
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

$statsRow = executeQuery('SELECT * FROM v_owner_product_stats WHERE user_id = ?', [$userId], 'fetch');
$stats = is_array($statsRow)
    ? $statsRow
    : ['barang' => 0, 'dijual' => 0, 'pesanan' => 0, 'terjual' => 0];

// Seller-level activity counters for the strip: open offers of any stage, and
// purchases that are neither finished nor cancelled.
$activeOfferRow = fetch(executeQuery(
    "SELECT COUNT(*) AS total FROM v_offer_inbox WHERE seller_id = ? AND status = 'OPEN'",
    [$userId]
));
$activeOfferCount = (int) ($activeOfferRow['total'] ?? 0);

$activeOrderRow = fetch(executeQuery(
    "SELECT COUNT(*) AS total FROM v_purchase_history WHERE seller_id = ? AND status IN ('appointment', 'pickup')",
    [$userId]
));
$activeOrderCount = (int) ($activeOrderRow['total'] ?? 0);

$hasActiveActivity = $activeOfferCount > 0 || $activeOrderCount > 0;
$offerBannerClass = $hasActiveActivity
    ? 'border-2 border-primary bg-primary-subtle'
    : 'border bg-body-tertiary';
$offerButtonClass = $activeOfferCount > 0 ? 'btn-primary' : 'btn-outline-primary';
$orderButtonClass = $activeOrderCount > 0 ? 'btn-primary' : 'btn-outline-primary';
?>

<?php Component::flush() ?>
<link rel="stylesheet" href="<?= asset('css/katalog.css') ?>">
<?php $styles = Component::endFlush() ?>

<?php Component::include('user/header', [
    'title' => 'Barang Saya',
    'styles' => $styles,
]); ?>

<main class="container py-4 py-lg-5" id="barangPage">

    <?php Component::flush() ?>
    <a class="btn btn-primary d-inline-flex align-items-center gap-2 px-3" href="<?= esc(route('/barang/tambah')) ?>">
        <i data-lucide="plus" class="pk-icon" aria-hidden="true"></i>
        <span>Tambah</span>
    </a>
    <?php $extra = Component::endFlush() ?>

    <?php Component::include("common/banner", [
        "title" => 'Barang Saya',
        "subtitle" => "Kelola barang yang kamu jual di PasarKampus",
        "extra" => $extra
    ]) ?>

    <div class="row row-cols-2 row-cols-lg-4 g-3 mb-4" id="barangStats">
        <div class="col">
            <div class="card h-100 rounded-2" data-kpi="barang">
                <div class="card-body d-flex flex-column justify-content-between gap-1">
                    <span class="text-uppercase small fw-bold text-body-secondary">Barang Saya</span>
                    <strong class="fs-3 fw-bold lh-1 text-body-emphasis"><?= esc((string) $stats['barang']) ?></strong>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 rounded-2" data-kpi="dijual">
                <div class="card-body d-flex flex-column justify-content-between gap-1">
                    <span class="text-uppercase small fw-bold text-body-secondary">Sedang Dijual</span>
                    <strong class="fs-3 fw-bold lh-1 text-body-emphasis"><?= esc((string) $stats['dijual']) ?></strong>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 rounded-2" data-kpi="pesanan">
                <div class="card-body d-flex flex-column justify-content-between gap-1">
                    <span class="text-uppercase small fw-bold text-body-secondary">Pesanan</span>
                    <strong class="fs-3 fw-bold lh-1 text-body-emphasis"><?= esc((string) $stats['pesanan']) ?></strong>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 rounded-2" data-kpi="terjual">
                <div class="card-body d-flex flex-column justify-content-between gap-1">
                    <span class="text-uppercase small fw-bold text-body-secondary">Terjual</span>
                    <strong class="fs-3 fw-bold lh-1 text-body-emphasis"><?= esc((string) $stats['terjual']) ?></strong>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-12 g-3 px-2 mb-4">
        <div class="col card h-100 rounded-2 <?= esc($offerBannerClass) ?>">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <?php if ($hasActiveActivity): ?>
                        <i data-lucide="triangle-exclamation-point" class="pk-icon--md" aria-hidden="true"></i>
                        <p class="mb-0">Kamu memiliki pesanan/tawaran aktif dari pembeli.</p>
                    <?php else: ?>
                        <i data-lucide="history" class="pk-icon--md" aria-hidden="true"></i>
                        <p class="mb-0">Lihat riwayat tawaran dari pembeli.</p>
                    <?php endif; ?>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn <?= esc($offerButtonClass) ?> position-relative d-inline-flex align-items-center gap-2 px-3" href="<?= esc(route('/tawaran/penjualan')) ?>">
                        <?php if ($activeOfferCount > 0): ?>
                            <span class="pk-count-dot badge rounded-pill text-bg-danger position-absolute top-0 start-100 translate-middle"><span class="pk-count-dot__value"><?= (int) $activeOfferCount ?></span><span class="visually-hidden"> tawaran aktif</span></span>
                        <?php endif; ?>
                        <i data-lucide="inbox" class="pk-icon" aria-hidden="true"></i>
                        <span>Lihat Tawaran</span>
                    </a>
                    <a class="btn <?= esc($orderButtonClass) ?> position-relative d-inline-flex align-items-center gap-2 px-3" href="<?= esc(route('/riwayat/penjualan')) ?>">
                        <?php if ($activeOrderCount > 0): ?>
                            <span class="pk-count-dot badge rounded-pill text-bg-danger position-absolute top-0 start-100 translate-middle"><span class="pk-count-dot__value"><?= (int) $activeOrderCount ?></span><span class="visually-hidden"> pesanan aktif</span></span>
                        <?php endif; ?>
                        <i data-lucide="shopping-bag" class="pk-icon" aria-hidden="true"></i>
                        <span>Daftar Pesanan</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="pk-catalog-layout" id="catalogLayout">
        <aside class="pk-catalog-sidebar">
            <div class="offcanvas-lg offcanvas-start" tabindex="-1" id="catalogSidebar" aria-labelledby="barangSidebarLabel">
                <div class="offcanvas-header">
                    <h2 class="offcanvas-title h5 mb-0" id="barangSidebarLabel">Filter Barang</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#catalogSidebar" aria-label="Tutup filter"></button>
                </div>
                <div class="offcanvas-body">
                    <form id="catalogFilterForm" method="GET" action="<?= esc(route('/barang')) ?>" class="d-flex flex-column gap-4">
                        <div>
                            <label class="form-label small fw-semibold" for="barangStatus">Status</label>
                            <select class="form-select" id="barangStatus" name="status">
                                <option value="">Semua</option>
                                <?php foreach ($statusOptions as $option): ?>
                                    <option value="<?= esc($option) ?>" <?= $statusInput === $option ? 'selected' : '' ?>><?= esc($option) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    
                        <div>
                            <label class="form-label small fw-semibold" for="barangQuery">Cari barang</label>
                            <div class="pk-field-search">
                                <i data-lucide="search" class="pk-icon" aria-hidden="true"></i>
                                <input class="form-control" type="search" id="barangQuery" name="q" value="<?= esc($q) ?>" placeholder="Nama barang...">
                            </div>
                        </div>

                        <div>
                            <h3 class="h6 text-uppercase fw-bold mb-2">Kategori</h3>
                            <div class="d-grid gap-2">
                                <?php foreach ($categoryOptions as $option): ?>
                                    <label class="form-check d-flex align-items-center gap-2 mb-0 ps-0">
                                        <input class="form-check-input m-0" type="checkbox" name="category[]" value="<?= esc($option) ?>" id="barangCategory-<?= esc(strtolower(str_replace(' ', '-', $option))) ?>" <?= in_array($option, $categoryFilter, true) ? 'checked' : '' ?>>
                                        <span class="form-check-label small"><?= esc($option) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a class="btn btn-outline-secondary flex-fill" href="<?= esc(route('/barang')) ?>">Reset</a>
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
                        <a class="btn btn-sm btn-outline-primary rounded-pill" href="<?= esc($barangUrl(['q' => null])) ?>" title="Hapus kata kunci">
                            Kata kunci: <?= esc($q) ?> <span aria-hidden="true">&times;</span>
                        </a>
                    <?php endif; ?>
                    <?php foreach ($categoryFilter as $activeCategory): ?>
                        <a class="btn btn-sm btn-outline-primary rounded-pill" href="<?= esc($barangUrl(['category' => array_values(array_diff($categoryFilter, [$activeCategory]))])) ?>" title="Hapus kategori">
                            <?= esc($activeCategory) ?> <span aria-hidden="true">&times;</span>
                        </a>
                    <?php endforeach; ?>
                    <?php if ($statusInput !== ''): ?>
                        <a class="btn btn-sm btn-outline-primary rounded-pill" href="<?= esc($barangUrl(['status' => null])) ?>" title="Hapus status">
                            <?= esc($statusInput) ?> <span aria-hidden="true">&times;</span>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="row align-items-center g-2 mb-3">
                <div class="col">
                    <p class="text-body-secondary small mb-0">
                        <?php if ($total > 0): ?>
                            Menampilkan <?= (int) $startItem ?>&ndash;<?= (int) $endItem ?> dari <?= (int) $total ?> barang
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn d-inline-flex align-items-center gap-2 px-3 text-xs" id="catalogFilterToggle" aria-controls="catalogSidebar" aria-expanded="true">
                        <i data-lucide="sliders-horizontal" class="pk-icon" aria-hidden="true"></i>
                        <span>Filter</span>
                    </button>
                </div>
            </div>

            <?php if ($items !== []): ?>
                <div class="d-grid gap-3" id="barangCardList">
                    <?php foreach ($items as $item): ?>
                        <?php Component::include('common/card-long', $item) ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5 px-3 border rounded-3 pk-border-dashed mb-4">
                    <h2 class="h5 fw-bold text-primary mb-2">Tidak ada barang yang cocok</h2>
                    <p class="text-body-secondary mb-3">Coba ganti kata kunci atau sesuaikan filter pencarian.</p>
                </div>
            <?php endif; ?>

            <?php if ($totalPages > 1): ?>
                <nav aria-label="Navigasi halaman barang" class="mt-4">
                    <ul class="pagination justify-content-end mb-0">
                        <li class="page-item<?= $page > 1 ? '' : ' disabled' ?>">
                            <a class="page-link" href="<?= esc($barangUrl(['page' => $page - 1])) ?>" aria-label="Halaman sebelumnya">&lt;</a>
                        </li>
                        <?php foreach ($pageNumbers as $pageNumber): ?>
                            <?php if ($pageNumber === null): ?>
                                <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                            <?php elseif ($pageNumber === $page): ?>
                                <li class="page-item active" aria-current="page"><span class="page-link"><?= (int) $pageNumber ?></span></li>
                            <?php else: ?>
                                <li class="page-item"><a class="page-link" href="<?= esc($barangUrl(['page' => $pageNumber])) ?>"><?= (int) $pageNumber ?></a></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <li class="page-item<?= $page < $totalPages ? '' : ' disabled' ?>">
                            <a class="page-link" href="<?= esc($barangUrl(['page' => $page + 1])) ?>" aria-label="Halaman berikutnya">&gt;</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

</main>

<?php Component::flush() ?>
<script src="<?= asset('js/katalog.js') ?>" defer></script>
<script src="<?= asset('js/barang.js') ?>" defer></script>
<?php $script = Component::endFlush() ?>

<?php Component::include('user/footer', compact('script')); ?>
