/**
 * Pasar Kampus - Superadmin Dashboard & Central Monitoring System
 * Handles CRUD for Users, Products, Categories, Reports, and System-wide COD Transactions
 */

// Initial Seed Data Keys in LocalStorage
const STORAGE_KEYS = {
    USERS: 'pk_admin_users',
    BARANG: 'pk_admin_barang',
    KATEGORI: 'pk_admin_kategori',
    REPORTS: 'pk_admin_reports',
    PENGAJUAN: 'pk_admin_pengajuan',
    CURRENT_TAB: 'pk_admin_active_tab'
};

// ==========================================
// 1. DATA INITIALIZATION / SEEDING
// ==========================================
function initAdminStorage() {
    if (!localStorage.getItem(STORAGE_KEYS.USERS)) {
        const seedUsers = [
            { id: 1, nama: "Andi Wijaya", nim: "2141720012", kampus: "Universitas Brawijaya", email: "andi@student.ub.ac.id", role: "Mahasiswa", status: "Aktif", sanksi: "" },
            { id: 2, nama: "Budi Santoso", nim: "2241760085", kampus: "Universitas Negeri Malang", email: "budi@student.um.ac.id", role: "Mahasiswa", status: "Suspend", sanksi: "Terbukti melanggar aturan hit-and-run 3x berturut-turut pada saat COD." },
            { id: 3, nama: "Citra Lestari", nim: "2341710156", kampus: "Univ. Muhammadiyah Malang", email: "citra@student.umm.ac.id", role: "Mahasiswa", status: "Aktif", sanksi: "" },
            { id: 4, nama: "Doni Pratama", nim: "2141720099", kampus: "Universitas Brawijaya", email: "doni@student.ub.ac.id", role: "Mahasiswa", status: "Banned", sanksi: "Terindikasi meminta transfer DP fiktif di luar ketentuan sistem." },
            { id: 5, nama: "Eka Rahma", nim: "2241730044", kampus: "Universitas Negeri Malang", email: "eka@student.um.ac.id", role: "Mahasiswa", status: "Aktif", sanksi: "" },
            { id: 6, nama: "Farhan Nugraha", nim: "2141720088", kampus: "Universitas Brawijaya", email: "farhan@student.ub.ac.id", role: "Mahasiswa", status: "Aktif", sanksi: "" }
        ];
        localStorage.setItem(STORAGE_KEYS.USERS, JSON.stringify(seedUsers));
    }

    if (!localStorage.getItem(STORAGE_KEYS.BARANG)) {
        const seedBarang = [
            {
                id: 10,
                nama: "Jas Lab UB Ukuran L (Bersih & Rapi)",
                penjual: "Andi Wijaya (@andi_ig)",
                kategori: "Pakaian",
                harga: "Rp 65.000",
                status: "Tersedia",
                alasanHapus: "",
                pengajuan: [
                    { pembeli: "Farhan (214...)", kampus: "UB", waktu: "28 Okt 2026", status: "SELESAI (CLOSED)" }
                ]
            },
            {
                id: 11,
                nama: "Helm Bogo Retro Cokelat Glossy",
                penjual: "Budi Santoso (@budi_ig)",
                kategori: "Lainnya",
                harga: "Rp 120.000",
                status: "Process",
                alasanHapus: "Barang terindikasi tidak sesuai dengan foto / palsu",
                pengajuan: [
                    { pembeli: "Doni Pratama (214...)", kampus: "UMM", waktu: "18 Okt 2026", status: "DI-ACC PENJUAL (PROCESS)" },
                    { pembeli: "Eka Rahma (224...)", kampus: "UB", waktu: "18 Okt 2026", status: "DITOLAK (CLOSED)" }
                ]
            },
            {
                id: 12,
                nama: "Kalkulator Ilmiah Casio fx-991EX Original",
                penjual: "Citra Lestari (@citra_ig)",
                kategori: "Elektronik",
                harga: "Rp 180.000",
                status: "Tersedia",
                alasanHapus: "",
                pengajuan: [
                    { pembeli: "Rian (234...)", kampus: "UM", waktu: "27 Okt 2026", status: "MENUNGGU KONFIRMASI" }
                ]
            },
            {
                id: 13,
                nama: "Buku Kalkulus Edisi 8 - James Stewart",
                penjual: "Farhan Nugraha (@farhan_ig)",
                kategori: "Buku",
                harga: "Rp 95.000",
                status: "Tersedia",
                alasanHapus: "",
                pengajuan: []
            },
            {
                id: 14,
                nama: "Kipas Angin Meja Mini USB",
                penjual: "Siti Rahma (@siti_ig)",
                kategori: "Perlengkapan Kos",
                harga: "Rp 40.000",
                status: "Dihapus",
                alasanHapus: "Barang terindikasi tidak sesuai dengan foto / palsu",
                pengajuan: []
            }
        ];
        localStorage.setItem(STORAGE_KEYS.BARANG, JSON.stringify(seedBarang));
    }

    if (!localStorage.getItem(STORAGE_KEYS.KATEGORI)) {
        const seedKategori = [
            { id: 1, nama: "Buku", deskripsi: "Diktat kuliah, buku ajar, komik & modul praktikum", jmlBarang: 120, status: "Aktif" },
            { id: 2, nama: "Pakaian", deskripsi: "Jas almamater, jas lab, kemeja sidang, jaket himpunan", jmlBarang: 85, status: "Aktif" },
            { id: 3, nama: "Elektronik", deskripsi: "Kalkulator, flashdisk, mouse, charger laptop", jmlBarang: 42, status: "Aktif" },
            { id: 4, nama: "Perlengkapan Kos", deskripsi: "Kipas angin, rak susun, ember cucian, jemuran portable", jmlBarang: 15, status: "Aktif" },
            { id: 5, nama: "Lainnya", deskripsi: "Barang serba-serbi kebutuhan perkuliahan mahasiswa", jmlBarang: 38, status: "Aktif" }
        ];
        localStorage.setItem(STORAGE_KEYS.KATEGORI, JSON.stringify(seedKategori));
    }

    if (!localStorage.getItem(STORAGE_KEYS.REPORTS)) {
        const seedReports = [
            {
                id: 90,
                pelapor: "Citra Lestari (@citra_ig)",
                terlapor: "Andi Wijaya (@andi_ig)",
                alasan: "Penipuan / Barang Palsu",
                deskripsi: "Barang tidak sesuai foto dan penjual memaksa COD di tempat yang sepi serta menolak membuka paket di tempat.",
                bukti: [
                    "https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&q=80&w=400",
                    "https://images.unsplash.com/photo-1512496015851-a90fb38ba796?auto=format&fit=crop&q=80&w=400"
                ],
                status: "Butuh Tinjauan",
                keputusan: ""
            },
            {
                id: 91,
                pelapor: "Doni Pratama (@doni_ig)",
                terlapor: "Budi Santoso (@budi_ig)",
                alasan: "Spam & Hit-and-Run COD",
                deskripsi: "Penjual berjanji COD helm di depan Gerbang UMM pukul 16:00, namun ditunggu hingga maghrib tidak hadir dan chat diblokir.",
                bukti: [
                    "https://images.unsplash.com/photo-1579389083078-4e7018379f7e?auto=format&fit=crop&q=80&w=400"
                ],
                status: "Menunggu",
                keputusan: ""
            }
        ];
        localStorage.setItem(STORAGE_KEYS.REPORTS, JSON.stringify(seedReports));
    }

    if (!localStorage.getItem(STORAGE_KEYS.PENGAJUAN)) {
        const seedPengajuan = [
            {
                id: "TRX-101",
                barangId: 11,
                barangNama: "Helm Bogo Retro Cokelat Glossy",
                penjual: "Budi Santoso (@budi_ig)",
                pembeli: "Doni Pratama (NIM: 2141720099)",
                kampus: "UMM",
                waktu: "18 Okt 2026 14:20",
                lokasiCod: "Gerbang 3 UMM Kampus 3",
                status: "DI-ACC PENJUAL (PROCESS)"
            },
            {
                id: "TRX-102",
                barangId: 11,
                barangNama: "Helm Bogo Retro Cokelat Glossy",
                penjual: "Budi Santoso (@budi_ig)",
                pembeli: "Eka Rahma (NIM: 2241730044)",
                kampus: "UB",
                waktu: "18 Okt 2026 11:05",
                lokasiCod: "Gazebo FIB UB",
                status: "DITOLAK (CLOSED)"
            },
            {
                id: "TRX-103",
                barangId: 10,
                barangNama: "Jas Lab UB Ukuran L",
                penjual: "Andi Wijaya (@andi_ig)",
                pembeli: "Farhan Nugraha (NIM: 2141720088)",
                kampus: "UB",
                waktu: "28 Okt 2026 09:15",
                lokasiCod: "Perpustakaan Pusat UB",
                status: "SELESAI (COD SUKSES)"
            },
            {
                id: "TRX-104",
                barangId: 12,
                barangNama: "Kalkulator Ilmiah Casio fx-991EX",
                penjual: "Citra Lestari (@citra_ig)",
                pembeli: "Rian (NIM: 2341790011)",
                kampus: "UM",
                waktu: "27 Okt 2026 16:40",
                lokasiCod: "Gedung Kuliah Bersama UM",
                status: "MENUNGGU RESPON"
            }
        ];
        localStorage.setItem(STORAGE_KEYS.PENGAJUAN, JSON.stringify(seedPengajuan));
    }
}

// Helpers to get & set data
function getUsers() {
    return JSON.parse(localStorage.getItem(STORAGE_KEYS.USERS)) || [];
}
function setUsers(data) {
    localStorage.setItem(STORAGE_KEYS.USERS, JSON.stringify(data));
}
function getBarang() {
    return JSON.parse(localStorage.getItem(STORAGE_KEYS.BARANG)) || [];
}
function setBarang(data) {
    localStorage.setItem(STORAGE_KEYS.BARANG, JSON.stringify(data));
}
function getKategori() {
    return JSON.parse(localStorage.getItem(STORAGE_KEYS.KATEGORI)) || [];
}
function setKategori(data) {
    localStorage.setItem(STORAGE_KEYS.KATEGORI, JSON.stringify(data));
}
function getReports() {
    return JSON.parse(localStorage.getItem(STORAGE_KEYS.REPORTS)) || [];
}
function setReports(data) {
    localStorage.setItem(STORAGE_KEYS.REPORTS, JSON.stringify(data));
}
function getPengajuan() {
    return JSON.parse(localStorage.getItem(STORAGE_KEYS.PENGAJUAN)) || [];
}
function setPengajuan(data) {
    localStorage.setItem(STORAGE_KEYS.PENGAJUAN, JSON.stringify(data));
}

// ==========================================
// 2. TAB NAVIGATION & VIEW SWITCHER
// ==========================================
function switchAdminTab(tabName) {
    // Hide all view panels
    const panels = document.querySelectorAll('.admin-view-panel');
    panels.forEach(p => p.classList.remove('active'));

    // Remove active class from nav links
    const links = document.querySelectorAll('.admin-nav-link');
    links.forEach(l => l.classList.remove('active'));

    // Activate selected panel & link
    const targetPanel = document.getElementById(`view-${tabName}`);
    const targetLink = document.getElementById(`nav-${tabName}`);
    if (targetPanel) targetPanel.classList.add('active');
    if (targetLink) targetLink.classList.add('active');

    // Update breadcrumb
    const breadcrumbLabel = document.getElementById('adminCurrentBreadcrumb');
    if (breadcrumbLabel) {
        const titles = {
            dashboard: 'Dashboard Analitik',
            users: 'Kelola Pengguna (User)',
            barang: 'Manajemen Barang Katalog',
            pengajuan: 'Monitoring Pengajuan Transaksi COD',
            report: 'Laporan Aduan Masuk',
            kategori: 'Master Kategori'
        };
        breadcrumbLabel.textContent = titles[tabName] || 'Dashboard';
    }

    // Save active tab
    localStorage.setItem(STORAGE_KEYS.CURRENT_TAB, tabName);

    // Refresh view specific data
    if (tabName === 'dashboard') renderDashboardView();
    if (tabName === 'users') renderUsersTable();
    if (tabName === 'barang') renderBarangTable();
    if (tabName === 'pengajuan') renderPengajuanTable();
    if (tabName === 'report') renderReportTable();
    if (tabName === 'kategori') renderKategoriTable();

    // Close mobile sidebar if open
    const sidebar = document.getElementById('adminSidebar');
    if (sidebar) sidebar.classList.remove('show-mobile');
}

function toggleMobileSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    if (sidebar) sidebar.classList.toggle('show-mobile');
}

// ==========================================
// 3. DASHBOARD ANALYTICS & PIE CHART
// ==========================================
function renderDashboardView() {
    const users = getUsers();
    const barang = getBarang();
    const kategori = getKategori();
    const reports = getReports();

    // Calculate real counts or wireframe counts
    const activeUsers = users.filter(u => u.status === 'Aktif').length;
    const pendingReports = reports.filter(r => r.status !== 'Selesai' && r.status !== 'Abaikan').length;

    // Stat cards values from wireframe
    document.getElementById('statTotalBarang').textContent = "1,245";
    document.getElementById('statJmlKategori').textContent = kategori.length.toString();
    document.getElementById('statJmlReport').textContent = (28 + pendingReports - 2).toString();
    document.getElementById('statJmlUser').textContent = "3,400";

    // Update Report badge in sidebar
    const repBadge = document.getElementById('sidebarReportBadge');
    if (repBadge) {
        repBadge.textContent = pendingReports;
        repBadge.style.display = pendingReports > 0 ? 'inline-block' : 'none';
    }
}

// ==========================================
// 4. CRUD USER (Kelola User)
// ==========================================
function renderUsersTable() {
    const users = getUsers();
    const tbody = document.getElementById('userTableBody');
    if (!tbody) return;

    const searchVal = (document.getElementById('userSearchInput')?.value || '').toLowerCase().trim();
    const filterKampus = document.getElementById('userFilterKampus')?.value || 'all';
    const filterStatus = document.getElementById('userFilterStatus')?.value || 'all';

    const filtered = users.filter(u => {
        const matchSearch = u.nama.toLowerCase().includes(searchVal) || 
                            u.nim.toLowerCase().includes(searchVal) ||
                            u.id.toString().includes(searchVal);
        const matchKampus = filterKampus === 'all' || u.kampus.toLowerCase().includes(filterKampus.toLowerCase());
        const matchStatus = filterStatus === 'all' || u.status.toLowerCase() === filterStatus.toLowerCase();
        return matchSearch && matchKampus && matchStatus;
    });

    if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 24px; color: #94A3B8;">Tidak ada data pengguna yang sesuai.</td></tr>`;
        return;
    }

    tbody.innerHTML = filtered.map(u => {
        let badgeClass = 'admin-badge-success';
        if (u.status === 'Suspend') badgeClass = 'admin-badge-warning';
        if (u.status === 'Banned') badgeClass = 'admin-badge-danger';

        const banToggleLabel = u.status === 'Banned' ? 'Unban' : 'Ban';
        const banToggleClass = u.status === 'Banned' ? 'btn-action-detail' : 'btn-action-danger';

        return `
            <tr>
                <td style="font-weight: 700; color: #1D356D;">#${u.id}</td>
                <td>
                    <div style="font-weight: 700; color: #0F172A;">${u.nama}</div>
                    <div style="font-size: 11.5px; color: #64748B;">${u.email}</div>
                </td>
                <td style="font-family: monospace; font-size: 13px;">${u.nim}</td>
                <td><span style="font-weight: 600; color: #334155;">${u.kampus}</span></td>
                <td><span class="admin-badge ${badgeClass}">${u.status}</span></td>
                <td>
                    <div class="admin-action-group">
                        <button type="button" class="btn-action btn-action-edit" onclick="openEditUserModal(${u.id})">
                            ✏️ Edit Status
                        </button>
                        <button type="button" class="btn-action ${banToggleClass}" onclick="quickToggleBanUser(${u.id})">
                            ${banToggleLabel}
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

// User Modal: Tambah Pengguna
function openAddUserModal() {
    document.getElementById('addUserForm').reset();
    document.getElementById('modalAddUser').classList.add('open');
}

function handleAddUserSubmit(e) {
    e.preventDefault();
    const nama = document.getElementById('newUserName').value.trim();
    const nim = document.getElementById('newUserNim').value.trim();
    const email = document.getElementById('newUserEmail').value.trim();
    const kampus = document.getElementById('newUserKampus').value;
    const roleRadio = document.querySelector('input[name="newUserRole"]:checked');
    const role = roleRadio ? roleRadio.value : 'Mahasiswa';

    if (!nama || !nim || !email) {
        alert('Harap lengkapi semua kolom form tambah pengguna!');
        return;
    }

    const users = getUsers();
    const newId = users.length > 0 ? Math.max(...users.map(u => u.id)) + 1 : 1;

    users.push({
        id: newId,
        nama: nama,
        nim: nim,
        email: email,
        kampus: kampus,
        role: role,
        status: "Aktif",
        sanksi: ""
    });

    setUsers(users);
    closeModal('modalAddUser');
    renderUsersTable();
    renderDashboardView();
    showAdminToast(`Pengguna baru "${nama}" berhasil ditambahkan!`);
}

// User Modal: Edit Status Pengguna
function openEditUserModal(userId) {
    const users = getUsers();
    const user = users.find(u => u.id === userId);
    if (!user) return;

    document.getElementById('editUserId').value = user.id;
    document.getElementById('editUserNameView').textContent = `${user.nama} (NIM: ${user.nim}) - [Read Only]`;
    document.getElementById('editUserKampusView').textContent = `${user.kampus} - [Read Only]`;
    document.getElementById('editUserSanksi').value = user.sanksi || '';

    // Check correct radio
    const statusRadio = document.querySelector(`input[name="editUserStatus"][value="${user.status}"]`);
    if (statusRadio) statusRadio.checked = true;

    document.getElementById('modalEditUser').classList.add('open');
}

function handleEditUserSubmit(e) {
    e.preventDefault();
    const id = parseInt(document.getElementById('editUserId').value);
    const selectedStatus = document.querySelector('input[name="editUserStatus"]:checked')?.value || 'Aktif';
    const sanksi = document.getElementById('editUserSanksi').value.trim();

    const users = getUsers();
    const idx = users.findIndex(u => u.id === id);
    if (idx !== -1) {
        users[idx].status = selectedStatus;
        users[idx].sanksi = sanksi;
        setUsers(users);
        closeModal('modalEditUser');
        renderUsersTable();
        renderDashboardView();
        showAdminToast(`Status pengguna #${id} diperbarui menjadi [${selectedStatus}]. Notifikasi dikirimkan ke email.`);
    }
}

function quickToggleBanUser(id) {
    const users = getUsers();
    const idx = users.findIndex(u => u.id === id);
    if (idx !== -1) {
        if (users[idx].status === 'Banned') {
            users[idx].status = 'Aktif';
            users[idx].sanksi = '';
            showAdminToast(`Akun #${id} (${users[idx].nama}) berhasil di-Unban.`);
        } else {
            const reason = prompt(`Masukkan alasan sanksi Banned untuk ${users[idx].nama}:`, "Pelanggaran berulang transaksi preloved kampus");
            if (reason === null) return; // user cancelled
            users[idx].status = 'Banned';
            users[idx].sanksi = reason || 'Pelanggaran ketentuan sistem Pasar Kampus.';
            showAdminToast(`Akun #${id} telah di-Banned.`);
        }
        setUsers(users);
        renderUsersTable();
        renderDashboardView();
    }
}

// ==========================================
// 5. CRUD BARANG (Manajemen Barang Katalog)
// ==========================================
function renderBarangTable() {
    const barang = getBarang();
    const tbody = document.getElementById('barangTableBody');
    if (!tbody) return;

    const searchVal = (document.getElementById('barangSearchInput')?.value || '').toLowerCase().trim();
    const filterKat = document.getElementById('barangFilterKategori')?.value || 'all';
    const filterStat = document.getElementById('barangFilterStatus')?.value || 'all';

    const filtered = barang.filter(b => {
        const matchSearch = b.nama.toLowerCase().includes(searchVal) ||
                            b.penjual.toLowerCase().includes(searchVal) ||
                            b.id.toString().includes(searchVal);
        const matchKat = filterKat === 'all' || b.kategori.toLowerCase() === filterKat.toLowerCase();
        const matchStat = filterStat === 'all' || b.status.toLowerCase() === filterStat.toLowerCase();
        return matchSearch && matchKat && matchStat;
    });

    if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 24px; color: #94A3B8;">Tidak ada data barang yang sesuai.</td></tr>`;
        return;
    }

    tbody.innerHTML = filtered.map(b => {
        let badgeClass = 'admin-badge-success';
        if (b.status === 'Process') badgeClass = 'admin-badge-warning';
        if (b.status === 'Dihapus') badgeClass = 'admin-badge-danger';
        if (b.status === 'Terjual') badgeClass = 'admin-badge-info';

        return `
            <tr>
                <td style="font-weight: 700; color: #1D356D;">#${b.id}</td>
                <td>
                    <div style="font-weight: 700; color: #0F172A;">${b.nama}</div>
                    <div style="font-size: 11.5px; color: #2563EB; font-weight: 600;">${b.harga}</div>
                </td>
                <td><span style="font-weight: 500; color: #475569;">${b.penjual}</span></td>
                <td><span class="admin-badge admin-badge-neutral">${b.kategori}</span></td>
                <td><span class="admin-badge ${badgeClass}">${b.status}</span></td>
                <td>
                    <div class="admin-action-group">
                        <button type="button" class="btn-action btn-action-detail" onclick="openDetailBarangModal(${b.id})">
                            👁️ Detail
                        </button>
                        <button type="button" class="btn-action btn-action-edit" onclick="openEditBarangModal(${b.id})">
                            ⚙️ Edit Status
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

// Modal Detail Barang & Riwayat Pengajuan
function openDetailBarangModal(id) {
    const barangList = getBarang();
    const item = barangList.find(b => b.id === id);
    if (!item) return;

    document.getElementById('detailBarangNama').textContent = `${item.nama} (ID: ${item.id})`;
    document.getElementById('detailBarangPenjual').textContent = item.penjual;
    document.getElementById('detailBarangStatus').textContent = item.status.toUpperCase();

    const tbody = document.getElementById('detailBarangPengajuanBody');
    if (tbody) {
        if (!item.pengajuan || item.pengajuan.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" style="text-align: center; color: #94A3B8; padding: 12px;">Belum ada riwayat pengajuan masuk untuk barang ini.</td></tr>`;
        } else {
            tbody.innerHTML = item.pengajuan.map(p => `
                <tr>
                    <td style="font-weight: 600;">${p.pembeli}</td>
                    <td><span class="admin-badge admin-badge-neutral">${p.kampus}</span></td>
                    <td style="color: #64748B;">${p.waktu}</td>
                    <td><span class="admin-badge admin-badge-info">${p.status}</span></td>
                </tr>
            `).join('');
        }
    }

    document.getElementById('modalDetailBarang').classList.add('open');
}

// Modal Edit Status Barang (Takedown / Biarkan)
function openEditBarangModal(id) {
    const barangList = getBarang();
    const item = barangList.find(b => b.id === id);
    if (!item) return;

    document.getElementById('editBarangId').value = item.id;
    document.getElementById('editBarangNamaView').textContent = `${item.nama} (ID: ${item.id}) - [Read Only]`;
    document.getElementById('editBarangPenjualView').textContent = `${item.penjual} - [Read Only]`;
    document.getElementById('editBarangAlasan').value = item.alasanHapus || 'Barang terindikasi tidak sesuai dengan foto / palsu';

    // Radio
    const isDeleted = item.status === 'Dihapus';
    const radioVal = isDeleted ? 'Dihapus' : 'Tersedia';
    const radio = document.querySelector(`input[name="editBarangTindakan"][value="${radioVal}"]`);
    if (radio) radio.checked = true;

    document.getElementById('modalEditBarang').classList.add('open');
}

function handleEditBarangSubmit(e) {
    e.preventDefault();
    const id = parseInt(document.getElementById('editBarangId').value);
    const tindakan = document.querySelector('input[name="editBarangTindakan"]:checked')?.value || 'Tersedia';
    const alasan = document.getElementById('editBarangAlasan').value.trim();

    const barangList = getBarang();
    const idx = barangList.findIndex(b => b.id === id);
    if (idx !== -1) {
        barangList[idx].status = tindakan === 'Dihapus' ? 'Dihapus' : 'Tersedia';
        barangList[idx].alasanHapus = tindakan === 'Dihapus' ? alasan : '';
        setBarang(barangList);
        closeModal('modalEditBarang');
        renderBarangTable();
        renderDashboardView();
        showAdminToast(`Status Barang #${id} diperbarui: [${barangList[idx].status}].`);
    }
}

// ==========================================
// 6. CRUD KATEGORI (Master Tag)
// ==========================================
function renderKategoriTable() {
    const kategori = getKategori();
    const tbody = document.getElementById('kategoriTableBody');
    if (!tbody) return;

    tbody.innerHTML = kategori.map(k => `
        <tr>
            <td style="font-weight: 700; color: #1D356D;">#${k.id}</td>
            <td>
                <div style="font-weight: 700; color: #0F172A;">${k.nama}</div>
                <div style="font-size: 12px; color: #64748B;">${k.deskripsi}</div>
            </td>
            <td><strong>${k.jmlBarang}</strong> Barang</td>
            <td><span class="admin-badge ${k.status === 'Aktif' ? 'admin-badge-success' : 'admin-badge-danger'}">${k.status}</span></td>
            <td>
                <div class="admin-action-group">
                    <button type="button" class="btn-action btn-action-edit" onclick="openEditKategoriModal(${k.id})">
                        ✏️ Edit
                    </button>
                    <button type="button" class="btn-action btn-action-danger" onclick="deleteKategori(${k.id})">
                        🗑️ Del
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function openAddKategoriModal() {
    document.getElementById('kategoriForm').reset();
    document.getElementById('kategoriModalTitle').textContent = 'TAMBAH KATEGORI BARU';
    document.getElementById('kategoriId').value = '';
    document.getElementById('modalKategori').classList.add('open');
}

function openEditKategoriModal(id) {
    const kategoriList = getKategori();
    const item = kategoriList.find(k => k.id === id);
    if (!item) return;

    document.getElementById('kategoriModalTitle').textContent = 'EDIT KATEGORI';
    document.getElementById('kategoriId').value = item.id;
    document.getElementById('kategoriNama').value = item.nama;
    document.getElementById('kategoriDeskripsi').value = item.deskripsi;
    
    const radio = document.querySelector(`input[name="kategoriStatus"][value="${item.status}"]`);
    if (radio) radio.checked = true;

    document.getElementById('modalKategori').classList.add('open');
}

function handleKategoriSubmit(e) {
    e.preventDefault();
    const idVal = document.getElementById('kategoriId').value;
    const nama = document.getElementById('kategoriNama').value.trim();
    const deskripsi = document.getElementById('kategoriDeskripsi').value.trim();
    const status = document.querySelector('input[name="kategoriStatus"]:checked')?.value || 'Aktif';

    if (!nama) {
        alert('Nama Kategori wajib diisi!');
        return;
    }

    const kategoriList = getKategori();

    if (idVal) {
        // Edit existing
        const idx = kategoriList.findIndex(k => k.id === parseInt(idVal));
        if (idx !== -1) {
            kategoriList[idx].nama = nama;
            kategoriList[idx].deskripsi = deskripsi;
            kategoriList[idx].status = status;
            showAdminToast(`Kategori "${nama}" berhasil diperbarui.`);
        }
    } else {
        // Add new
        const newId = kategoriList.length > 0 ? Math.max(...kategoriList.map(k => k.id)) + 1 : 1;
        kategoriList.push({
            id: newId,
            nama: nama,
            deskripsi: deskripsi,
            jmlBarang: 0,
            status: status
        });
        showAdminToast(`Kategori baru "${nama}" berhasil ditambahkan.`);
    }

    setKategori(kategoriList);
    closeModal('modalKategori');
    renderKategoriTable();
    renderDashboardView();
}

function deleteKategori(id) {
    if (!confirm(`Yakin ingin menghapus kategori #${id}?`)) return;
    let list = getKategori();
    list = list.filter(k => k.id !== id);
    setKategori(list);
    renderKategoriTable();
    renderDashboardView();
    showAdminToast(`Kategori #${id} telah dihapus.`);
}

// ==========================================
// 7. REPORT (Laporan Aduan Masuk & Tinjau)
// ==========================================
function renderReportTable() {
    const reports = getReports();
    const tbody = document.getElementById('reportTableBody');
    if (!tbody) return;

    if (reports.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 24px; color: #94A3B8;">Tidak ada aduan laporan masuk.</td></tr>`;
        return;
    }

    tbody.innerHTML = reports.map(r => `
        <tr>
            <td style="font-weight: 700; color: #1D356D;">#${r.id}</td>
            <td style="font-weight: 600;">${r.pelapor}</td>
            <td style="font-weight: 600; color: #DC2626;">${r.terlapor}</td>
            <td><span class="admin-badge admin-badge-warning">${r.alasan}</span></td>
            <td>
                <button type="button" class="btn-action btn-action-detail" onclick="openTinjauReportModal(${r.id})">
                    🖼️ [Img] Bukti
                </button>
            </td>
            <td>
                <span class="admin-badge ${r.status === 'Selesai' ? 'admin-badge-success' : 'admin-badge-danger'}">${r.status}</span>
            </td>
            <td>
                <div class="admin-action-group">
                    <button type="button" class="btn-action btn-action-edit" onclick="openTinjauReportModal(${r.id})">
                        🔍 Tinjau
                    </button>
                    <button type="button" class="btn-action btn-action-danger" onclick="deleteReport(${r.id})">
                        Del
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function openTinjauReportModal(id) {
    const reports = getReports();
    const rep = reports.find(r => r.id === id);
    if (!rep) return;

    document.getElementById('tinjauReportId').value = rep.id;
    document.getElementById('tinjauHeaderBadge').textContent = `Detail Laporan #${rep.id}`;
    document.getElementById('tinjauPelapor').textContent = rep.pelapor;
    document.getElementById('tinjauTerlapor').textContent = rep.terlapor;
    document.getElementById('tinjauAlasan').textContent = rep.alasan;
    document.getElementById('tinjauDeskripsi').textContent = `"${rep.deskripsi}"`;

    // Evidence images
    const gallery = document.getElementById('tinjauEvidenceGallery');
    if (gallery) {
        if (!rep.bukti || rep.bukti.length === 0) {
            gallery.innerHTML = `<span style="font-size: 12px; color: #94A3B8;">(Tidak ada foto bukti terlampir)</span>`;
        } else {
            gallery.innerHTML = rep.bukti.map((imgUrl, idx) => `
                <a href="${imgUrl}" target="_blank" title="Klik untuk memperbesar">
                    <img src="${imgUrl}" alt="Bukti ${idx+1}" class="admin-evidence-thumb">
                </a>
            `).join('');
        }
    }

    document.getElementById('modalTinjauReport').classList.add('open');
}

function handleEksekusiReportSubmit(e) {
    e.preventDefault();
    const id = parseInt(document.getElementById('tinjauReportId').value);
    const keputusan = document.querySelector('input[name="reportKeputusan"]:checked')?.value || 'Abaikan';

    const reports = getReports();
    const repIdx = reports.findIndex(r => r.id === id);
    if (repIdx !== -1) {
        reports[repIdx].status = keputusan === 'Abaikan' ? 'Diabaikan' : 'Selesai';
        reports[repIdx].keputusan = keputusan;
        setReports(reports);

        // Synchronize with Users list if punishment was applied
        if (keputusan === 'Suspend' || keputusan === 'Banned') {
            const terlaporName = reports[repIdx].terlapor;
            const users = getUsers();
            const matchedUser = users.find(u => terlaporName.toLowerCase().includes(u.nama.toLowerCase()) || 
                                                 terlaporName.toLowerCase().includes(u.email.toLowerCase()));
            if (matchedUser) {
                matchedUser.status = keputusan;
                matchedUser.sanksi = `Sanksi dari Laporan Aduan #${id}: ${reports[repIdx].alasan}.`;
                setUsers(users);
                renderUsersTable();
            }
        }

        closeModal('modalTinjauReport');
        renderReportTable();
        renderDashboardView();
        showAdminToast(`Keputusan laporan #${id} dieksekusi: [${keputusan}].`);
    }
}

function deleteReport(id) {
    if (!confirm(`Hapus data laporan aduan #${id}?`)) return;
    let list = getReports();
    list = list.filter(r => r.id !== id);
    setReports(list);
    renderReportTable();
    renderDashboardView();
    showAdminToast(`Laporan aduan #${id} telah dihapus.`);
}

// ==========================================
// 8. MONITORING PENGAJUAN (Seluruh Website)
// ==========================================
function renderPengajuanTable() {
    const list = getPengajuan();
    const tbody = document.getElementById('pengajuanTableBody');
    if (!tbody) return;

    const filterKampus = document.getElementById('pengajuanFilterKampus')?.value || 'all';
    const filterStatus = document.getElementById('pengajuanFilterStatus')?.value || 'all';

    const filtered = list.filter(p => {
        const matchKampus = filterKampus === 'all' || p.kampus.toLowerCase() === filterKampus.toLowerCase();
        const matchStatus = filterStatus === 'all' || p.status.toLowerCase().includes(filterStatus.toLowerCase());
        return matchKampus && matchStatus;
    });

    if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 24px; color: #94A3B8;">Tidak ada riwayat pengajuan COD yang cocok.</td></tr>`;
        return;
    }

    tbody.innerHTML = filtered.map(p => {
        let badge = 'admin-badge-info';
        if (p.status.includes('PROCESS')) badge = 'admin-badge-warning';
        if (p.status.includes('CLOSED') || p.status.includes('DITOLAK')) badge = 'admin-badge-danger';
        if (p.status.includes('SUKSES') || p.status.includes('SELESAI')) badge = 'admin-badge-success';

        return `
            <tr>
                <td style="font-weight: 700; color: #1D356D;">${p.id}</td>
                <td>
                    <div style="font-weight: 700; color: #0F172A;">${p.barangNama}</div>
                    <div style="font-size: 11.5px; color: #64748B;">Lokasi: ${p.lokasiCod}</div>
                </td>
                <td style="font-weight: 600;">${p.penjual}</td>
                <td><span style="font-weight: 600; color: #1E293B;">${p.pembeli}</span></td>
                <td><span class="admin-badge admin-badge-neutral">${p.kampus}</span></td>
                <td style="font-size: 12px; color: #64748B;">${p.waktu}</td>
                <td><span class="admin-badge ${badge}">${p.status}</span></td>
            </tr>
        `;
    }).join('');
}

// ==========================================
// 9. GENERAL MODAL & TOAST HELPERS
// ==========================================
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.remove('open');
}

function showAdminToast(msg) {
    let toast = document.getElementById('adminToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'adminToast';
        toast.style.cssText = `
            position: fixed;
            bottom: 24px;
            right: 28px;
            background: #0F172A;
            color: #FFFFFF;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            z-index: 99999;
            box-shadow: 0 10px 25px rgba(0,0,0,0.25);
            display: flex;
            align-items: center;
            gap: 10px;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.25s ease;
            pointer-events: none;
            border-left: 4px solid #22C55E;
        `;
        document.body.appendChild(toast);
    }
    toast.innerHTML = `<span>⚡</span> <span>${msg}</span>`;
    toast.style.opacity = '1';
    toast.style.transform = 'translateY(0)';
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
    }, 3500);
}

function handleAdminLogout() {
    if (confirm('Apakah Anda yakin ingin keluar dari Portal Superadmin?')) {
        localStorage.removeItem('pk_admin_logged_in');
        window.location.href = 'login-admin.php';
    }
}

// Close modals when clicking backdrop
window.addEventListener('click', (e) => {
    if (e.target.classList.contains('admin-modal-backdrop')) {
        e.target.classList.remove('open');
    }
});

// Window Onload Dispatcher
window.addEventListener('DOMContentLoaded', () => {
    initAdminStorage();
    
    // Check active tab from storage or default to dashboard
    const savedTab = localStorage.getItem(STORAGE_KEYS.CURRENT_TAB) || 'dashboard';
    switchAdminTab(savedTab);
});
