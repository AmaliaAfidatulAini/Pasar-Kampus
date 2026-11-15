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

$condition = ["Bekas", "Cukup", "Baik", "Sangat Baik"][((int) $condition)];
?>

<div class="product-card" onclick="if (!event.target.closest('a')) window.location.href='<?= route('/barang?id=' . $id) ?>'" style="cursor: pointer;">
	<div>
		<div class="product-img-wrapper">
			<img src="<?= $foto ?>" alt="<?= esc($title) ?>"> 
			<span class="badge-condition"><?= esc($condition) ?></span>
			<span class="badge-category"><?= esc($category) ?></span>
		</div>
		<div class="product-body">
			<div class="product-location"><?= esc($location) ?></div>
			<h3 class="product-title"><?= esc($title) ?></h3>
			<p class="product-desc"><?= esc($description) ?></p>
		</div>
	</div>
	<div class="product-footer">
		<div class="product-price-box">
			<spane class="orig-price">Rp. <?= esc($o_price) ?></spane(>
			<div class="cur-price">Rp. <?= esc($price) ?></div>
		</div>
		<a href="<?= route('/barang?id=' . $id) ?>" class="btn-product-detail">
			Detail
		</a>
	</div>
</div>