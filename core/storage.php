<?php

if (!defined('UPLOAD_BASEDIR')) {
	define('UPLOAD_BASEDIR', dirname(__DIR__) . '/public/uploads');
}

function storeFile(string $target_dir, array $file, string $new_namefile): bool {
	if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name']) || $new_namefile === '' || basename($new_namefile) !== $new_namefile) {
		return false;
	}

	$base = realpath(UPLOAD_BASEDIR);
	if ($base === false) {
		return false;
	}
	$target = rtrim($base . '/' . trim($target_dir, '/'), '/');
	if (!is_dir($target) && !mkdir($target, 0775, true) && !is_dir($target)) {
		return false;
	}
	$target = realpath($target);
	if ($target === false || ($target !== $base && !str_starts_with($target, $base . DIRECTORY_SEPARATOR))) {
		return false;
	}

	return move_uploaded_file($file['tmp_name'], $target . DIRECTORY_SEPARATOR . $new_namefile);
}

function checkFile(string $dir_file): bool {
	$base = realpath(UPLOAD_BASEDIR);
	$file = realpath(UPLOAD_BASEDIR . '/' . ltrim($dir_file, '/'));
	return $base !== false && $file !== false && is_file($file) && str_starts_with($file, $base . DIRECTORY_SEPARATOR);
}

function deleteFile(string $dir_file): bool {
	return checkFile($dir_file) && unlink(UPLOAD_BASEDIR . '/' . ltrim($dir_file, '/'));
}