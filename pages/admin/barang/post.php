<?php

declare(strict_types=1);

use Azolee\Validator\Validator;

role_include('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('/admin/barang', 303);
}

function update(): void
{
    $validation = Validator::make([
        'id' => 'bail|required|numeric|min:1',
        'status' => 'bail|required|string|in:Tersedia,Proses,Terjual,Ditolak,Dihapus',
        'moderation_note' => 'string|max:255',
    ], $_POST);
    if ($validation->isFailed()) {
        throw new InvalidArgumentException(implode(' ', parse_errors($validation)));
    }
    $result = (object) $validation->validated();
    $id = (int) $result->id;
    $status = $result->status;
    $note = is_string($_POST['moderation_note'] ?? null) ? trim($_POST['moderation_note']) : '';

    $product = fetch(executeQuery('SELECT product_id FROM products WHERE product_id = ?', [$id]));
    if (!$product) {
        throw new InvalidArgumentException('Barang tidak ditemukan.');
    }

    executeQuery(
        'UPDATE products SET status = ?, moderation_note = ?, updated_at = CURRENT_TIMESTAMP WHERE product_id = ?',
        [$status, $note, $id]
    );

    // Sync related requests with the new product status
    if (in_array($status, ['Ditolak', 'Dihapus'], true)) {
        executeQuery(
            "UPDATE requests SET status = 'CANCELLED', updated_at = CURRENT_TIMESTAMP WHERE product_id = ? AND status IN ('PENDING', 'PROCESS')",
            [$id]
        );
    } elseif ($status === 'Terjual') {
        executeQuery(
            "UPDATE requests SET status = 'COMPLETED', completed_date = CURRENT_DATE, updated_at = CURRENT_TIMESTAMP WHERE product_id = ? AND status = 'PROCESS'",
            [$id]
        );
    } elseif ($status === 'Tersedia') {
        executeQuery(
            "UPDATE requests SET status = 'CANCELLED', updated_at = CURRENT_TIMESTAMP WHERE product_id = ? AND status = 'PROCESS'",
            [$id]
        );
    }
}

function handle(): void
{
    $target = $_POST['target'] ?? '';

    try {
        if ($target === 'update') {
            update();
            flash('success', 'Status barang berhasil diperbarui.');
        } else {
            flash('error', 'Tindakan barang tidak valid.');
        }
    } catch (InvalidArgumentException $error) {
        flash('error', $error->getMessage());
    } catch (PDOException $error) {
        if ($error->getCode() === '23505') {
            flash('error', 'Data dengan nilai unik tersebut sudah ada.');
        } else {
            error_log($error->getMessage());
            flash('error', 'Terjadi kesalahan database.');
        }
    } catch (Throwable $error) {
        error_log($error->getMessage());
        flash('error', 'Terjadi kesalahan server.');
    }

    redirect('/admin/barang', 303);
}

handle();
