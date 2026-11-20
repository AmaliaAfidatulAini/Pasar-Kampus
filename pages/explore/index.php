<?php
declare(strict_types=1);

require_model('catalog');

$session = get_session();

$getString = static function (string $key): string {
    $value = $_GET[$key] ?? '';
    return is_string($value) ? trim($value) : '';
};

$q = $getString('q') !== '' ? $getString('q') : $getString('search');
$categoryInput = $_GET['category'] ?? [];
$categoryInput = is_array($categoryInput) ? $categoryInput : [$categoryInput];
$categoryFilter = array_values(array_unique(array_filter(
    array_map(static fn ($value): string => is_string($value) ? trim($value) : '', $categoryInput),
    static fn (string $value): bool => $value !== ''
)));
$campus = $getString('campus');
$minPriceInput = $getString('min_price');
$maxPriceInput = $getString('max_price');
$conditionInput = $getString('condition');
$sort = $getString('sort');
$page = max(1, (int) ($_GET['page'] ?? 1));

$condition = ctype_digit($conditionInput) ? (int) $conditionInput : 0;
if ($condition < 1 || $condition > 4) {
    $condition = 0;
}

$allowedSorts = ['newest', 'price_asc', 'price_desc'];
if (!in_array($sort, $allowedSorts, true)) {
    $sort = 'newest';
}

$minPrice = ($minPriceInput !== '' && is_numeric($minPriceInput)) ? max(0, (int) $minPriceInput) : null;
$maxPrice = ($maxPriceInput !== '' && is_numeric($maxPriceInput)) ? max(0, (int) $maxPriceInput) : null;

$perPage = 12;
$filters = [
    'search' => $q,
    'category' => $categoryFilter,
    'campus' => $campus,
    'condition' => $condition,
    'sort' => $sort,
];

if ($minPrice !== null) {
    $filters['min_price'] = $minPrice;
}
if ($maxPrice !== null) {
    $filters['max_price'] = $maxPrice;
}

$total = count_catalog_products($filters);
$totalPages = max(1, (int) ceil($total / $perPage));

$queryBase = [];
if ($q !== '') {
    $queryBase['q'] = $q;
}
if ($categoryFilter !== []) {
    $queryBase['category'] = $categoryFilter;
}
if ($campus !== '') {
    $queryBase['campus'] = $campus;
}
if ($minPrice !== null) {
    $queryBase['min_price'] = $minPrice;
}
if ($maxPrice !== null) {
    $queryBase['max_price'] = $maxPrice;
}
if ($condition > 0) {
    $queryBase['condition'] = $condition;
}
if ($sort !== 'newest') {
    $queryBase['sort'] = $sort;
}

if ($page > $totalPages) {
    $redirectParams = $queryBase;
    if ($totalPages > 1) {
        $redirectParams['page'] = $totalPages;
    }
    redirect('/explore' . ($redirectParams !== [] ? '?' . http_build_query($redirectParams) : ''), 302);
}

$products = get_catalog_products($filters + [
    'limit' => $perPage,
    'offset' => ($page - 1) * $perPage,
]);

$categories = get_catalog_categories();
$campuses = get_catalog_campuses();

$conditionLabels = [
    1 => 'Bekas',
    2 => 'Cukup',
    3 => 'Baik',
    4 => 'Sangat Bagus',
];

$catalogUrl = static function (array $overrides = []) use ($queryBase): string {
    $params = array_merge($queryBase, $overrides);
    $params = array_filter(
        $params,
        static fn ($value): bool => $value !== null && $value !== ''
    );
    $query = http_build_query($params);

    return route('/explore' . ($query !== '' ? '?' . $query : ''));
};

$startItem = $total > 0 ? (($page - 1) * $perPage) + 1 : 0;
$endItem = min($page * $perPage, $total);

$pageNumbers = [];
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
?>

<?php Component::flush() ?>
<link rel="stylesheet" href="<?= asset('css/card.css') ?>">
<link rel="stylesheet" href="<?= asset('css/katalog.css') ?>">
<?php $styles = Component::endFlush() ?>

<?php Component::include('user/header', [
    'title' => $q !== '' ? 'Cari: ' . $q : 'Katalog',
    'styles' => $styles,
]); ?>

<main class="container py-4 py-lg-5">
    <?php Component::include("common/banner", [
        "title" => ($q !== '') ? 'Hasil pencarian “' . esc($q) . '”' : 'Cari Barang',
        "subtitle" => "Temukan semua barang bekas yang ingin kamu cari"
    ]) ?>

    <div class="pk-catalog-layout" id="catalogLayout">
        <aside class="pk-catalog-sidebar">
            <div class="offcanvas-lg offcanvas-start" tabindex="-1" id="catalogSidebar" aria-labelledby="catalogSidebarLabel">
                <div class="offcanvas-header">
                    <h2 class="offcanvas-title h5 mb-0" id="catalogSidebarLabel">Filter Barang</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#catalogSidebar" aria-label="Tutup filter"></button>
                </div>
                <div class="offcanvas-body">
                    <form id="catalogFilterForm" method="GET" action="<?= esc(route('/explore')) ?>" class="d-flex flex-column gap-4">
                        <div>
                            <label class="form-label small fw-semibold" for="catalogQuery">Kata kunci</label>
                            <div class="pk-field-search">
                                <i data-lucide="search" class="pk-icon" aria-hidden="true"></i>
                                <input
                                    class="form-control"
                                    type="search"
                                    id="catalogQuery"
                                    name="q"
                                    value="<?= esc($q) ?>"
                                    placeholder="Cari barang, deskripsi, kampus..."
                                >
                            </div>
                        </div>

                        <div>
                            <h3 class="h6 text-uppercase fw-bold mb-2">
                                <button class="btn btn-link p-0 w-100 d-flex justify-content-between align-items-center text-decoration-none text-body" type="button" data-bs-toggle="collapse" data-bs-target="#filterKategori" aria-expanded="true" aria-controls="filterKategori">
                                    <span>Kategori</span>
                                    <i data-lucide="chevron-up" class="pk-icon pk-chevron" aria-hidden="true"></i>
                                </button>
                            </h3>
                            <div class="collapse show" id="filterKategori">
                                <div class="d-grid gap-2">
                                    <?php foreach ($categories as $option): ?>
                                        <label class="form-check d-flex align-items-center gap-2 mb-0 ps-0">
                                            <input class="form-check-input m-0" type="checkbox" name="category[]" value="<?= esc((string) $option['name']) ?>" id="category-<?= (int) $option['id'] ?>" <?= in_array((string) $option['name'], $categoryFilter, true) ? 'checked' : '' ?>>
                                            <span class="form-check-label small"><?= esc((string) $option['name']) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="h6 text-uppercase fw-bold mb-2">
                                <button class="btn btn-link p-0 w-100 d-flex justify-content-between align-items-center text-decoration-none text-body" type="button" data-bs-toggle="collapse" data-bs-target="#filterKampus" aria-expanded="true" aria-controls="filterKampus">
                                    <span>Kampus</span>
                                    <i data-lucide="chevron-up" class="pk-icon pk-chevron" aria-hidden="true"></i>
                                </button>
                            </h3>
                            <div class="collapse show" id="filterKampus">
                                <select class="form-select" id="catalogCampus" name="campus">
                                    <option value="">Semua kampus</option>
                                    <?php foreach ($campuses as $option): ?>
                                        <option value="<?= esc((string) $option['campus']) ?>" <?= $campus === (string) $option['campus'] ? 'selected' : '' ?>>
                                            <?= esc((string) $option['campus']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div>
                            <h3 class="h6 text-uppercase fw-bold mb-2">
                                <button class="btn btn-link p-0 w-100 d-flex justify-content-between align-items-center text-decoration-none text-body" type="button" data-bs-toggle="collapse" data-bs-target="#filterHarga" aria-expanded="true" aria-controls="filterHarga">
                                    <span>Rentang harga</span>
                                    <i data-lucide="chevron-up" class="pk-icon pk-chevron" aria-hidden="true"></i>
                                </button>
                            </h3>
                            <div class="collapse show" id="filterHarga">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold text-body-secondary" for="catalogMinPrice">Minimal</label>
                                        <input class="form-control" type="number" id="catalogMinPrice" name="min_price" min="0" step="1000" inputmode="numeric" value="<?= esc($minPriceInput) ?>" placeholder="Rp">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold text-body-secondary" for="catalogMaxPrice">Maksimal</label>
                                        <input class="form-control" type="number" id="catalogMaxPrice" name="max_price" min="0" step="1000" inputmode="numeric" value="<?= esc($maxPriceInput) ?>" placeholder="Rp">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="h6 text-uppercase fw-bold mb-2">
                                <button class="btn btn-link p-0 w-100 d-flex justify-content-between align-items-center text-decoration-none text-body" type="button" data-bs-toggle="collapse" data-bs-target="#filterKondisi" aria-expanded="true" aria-controls="filterKondisi">
                                    <span>Kondisi</span>
                                    <i data-lucide="chevron-up" class="pk-icon pk-chevron" aria-hidden="true"></i>
                                </button>
                            </h3>
                            <div class="collapse show" id="filterKondisi">
                                <select class="form-select" id="catalogCondition" name="condition">
                                    <option value="">Semua kondisi</option>
                                    <?php foreach ($conditionLabels as $value => $label): ?>
                                        <option value="<?= (int) $value ?>" <?= $condition === $value ? 'selected' : '' ?>>
                                            <?= esc($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div>
                            <h3 class="h6 text-uppercase fw-bold mb-2">
                                <button class="btn btn-link p-0 w-100 d-flex justify-content-between align-items-center text-decoration-none text-body" type="button" data-bs-toggle="collapse" data-bs-target="#filterUrutkan" aria-expanded="true" aria-controls="filterUrutkan">
                                    <span>Urutkan</span>
                                    <i data-lucide="chevron-up" class="pk-icon pk-chevron" aria-hidden="true"></i>
                                </button>
                            </h3>
                            <div class="collapse show" id="filterUrutkan">
                                <select class="form-select" id="catalogSort" name="sort">
                                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Terbaru</option>
                                    <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Harga terendah</option>
                                    <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Harga tertinggi</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a class="btn btn-outline-secondary flex-fill" href="<?= esc(route('/explore')) ?>">Reset</a>
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
                        <a class="btn btn-sm btn-outline-primary rounded-pill" href="<?= esc($catalogUrl(['q' => null])) ?>" title="Hapus kata kunci">
                            Kata kunci: <?= esc($q) ?> <span aria-hidden="true">&times;</span>
                        </a>
                    <?php endif; ?>
                    <?php foreach ($categoryFilter as $activeCategory): ?>
                        <a class="btn btn-sm btn-outline-primary rounded-pill" href="<?= esc($catalogUrl(['category' => array_values(array_diff($categoryFilter, [$activeCategory]))])) ?>" title="Hapus kategori">
                            <?= esc($activeCategory) ?> <span aria-hidden="true">&times;</span>
                        </a>
                    <?php endforeach; ?>
                    <?php if ($campus !== ''): ?>
                        <a class="btn btn-sm btn-outline-primary rounded-pill" href="<?= esc($catalogUrl(['campus' => null])) ?>" title="Hapus kampus">
                            <?= esc($campus) ?> <span aria-hidden="true">&times;</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($minPrice !== null || $maxPrice !== null): ?>
                        <a class="btn btn-sm btn-outline-primary rounded-pill" href="<?= esc($catalogUrl(['min_price' => null, 'max_price' => null])) ?>" title="Hapus rentang harga">
                            Harga:
                            <?= $minPrice !== null ? 'Rp ' . number_format($minPrice, 0, ',', '.') : 'Rp 0' ?>
                            &ndash;
                            <?= $maxPrice !== null ? 'Rp ' . number_format($maxPrice, 0, ',', '.') : 'tanpa batas' ?>
                            <span aria-hidden="true">&times;</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($condition > 0): ?>
                        <a class="btn btn-sm btn-outline-primary rounded-pill" href="<?= esc($catalogUrl(['condition' => null])) ?>" title="Hapus kondisi">
                            <?= esc($conditionLabels[$condition]) ?> <span aria-hidden="true">&times;</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($sort !== 'newest'): ?>
                        <a class="btn btn-sm btn-outline-primary rounded-pill" href="<?= esc($catalogUrl(['sort' => null])) ?>" title="Hapus urutan">
                            <?= $sort === 'price_asc' ? 'Harga terendah' : 'Harga tertinggi' ?> <span aria-hidden="true">&times;</span>
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
                    <button type="button" class="btn btn-link border-0 px-2 py-1 d-inline-flex align-items-center gap-2 text-decoration-none fw-semibold text-xs" id="catalogFilterToggle" aria-controls="catalogSidebar" aria-expanded="true">
                        <i data-lucide="sliders-horizontal" class="pk-icon pk-icon--md" aria-hidden="true"></i>
                        <span>Filter</span>
                    </button>
                </div>
            </div>

            <?php if ($products !== []): ?>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-4 pk-catalog-grid">
                    <?php foreach ($products as $item): ?>
                        <div class="col">
                            <?php Component::include('common/card', [
                                'id' => $item['id'],
                                'title' => $item['title'],
                                'description' => $item['short_desc'] ?? $item['description'] ?? '',
                                'category' => $item['category'] ?? '',
                                'location' => $item['location'] ?? '',
                                'condition' => $item['condition'] ?? '',
                                'o_price' => $item['original_price'] ?? '',
                                'price' => $item['price'] ?? '',
                                'foto' => $item['image'] ?? '',
                            ]); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5 px-3 border rounded-3 pk-border-dashed mb-4">
                    <h2 class="h5 fw-bold text-primary mb-2">Tidak ada barang yang cocok</h2>
                    <p class="text-body-secondary mb-3">Coba ganti kata kunci atau sesuaikan filter pencarian.</p>
                </div>
            <?php endif; ?>

            <?php if ($totalPages > 1): ?>
                <nav aria-label="Navigasi halaman katalog">
                    <ul class="pagination justify-content-end mb-0">
                        <li class="page-item<?= $page > 1 ? '' : ' disabled' ?>">
                            <a class="page-link" href="<?= esc($catalogUrl(['page' => $page - 1])) ?>" aria-label="Halaman sebelumnya">&lt;</a>
                        </li>
                        <?php foreach ($pageNumbers as $pageNumber): ?>
                            <?php if ($pageNumber === null): ?>
                                <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                            <?php elseif ($pageNumber === $page): ?>
                                <li class="page-item active" aria-current="page"><span class="page-link"><?= (int) $pageNumber ?></span></li>
                            <?php else: ?>
                                <li class="page-item"><a class="page-link" href="<?= esc($catalogUrl(['page' => $pageNumber])) ?>"><?= (int) $pageNumber ?></a></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <li class="page-item<?= $page < $totalPages ? '' : ' disabled' ?>">
                            <a class="page-link" href="<?= esc($catalogUrl(['page' => $page + 1])) ?>" aria-label="Halaman berikutnya">&gt;</a>
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
