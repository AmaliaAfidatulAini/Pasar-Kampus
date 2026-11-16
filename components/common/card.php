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
?>

<div class="product-card" onclick="if (!event.target.closest('a')) window.location.href='<?= route('/barang?id=' . urlencode((string)$id)) ?>'" style="cursor: pointer;">
	<div>
		<div class="product-img-wrapper">
			<img src="<?= esc($foto ?: 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=300') ?>" alt="<?= esc($title) ?>" loading="lazy"> 
			<span class="badge-condition"><?= esc($condition) ?></span>
			<span class="badge-category"><?= esc($category) ?></span>
		</div>
		<div class="product-body">
			<div class="product-location"><?= esc((str_starts_with($location, '📍') ? '' : '📍 ') . ($location ?: 'Area Kampus')) ?></div>
			<h3 class="product-title"><?= esc($title) ?></h3>
			<p class="product-desc"><?= esc($description) ?></p>
		</div>
	</div>
	<div class="product-footer">
		<div class="product-price-box">
			<?php if ($formattedOPrice !== ''): ?>
				<span class="orig-price">Rp. <?= esc($formattedOPrice) ?></span>
			<?php endif; ?>
			<div class="cur-price">Rp. <?= esc($formattedPrice) ?></div>
		</div>
		<a href="<?= route('/barang?id=' . urlencode((string)$id)) ?>" class="btn-product-detail">
			Detail
		</a>
	</div>
</div>
