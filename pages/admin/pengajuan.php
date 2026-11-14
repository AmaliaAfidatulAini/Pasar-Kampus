<?php
role_include('admin');
require_model('admin/requests');
admin_handle_request_query('/admin/pengajuan');

Component::include('admin/header', [
    'activeTab' => 'pengajuan',
    'title' => 'Monitoring Pengajuan Transaksi COD',
    'subtitle' => 'Pantau kesepakatan transaksi COD antara pembeli dan penjual di seluruh kampus.'
]);
?>
<section id="view-pengajuan" class="admin-view-panel active">
    <div class="admin-table-card">
        <div class="admin-table-responsive">
            <table class="admin-table" id="admin-request-table" data-query-url="<?= esc(route('/admin/pengajuan')) ?>">
                <thead>
                    <tr>
                        <th class="pengajuan-table-index-heading">No</th>
                        <th>Nama Barang &amp; Titik COD</th>
                        <th>Penjual</th>
                        <th>Calon Pembeli</th>
                        <th>Kampus</th>
                        <th>Waktu</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</section>

<?php Component::flush(); ?>
<script>
    const requestTable = document.getElementById('admin-request-table');
    const queryUrl = requestTable.dataset.queryUrl;
    const requestStatusClasses = {
        PENDING: 'admin-badge-warning',
        PROCESS: 'admin-badge-info',
        CANCELLED: 'admin-badge-danger',
        COMPLETED: 'admin-badge-success'
    };

    new DataTable(requestTable, {
        processing: true,
        serverSide: true,
        ajax: (data, callback) => fetch(queryUrl, {
            method: 'QUERY',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
            body: JSON.stringify({ ...data, query: 'table' })
        }).then(response => {
            if (!response.ok) throw new Error('Gagal memuat pengajuan.');
            return response.json();
        }).then(callback).catch(() => Swal.fire('Gagal memuat pengajuan', 'Muat ulang halaman dan coba lagi.', 'error')),
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                render: (_data, _type, _row, meta) => Number(meta.settings?._iDisplayStart ?? 0) + Number(meta.row ?? 0) + 1
            },
            {
                data: null,
                render: (_data, _type, row) => `<strong>${esc(row.barangNama || '-')}</strong><div>Lokasi: ${esc(row.lokasiCod || '-')}</div>`
            },
            { data: 'penjual', render: value => esc(value || '-') },
            { data: 'pembeli', render: value => esc(value || '-') },
            { data: 'kampus', render: value => esc(value || '-') },
            {
                data: 'waktu',
                render: value => {
                    if (!value) return '-';
                    const date = new Date(value.replace(' ', 'T'));
                    if (Number.isNaN(date.getTime())) return '-';
                    const pad = number => String(number).padStart(2, '0');
                    return `${date.getDate()}-${date.getMonth() + 1}-${date.getFullYear()} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
                }
            },
            { data: 'status', render: value => `<span class="admin-badge ${requestStatusClasses[value] || 'admin-badge-neutral'}">${esc(value || '-')}</span>` }
        ],
        drawCallback: () => lucide.createIcons(),
        layout: { topStart: 'pageLength', topEnd: 'search', bottomStart: 'info', bottomEnd: { paging: { firstLast: false } } },
        language: { emptyTable: 'Belum ada pengajuan COD.', processing: 'Memuat pengajuan…' }
    });
</script>
<?php $scripts = Component::endFlush(); ?>
<?php Component::include('admin/footer', ['scripts' => $scripts]); ?>
