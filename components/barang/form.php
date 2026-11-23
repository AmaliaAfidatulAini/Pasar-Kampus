<?php
// COMPONENTS PROPS //
$action = $action ?? "";
$target = $target ?? "";
$productId = $productId ?? "";
$submitLabel = $submitLabel ?? "";
$formValues = $formValues ?? [];
$categories = $categories ?? [];
$coverImage = $coverImage ?? "";
$thumbImages = $thumbImages ?? [];
$productTags = $productTags ?? [];
// END OF COMPONENTS PROPS //

$conditions = [
	'Sangat Bagus' => 'Barang baru, masih terawat, atau belum pernah dipakai sebelumnya.',
	'Baik' => 'Sudah dipakai, fungsi normal, dengan sedikit tanda pemakaian.',
	'Cukup' => 'Ada lecet atau goresan, tetapi masih berfungsi dengan baik.',
	'Bekas' => 'Tanda pemakaian jelas, mungkin ada cacat kecil.',
];
$selectedCondition = (string) ($formValues['kondisi'] ?? 'Sangat Bagus');
?>

<form id="barangForm" class="card rounded-3 overflow-hidden" method="post" action="<?= esc($action) ?>" enctype="multipart/form-data">
	<input type="hidden" name="target" value="<?= esc($target) ?>">
	<?php if ($productId !== ''): ?>
		<input type="hidden" name="id" value="<?= (int) $productId ?>">
	<?php endif; ?>
	<div class="card-body p-3 p-lg-4">
		<div class="row g-4">
			<div class="col-12 col-lg-4">
				<h2 class="text-uppercase fw-bold text-secondary small mb-3">Foto produk</h2>
				<div class="d-grid gap-2">
					<button type="button" class="ratio ratio-1x1 border rounded-3 pk-border-dashed bg-body-tertiary overflow-hidden position-relative pk-drop-slot" data-file-target="imgCover">
						<span class="d-flex flex-column align-items-center justify-content-center text-body-secondary small<?= $coverImage !== '' ? ' d-none' : '' ?>" data-placeholder>
							Maksimal 5MB per foto <br /> 
							<code class="d-block">(JPG, PNG, WEBP, GIF)</code>
						</span>
						<img class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover<?= $coverImage !== '' ? '' : ' d-none' ?>" data-preview alt=""<?= $coverImage !== '' ? ' src="' . esc($coverImage) . '"' : '' ?>>
					</button>
					<input type="file" id="imgCover" name="imgCover" accept="image/*" class="d-none" data-preview-input="imgCover">
					<div class="row row-cols-5 g-2">
						<?php for ($i = 1; $i <= 5; $i++): ?>
							<?php $thumbImage = (string) ($thumbImages[$i - 1] ?? ''); ?>
							<div class="col">
								<button type="button" class="ratio ratio-1x1 border rounded-2 pk-border-dashed bg-body-tertiary overflow-hidden position-relative pk-drop-slot" data-file-target="imgThumb<?= $i ?>">
									<span class="d-flex align-items-center justify-content-center text-body-secondary small<?= $thumbImage !== '' ? ' d-none' : '' ?>" data-placeholder>+<?= $i ?></span>
									<img class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover<?= $thumbImage !== '' ? '' : ' d-none' ?>" data-preview alt=""<?= $thumbImage !== '' ? ' src="' . esc($thumbImage) . '"' : '' ?>>
								</button>
								<input type="file" id="imgThumb<?= $i ?>" name="imgThumb<?= $i ?>" accept="image/*" class="d-none" data-preview-input="imgThumb<?= $i ?>">
							</div>
						<?php endfor; ?>
					</div>
				</div>
			</div>

			<div class="col-12 col-lg-8">
				<section>
					<h2 class="text-uppercase fw-bold text-secondary small mb-3">Informasi barang</h2>
					<div class="row g-3">
						<div class="col-12 col-md-6">
							<label class="form-label fw-semibold small" for="namaBarang">Nama barang</label>
							<input class="form-control" type="text" id="namaBarang" name="namaBarang" placeholder="Tumbler Stainless 500ml" value="<?= esc((string) ($formValues['namaBarang'] ?? '')) ?>" required>
						</div>
						<div class="col-12 col-md-6">
							<label class="form-label fw-semibold small" for="kategori">Kategori</label>
							<select class="form-select" id="kategori" name="kategori" required>
								<option value="">Pilih Kategori</option>
								<?php foreach ($categories as $option): ?>
									<option value="<?= esc((string) $option) ?>" <?= ($formValues['kategori'] ?? '') === $option ? 'selected' : '' ?>><?= esc((string) $option) ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-12">
							<label class="form-label fw-semibold small" for="kategori">Tag</label>
							<div class="border rounded-3 px-3 py-1 d-flex flex-wrap align-items-center gap-2" id="selectedTagsBox">
								<input class="form-control form-control-sm border-0 shadow-none p-0 flex-grow-1" type="text" id="customTagInput" placeholder="Ketik tag & tekan Enter...">
							</div>
							<input type="hidden" name="tags" id="tagsInput" value="<?= esc(json_encode($productTags)) ?>">
						</div>
					</div>
				</section>

				<section class="mt-4 pt-4 border-top">
					<h2 class="text-uppercase fw-bold text-secondary small mb-3">Harga</h2>
					<div class="row g-3">
						<div class="col-12 col-md-6">
							<label class="form-label fw-semibold small" for="harga">Harga jual</label>
							<div class="input-group">
								<span class="input-group-text fw-semibold text-secondary">Rp</span>
								<input class="form-control" type="number" id="harga" name="harga" min="1" step="1" placeholder="50000" value="<?= esc((string) ($formValues['harga'] ?? '')) ?>" required>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<label class="form-label fw-semibold small" for="hargaAsli">Harga asli</label>
							<div class="input-group">
								<span class="input-group-text fw-semibold text-secondary">Rp</span>
								<input class="form-control" type="number" id="hargaAsli" name="hargaAsli" min="1" step="1" placeholder="50000" value="<?= esc((string) ($formValues['hargaAsli'] ?? '')) ?>" aria-describedby="hargaAsliHelp">
							</div>
							<div class="form-text float-end" id="hargaAsliHelp">Isi dengan harga pasar jika ada</div>
						</div>
					</div>
				</section>

				<section class="mt-4 pt-4 border-top">
					<h2 class="text-uppercase fw-bold text-secondary small mb-3">Kondisi barang</h2>
					<fieldset>
						<legend class="visually-hidden">Kondisi barang</legend>
						<div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-2">
							<?php foreach ($conditions as $option => $explanation): ?>
								<?php $conditionId = 'kondisi-' . strtolower(str_replace(' ', '-', $option)); ?>
								<div class="col">
									<input class="btn-check" type="radio" name="kondisi" id="<?= esc($conditionId) ?>" value="<?= esc($option) ?>" <?= $selectedCondition === $option ? 'checked' : '' ?>>
									<label class="btn btn-outline-primary text-start w-100 h-100 p-3" for="<?= esc($conditionId) ?>">
										<span class="d-block fw-bold small"><?= esc($option) ?></span>
										<span class="d-block small lh-sm mt-1 opacity-75"><?= esc($explanation) ?></span>
									</label>
								</div>
							<?php endforeach; ?>
						</div>
					</fieldset>
				</section>

				<section class="mt-4 pt-4 border-top">
					<h2 class="text-uppercase fw-bold text-secondary small mb-3">Deskripsi barang</h2>
					<div id="deskripsiEditor" class="pk-editor"></div>
					<input type="hidden" name="deskripsi" id="deskripsi" value="<?= esc((string) ($formValues['deskripsi'] ?? '')) ?>">
				</section>
			</div>
		</div>
	</div>
	<div class="card-footer p-3 d-flex justify-content-end">
		<button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2" id="btnSubmitBarang">
			<i data-lucide="save" class="pk-icon" aria-hidden="true"></i>
			<?= esc($submitLabel) ?>
		</button>
	</div>
</form>
