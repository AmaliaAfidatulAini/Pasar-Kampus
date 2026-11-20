<?php
// COMPONENTS PROPS //
$title = $title ?? "";
$subtitle = $subtitle ?? "";
$extra = $extra ?? "";
// END OF COMPONENTS PROPS //
?>

<div class="d-flex flex-wrap align-items-start justify-content-between gap-3 pb-3 mb-5 border-bottom">
	<div>
		<h1 class="h2 fw-bold text-primary mb-1"><?= $title ?></h1>
		<p class="text-body-secondary mb-0"><?= $subtitle ?></p>
	</div>
	<div>
		<?= $extra ?>
	</div>
</div>