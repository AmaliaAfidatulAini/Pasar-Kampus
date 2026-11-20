<?php
// COMPONENTS PROPS //
$id = $id ?? "";
$title = $title ?? "";
$description = $description ?? "";
$category = $category ?? "";
$location = $location ?? "";
$condition = $condition ?? "";
$o_price = $o_price ?? "";
$price = $price ?? "";
$foto = $foto ?? "";
// END OF COMPONENTS PROPS //

if (is_numeric($condition)) {
    $condMap = [
        1 => 'Bekas',
        2 => 'Cukup',
        3 => 'Baik',
        4 => 'Sangat Bagus'
    ];
    $condition = $condMap[(int) $condition] ?? 'Baik';
} elseif (empty($condition)) {
    $condition = 'Baik';
}

$formattedPrice = is_numeric($price) ? number_format((float)$price, 0, ',', '.') : (string)$price;
$formattedOPrice = (is_numeric($o_price) && (float)$o_price > (float)$price) ? number_format((float)$o_price, 0, ',', '.') : '';
$detailUrl = route('/barang/detail?id=' . urlencode((string) $id));
?>

<div class="card product-card h-100" onclick="if (!event.target.closest('a')) window.location.href='<?= $detailUrl ?>'">
	<div class="ratio ratio-1x1 product-card__media">
		<img src="<?= esc($foto ?: 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=300') ?>" alt="<?= esc($title) ?>" class="card-img-top w-100 h-100 object-fit-cover product-card__img" loading="lazy">
	</div>
	<span class="badge product-card__badge product-card__badge--condition position-absolute top-0 start-0 m-2"><?= esc($condition) ?></span>
	<span class="badge product-card__badge product-card__badge--category position-absolute top-0 end-0 m-2"><?= esc($category) ?></span>
	<div class="card-body">
		<span class="badge product-card__location mb-2"><?= esc(($location ?: 'Area Kampus')) ?></span>
		<h3 class="card-title product-card__title"><?= esc($title) ?></h3>
		<p class="card-text product-card__desc"><?= esc($description) ?></p>
	</div>
	<div class="card-footer product-card__footer d-flex align-items-center justify-content-between gap-2">
		<div>
			<?php if ($formattedOPrice !== ''): ?>
				<span class="d-block text-decoration-line-through product-card__orig">Rp. <?= esc($formattedOPrice) ?></span>
			<?php endif; ?>
			<span class="fw-bold product-card__price">Rp. <?= esc($formattedPrice) ?></span>
		</div>
		<a href="<?= $detailUrl ?>" class="btn btn-primary product-card__action">
			Detail
		</a>
	</div>
</div>
