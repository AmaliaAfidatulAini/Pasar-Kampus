<?php

$session = session_include('/login');
$userId = (int) ($session['user_id'] ?? 0);
$invoiceId = (int) ($_GET['id'] ?? 0);

if ($invoiceId < 1) {
    flash('warning', 'Invoice tidak ditemukan.');
    redirect('/riwayat/pembelian', 302);
}

$invoice = fetch(executeQuery(
    'SELECT * FROM v_purchase_history WHERE invoice_id = ?',
    [$invoiceId]
));

if (!is_array($invoice)) {
    flash('warning', 'Invoice tidak ditemukan.');
    redirect('/riwayat/pembelian', 302);
}

if ((int) $invoice['seller_id'] !== $userId && (int) $invoice['buyer_id'] !== $userId) {
    redirect('/403', 403);
}

$invoiceCode = 'INV-' . str_pad((string) $invoiceId, 6, '0', STR_PAD_LEFT);
$transactionCode = 'PK-' . str_pad((string) $invoice['purchase_id'], 6, '0', STR_PAD_LEFT);
$detailUrl = route('/riwayat/detail?id=' . (int) $invoice['purchase_id']);
$productUrl = route('/barang/detail?id=' . (int) $invoice['product_id']);
$invoicedAt = format_tanggal((string) $invoice['invoiced_at']) . ', ' . date('H:i', strtotime((string) $invoice['invoiced_at']));
?>

<?php Component::flush() ?>
<link rel="stylesheet" href="<?= asset('css/riwayat.css') ?>">
<?php $styles = Component::endFlush() ?>

<?php Component::include('user/header', [
    'title' => 'Detail Invoice',
    'styles' => $styles,
]); ?>

<main class="container py-4 py-lg-5">
    <div class="pb-3 mb-4 border-bottom">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
            <h1 class="h2 fw-bold text-primary mb-0">Detail Invoice</h1>
            <span class="badge rounded-pill bg-success-subtle text-success-emphasis">Lunas</span>
        </div>
        <p class="text-body-secondary mb-0">Rincian pembayaran dan bukti transaksi.</p>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-7">
            <section class="card rounded-3" aria-labelledby="invoiceSummaryTitle">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold text-primary mb-3" id="invoiceSummaryTitle">Ringkasan Invoice</h2>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 pk-order-table">
                            <tbody>
                                <tr>
                                    <th scope="row">ID Invoice</th>
                                    <td><?= esc($invoiceCode) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">ID Transaksi</th>
                                    <td><a href="<?= esc($detailUrl) ?>"><?= esc($transactionCode) ?></a></td>
                                </tr>
                                <tr>
                                    <th scope="row">Barang</th>
                                    <td><a href="<?= esc($productUrl) ?>"><?= esc((string) $invoice['product_title']) ?></a></td>
                                </tr>
                                <tr>
                                    <th scope="row">Penjual</th>
                                    <td><?= esc((string) $invoice['seller_name']) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Pembeli</th>
                                    <td><?= esc((string) $invoice['buyer_name']) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Metode</th>
                                    <td><?= esc((string) $invoice['method']) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Total Transaksi</th>
                                    <td><?= esc(format_rupiah((int) $invoice['amount'])) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Dibayar</th>
                                    <td><?= esc(format_rupiah((int) $invoice['pay'])) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Kembalian</th>
                                    <td><?= esc(format_rupiah((int) $invoice['change_amount'])) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Tanggal Invoice</th>
                                    <td><?= esc($invoicedAt) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-12 col-lg-5">
            <section class="card rounded-3 mb-4" aria-labelledby="invoiceProofTitle">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold text-primary mb-3" id="invoiceProofTitle">Bukti Pembayaran</h2>
                    <img class="pk-proof-img" src="<?= esc((string) $invoice['proof_path']) ?>" alt="Bukti pembayaran transaksi <?= esc($transactionCode) ?>" width="900" height="600" loading="lazy">
                </div>
            </section>

            <section class="card rounded-3" aria-labelledby="invoiceActionsTitle">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold text-primary mb-3" id="invoiceActionsTitle">Tindakan</h2>
                    <div class="d-grid gap-2">
                        <a class="btn btn-outline-primary w-100 d-inline-flex align-items-center justify-content-center gap-2" href="<?= esc($detailUrl) ?>">
                            <i data-lucide="arrow-left" class="pk-icon" aria-hidden="true"></i><span>Kembali ke Transaksi</span>
                        </a>
                        <a class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-2" href="<?= esc($productUrl) ?>">
                            <i data-lucide="package" class="pk-icon" aria-hidden="true"></i><span>Lihat Barang</span>
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>

<?php Component::include('user/footer'); ?>
