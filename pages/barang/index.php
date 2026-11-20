<?php
declare(strict_types=1);

require_model('catalog');

$session = get_session();

$getString = static function (string $key): string {
    $value = $_GET[$key] ?? '';
    return is_string($value) ? trim($value) : '';
};

$q = $getString('q') !== '' ? $getString('q') : $getString('search');
$category = $getString('category');
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
    'category' => $category,
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
if ($category !== '') {
    $queryBase['category'] = $category;
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
    redirect('/barang' . ($redirectParams !== [] ? '?' . http_build_query($redirectParams) : ''), 302);
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

    return route('/barang' . ($query !== '' ? '?' . $query : ''));
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

$heading = $q !== ''
    ? 'Hasil pencarian “' . $q . '”'
    : 'Katalog Barang Preloved';
?>

<?php Component::flush() ?>
<link rel="stylesheet" href="<?= asset('css/katalog.css') ?>">
<?php $styles = Component::endFlush() ?>

<?php Component::include('user/header', [
    'title' => $q !== '' ? 'Cari: ' . $q : 'Katalog',
    'styles' => $styles,
]); ?>

<main class="pk-main-wrapper">
    <div class="pk-content-container">
        <div class="pk-sidebar-backdrop" id="catalogSidebarBackdrop"></div>

        <div class="pk-catalog-layout">
            <aside class="pk-sidebar-drawer" id="catalogSidebar" aria-label="Filter barang">
                <div class="pk-sidebar-header">
                    <span class="pk-sidebar-title">Filter Barang</span>
                    <button type="button" class="pk-sidebar-close" id="catalogSidebarClose" aria-label="Tutup filter">&times;</button>
                </div>

                <form class="pk-sidebar-body pk-filter-form" id="catalogFilterForm" method="GET" action="<?= esc(route('/barang')) ?>">
                    <div class="pk-form-group">
                        <label class="pk-form-label" for="catalogQuery">Kata kunci</label>
                        <input
                            type="search"
                            class="pk-form-input"
                            id="catalogQuery"
                            name="q"
                            value="<?= esc($q) ?>"
                            placeholder="Cari barang, deskripsi, kampus..."
                        >
                    </div>

                    <div class="pk-form-group">
                        <label class="pk-form-label" for="catalogCategory">Kategori</label>
                        <select class="pk-form-select" id="catalogCategory" name="category">
                            <option value="">Semua kategori</option>
                            <?php foreach ($categories as $option): ?>
                                <option value="<?= esc((string) $option['name']) ?>" <?= $category === (string) $option['name'] ? 'selected' : '' ?>>
                                    <?= esc((string) $option['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="pk-form-group">
                        <label class="pk-form-label" for="catalogCampus">Kampus</label>
                        <select class="pk-form-select" id="catalogCampus" name="campus">
                            <option value="">Semua kampus</option>
                            <?php foreach ($campuses as $option): ?>
                                <option value="<?= esc((string) $option['campus']) ?>" <?= $campus === (string) $option['campus'] ? 'selected' : '' ?>>
                                    <?= esc((string) $option['campus']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="pk-form-group">
                        <span class="pk-form-label">Rentang harga</span>
                        <div class="pk-price-row">
                            <div class="pk-price-box">
                                <label for="catalogMinPrice">Minimal</label>
                                <input
                                    type="number"
                                    id="catalogMinPrice"
                                    name="min_price"
                                    min="0"
                                    step="1000"
                                    inputmode="numeric"
                                    value="<?= esc($minPriceInput) ?>"
                                    placeholder="Rp"
                                >
                            </div>
                            <div class="pk-price-box">
                                <label for="catalogMaxPrice">Maksimal</label>
                                <input
                                    type="number"
                                    id="catalogMaxPrice"
                                    name="max_price"
                                    min="0"
                                    step="1000"
                                    inputmode="numeric"
                                    value="<?= esc($maxPriceInput) ?>"
                                    placeholder="Rp"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="pk-form-group">
                        <label class="pk-form-label" for="catalogCondition">Kondisi</label>
                        <select class="pk-form-select" id="catalogCondition" name="condition">
                            <option value="">Semua kondisi</option>
                            <?php foreach ($conditionLabels as $value => $label): ?>
                                <option value="<?= (int) $value ?>" <?= $condition === $value ? 'selected' : '' ?>>
                                    <?= esc($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="pk-form-group">
                        <label class="pk-form-label" for="catalogSort">Urutkan</label>
                        <select class="pk-form-select" id="catalogSort" name="sort">
                            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Terbaru</option>
                            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Harga terendah</option>
                            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Harga tertinggi</option>
                        </select>
                    </div>

                    <div class="pk-sidebar-actions">
                        <a class="pk-reset-btn" href="<?= esc(route('/barang')) ?>">Reset</a>
                        <button type="submit" class="pk-submit-btn">Terapkan</button>
                    </div>
                </form>
            </aside>

            <div class="pk-catalog-main">
                <div class="pk-results-heading">
                    <div>
                        <h1><?= esc($heading) ?></h1>
                        <p>Barang preloved dari mahasiswa di sekitar kampus.</p>
                    </div>
                    <button type="button" class="pk-filter-btn" id="catalogFilterToggle">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="4" y1="21" x2="4" y2="14"></line>
                            <line x1="4" y1="10" x2="4" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12" y2="3"></line>
                            <line x1="20" y1="21" x2="20" y2="16"></line>
                            <line x1="20" y1="12" x2="20" y2="3"></line>
                            <line x1="1" y1="14" x2="7" y2="14"></line>
                            <line x1="9" y1="8" x2="15" y2="8"></line>
                            <line x1="17" y1="16" x2="23" y2="16"></line>
                        </svg>
                        Filter
                    </button>
                </div>

                <?php if ($queryBase !== []): ?>
                    <div class="pk-active-filters">
                        <?php if ($q !== ''): ?>
                            <a class="pk-filter-chip" href="<?= esc($catalogUrl(['q' => null])) ?>" title="Hapus kata kunci">
                                Kata kunci: <?= esc($q) ?> <span aria-hidden="true">&times;</span>
                            </a>
                        <?php endif; ?>
                        <?php if ($category !== ''): ?>
                            <a class="pk-filter-chip" href="<?= esc($catalogUrl(['category' => null])) ?>" title="Hapus kategori">
                                <?= esc($category) ?> <span aria-hidden="true">&times;</span>
                            </a>
                        <?php endif; ?>
                        <?php if ($campus !== ''): ?>
                            <a class="pk-filter-chip" href="<?= esc($catalogUrl(['campus' => null])) ?>" title="Hapus kampus">
                                <?= esc($campus) ?> <span aria-hidden="true">&times;</span>
                            </a>
                        <?php endif; ?>
                        <?php if ($minPrice !== null || $maxPrice !== null): ?>
                            <a class="pk-filter-chip" href="<?= esc($catalogUrl(['min_price' => null, 'max_price' => null])) ?>" title="Hapus rentang harga">
                                Harga:
                                <?= $minPrice !== null ? 'Rp ' . number_format($minPrice, 0, ',', '.') : 'Rp 0' ?>
                                &ndash;
                                <?= $maxPrice !== null ? 'Rp ' . number_format($maxPrice, 0, ',', '.') : 'tanpa batas' ?>
                                <span aria-hidden="true">&times;</span>
                            </a>
                        <?php endif; ?>
                        <?php if ($condition > 0): ?>
                            <a class="pk-filter-chip" href="<?= esc($catalogUrl(['condition' => null])) ?>" title="Hapus kondisi">
                                <?= esc($conditionLabels[$condition]) ?> <span aria-hidden="true">&times;</span>
                            </a>
                        <?php endif; ?>
                        <?php if ($sort !== 'newest'): ?>
                            <a class="pk-filter-chip" href="<?= esc($catalogUrl(['sort' => null])) ?>" title="Hapus urutan">
                                <?= $sort === 'price_asc' ? 'Harga terendah' : 'Harga tertinggi' ?> <span aria-hidden="true">&times;</span>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="pk-toolbar">
                    <div class="pk-toolbar-count">
                        <?php if ($total > 0): ?>
                            Menampilkan <?= (int) $startItem ?>&ndash;<?= (int) $endItem ?> dari <?= (int) $total ?> barang
                        <?php else: ?>
                            Tidak ada barang yang cocok
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($products !== []): ?>
                    <div class="pk-grid">
                        <?php foreach ($products as $item): ?>
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
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="pk-empty-state">
                        <div class="pk-empty-icon" aria-hidden="true">📦</div>
                        <h2>Tidak ada barang yang cocok</h2>
                        <p>Coba ubah kata kunci atau longgarkan filter yang aktif.</p>
                        <a class="pk-reset-btn" href="<?= esc(route('/barang')) ?>">Reset Semua Filter</a>
                    </div>
                <?php endif; ?>

                <?php if ($totalPages > 1): ?>
                    <nav class="pk-pagination" aria-label="Navigasi halaman katalog">
                        <?php if ($page > 1): ?>
                            <a class="pk-page-btn" href="<?= esc($catalogUrl(['page' => $page - 1])) ?>" aria-label="Halaman sebelumnya">&lt;</a>
                        <?php endif; ?>

                        <?php foreach ($pageNumbers as $pageNumber): ?>
                            <?php if ($pageNumber === null): ?>
                                <span class="pk-page-gap" aria-hidden="true">&hellip;</span>
                            <?php elseif ($pageNumber === $page): ?>
                                <span class="pk-page-btn active" aria-current="page"><?= (int) $pageNumber ?></span>
                            <?php else: ?>
                                <a class="pk-page-btn" href="<?= esc($catalogUrl(['page' => $pageNumber])) ?>"><?= (int) $pageNumber ?></a>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <?php if ($page < $totalPages): ?>
                            <a class="pk-page-btn" href="<?= esc($catalogUrl(['page' => $page + 1])) ?>" aria-label="Halaman berikutnya">&gt;</a>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php Component::flush() ?>
<script src="<?= asset('js/katalog.js') ?>" defer></script>
<?php $script = Component::endFlush() ?>

<?php Component::include('user/footer', ['script' => $script]); ?>
