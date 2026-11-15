<?php
role_include('admin');
require_model('admin/report');
admin_report_query('/admin/report');
admin_report_post('/admin/report');
?>

<?php
Component::include('admin/header', [
    'activeTab' => 'report',
    'title' => 'Laporan Aduan',
    'subtitle' => 'Tinjau pengaduan penipuan, barang palsu, atau ghosting dari pengguna.'
]);
?>

<section id="view-report" class="admin-view-panel active">
    <div class="admin-table-card">
        <div class="admin-table-responsive">
            <table class="admin-table" id="admin-report-table" data-query-url="<?= esc(route('/admin/report')) ?>">
                <thead>
                    <tr>
                        <th class="report-table-index-heading">No</th>
                        <th>Pelapor</th>
                        <th>Terlapor</th>
                        <th>Alasan Aduan</th>
                        <th>Bukti Foto</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th class="report-table-actions-heading">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</section>

<div id="modalTinjauReport" class="admin-modal-backdrop">
    <div class="admin-modal-card report-modal-card">
        <div class="admin-modal-header">
            <h2 class="admin-modal-title">TINJAU LAPORAN ADUAN</h2>
            <button type="button" class="admin-modal-close" onclick="closeModal('modalTinjauReport')" aria-label="Tutup">&times;</button>
        </div>
        <form id="tinjauReportForm" method="post">
            <input type="hidden" name="action" value="resolve-report">
            <input type="hidden" id="tinjauReportId" name="id">
            <div class="admin-modal-body">
                <div class="admin-info-box">
                    <div id="tinjauHeaderBadge" class="admin-info-box-title">Detail Laporan</div>
                    <div class="admin-info-row"><span class="admin-info-label">Pelapor:</span><span id="tinjauPelapor" class="admin-info-value"></span></div>
                    <div class="admin-info-row"><span class="admin-info-label">Terlapor:</span><span id="tinjauTerlapor" class="admin-info-value"></span></div>
                    <div class="admin-info-row"><span class="admin-info-label">Alasan Aduan:</span><span id="tinjauAlasan" class="admin-info-value"></span></div>
                    <div class="admin-info-row report-description-row"><span class="admin-info-label">Deskripsi Pengaduan:</span><span id="tinjauDeskripsi" class="report-description"></span></div>
                </div>
                <div class="admin-form-group"><span class="admin-form-label">Bukti Foto Pengaduan</span>
                    <div id="tinjauEvidenceGallery" class="admin-evidence-gallery"></div>
                </div>
                <div class="admin-form-group report-decision-group"><span class="admin-form-label">Tindakan Admin</span>
                    <div class="admin-radio-group">
                        <label class="admin-radio-label"><input type="radio" name="decision" value="Abaikan" required><span><strong>Abaikan Laporan</strong> (Laporan Tidak Valid / Salah Paham)</span></label>
                        <label class="admin-radio-label"><input type="radio" name="decision" value="Suspend" required><span><strong>Suspend Akun Terlapor</strong> (Sanksi Sementara &amp; Peringatan)</span></label>
                        <label class="admin-radio-label"><input type="radio" name="decision" value="Banned" required><span><strong>Banned Permanen Akun Terlapor</strong> (Sanksi Berat Penipuan)</span></label>
                    </div>
                </div>
            </div>
            <div class="admin-modal-footer">
                <button type="button" class="btn-admin-secondary" onclick="closeModal('modalTinjauReport')">Batal</button>
                <button type="submit" class="btn-admin-danger">Save</button>
            </div>
        </form>
    </div>
</div>

<?php Component::flush(); ?>
<script>
    const reportTable = document.getElementById('admin-report-table');
    const queryUrl = reportTable.dataset.queryUrl;

    const reportEvidence = value => {
        try {
            const evidence = typeof value === 'string' ? JSON.parse(value || '[]') : value;
            return Array.isArray(evidence) ? evidence : [];
        } catch (error) {
            return [];
        }
    };

    const reportEvidenceCount = value => {
        const count = Number(value ?? 0);
        return count > 0 ? `${count} bukti` : 'Tidak ada bukti';
    };

    function openTinjauReport(report) {
        document.getElementById('tinjauReportId').value = report.id;
        document.getElementById('tinjauHeaderBadge').textContent = `Detail Laporan #${report.id}`;
        document.getElementById('tinjauPelapor').textContent = report.pelapor || '-';
        document.getElementById('tinjauTerlapor').textContent = report.terlapor || '-';
        document.getElementById('tinjauAlasan').textContent = report.alasan || '-';
        document.getElementById('tinjauDeskripsi').textContent = report.deskripsi || '-';
        const gallery = document.getElementById('tinjauEvidenceGallery');
        gallery.replaceChildren();
        reportEvidence(report.bukti).forEach((evidence, index) => {
            const rawUrl = typeof evidence === 'string' ? evidence : evidence?.url || evidence?.path || '';
            let parsed;
            try {
                parsed = new URL(rawUrl, window.location.origin);
            } catch (error) {
                return;
            }
            if (!['http:', 'https:'].includes(parsed.protocol)) return;
            const link = document.createElement('a');
            link.href = parsed.href;
            link.target = '_blank';
            link.rel = 'noopener noreferrer';
            link.setAttribute('aria-label', `Buka bukti ${index + 1}`);
            const image = document.createElement('img');
            image.src = parsed.href;
            image.alt = `Bukti ${index + 1}`;
            image.width = 90;
            image.height = 90;
            image.className = 'admin-evidence-thumb';
            link.append(image);
            gallery.append(link);
        });
        const decision = report.keputusan || 'Suspend';
        document.querySelectorAll('#tinjauReportForm input[name="decision"]').forEach(input => input.checked = input.value === decision);
        openModal('modalTinjauReport');
    }

    new DataTable(reportTable, {
        processing: true,
        serverSide: true,
        ajax: (data, callback) => fetch(queryUrl, {
            method: 'QUERY',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json'
            },
            body: JSON.stringify({
                ...data,
                query: 'table'
            })
        }).then(response => {
            if (!response.ok) throw new Error('Gagal memuat laporan.');
            return response.json();
        }).then(callback).catch(() => Swal.fire('Gagal memuat laporan', 'Muat ulang halaman dan coba lagi.', 'error')),
        columns: [{
                data: null,
                orderable: false,
                searchable: false,
                render: (_data, _type, _row, meta) => Number(meta.settings?._iDisplayStart ?? 0) + Number(meta.row ?? 0) + 1
            },
            {
                data: 'pelapor',
                render: value => esc(value || '-')
            },
            {
                data: 'terlapor',
                render: value => esc(value || '-')
            },
            {
                data: 'alasan',
                render: value => esc(value || '-')
            },
            {
                data: 'jmlBukti',
                orderable: false,
                searchable: false,
                render: value => esc(reportEvidenceCount(value))
            },
            {
                data: 'status',
                render: value => `<span class="admin-badge ${value === 'Menunggu' ? 'admin-badge-warning' : value === 'Diabaikan' ? 'admin-badge-neutral' : 'admin-badge-success'}">${esc(value || '-')}</span>`
            },
            {
                data: 'created_at',
                render: value => {
                    if (!value) return '-';
                    const date = new Date(value.replace(' ', 'T'));
                    if (Number.isNaN(date.getTime())) return '-';
                    const pad = number => String(number).padStart(2, '0');
                    return `${date.getDate()}-${date.getMonth() + 1}-${date.getFullYear()} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: (_data, _type, row) => `<div class="admin-action-group"><button type="button" class="btn-action btn-action-edit report-detail-button" data-id="${Number(row.id)}"><i data-lucide="eye" aria-hidden="true"></i>Detail</button><form method="post" class="report-delete-form"><input type="hidden" name="action" value="delete-report"><input type="hidden" name="id" value="${Number(row.id)}"><button type="submit" class="btn-action btn-action-danger"><i data-lucide="trash-2" aria-hidden="true"></i>Hapus</button></form></div>`
            }
        ],
        drawCallback: () => {
            lucide.createIcons();
            document.querySelectorAll('.report-delete-form').forEach(form => {
                if (form.dataset.confirmBound) return;
                form.dataset.confirmBound = 'true';
                form.addEventListener('submit', async event => {
                    event.preventDefault();
                    const result = await Swal.fire({
                        title: 'Hapus laporan?',
                        text: 'Laporan yang dihapus tidak dapat dipulihkan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Hapus',
                        cancelButtonText: 'Batal'
                    });
                    if (result.isConfirmed) form.submit();
                });
            });
        },
        layout: {
            topStart: 'pageLength',
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: {
                paging: {
                    firstLast: false
                }
            }
        },
        language: {
            emptyTable: 'Belum ada laporan masuk.',
            processing: 'Memuat laporan…'
        }
    });

    reportTable.addEventListener('click', async event => {
        const button = event.target.closest('.report-detail-button[data-id]');
        if (!button) return;
        button.disabled = true;
        try {
            const response = await fetch(queryUrl, {
                method: 'QUERY',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json'
                },
                body: JSON.stringify({
                    query: 'edit-report',
                    id: button.dataset.id
                })
            });
            const report = await response.json();
            if (!response.ok) throw new Error(report.error || 'Gagal memuat laporan.');
            openTinjauReport(report);
        } catch (error) {
            Swal.fire('Gagal memuat laporan', error.message, 'error');
        } finally {
            button.disabled = false;
        }
    });
</script>
<?php $scripts = Component::endFlush(); ?>

<?php Component::include('admin/footer', ['scripts' => $scripts]); ?>