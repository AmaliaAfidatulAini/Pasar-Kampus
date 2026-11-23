<?php

$session = session_include('/login');
$userId = (int) ($session['user_id'] ?? 0);

// GET-driven filtering over the buyer's purchases.
$getString = static function (string $key): string {
    $value = $_GET[$key] ?? '';
    return is_string($value) ? trim($value) : '';
};

$q = $getString('q');

$statusOptions = [
    'appointment' => 'Appointment',
    'pickup' => 'Pickup',
    'done' => 'Selesai',
    'cancelled' => 'Dibatalkan',
];
$statusInput = $getString('status');
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
$page = max(1, (int) ($_GET['page'] ?? 1));

$where = 'buyer_id = ?';
$params = [$userId];
if ($status !== '') {
    $where .= ' AND status = ?';
    $params[] = $status;
}
if ($q !== '') {
    $where .= ' AND product_title ILIKE ?';
    $params[] = '%' . $q . '%';
}
if ($start !== null) {
    $where .= ' AND created_at >= ?';
    $params[] = $start;
}
if ($end !== null) {
    $where .= ' AND created_at < ?';
    $params[] = date('Y-m-d', strtotime($end . ' +1 day'));
}

$totalRow = fetch(executeQuery('SELECT COUNT(*) AS total FROM v_purchase_history WHERE ' . $where, $params));
$total = (int) ($totalRow['total'] ?? 0);
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
    redirect('/riwayat/pembelian' . ($redirectParams !== [] ? '?' . http_build_query($redirectParams) : ''), 302);
}

$items = fetchAll(executeQuery(
    'SELECT * FROM v_purchase_history WHERE ' . $where
    . ' ORDER BY created_at DESC, purchase_id DESC LIMIT CAST(? AS integer) OFFSET CAST(? AS integer)',
    array_merge($params, [$perPage, ($page - 1) * $perPage])
));

$riwayatUrl = static function (array $overrides = []) use ($queryBase): string {
    $params = array_merge($queryBase, $overrides);
    $params = array_filter($params, static fn ($value): bool => $value !== null && $value !== '');
    $query = http_build_query($params);

    return route('/riwayat/pembelian' . ($query !== '' ? '?' . $query : ''));
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

$badgeMap = [
    'appointment' => ['class' => 'bg-warning-subtle text-warning-emphasis', 'icon' => 'calendar-clock', 'label' => 'Appointment'],
    'pickup' => ['class' => 'bg-info-subtle text-info-emphasis', 'icon' => 'package', 'label' => 'Pickup'],
    'done' => ['class' => 'bg-success-subtle text-success-emphasis', 'icon' => 'circle-check-big', 'label' => 'Selesai'],
    'cancelled' => ['class' => 'bg-secondary-subtle text-secondary-emphasis', 'icon' => 'x-circle', 'label' => 'Dibatalkan'],
];
?>

<?php Component::flush() ?>
<link rel="stylesheet" href="<?= asset('css/katalog.css') ?>">
<?php $styles = Component::endFlush() ?>

<?php Component::include('user/header', [
    'title' => 'Riwayat Pembelian',
    'styles' => $styles,
]); ?>

<main class="container py-4 py-lg-5">

    <?php Component::include('common/banner', [
        'title' => 'Riwayat Pembelian',
        'subtitle' => 'Semua transaksi pembelianmu dari penjual, lengkap dengan statusnya.',
    ]) ?>

    <div class="pk-catalog-layout" id="catalogLayout">
        <aside class="pk-catalog-sidebar">
            <div class="offcanvas-lg offcanvas-start" tabindex="-1" id="catalogSidebar" aria-labelledby="riwayatSidebarLabel">
                <div class="offcanvas-header">
                    <h2 class="offcanvas-title h5 mb-0" id="riwayatSidebarLabel">Filter Transaksi</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#catalogSidebar" aria-label="Tutup filter"></button>
                </div>
                <div class="offcanvas-body">
                    <form id="catalogFilterForm" method="GET" action="<?= esc(route('/riwayat/pembelian')) ?>" class="d-flex flex-column gap-4">
                        <div>
                            <label class="form-label small fw-semibold" for="riwayatStatus">Status</label>
                            <select class="form-select" id="riwayatStatus" name="status">
                                <option value="">Semua</option>
                                <?php foreach ($statusOptions as $value => $label): ?>
                                    <option value="<?= esc($value) ?>" <?= $status === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="form-label small fw-semibold" for="riwayatQuery">Cari barang</label>
                            <div class="pk-field-search">
                                <i data-lucide="search" class="pk-icon" aria-hidden="true"></i>
                                <input class="form-control" type="search" id="riwayatQuery" name="q" value="<?= esc($q) ?>" placeholder="Nama barang...">
                            </div>
                        </div>

                        <div>
                            <label class="form-label small fw-semibold" for="riwayatStart">Tanggal Mulai</label>
                            <input class="form-control" type="date" id="riwayatStart" name="start" value="<?= esc((string) $start) ?>">
                        </div>

                        <div>
                            <label class="form-label small fw-semibold" for="riwayatEnd">Tanggal Selesai</label>
                            <input class="form-control" type="date" id="riwayatEnd" name="end" value="<?= esc((string) $end) ?>">
                        </div>

                        <div class="d-flex gap-2">
                            <a class="btn btn-outline-secondary flex-fill" href="<?= esc(route('/riwayat/pembelian')) ?>">Reset</a>
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
                        <a class="btn btn-sm btn-outline-primary rounded-pill" href="<?= esc($riwayatUrl(['q' => null])) ?>" title="Hapus kata kunci">
                            Kata kunci: <?= esc($q) ?> <span aria-hidden="true">&times;</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($status !== ''): ?>
                        <a class="btn btn-sm btn-outline-primary rounded-pill" href="<?= esc($riwayatUrl(['status' => null])) ?>" title="Hapus status">
                            <?= esc($statusOptions[$status]) ?> <span aria-hidden="true">&times;</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($start !== null || $end !== null): ?>
                        <a class="btn btn-sm btn-outline-primary rounded-pill" href="<?= esc($riwayatUrl(['start' => null, 'end' => null])) ?>" title="Hapus rentang tanggal">
                            <?= esc(($start ?? 'Awal') . ' – ' . ($end ?? 'Sekarang')) ?> <span aria-hidden="true">&times;</span>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="row align-items-center g-2 mb-3">
                <div class="col">
                    <p class="text-body-secondary small mb-0">
                        <?php if ($total > 0): ?>
                            Menampilkan <?= (int) $startItem ?>&ndash;<?= (int) $endItem ?> dari <?= (int) $total ?> transaksi
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
                <div class="d-grid gap-3">
                    <?php foreach ($items as $item): ?>
                        <?php
                        $itemStatus = (string) $item['status'];
                        $badge = $badgeMap[$itemStatus];
                        $cancelled = $itemStatus === 'cancelled';
                        ?>
                        <article class="card rounded-3">
                            <div class="card-body d-flex flex-wrap flex-sm-nowrap align-items-center gap-3 p-3">
                                <img src="<?= esc((string) $item['thumbnail']) ?>" alt="<?= esc((string) $item['product_title']) ?>"
                                    width="64" height="64" loading="lazy"
                                    class="rounded-2 object-fit-cover flex-shrink-0 <?= $cancelled ? 'opacity-75' : 'bg-body-tertiary' ?>">
                                <div class="flex-grow-1 min-w-0">
                                    <h2 class="h6 fw-bold mb-1 <?= $cancelled ? 'text-body-secondary' : 'text-body-emphasis' ?>"><?= esc((string) $item['product_title']) ?></h2>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="small text-body-secondary"><?= esc((string) $item['seller_name']) ?> &middot; <?= esc(format_tanggal((string) $item['created_at'])) ?></span>
                                        <span class="badge rounded-1 <?= esc($badge['class']) ?> d-inline-flex align-items-center gap-1">
                                            <i data-lucide="<?= esc($badge['icon']) ?>" class="pk-icon--xs" aria-hidden="true"></i><?= esc($badge['label']) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 ms-auto">
                                    <a class="btn btn-sm btn-outline-primary" href="<?= esc(route('/riwayat/detail?id=' . (int) $item['purchase_id'])) ?>" aria-label="Lihat detail transaksi <?= esc((string) $item['product_title']) ?>">Detail</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php elseif ($total === 0 && $queryBase === []): ?>
                <div class="text-center py-5 px-3 border rounded-3 pk-border-dashed">
                    <i data-lucide="search-x" class="pk-icon--xl text-body-tertiary d-block mx-auto mb-3" aria-hidden="true"></i>
                    <h2 class="h5 fw-bold text-primary mb-2">Belum ada transaksi pembelian</h2>
                    <p class="text-body-secondary mb-0">Transaksi yang kamu ajukan akan muncul di sini beserta statusnya.</p>
                </div>
            <?php else: ?>
                <div class="text-center py-5 px-3 border rounded-3 pk-border-dashed">
                    <i data-lucide="search-x" class="pk-icon--xl text-body-tertiary d-block mx-auto mb-3" aria-hidden="true"></i>
                    <h2 class="h5 fw-bold text-primary mb-2">Tidak ada transaksi cocok</h2>
                    <p class="text-body-secondary mb-0">Ubah kata kunci, status, atau rentang tanggal pada filter.</p>
                </div>
            <?php endif; ?>

            <?php if ($totalPages > 1): ?>
                <nav aria-label="Navigasi halaman transaksi" class="mt-4">
                    <ul class="pagination justify-content-end mb-0">
                        <li class="page-item<?= $page > 1 ? '' : ' disabled' ?>">
                            <a class="page-link" href="<?= esc($riwayatUrl(['page' => $page - 1])) ?>" aria-label="Halaman sebelumnya">&lt;</a>
                        </li>
                        <?php foreach ($pageNumbers as $pageNumber): ?>
                            <?php if ($pageNumber === null): ?>
                                <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                            <?php elseif ($pageNumber === $page): ?>
                                <li class="page-item active" aria-current="page"><span class="page-link"><?= (int) $pageNumber ?></span></li>
                            <?php else: ?>
                                <li class="page-item"><a class="page-link" href="<?= esc($riwayatUrl(['page' => $pageNumber])) ?>"><?= (int) $pageNumber ?></a></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <li class="page-item<?= $page < $totalPages ? '' : ' disabled' ?>">
                            <a class="page-link" href="<?= esc($riwayatUrl(['page' => $page + 1])) ?>" aria-label="Halaman berikutnya">&gt;</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

</main>

<?php Component::flush() ?>
<script src="<?= asset('js/katalog.js') ?>" defer></script>
<?php $script = Component::endFlush() ?>

<?php Component::include('user/footer', ['script' => $script]); ?>
