<?php
// COMPONENTS PROPS //
$id = $id ?? "";
$thumbnail = $thumbnail ?? "";
$nama = $nama ?? "";
$harga = $harga ?? "";
$kategori = $kategori ?? "";
$status = $status ?? "";
$detailUrl = $detailUrl ?? "";
$editUrl = $editUrl ?? "";
// END COMPONENTS PROPS //

$statusVariants = [
	'Dijual' => 'text-bg-success',
	'Pemesanan' => 'text-bg-warning',
	'Pengambilan' => 'text-bg-info',
	'Terjual' => 'text-bg-secondary',
	'Ditolak' => 'text-bg-danger',
];
$badgeClass = $statusVariants[$status] ?? 'text-bg-secondary';
$formattedPrice = is_numeric($harga) ? number_format((float) $harga, 0, ',', '.') : (string) $harga;
$imageUrl = $thumbnail !== '' ? $thumbnail : 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&q=80&w=800';
$detailHref = $detailUrl !== '' ? $detailUrl : route('/barang/detail') . '?id=' . urlencode((string) $id);
$editHref = $editUrl !== '' ? $editUrl : route('/barang/edit') . '?id=' . urlencode((string) $id);
?>

<article class="card">
	<div class="card-body d-flex flex-column flex-sm-row gap-3 align-items-start">
		<img class="rounded-3 object-fit-cover flex-shrink-0"
			src="<?= esc($imageUrl) ?>"
			alt="<?= esc($nama) ?>" width="110" height="110"
		>
		<div class="flex-grow-1 d-flex flex-column h-100 w-100">
			<div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-2">
				<h2 class="h5 fw-bold text-primary mb-0"><?= esc($nama) ?></h2>
				<span class="badge <?= esc($badgeClass) ?>"><?= esc($status) ?></span>
			</div>
			<div class="row flex-grow-1">
				<div class="col-12 col-sm-6">
					<div class="text-body-secondary medium">Kategori: <?= esc($kategori) ?></div>
					<p class="text-body-secondary small mb-0">Harga: Rp. <?= esc($formattedPrice) ?></p>
				</div>
				<div class="col-12 col-sm-6 d-flex align-items-end justify-content-end column-gap-2 mt-3 mt-sm-0">
					<a class="btn btn-sm btn-outline-primary" href="<?= esc($detailHref) ?>">Detail</a>
					<a class="btn btn-sm btn-primary d-inline-flex align-items-center" href="<?= esc($editHref) ?>" title="Edit barang" aria-label="Edit barang">
						<i data-lucide="pencil" class="pk-icon" aria-hidden="true"></i>
					</a>
					<form method="post" action="<?= esc(route('/barang/post')) ?>" class="d-inline-flex" data-confirm-hapus="<?= esc($nama) ?>">
						<input type="hidden" name="target" value="hapus">
						<input type="hidden" name="id" value="<?= (int) $id ?>">
						<button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center" title="Hapus barang" aria-label="Hapus barang">
							<i data-lucide="trash-2" class="pk-icon" aria-hidden="true"></i>
						</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</article>
