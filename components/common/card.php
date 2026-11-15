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

$condition = ["Bekas", "Cukup", "Baik", "Sangat Baik"][$condition-1];
?>

<div class="product-card" onclick="if (!event.target.closest('a')) window.location.href='<?= route('/barang?id=' . $id) ?>'" style="cursor: pointer;">
	<div>
		<div class="product-img-wrapper">
			<img src="<?= esc($foto) ?>" alt="<?= esc($title !== '' ? $title : 'Produk') ?>" loading="lazy"> 
			<?php if ($condition !== ''): ?>
				<span class="badge-condition"><?= esc($condition) ?></span>
			<?php endif; ?>
			<?php if ($category !== ''): ?>
				<span class="badge-category"><?= esc($category) ?></span>
			<?php endif; ?>
		</div>
		<div class="product-body">
			<?php if ($displayLocation !== ''): ?>
				<div class="product-location"><?= esc($displayLocation) ?></div>
			<?php endif; ?>
			<h3 class="product-title"><?= esc($title) ?></h3>
			<p class="product-desc"><?= esc($description) ?></p>
		</div>
	</div>
	<div class="product-footer">
		<div class="product-price-box">
			<?php if ($displayOPrice !== ''): ?>
				<span class="orig-price">Rp. <?= $displayOPrice ?></span>
			<?php endif; ?>
			<div class="cur-price">Rp. <?= $displayPrice ?></div>
		</div>
		<a href="<?= route('/barang?id=' . $id) ?>" class="btn-product-detail">
			Detail
		</a>
	</div>
</div>