<?php
$flashes = flash_pull();

if ($flashes === []) {
	return;
}

$variants = [
	'success' => [
		'role' => 'status',
		'icon' => '<path d="M3.5 8.5 6.6 11.6 12.5 4.6"/>',
	],
	'warning' => [
		'role' => 'alert',
		'icon' => '<path d="M8 2.4 14.6 13.4H1.4L8 2.4Z"/><path d="M8 6.5v3.2"/><path d="M8 11.7h.01"/>',
	],
	'error' => [
		'role' => 'alert',
		'icon' => '<circle cx="8" cy="8" r="6.3"/><path d="M6 6l4 4"/><path d="M10 6l-4 4"/>',
	],
];
?>
<div class="lp-flasher" data-flasher>
	<?php foreach ($flashes as $flash): ?>
		<?php
		$status = isset($variants[$flash['status'] ?? '']) ? $flash['status'] : 'error';
		$variant = $variants[$status];
		?>
		<div class="lp-flash lp-flash-<?= $status ?>" role="<?= $variant['role'] ?>">
			<span class="lp-flash-icon" aria-hidden="true">
				<svg width="24" height="24" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?= $variant['icon'] ?></svg>
			</span>
			<p class="lp-flash-message"><?= esc((string) ($flash['message'] ?? '')) ?></p>
			<button type="button" class="lp-flash-close" data-flash-close aria-label="Tutup notifikasi">
				<svg width="18" height="18" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 4l8 8"/><path d="M12 4l-8 8"/></svg>
			</button>
			<span class="lp-flash-progress" aria-hidden="true"></span>
		</div>
	<?php endforeach; ?>
</div>
<script src="<?= asset('js/flasher.js') ?>" defer></script>