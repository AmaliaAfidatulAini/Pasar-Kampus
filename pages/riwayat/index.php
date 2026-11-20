<?php

$session = session_include('/login');
$userId = $session['user_id'] ?? null;
?>

<?php Component::include('user/header', ['title' => 'Riwayat Pembelian']); ?>

<main class="container py-4 py-lg-5">
    
    <?php Component::include("common/banner", [
        "title" => 'Riwayat Pembelian',
        "subtitle" => "Lihat riwayat transaksi pembelian yang pernah kamu lakukan",
    ]) ?>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col" class="text-nowrap">ID</th>
                        <th scope="col">Nama Barang</th>
                        <th scope="col">Nama Penjual</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody id="riwayatTableBody"></tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="modalHeaderTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h5 fw-bold text-primary" id="modalHeaderTitle">Detail Pengajuan</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body" id="modalBodyContent"></div>
            </div>
        </div>
    </div>
</main>

<?php Component::include('user/footer'); ?>
