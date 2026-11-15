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

/**
 * Drain the queue: [['status' => 'success', 'message' => '...'], ...]
 */
function flash_pull(): array
{
	$queued = $_SESSION['flash'] ?? [];
	unset($_SESSION['flash']);

	return is_array($queued) ? $queued : [];
}