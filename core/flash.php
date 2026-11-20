<?php

declare(strict_types=1);

const FLASH_STATUSES = ['success', 'warning', 'error'];

/**
 * Queue a one-shot notification for the next rendered page.
 */
function flash(string $status, string $message): void
{
	if (!in_array($status, FLASH_STATUSES, true)) {
		throw new InvalidArgumentException('Unknown flash status: ' . $status);
	}

	$_SESSION['flash'][] = [
		'status' => $status,
		'message' => $message,
	];
}