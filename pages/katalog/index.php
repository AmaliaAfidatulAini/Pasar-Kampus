<?php
require_model('catalog');
$session = get_session();
$isLoggedIn = !empty($session);
$userName = $session['name'] ?? 'Pengguna';
$displayName = $session['name'] ?? $session['email'] ?? 'Pengguna';
$initial = strtoupper(substr($displayName, 0, 1));
$initialCategory = trim((string)($_GET['category'] ?? ''));
$initialSearch = trim((string)($_GET['q'] ?? $_GET['search'] ?? ''));
$allProducts = get_catalog_products();
?>

<?php Component::flush() ?>
<link rel="stylesheet" href="<?= asset("css/katalog.css") ?>">
<?php $styles = Component::endFlush() ?>

<?php 
Component::include("user/header", [
    "title" => "Beranda",
    "styles" => $styles
]); 
?>

<main class="pk-main-wrapper">
    <div class="pk-content-container">

        <!-- Backdrop for Mobile Drawer -->
        <div class="pk-sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>

        <!-- ================= 3. TWO-FRAME SPLIT LAYOUT ================= -->
        <div class="pk-catalog-layout">

            <!-- SIDEBAR FILTER -->
            <aside class="pk-sidebar-drawer" id="sidebarDrawer" aria-label="Filter Barang">
                <div class="pk-sidebar-header">
                    <span class="pk-sidebar-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
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
                        FILTER
                    </span>
                    <button type="button" class="pk-sidebar-close" onclick="closeSidebar()" title="Tutup Filter">&times;</button>
                </div>

                <div class="pk-sidebar-body">
                    <!-- 1. KATEGORI -->
                    <div>
                        <div class="pk-filter-group-title">KATEGORI</div>
                        <ul class="pk-filter-list" id="sidebarCatList">
                            <li class="pk-filter-item" onclick="selectCategory('Buku', this)">Buku</li>
                            <li class="pk-filter-item" onclick="selectCategory('Elektronik', this)">Elektronik</li>
                            <li class="pk-filter-item" onclick="selectCategory('Perlengkapan Kos', this)">Perlengkapan Kos</li>
                            <li class="pk-filter-item" onclick="selectCategory('Pakaian', this)">Pakaian</li>
                            <li class="pk-filter-item" onclick="selectCategory('ATK', this)">ATK</li>
                            <li class="pk-filter-item" onclick="selectCategory('Lainnya', this)">Lainnya</li>
                        </ul>
                        <div class="pk-see-more" onclick="toggleExpandList('sidebarCatList', this)">Lihat semua &#x25BE;</div>
                    </div>

                    <!-- 2. TAGS -->
                    <div>
                        <div class="pk-filter-group-title">TAGS</div>
                        <ul class="pk-filter-list" id="sidebarTagList">
                            <li class="pk-filter-item" onclick="selectTag('#ModulKuliah', this)">#ModulKuliah</li>
                            <li class="pk-filter-item" onclick="selectTag('#JasAlmamater', this)">#JasAlmamater</li>
                            <li class="pk-filter-item" onclick="selectTag('#MejaBelajar', this)">#MejaBelajar</li>
                            <li class="pk-filter-item" onclick="selectTag('#Jaket', this)">#Jaket</li>
                        </ul>
                        <div class="pk-see-more" onclick="toggleExpandList('sidebarTagList', this)">Lihat semua &#x25BE;</div>
                    </div>

                    <!-- 3. LOKASI -->
                    <div>
                        <div class="pk-filter-group-title">LOKASI</div>
                        <div class="pk-checkbox-group" id="sidebarLocGroup">
                            <label class="pk-checkbox-item">
                                <input type="checkbox" name="locFilter" value="Area Universitas Brawijaya" onchange="applyCatalogFilters()">
                                <span>Area Universitas Brawijaya</span>
                            </label>
                            <label class="pk-checkbox-item">
                                <input type="checkbox" name="locFilter" value="Area Universitas Negeri Malang" onchange="applyCatalogFilters()">
                                <span>Area Universitas Negeri Malang</span>
                            </label>
                            <label class="pk-checkbox-item">
                                <input type="checkbox" name="locFilter" value="Area Politeknik Negeri Malang" onchange="applyCatalogFilters()">
                                <span>Area Politeknik Negeri Malang</span>
                            </label>
                            <label class="pk-checkbox-item">
                                <input type="checkbox" name="locFilter" value="Area Universitas Terbuka" onchange="applyCatalogFilters()">
                                <span>Area Universitas Terbuka</span>
                            </label>
                        </div>
                        <div class="pk-see-more" onclick="toggleExpandList('sidebarLocGroup', this)">Lihat semua &#x25BE;</div>
                    </div>

                    <!-- 4. RENTANG HARGA -->
                    <div>
                        <div class="pk-filter-group-title">RENTANG HARGA</div>
                        <div class="pk-price-row">
                            <div class="pk-price-box">
                                <label for="priceMinInput">Rp. Min</label>
                                <input type="number" id="priceMinInput" placeholder="" oninput="applyCatalogFilters()">
                            </div>
                            <div class="pk-price-box">
                                <label for="priceMaxInput">Rp. Max</label>
                                <input type="number" id="priceMaxInput" placeholder="" oninput="applyCatalogFilters()">
                            </div>
                        </div>
                    </div>

                    <!-- 5. KONDISI BARANG (Slider + Label Interaktif 2 Arah) -->
                    <div>
                        <div class="pk-filter-group-title">KONDISI BARANG</div>
                        <div style="padding: 4px 0 8px;">
                            <input type="range" id="conditionRangeInput" min="0" max="3" value="0" step="1" style="width: 100%; accent-color: var(--pk-navy); cursor: pointer;" oninput="onConditionChange()">
                            <div style="display: flex; justify-content: space-between; margin-top: 8px; font-size: 0.68rem;">
                                <span class="cond-label" id="condLabel0" onclick="setConditionRange(0)">Bekas</span>
                                <span class="cond-label" id="condLabel1" onclick="setConditionRange(1)">Cukup</span>
                                <span class="cond-label" id="condLabel2" onclick="setConditionRange(2)">Baik</span>
                                <span class="cond-label" id="condLabel3" onclick="setConditionRange(3)">Sangat bagus</span>
                            </div>
                        </div>
                    </div>

                    <!-- 6. ACTION BUTTONS -->
                    <div class="pk-sidebar-actions">
                        <button type="button" class="pk-reset-btn" onclick="resetSidebarFilters()">Reset all</button>
                        <button type="button" class="pk-apply-btn" onclick="closeSidebar()">Lihat Hasil</button>
                    </div>
                </div>
            </aside>

            <!-- RIGHT FRAME: HERO BANNER + CATALOG MAIN CONTENT -->
            <div class="pk-catalog-main">

                <!-- Hero Banner -->
                <section class="pk-hero-banner">
                    <div class="pk-hero-left">
                        <h1>
                            Cari Barang Bekas<br>
                            <span class="pk-hero-highlight">Berkualitas</span> &amp; Murah!
                        </h1>
                        <p class="pk-hero-desc">
                            Temukan buku kuliah, elektronik, perlengkapan kost, ATK, hingga pakaian preloved dari teman sekampus dan sekitar domisili Anda dengan aman &amp; mudah.
                        </p>
                        <div class="pk-hero-pills">
                            <div class="pk-pill-badge">🎓 100% Khusus Mahasiswa</div>
                            <div class="pk-pill-badge">🤝 Aman COD Area Kampus</div>
                            <div class="pk-pill-badge">🏷️ Harga terjangkau</div>
                        </div>
                    </div>

                    <div class="pk-hero-right" style="display: flex; flex-direction: column; align-items: center;">
                        <span class="pk-featured-tag">Baru Diunggah</span>
                        <?php
                        $featuredItem = $allProducts[0] ?? [
                            'id' => 1,
                            'title' => 'Keyboard Logiac - Silver',
                            'short_desc' => 'Keyboard wireless dengan balutan warna silver elegan dan desain slim yang hemat tempat. ..',
                            'category' => 'Elektronik',
                            'location' => 'Kecamatan Lowokwaru',
                            'condition' => 'Layak pakai',
                            'original_price' => 150000,
                            'price' => 90000,
                            'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
                        ];
                        Component::include('common/card', [
                            'id' => $featuredItem['id'],
                            'title' => $featuredItem['title'],
                            'description' => $featuredItem['short_desc'] ?? $featuredItem['description'] ?? '',
                            'category' => $featuredItem['category'] ?? '',
                            'location' => $featuredItem['location'] ?? '',
                            'condition' => $featuredItem['condition'] ?? '',
                            'o_price' => $featuredItem['original_price'] ?? '',
                            'price' => $featuredItem['price'] ?? '',
                            'foto' => $featuredItem['image'] ?? '',
                        ]);
                        ?>
                    </div>
                </section>

                <!-- Toolbar -->
                <div class="pk-toolbar">
                    <button type="button" class="pk-filter-btn" onclick="openSidebar()" title="Buka Filter">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
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
                        <span>Filter</span>
                    </button>
                    <div class="pk-toolbar-count" id="itemCountDisplay">
                        Menampilkan <?= count($allProducts) > 0 ? ('1-' . min(count($allProducts), 12)) : '0' ?> dari <?= count($allProducts) ?> barang
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="pk-grid" id="productGrid">
                    <?php foreach (array_slice($allProducts, 0, 12) as $item): ?>
                        <?php
                        Component::include('common/card', [
                            'id' => $item['id'],
                            'title' => $item['title'],
                            'description' => $item['short_desc'] ?? $item['description'] ?? '',
                            'category' => $item['category'] ?? '',
                            'location' => $item['location'] ?? '',
                            'condition' => $item['condition'] ?? '',
                            'o_price' => $item['original_price'] ?? '',
                            'price' => $item['price'] ?? '',
                            'foto' => $item['image'] ?? '',
                        ]);
                        ?>
                    <?php endforeach; ?>
                </div>

                <!-- Empty State -->
                <div id="gridEmpty" style="display: none; text-align: center; padding: 48px 20px; background: #FFF; border-radius: 14px; border: 1px dashed var(--pk-border); margin: 20px 0;">
                    <div style="font-size: 2.5rem; margin-bottom: 8px;">📦</div>
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--pk-navy); margin: 0 0 6px 0;">Tidak ada barang yang cocok</h3>
                    <p style="font-size: 0.84rem; color: #64748B; margin: 0 0 16px 0;">Coba atur ulang filter pencarian atau gunakan kata kunci lain.</p>
                    <button type="button" onclick="resetSidebarFilters()" class="pk-reset-btn" style="align-self: center;">Reset Semua Filter</button>
                </div>

                <!-- Pagination -->
                <div class="pk-pagination" id="paginationBar">
                    <button type="button" class="pk-page-btn" onclick="changePage('prev')">&lt;</button>
                    <button type="button" class="pk-page-btn active" onclick="setPage(1)">1</button>
                    <button type="button" class="pk-page-btn" onclick="setPage(2)">2</button>
                    <button type="button" class="pk-page-btn" onclick="setPage(3)">3</button>
                    <button type="button" class="pk-page-btn" onclick="setPage(4)">4</button>
                    <button type="button" class="pk-page-btn" onclick="setPage(5)">5</button>
                    <button type="button" class="pk-page-btn" onclick="changePage('next')">&gt;</button>
                </div>

            </div>
        </div>

    </div>
</main>

<?php Component::flush() ?>
<script>
    const rawProducts = <?= json_encode($allProducts, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const initialSearch = <?= json_encode($initialSearch) ?>;
    
    let selectedCategory = <?= json_encode($initialCategory) ?>;
    let selectedTag = '';
    let conditionActive = false; // Status apakah filter kondisi sedang aktif digunakan
    let currentPage = 1;
    const pageSize = 12;

    function formatRupiah(num) {
        return 'Rp. ' + Number(num).toLocaleString('id-ID');
    }

    function openSidebar() {
        const drawer = document.getElementById('sidebarDrawer');
        const backdrop = document.getElementById('sidebarBackdrop');
        if (drawer) drawer.classList.add('active');
        if (backdrop) backdrop.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        const drawer = document.getElementById('sidebarDrawer');
        const backdrop = document.getElementById('sidebarBackdrop');
        if (drawer) drawer.classList.remove('active');
        if (backdrop) backdrop.classList.remove('active');
        document.body.style.overflow = '';
    }

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            closeSidebar();
        }
    });

    function selectCategory(cat, element) {
        if (selectedCategory === cat) {
            selectedCategory = '';
            element.classList.remove('active');
        } else {
            selectedCategory = cat;
            document.querySelectorAll('#sidebarCatList .pk-filter-item').forEach(el => el.classList.remove('active'));
            element.classList.add('active');
        }
        applyCatalogFilters();
    }

    function selectTag(tag, element) {
        if (selectedTag === tag) {
            selectedTag = '';
            element.classList.remove('active');
        } else {
            selectedTag = tag;
            document.querySelectorAll('#sidebarTagList .pk-filter-item').forEach(el => el.classList.remove('active'));
            element.classList.add('active');
        }
        applyCatalogFilters();
    }

    function updateConditionLabels(val) {
        document.querySelectorAll('.cond-label').forEach((el, idx) => {
            if (idx === parseInt(val)) {
                el.classList.add('active');
            } else {
                el.classList.remove('active');
            }
        });
    }

    function onConditionChange() {
        conditionActive = true;
        const val = document.getElementById('conditionRangeInput')?.value || 0;
        updateConditionLabels(val);
        applyCatalogFilters();
    }

    function setConditionRange(val) {
        const slider = document.getElementById('conditionRangeInput');
        if (slider) {
            slider.value = val;
            conditionActive = true;
            updateConditionLabels(val);
            applyCatalogFilters();
        }
    }

    function toggleExpandList(listId, toggleBtn) {
        const list = document.getElementById(listId);
        if (!list) return;
        if (toggleBtn.innerText.includes('▼')) {
            toggleBtn.innerText = 'Tutup ▲';
        } else {
            toggleBtn.innerText = 'Lihat semua ▼';
        }
    }

    function resetSidebarFilters() {
        selectedCategory = '';
        selectedTag = '';
        conditionActive = false;
        document.querySelectorAll('#sidebarCatList .pk-filter-item').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('#sidebarTagList .pk-filter-item').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.cond-label').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('input[name="locFilter"]').forEach(cb => cb.checked = false);
        
        const condEl = document.getElementById('conditionRangeInput');
        if (condEl) condEl.value = 0;

        const minEl = document.getElementById('priceMinInput');
        const maxEl = document.getElementById('priceMaxInput');
        if (minEl) minEl.value = '';
        if (maxEl) maxEl.value = '';
        
        const searchEl = document.getElementById('dashSearch');
        if (searchEl) searchEl.value = '';

        currentPage = 1;
        applyCatalogFilters();
    }

    function applyCatalogFilters() {
        const searchEl = document.getElementById('dashSearch');
        const searchVal = ((searchEl?.value) || initialSearch || '').toLowerCase().trim();
        const minPrice = parseFloat(document.getElementById('priceMinInput')?.value) || 0;
        const maxPrice = parseFloat(document.getElementById('priceMaxInput')?.value) || Infinity;

        const checkedLocations = Array.from(document.querySelectorAll('input[name="locFilter"]:checked'))
            .map(cb => cb.value.toLowerCase());
        
        const conditionVal = parseInt(document.getElementById('conditionRangeInput')?.value) || 0;

        const filtered = rawProducts.filter(item => {
            // Category match
            if (selectedCategory) {
                const itemCat = (item.category || '').toLowerCase();
                const selCat = selectedCategory.toLowerCase();
                if (!itemCat.includes(selCat) && !selCat.includes(itemCat)) {
                    return false;
                }
            }

            // Tag match
            if (selectedTag && !item.tags.map(t => t.toLowerCase()).includes(selectedTag.toLowerCase())) {
                return false;
            }

            // Location match
            if (checkedLocations.length > 0) {
                const itemLoc = (item.campus || '').toLowerCase();
                const hasMatch = checkedLocations.some(loc => itemLoc.includes(loc) || loc.includes(itemLoc));
                if (!hasMatch) return false;
            }

            // Price range match
            if (item.price < minPrice || item.price > maxPrice) {
                return false;
            }
            
            // Condition match
            if (conditionActive) {
                const itemCond = (item.condition || '').toLowerCase();
                if (conditionVal === 0 && !itemCond.includes('bekas')) {
                    return false;
                }
                if (conditionVal === 1 && !itemCond.includes('cukup')) {
                    return false;
                }
                if (conditionVal === 2 && !itemCond.includes('baik')) {
                    return false;
                }
                if (conditionVal === 3 && !itemCond.includes('sangat bagus')) {
                    return false;
                }
            }

            // Keyword search match
            if (searchVal) {
                const inTitle = item.title.toLowerCase().includes(searchVal);
                const inDesc = (item.description || '').toLowerCase().includes(searchVal);
                const inLoc = (item.location || '').toLowerCase().includes(searchVal);
                const inCampus = (item.campus || '').toLowerCase();
                const inTags = (item.tags || []).some(t => t.toLowerCase().includes(searchVal));
                if (!inTitle && !inDesc && !inLoc && !inCampus && !inTags) {
                    return false;
                }
            }

            return true;
        });

        renderGrid(filtered);
    }

    function renderGrid(items) {
        const grid = document.getElementById('productGrid');
        const empty = document.getElementById('gridEmpty');
        const countDisplay = document.getElementById('itemCountDisplay');
        const paginationBar = document.getElementById('paginationBar');

        if (!grid) return;

        if (items.length === 0) {
            grid.innerHTML = '';
            if (empty) empty.style.display = 'block';
            if (countDisplay) countDisplay.innerText = 'Menampilkan 0 barang';
            if (paginationBar) paginationBar.style.display = 'none';
            return;
        }

        if (empty) empty.style.display = 'none';
        const totalItems = items.length;
        const totalPages = Math.ceil(totalItems / pageSize);
        if (currentPage > totalPages && totalPages > 0) currentPage = 1;

        const startIdx = (currentPage - 1) * pageSize;
        const endIdx = Math.min(startIdx + pageSize, totalItems);
        const pageItems = items.slice(startIdx, endIdx);

        if (countDisplay) {
            countDisplay.innerText = `Menampilkan ${startIdx + 1}-${endIdx} dari ${totalItems} barang`;
        }

        if (paginationBar) {
            if (totalPages <= 1) {
                paginationBar.style.display = 'none';
            } else {
                paginationBar.style.display = 'flex';
                let pageHtml = `<button type="button" class="pk-page-btn" onclick="changePage('prev', ${totalPages})">&lt;</button>`;
                for (let p = 1; p <= totalPages; p++) {
                    pageHtml += `<button type="button" class="pk-page-btn ${p === currentPage ? 'active' : ''}" onclick="setPage(${p})">${p}</button>`;
                }
                pageHtml += `<button type="button" class="pk-page-btn" onclick="changePage('next', ${totalPages})">&gt;</button>`;
                paginationBar.innerHTML = pageHtml;
            }
        }

        grid.innerHTML = pageItems.map(item => `
            <div class="product-card" onclick="if (!event.target.closest('a')) goToProductDetail(${item.id})" style="cursor: pointer;">
                <div>
                    <div class="product-img-wrapper">
                        <img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.title)}" loading="lazy">
                        <span class="badge-condition">${escapeHtml(item.condition || 'Layak pakai')}</span>
                        <span class="badge-category">${escapeHtml(item.category || 'Umum')}</span>
                    </div>
                    <div class="product-body">
                        <div class="product-location">${escapeHtml((item.location && item.location.startsWith('📍')) ? item.location : ('📍 ' + (item.location || 'Kecamatan Lowokwaru')))}</div>
                        <h3 class="product-title">${escapeHtml(item.title)}</h3>
                        <p class="product-desc">${escapeHtml(item.short_desc || item.description || '')}</p>
                    </div>
                </div>
                <div class="product-footer">
                    <div class="product-price-box">
                        ${item.original_price && item.original_price > item.price ? `<span class="orig-price">Rp. ${Number(item.original_price).toLocaleString('id-ID')}</span>` : ''}
                        <div class="cur-price">Rp. ${Number(item.price).toLocaleString('id-ID')}</div>
                    </div>
                    <a href="<?= route('/barang') ?>?id=${encodeURIComponent(item.id)}" class="btn-product-detail">
                        Detail
                    </a>
                </div>
            </div>
        `).join('');
    }

    function goToProductDetail(id) {
        window.location.href = `<?= route('/barang') ?>?id=${encodeURIComponent(id)}`;
    }

    function setPage(pageNum) {
        currentPage = pageNum;
        document.querySelectorAll('#paginationBar .pk-page-btn').forEach(btn => {
            if (btn.innerText.trim() === String(pageNum)) {
                btn.classList.add('active');
            } else if (!['<', '>'].includes(btn.innerText.trim())) {
                btn.classList.remove('active');
            }
        });
        applyCatalogFilters();
        window.scrollTo({ top: 380, behavior: 'smooth' });
    }

    function changePage(direction, totalPages = 999) {
        if (direction === 'prev' && currentPage > 1) {
            setPage(currentPage - 1);
        } else if (direction === 'next' && currentPage < totalPages) {
            setPage(currentPage + 1);
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function toggleProfileDropdown(event) {
        if (event) event.stopPropagation();
        const dropdown = document.getElementById('lpProfileDropdown');
        const btn = document.getElementById('lpProfileBtn');
        if (!dropdown) return;

        const isShown = dropdown.classList.contains('show');
        dropdown.classList.toggle('show', !isShown);
        if (btn) btn.setAttribute('aria-expanded', isShown ? 'false' : 'true');
    }

    function closeProfileDropdown() {
        const dropdown = document.getElementById('lpProfileDropdown');
        const btn = document.getElementById('lpProfileBtn');
        if (dropdown) dropdown.classList.remove('show');
        if (btn) btn.setAttribute('aria-expanded', 'false');
    }

    document.addEventListener('click', (event) => {
        const dropdown = document.getElementById('lpProfileDropdown');
        const btn = document.getElementById('lpProfileBtn');
        if (dropdown && dropdown.classList.contains('show')) {
            if (!dropdown.contains(event.target) && (!btn || !btn.contains(event.target))) {
                closeProfileDropdown();
            }
        }
    });
    document.addEventListener('DOMContentLoaded', () => {
        // Clean up legacy localStorage caches so DB is always the single source of truth
        try {
            localStorage.removeItem('pk_penjual_barang');
            localStorage.removeItem('pk_user_riwayat');
        } catch(e) {}

        if (selectedCategory) {
            document.querySelectorAll('#sidebarCatList .pk-filter-item').forEach(el => {
                if (el.innerText.trim().toLowerCase() === selectedCategory.toLowerCase()) {
                    el.classList.add('active');
                }
            });
        }
        if (selectedCategory || initialSearch) {
            applyCatalogFilters();
        }
    });
</script>
<?php $script = Component::endFlush() ?>


<?php Component::include("user/footer", compact('script')); ?>