<?php

$id = $id ?? "";
$title = $title ?? "";
$description = $description ?? "";
$category = $category ?? "";
$location = $location ?? "";
$condition = $condition ?? "";
$o_price = $o_price ?? "";
$price = $price ?? "";
$foto = $foto ?? "";

?>

<div class="product-card">
	<div>
		<div class="product-img-wrapper">
			<img src="<?= $foto ?>" alt="Keyboard Logiac"> 
			<span class="badge-condition"><?= $condition ?></span>
			<span class="badge-category"><?= $category ?></span>
		</div>
		<div class="product-body">
			<div class="product-location"><?= $location ?></div>
			<h3 class="product-title"><?= $title ?></h3>
			<p class="product-desc"><?= $description ?></p>
		</div>
	</div>
	<div class="product-footer">
		<div class="product-price-box">
			<span class="orig-price">Rp. <?= $o_price ?></span>
			<div class="cur-price">Rp. <?= $price ?></div>
		</div>
		<a href="<?= route('/barang?id=' . $id) ?>" class="btn-product-detail">
			Detail
		</a>
	</div>
</div>