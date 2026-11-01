/**
 * Pasar Kampus - Dashboard Penjual Management
 * Handles 'Barang Jualan Saya', 'Tambah Barang Baru', and 'Detail Pengajuan Masuk'
 * Colors: #1D356D, #FBBF24, #FFFFFF
 */

// Initial seed data if not present in localStorage
const defaultSellerProducts = [
    {
        id: 1,
        title: "Buku Kalkulus Purcell Edisi 9",
        desc: "Buku teks wajib matematika teknik. Bersih dan tersampul rapi.",
        price: 50000,
        condition: "Sangat Bagus",
        category: "Buku",
        status: "Tersedia",
        image: "https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&q=80&w=200",
        pengajuan: [
            { id: 101, buyerName: "Budi (2141720001)", campus: "UB", time: "12 Okt 2026, 14:20", status: "PENDING", phone: "081298765432" },
            { id: 102, buyerName: "Andi (2241720012)", campus: "UM", time: "11 Okt 2026, 09:15", status: "REJECTED", phone: "081234567890" }
        ]
    },
    {
        id: 2,
        title: "Kipas Angin Meja Portabel",
        desc: "Angin kencang 3 kecepatan, hemat listrik untuk kamar kos.",
        price: 75000,
        condition: "Baik",
        category: "Elektronik",
        status: "Process",
        image: "https://images.unsplash.com/photo-1585338107529-13afc5f02586?auto=format&fit=crop&q=80&w=200",
        pengajuan: [
            { id: 103, buyerName: "Citra (2141720088)", campus: "UMM", time: "14 Okt 2026, 11:30", status: "PENDING", phone: "085712345678" }
        ]
    }
];

let sellerProducts = [];
let currentViewingProductId = null;
let uploadedPhotoUrls = [];

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', () => {
    initSellerData();
    loadSellerProfile();
    renderSellerProducts();
});

// Initialize data from localStorage
function initSellerData() {
    const stored = localStorage.getItem('pasar_seller_products');
    if (stored) {
        try {
            sellerProducts = JSON.parse(stored);
        } catch (e) {
            sellerProducts = [...defaultSellerProducts];
        }
    } else {
        sellerProducts = [...defaultSellerProducts];
        saveSellerData();
    }
}

function saveSellerData() {
    localStorage.setItem('pasar_seller_products', JSON.stringify(sellerProducts));
}

// Load current user profile from register/login
function loadSellerProfile() {
    const raw = localStorage.getItem('preloved_user');
    if (raw) {
        try {
            const u = JSON.parse(raw);
            if (u.nama && document.getElementById('sellerUserName')) {
                document.getElementById('sellerUserName').innerText = `${u.nama} (Penjual)`;
            }
            if (u.foto && document.getElementById('sellerUserAvatar')) {
                document.getElementById('sellerUserAvatar').src = u.foto;
            }
        } catch (e) {
            console.error(e);
        }
    }
}

// Format Rupiah Currency
function formatRupiah(num) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(num);
}

// Switch View Panels
function switchSellerView(viewName) {
    document.querySelectorAll('.seller-view-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.seller-nav-link').forEach(l => l.classList.remove('active'));

    if (viewName === 'dashboard') {
        document.getElementById('viewBarangSaya').classList.add('active');
        document.getElementById('navTabDashboard').classList.add('active');
        renderSellerProducts();
    } else if (viewName === 'detail-pengajuan') {
        document.getElementById('viewDetailPengajuan').classList.add('active');
    } else if (viewName === 'all-pengajuan') {
        document.getElementById('viewAllPengajuan').classList.add('active');
        document.getElementById('navTabPengajuan').classList.add('active');
        renderAllPengajuan();
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Render Products Table & Stats
function renderSellerProducts() {
    const tbody = document.getElementById('sellerProductTableBody');
    const emptyState = document.getElementById('sellerProductEmpty');

    if (!tbody) return;

    // Update Stats
    const totalCount = sellerProducts.length;
    const tersediaCount = sellerProducts.filter(p => p.status === 'Tersedia').length;
    const processCount = sellerProducts.filter(p => p.status === 'Process').length;

    if (document.getElementById('statTotalBarang')) document.getElementById('statTotalBarang').innerText = totalCount;
    if (document.getElementById('statTersedia')) document.getElementById('statTersedia').innerText = tersediaCount;
    if (document.getElementById('statProcess')) document.getElementById('statProcess').innerText = processCount;

    if (sellerProducts.length === 0) {
        tbody.innerHTML = '';
        if (emptyState) emptyState.style.display = 'block';
        return;
    }

    if (emptyState) emptyState.style.display = 'none';

    tbody.innerHTML = sellerProducts.map(p => {
        const pengajuanCount = (p.pengajuan || []).length;
        const pendingCount = (p.pengajuan || []).filter(item => item.status === 'PENDING').length;
        const statusBadgeClass = p.status === 'Tersedia' ? 'status-tersedia' : 'status-process';

        return `
            <tr>
                <td style="font-weight: 700; color: var(--pbl-navy);">${p.id}</td>
                <td>
                    <img src="${p.image || 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&q=80&w=200'}" alt="${p.title}" class="product-thumbnail">
                </td>
                <td>
                    <div style="font-weight: 700; color: var(--pbl-navy);">${p.title}</div>
                    <div style="font-size: 0.75rem; color: var(--pbl-muted);">${p.category} &bull; ${p.condition}</div>
                </td>
                <td style="font-weight: 700; color: #047857;">${formatRupiah(p.price)}</td>
                <td>
                    <span class="status-badge ${statusBadgeClass}">
                        ${p.status === 'Tersedia' ? '●' : '⏳'} ${p.status}
                    </span>
                </td>
                <td>
                    <div style="display: flex; gap: 4px; align-items: center; flex-wrap: wrap;">
                        <button type="button" onclick="openEditModal(${p.id})" class="table-action-btn table-action-edit" title="Edit Barang">
                            ✏️ Edit
                        </button>
                        <button type="button" onclick="deleteProduct(${p.id})" class="table-action-btn table-action-del" title="Hapus Barang">
                            🗑️ Hapus
                        </button>
                        <button type="button" onclick="viewProductPengajuan(${p.id})" class="table-action-btn table-action-view" title="Lihat Pengajuan Masuk">
                            👁️ Lihat (${pengajuanCount}${pendingCount > 0 ? ` <span style="color:#b45309;">• ${pendingCount} baru</span>` : ''})
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

// Open Detail Pengajuan Masuk for a specific item
function viewProductPengajuan(productId) {
    const prod = sellerProducts.find(p => p.id === productId);
    if (!prod) return;

    currentViewingProductId = productId;

    const titleEl = document.getElementById('pengajuanCardTitle');
    if (titleEl) {
        titleEl.innerText = `Daftar Pengajuan untuk: ${prod.title} (ID: ${prod.id})`;
    }

    renderPengajuanTable(prod.pengajuan || []);
    switchSellerView('detail-pengajuan');
}

// Render Pengajuan Table for active viewing item
function renderPengajuanTable(pengajuanList) {
    const tbody = document.getElementById('pengajuanTableBody');
    const emptyState = document.getElementById('pengajuanEmpty');

    if (!tbody) return;

    if (!pengajuanList || pengajuanList.length === 0) {
        tbody.innerHTML = '';
        if (emptyState) emptyState.style.display = 'block';
        return;
    }

    if (emptyState) emptyState.style.display = 'none';

    tbody.innerHTML = pengajuanList.map(req => {
        let badgeClass = 'status-pending';
        let actionButtons = '-';

        if (req.status === 'PENDING') {
            badgeClass = 'status-pending';
            actionButtons = `
                <div style="display: flex; gap: 8px;">
                    <button type="button" onclick="handleAcceptPengajuan(${currentViewingProductId}, ${req.id})" class="table-action-btn table-action-accept">
                        ✓ Terima
                    </button>
                    <button type="button" onclick="handleRejectPengajuan(${currentViewingProductId}, ${req.id})" class="table-action-btn table-action-reject">
                        ✕ Tolak
                    </button>
                </div>
            `;
        } else if (req.status === 'ACCEPTED' || req.status === 'PROCESS') {
            badgeClass = 'status-process';
            actionButtons = `
                <a href="https://wa.me/${(req.phone || '6281234567890').replace(/^0/, '62')}?text=${encodeURIComponent(`Halo ${req.buyerName}, pengajuan pembelian barang Anda telah saya terima.`)}" target="_blank" class="table-action-btn" style="background:#25D366; color:#fff; border-radius:8px; padding:6px 10px;">
                    💬 Chat WA
                </a>
            `;
        } else if (req.status === 'REJECTED') {
            badgeClass = 'status-rejected';
            actionButtons = `<span style="color: var(--pbl-muted); font-size: 0.8rem;">Ditolak</span>`;
        }

        return `
            <tr>
                <td style="font-weight: 700; color: var(--pbl-navy);">${req.buyerName}</td>
                <td><span style="font-weight: 600; background: #e0e7ff; color: #3730a3; padding: 3px 8px; border-radius: 6px; font-size: 0.75rem;">${req.campus}</span></td>
                <td style="color: var(--pbl-muted);">${req.time}</td>
                <td><span class="status-badge ${badgeClass}">${req.status}</span></td>
                <td>${actionButtons}</td>
            </tr>
        `;
    }).join('');
}

// Render All Pengajuan Masuk across all products
function renderAllPengajuan() {
    const tbody = document.getElementById('allPengajuanTableBody');
    const emptyState = document.getElementById('allPengajuanEmpty');

    if (!tbody) return;

    let allReqs = [];
    sellerProducts.forEach(prod => {
        (prod.pengajuan || []).forEach(req => {
            allReqs.push({
                ...req,
                productId: prod.id,
                productTitle: prod.title
            });
        });
    });

    if (allReqs.length === 0) {
        tbody.innerHTML = '';
        if (emptyState) emptyState.style.display = 'block';
        return;
    }

    if (emptyState) emptyState.style.display = 'none';

    tbody.innerHTML = allReqs.map(req => {
        let badgeClass = 'status-pending';
        let actionButtons = '-';

        if (req.status === 'PENDING') {
            badgeClass = 'status-pending';
            actionButtons = `
                <div style="display: flex; gap: 8px;">
                    <button type="button" onclick="handleAcceptPengajuan(${req.productId}, ${req.id})" class="table-action-btn table-action-accept">
                        ✓ Terima
                    </button>
                    <button type="button" onclick="handleRejectPengajuan(${req.productId}, ${req.id})" class="table-action-btn table-action-reject">
                        ✕ Tolak
                    </button>
                </div>
            `;
        } else if (req.status === 'ACCEPTED' || req.status === 'PROCESS') {
            badgeClass = 'status-process';
            actionButtons = `
                <a href="https://wa.me/${(req.phone || '6281234567890').replace(/^0/, '62')}?text=${encodeURIComponent(`Halo ${req.buyerName}, pengajuan pembelian barang "${req.productTitle}" telah saya terima.`)}" target="_blank" class="table-action-btn" style="background:#25D366; color:#fff; border-radius:8px; padding:6px 10px;">
                    💬 Chat WA
                </a>
            `;
        } else if (req.status === 'REJECTED') {
            badgeClass = 'status-rejected';
            actionButtons = `<span style="color: var(--pbl-muted); font-size: 0.8rem;">Ditolak</span>`;
        }

        return `
            <tr>
                <td style="font-weight: 700; color: var(--pbl-navy);">
                    ${req.productTitle}
                    <div style="font-size:0.7rem; color:var(--pbl-muted);">ID: ${req.productId}</div>
                </td>
                <td style="font-weight: 600;">${req.buyerName}</td>
                <td><span style="font-weight: 600; background: #e0e7ff; color: #3730a3; padding: 3px 8px; border-radius: 6px; font-size: 0.75rem;">${req.campus}</span></td>
                <td style="color: var(--pbl-muted);">${req.time}</td>
                <td><span class="status-badge ${badgeClass}">${req.status}</span></td>
                <td>${actionButtons}</td>
            </tr>
        `;
    }).join('');
}

// Action: Accept Pengajuan
function handleAcceptPengajuan(productId, pengajuanId) {
    const prod = sellerProducts.find(p => p.id === productId);
    if (!prod) return;

    const req = (prod.pengajuan || []).find(r => r.id === pengajuanId);
    if (!req) return;

    if (confirm(`Apakah Anda yakin ingin menerima pengajuan dari ${req.buyerName}?`)) {
        req.status = 'PROCESS';
        prod.status = 'Process'; // Update parent item status to Process
        saveSellerData();

        alert(`Sukses! Pengajuan dari ${req.buyerName} diterima. Status barang diubah menjadi "Process". Anda sekarang dapat berkoordinasi via WhatsApp.`);

        // Re-render
        if (currentViewingProductId === productId) {
            renderPengajuanTable(prod.pengajuan);
        } else {
            renderAllPengajuan();
        }
        renderSellerProducts();
    }
}

// Action: Reject Pengajuan
function handleRejectPengajuan(productId, pengajuanId) {
    const prod = sellerProducts.find(p => p.id === productId);
    if (!prod) return;

    const req = (prod.pengajuan || []).find(r => r.id === pengajuanId);
    if (!req) return;

    if (confirm(`Apakah Anda yakin ingin menolak pengajuan dari ${req.buyerName}?`)) {
        req.status = 'REJECTED';
        saveSellerData();

        // Re-render
        if (currentViewingProductId === productId) {
            renderPengajuanTable(prod.pengajuan);
        } else {
            renderAllPengajuan();
        }
        renderSellerProducts();
    }
}

// Modal Form Tambah / Edit Barang
function openAddModal() {
    document.getElementById('editProductId').value = '';
    document.getElementById('modalFormTitle').innerText = 'Tambah Barang Baru';
    document.getElementById('sellerProductForm').reset();
    document.getElementById('photoPreviewContainer').innerHTML = '';
    uploadedPhotoUrls = [];
    document.getElementById('sellerProductModal').classList.add('active');
}

function openEditModal(productId) {
    const prod = sellerProducts.find(p => p.id === productId);
    if (!prod) return;

    document.getElementById('editProductId').value = prod.id;
    document.getElementById('modalFormTitle').innerText = 'Edit Barang Jualan';
    document.getElementById('prodName').value = prod.title;
    document.getElementById('prodDesc').value = prod.desc || '';
    document.getElementById('prodPrice').value = prod.price;
    document.getElementById('prodCategory').value = prod.category;

    // Set radio condition
    const radios = document.querySelectorAll('input[name="prodCondition"]');
    radios.forEach(r => {
        r.checked = (r.value === prod.condition);
    });

    // Preview current image
    uploadedPhotoUrls = [prod.image];
    const container = document.getElementById('photoPreviewContainer');
    container.innerHTML = `
        <img src="${prod.image}" class="photo-preview-item" alt="Foto">
    `;

    document.getElementById('sellerProductModal').classList.add('active');
}

function closeProductModal() {
    document.getElementById('sellerProductModal').classList.remove('active');
}

// Preview Uploaded Images (Supports multiple)
function previewProductImages(event) {
    const files = event.target.files;
    const container = document.getElementById('photoPreviewContainer');
    container.innerHTML = '';
    uploadedPhotoUrls = [];

    if (files && files.length > 0) {
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                uploadedPhotoUrls.push(e.target.result);
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'photo-preview-item';
                container.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    }
}

// Save Product (Add / Edit)
function handleSaveProduct(event) {
    event.preventDefault();

    const editId = document.getElementById('editProductId').value;
    const title = document.getElementById('prodName').value.trim();
    const desc = document.getElementById('prodDesc').value.trim();
    const price = parseInt(document.getElementById('prodPrice').value) || 0;
    const category = document.getElementById('prodCategory').value;
    const conditionRadio = document.querySelector('input[name="prodCondition"]:checked');
    const condition = conditionRadio ? conditionRadio.value : 'Sangat Bagus';

    const defaultImage = "https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&q=80&w=200";
    const image = (uploadedPhotoUrls.length > 0) ? uploadedPhotoUrls[0] : defaultImage;

    if (!title || price <= 0 || !category) {
        alert('Harap lengkapi semua kolom wajib dengan benar!');
        return;
    }

    if (editId) {
        // Update existing
        const index = sellerProducts.findIndex(p => p.id == editId);
        if (index !== -1) {
            sellerProducts[index].title = title;
            sellerProducts[index].desc = desc;
            sellerProducts[index].price = price;
            sellerProducts[index].category = category;
            sellerProducts[index].condition = condition;
            if (uploadedPhotoUrls.length > 0) {
                sellerProducts[index].image = image;
            }
            alert('Barang berhasil diperbarui!');
        }
    } else {
        // Add new
        const newProduct = {
            id: Date.now(),
            title: title,
            desc: desc,
            price: price,
            category: category,
            condition: condition,
            status: "Tersedia",
            image: image,
            pengajuan: []
        };
        sellerProducts.unshift(newProduct);
        alert('Sukses! Barang baru Anda berhasil disimpan dan ditayangkan di katalog Pasar Kampus.');
    }

    saveSellerData();
    closeProductModal();
    renderSellerProducts();
}

// Delete Product
function deleteProduct(productId) {
    const prod = sellerProducts.find(p => p.id === productId);
    if (!prod) return;

    if (confirm(`Apakah Anda yakin ingin menghapus barang "${prod.title}"?`)) {
        sellerProducts = sellerProducts.filter(p => p.id !== productId);
        saveSellerData();
        renderSellerProducts();
    }
}
