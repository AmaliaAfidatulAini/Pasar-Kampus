<?php

declare(strict_types=1);

use Azolee\Validator\Validator;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('/barang', 303);
}

$session = session_include('/login');
$userId = (int) ($session['user_id'] ?? 0);

function upload_path(string $filePath): ?string
{
    $prefix = rtrim(BASEURL, '/') . '/public/uploads/';

    return str_starts_with($filePath, $prefix) ? substr($filePath, strlen($prefix)) : null;
}

function hapus(): string
{
    global $userId, $pdo;

    $data = ['id' => trim((string) ($_POST['id'] ?? ''))];

    $validation = Validator::make(['id' => 'bail|required|numeric'], $data);
    if ($validation->isFailed()) {
        throw new InvalidArgumentException(implode(' ', parse_errors($validation)));
    }

    $productId = (int) $data['id'];
    if ($productId < 1 || !fetch(executeQuery('SELECT product_id FROM products WHERE product_id = ? AND user_id = ?', [$productId, $userId]))) {
        throw new InvalidArgumentException('Barang tidak ditemukan.');
    }

    $attachments = fetchAll(executeQuery('SELECT file_path FROM attachments WHERE product_id = ?', [$productId]));

    $pdo->beginTransaction();
    try {
        // requests cascade-delete with the product; cancel open ones first so the
        // buyer's pending transaction is closed before the row disappears.
        executeQuery(
            "UPDATE requests SET status = 'CANCELLED', updated_at = CURRENT_TIMESTAMP
             WHERE product_id = ? AND status IN ('PENDING', 'PROCESS')",
            [$productId]
        );
        // attachments.product_id is ON DELETE SET NULL, so remove the rows explicitly.
        executeQuery('DELETE FROM attachments WHERE product_id = ?', [$productId]);
        executeQuery('DELETE FROM products WHERE product_id = ? AND user_id = ?', [$productId, $userId]);
        $pdo->commit();
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $error;
    }

    foreach ($attachments as $attachment) {
        $relative = upload_path((string) $attachment['file_path']);
        if ($relative !== null && checkFile($relative)) {
            deleteFile($relative);
        }
    }

    return 'Barang berhasil dihapus.';
}

function handle(): void
{
    try {
        if (($_POST['target'] ?? '') !== 'hapus') {
            throw new InvalidArgumentException('Tindakan barang tidak valid.');
        }

        flash('success', hapus());
        redirect('/barang', 303);
    } catch (InvalidArgumentException $error) {
        flash('error', $error->getMessage());
    } catch (PDOException $error) {
        error_log($error->getMessage());
        flash('error', 'Terjadi kesalahan database.');
    } catch (Throwable $error) {
        error_log($error->getMessage());
        flash('error', 'Terjadi kesalahan server.');
    }

    redirect('/barang', 303);
}

handle();
