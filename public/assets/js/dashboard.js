/**
 * PreLovedHub - Marketplace Dashboard Management
 * Handles catalog display, category filtering, search, sorting, modal detail,
 * sell modal, and loading profile preferences from registration.
 */

const sampleProducts = [
    {
        id: 1,
        title: "Kalkulator Scientific Casio fx-991EX ClassWiz",
        category: "ATK",
        condition: "95% Mulus",
        price: 140000,
        originalPrice: 320000,
        image: "https://images.unsplash.com/photo-1584438784894-089d6a62b8fa?auto=format&fit=crop&q=80&w=600",
        campus: "ITB",
        campusFullName: "Institut Teknologi Bandung (ITB)",
        location: "Coblong, Bandung",
        seller: "Rian Saputra",
        desc: "Kalkulator wajib untuk anak teknik dan sains. Tombol responsif semua, solar cell lancar, lengkap dengan case pelindung asli."
    },
    {
        id: 2,
        title: "Monitor IPS LG 24MK600 75Hz Borderless",
        category: "Elektronik",
        condition: "Like New",
        price: 850000,
        originalPrice: 1600000,
        image: "https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?auto=format&fit=crop&q=80&w=600",
        campus: "UI",
        campusFullName: "Universitas Indonesia (UI)",
        location: "Beji, Depok",
        seller: "Dina Maulida",
        desc: "Pemakaian 6 bulan untuk skripsi. Layar bening tanpa dead pixel. Bonus kabel HDMI dan adaptor original."
    },
    {
        id: 3,
        title: "Kasur Inoac Single 90x200 + Bonus Bantal Kost",
        category: "Perlengkapan Kost",
        condition: "Layak Pakai",
        price: 195000,
        originalPrice: 550000,
        image: "https://images.unsplash.com/photo-1540518614846-7ede433c4ef0?auto=format&fit=crop&q=80&w=600",
        campus: "UGM",
        campusFullName: "Universitas Gadjah Mada (UGM)",
        location: "Sleman, DIY",
        seller: "Agung Wicaksono",
        desc: "Busa masih padat tidak kempes. Dijual cepat karena wisuda dan mau pindah kota minggu depan."
    },
    {
        id: 4,
        title: "Buku Kalkulus Purcell Edisi 9 Jilid 1 & 2 Lengkap",
        category: "Buku",
        condition: "95% Mulus",
        price: 80000,
        originalPrice: 280000,
        image: "https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&q=80&w=600",
        campus: "UI",
        campusFullName: "Universitas Indonesia (UI)",
        location: "Pondok Cina, Depok",
        seller: "Fadhil Akbar",
        desc: "Buku teks wajib TPB/Semester 1. Sudah disampul mika rapi, halaman lengkap tanpa robek, hanya ada sedikit stabilo rumus penting."
    },
    {
        id: 5,
        title: "Denim Jacket Vintage Oversized Unisex Size L",
        category: "Pakaian",
        condition: "90% Mulus",
        price: 85000,
        originalPrice: 240000,
        image: "https://images.unsplash.com/photo-1576995853123-5a10305d93c0?auto=format&fit=crop&q=80&w=600",
        campus: "Unpad",
        campusFullName: "Universitas Padjadjaran (Unpad)",
        location: "Jatinangor, Sumedang",
        seller: "Clarissa Putri",
        desc: "Bahan jeans tebal premium, warna masih pekat, baru dipakai 3 kali untuk acara kampus."
    },
    {
        id: 6,
        title: "Keyboard Mechanical RGB Blue Switch Tenkeyless",
        category: "Elektronik",
        condition: "Like New",
        price: 175000,
        originalPrice: 380000,
        image: "https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=600",
        campus: "ITS",
        campusFullName: "Institut Teknologi Sepuluh Nopember (ITS)",
        location: "Sukolilo, Surabaya",
        seller: "Bagus Tri",
        desc: "Semua switch berfungsi normal, lampu RGB banyak mode, kabel braided kuat anti kusut."
    },
    {
        id: 7,
        title: "Dispenser Galon Bawah Miyako Panas & Dingin",
        category: "Perlengkapan Kost",
        condition: "Layak Pakai",
        price: 260000,
        originalPrice: 750000,
        image: "https://images.unsplash.com/photo-1585338107529-13afc5f02586?auto=format&fit=crop&q=80&w=600",
        campus: "UI",
        campusFullName: "Universitas Indonesia (UI)",
        location: "Kukusan, Depok",
        seller: "Nadia Rahma",
        desc: "Pemanas air sangat cepat, sangat cocok untuk seduh kopi dan mie instan di kosan."
    },
    {
        id: 8,
        title: "Gitar Akustik Yamaha F310 Original + Tas Busa",
        category: "Lainnya",
        condition: "95% Mulus",
        price: 520000,
        originalPrice: 1200000,
        image: "https://images.unsplash.com/photo-1510915361894-db8b60106cb1?auto=format&fit=crop&q=80&w=600",
        campus: "UGM",
        campusFullName: "Universitas Gadjah Mada (UGM)",
        location: "Bulaksumur, Yogyakarta",
        seller: "Kevin Sanjaya",
        desc: "Senar baru diganti D'Addario, neck lurus tidak melengkung, suara akustik jernih dan empuk."
    }
];

let activeCategory = 'all';
let productsList = [...sampleProducts];

// Rupiah Formatter
function formatRupiah(num) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num);
}

// Render Products Grid
function renderCatalog(items) {
    const grid = document.getElementById('catalogGrid');
    const empty = document.getElementById('catalogEmpty');
    const countEl = document.getElementById('catalogCount');

    if (!grid) return;

    if (items.length === 0) {
        grid.innerHTML = '';
        if (empty) empty.style.display = 'block';
        if (countEl) countEl.innerText = '0';
        return;
    }

    if (empty) empty.style.display = 'none';
    if (countEl) countEl.innerText = items.length;

    grid.innerHTML = items.map(item => `
        <div class="product-card">
            <div>
                <div class="product-img-wrapper">
                    <img src="${item.image}" alt="${item.title}" loading="lazy">
                    <span class="badge-condition">${item.condition}</span>
                    <span class="badge-category">${item.category}</span>
                </div>
                <div class="product-body">
                    <div class="product-location">
                        📍 ${item.campus} - ${item.location}
                    </div>
                    <h3 class="product-title" title="${item.title}">${item.title}</h3>
                    <p class="product-desc">${item.desc}</p>
                </div>
            </div>
            <div class="product-footer">
                <div class="product-price-box">
                    ${item.originalPrice ? `<span class="orig-price">${formatRupiah(item.originalPrice)}</span>` : ''}
                    <div class="cur-price">${formatRupiah(item.price)}</div>
                </div>
                <button onclick="openProductDetail(${item.id})" class="btn btn-sm btn-dark">
                    👁️ Detail
                </button>
            </div>
        </div>
    `).join('');
}

// Set Active Category Filter
function setCategory(categoryName) {
    activeCategory = categoryName;

    const btns = document.querySelectorAll('.filter-chip-btn');
    btns.forEach(b => {
        if (b.getAttribute('data-category') === categoryName) {
            b.classList.add('active');
        } else {
            b.classList.remove('active');
        }
    });

    applyFilters();
}

// Filter and Search logic
function applyFilters() {
    const searchVal = (document.getElementById('dashSearch')?.value || '').toLowerCase().trim();
    const campusVal = document.getElementById('dashFilterCampus')?.value || 'all';
    const conditionVal = document.getElementById('dashFilterCondition')?.value || 'all';
    const sortVal = document.getElementById('dashSort')?.value || 'newest';

    let result = productsList.filter(item => {
        const matchCategory = (activeCategory === 'all') || (item.category.toLowerCase() === activeCategory.toLowerCase());
        const matchSearch = !searchVal || 
            item.title.toLowerCase().includes(searchVal) || 
            item.desc.toLowerCase().includes(searchVal) || 
            item.campusFullName.toLowerCase().includes(searchVal) ||
            item.location.toLowerCase().includes(searchVal);
        const matchCampus = (campusVal === 'all') || (item.campus === campusVal);
        const matchCondition = (conditionVal === 'all') || (item.condition.toLowerCase().includes(conditionVal.toLowerCase()));

        return matchCategory && matchSearch && matchCampus && matchCondition;
    });

    if (sortVal === 'price-low') {
        result.sort((a, b) => a.price - b.price);
    } else if (sortVal === 'price-high') {
        result.sort((a, b) => b.price - a.price);
    } else {
        result.sort((a, b) => b.id - a.id);
    }

    renderCatalog(result);
}

function resetAllFilters() {
    if (document.getElementById('dashSearch')) document.getElementById('dashSearch').value = '';
    if (document.getElementById('dashFilterCampus')) document.getElementById('dashFilterCampus').value = 'all';
    if (document.getElementById('dashFilterCondition')) document.getElementById('dashFilterCondition').value = 'all';
    if (document.getElementById('dashSort')) document.getElementById('dashSort').value = 'newest';
    setCategory('all');
}

// Modal Detail
function openProductDetail(id) {
    const item = productsList.find(p => p.id === id);
    if (!item) return;

    document.getElementById('modalCatBadge').innerText = item.category;
    document.getElementById('modalProductImg').src = item.image;
    document.getElementById('modalConditionBadge').innerText = item.condition;
    document.getElementById('modalProductTitle').innerText = item.title;
    document.getElementById('modalProductDesc').innerText = item.desc;
    document.getElementById('modalProductPrice').innerText = formatRupiah(item.price);
    document.getElementById('modalProductOrigPrice').innerText = item.originalPrice ? formatRupiah(item.originalPrice) : '';
    document.getElementById('modalSellerName').innerText = item.seller;
    document.getElementById('modalSellerCampus').innerText = `${item.campusFullName} - ${item.location}`;
    document.getElementById('modalWhatsAppBtn').href = `https://wa.me/6281234567890?text=${encodeURIComponent(`Halo ${item.seller}, saya tertarik dengan barang preloved "${item.title}" di PreLovedHub.`)}`;

    document.getElementById('detailProductModal').classList.add('show');
}

function closeProductDetail() {
    document.getElementById('detailProductModal').classList.remove('show');
}

// Modal Sell Item
function openSellModal() {
    document.getElementById('sellProductModal').classList.add('show');
}

function closeSellModal() {
    document.getElementById('sellProductModal').classList.remove('show');
}

function handlePostItem(event) {
    event.preventDefault();
    const user = JSON.parse(localStorage.getItem('preloved_user') || '{}');

    const newItem = {
        id: Date.now(),
        title: document.getElementById('newTitle').value,
        category: document.getElementById('newCategory').value,
        condition: document.getElementById('newCondition').value,
        price: parseInt(document.getElementById('newPrice').value) || 0,
        originalPrice: parseInt(document.getElementById('newOrigPrice').value) || null,
        image: "https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?auto=format&fit=crop&q=80&w=600",
        campus: user.kampus ? user.kampus.split(' ')[0] : 'UI',
        campusFullName: user.kampus || 'Universitas Indonesia (UI)',
        location: user.kecamatan ? (user.kelurahan ? `${user.kelurahan}, ${user.kecamatan}` : user.kecamatan) : 'Depok',
        seller: user.nama || 'Pengguna PreLovedHub',
        desc: document.getElementById('newDesc').value
    };

    productsList.unshift(newItem);
    closeSellModal();
    applyFilters();
    alert('Sukses! Barang preloved Anda berhasil diposting ke marketplace.');
}

// Load user registration profile
function loadUserProfile() {
    const raw = localStorage.getItem('preloved_user');
    if (raw) {
        try {
            const u = JSON.parse(raw);
            if (u.nama) {
                if (document.getElementById('headerUserName')) document.getElementById('headerUserName').innerText = u.nama;
                if (document.getElementById('dashWelcomeName')) document.getElementById('dashWelcomeName').innerText = u.nama;
            }
            if (u.kampus) {
                if (document.getElementById('headerUserCampus')) document.getElementById('headerUserCampus').innerText = u.kampus;
                if (document.getElementById('dashWelcomeCampus')) document.getElementById('dashWelcomeCampus').innerText = u.kampus;
            }
            if (u.kecamatan) {
                const domisiliText = u.kelurahan ? `${u.kelurahan}, ${u.kecamatan}` : u.kecamatan;
                if (document.getElementById('dashWelcomeDomisili')) document.getElementById('dashWelcomeDomisili').innerText = domisiliText;
            }
            if (u.foto && document.getElementById('headerAvatar')) {
                document.getElementById('headerAvatar').src = u.foto;
            }
            if (u.preferensi && u.preferensi.length > 0 && document.getElementById('dashPrefChips')) {
                document.getElementById('dashPrefChips').innerHTML = u.preferensi.map(p => `
                    <span class="chip">${p}</span>
                `).join('');
            }
        } catch (e) {
            console.error('Error loading user profile', e);
        }
    }

    // Check query parameters
    const urlParams = new URLSearchParams(window.location.search);
    const searchQuery = urlParams.get('q');
    if (searchQuery) {
        const searchInput = document.getElementById('dashSearch');
        if (searchInput) {
            searchInput.value = searchQuery;
            applyFilters();
            return;
        }
    }
    const cat = urlParams.get('category');
    if (cat) {
        const catMap = {
            'buku': 'Buku',
            'elektronik': 'Elektronik',
            'atk': 'ATK',
            'kost': 'Perlengkapan Kost',
            'pakaian': 'Pakaian',
            'lainnya': 'Lainnya'
        };
        if (catMap[cat.toLowerCase()]) {
            setCategory(catMap[cat.toLowerCase()]);
            return;
        }
    }

    renderCatalog(productsList);
}

document.addEventListener('DOMContentLoaded', () => {
    loadUserProfile();
});


function toggleDashDropdown(event) {
    if (event) event.stopPropagation();
    const dropdown = document.getElementById('dashDropdown');
    if (dropdown) {
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }
}

document.addEventListener('click', (e) => {
    const dropdown = document.getElementById('dashDropdown');
    if (dropdown && dropdown.style.display === 'block') {
        if (!dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    }
});
