<?php

declare(strict_types=1);

use Azolee\Validator\Validator;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('/riwayat/pembelian', 303);
}

$session = session_include('/login');
$userId = (int) ($session['user_id'] ?? 0);

/** Postgres RAISE EXCEPTION text, without the SQLSTATE wrapper. */
function db_message(PDOException $error): string
{
    if (preg_match('/ERROR:\s*(.+)$/s', $error->getMessage(), $matches) === 1) {
        return trim($matches[1]);
    }

    return 'Terjadi kesalahan database.';
}

/** The purchase row, once the actor is proven to be one of its two parties. */
function purchase_for_actor(int $purchaseId, int $actorId): array
{
    $purchase = fetch(executeQuery(
        'SELECT * FROM v_purchase_history WHERE purchase_id = ?',
        [$purchaseId]
    ));

    if (!is_array($purchase)) {
        throw new InvalidArgumentException('Transaksi tidak ditemukan.');
    }
    if ((int) $purchase['seller_id'] !== $actorId && (int) $purchase['buyer_id'] !== $actorId) {
        throw new InvalidArgumentException('Kamu bukan pihak pada transaksi ini.');
    }

    return $purchase;
}

/** Stores the payment proof and returns the row data for its attachment insert. */
function store_proof(array $file, int $purchaseId): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new InvalidArgumentException('Unggah satu file gambar bukti bayar.');
    }
    if ((int) ($file['size'] ?? 0) > 5 * 1024 * 1024) {
        throw new InvalidArgumentException('Ukuran bukti bayar maksimal 5MB.');
    }

    $mimes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file((string) ($file['tmp_name'] ?? ''));
    if (!is_string($mime) || !isset($mimes[$mime])) {
        throw new InvalidArgumentException('Unggah satu file gambar bukti bayar.');
    }

    $name = 'inv_' . $purchaseId . '_' . bin2hex(random_bytes(8)) . '.' . $mimes[$mime];
    if (!storeFile('invoice', $file, $name)) {
        throw new InvalidArgumentException('Bukti bayar gagal disimpan.');
    }

    return [
        'url' => upload('invoice/' . $name),
        'name' => $name,
        'mime' => $mime,
        'size' => (int) ($file['size'] ?? 0),
    ];
}

function save_meeting(): void
{
    global $userId;

    // `required` rejects the string "0", which is a valid coordinate.
    $input = [
        'purchase_id' => $_POST['purchase_id'] ?? '',
        'meeting_lat' => $_POST['meeting_lat'] ?? '',
        'meeting_lng' => $_POST['meeting_lng'] ?? '',
    ];
    foreach ($input as $value) {
        if (!is_string($value) || trim($value) === '') {
            throw new InvalidArgumentException('Tandai lokasi pada peta terlebih dahulu.');
        }
    }

    $validation = Validator::make([
        'purchase_id' => 'bail|required|numeric',
        'meeting_lat' => 'bail|numeric|min:-90|max:90',
        'meeting_lng' => 'bail|numeric|min:-180|max:180',
    ], $input);

    if ($validation->isFailed()) {
        throw new InvalidArgumentException('Tandai lokasi pada peta terlebih dahulu.');
    }

    $result = (object) $validation->validated();
    $purchaseId = (int) $result->purchase_id;
    purchase_for_actor($purchaseId, $userId);

    executeQuery('SELECT set_meeting_point(?, ?, ?, ?)', [
        $purchaseId,
        $userId,
        (float) $result->meeting_lat,
        (float) $result->meeting_lng,
    ]);
}

function save_invoice(): void
{
    global $userId, $pdo;

    // `required` rejects the string "0", and a zero change is legitimate, so
    // presence is checked by hand and the rules only assert the type.
    $input = [
        'purchase_id' => $_POST['purchase_id'] ?? '',
        'pay' => $_POST['pay'] ?? '',
        'return' => $_POST['return'] ?? '',
        'method' => $_POST['method'] ?? '',
    ];
    foreach (['purchase_id', 'pay', 'return'] as $field) {
        if (!is_string($input[$field]) || trim($input[$field]) === '') {
            throw new InvalidArgumentException('Lengkapi data invoice.');
        }
    }

    $validation = Validator::make([
        'purchase_id' => 'bail|required|numeric',
        'pay' => 'bail|numeric',
        'return' => 'bail|numeric',
        'method' => 'bail|required|string',
    ], $input);

    if ($validation->isFailed()) {
        throw new InvalidArgumentException('Lengkapi data invoice.');
    }

    $result = (object) $validation->validated();
    $purchaseId = (int) $result->purchase_id;
    $purchase = purchase_for_actor($purchaseId, $userId);
    $pay = (int) $result->pay;
    $change = (int) $result->return;
    $method = (string) $result->method;
    $amount = (int) $purchase['amount'];

    if ($pay < 0) {
        throw new InvalidArgumentException('Bayar harus angka minimal 0.');
    }
    if ($change < 0) {
        throw new InvalidArgumentException('Kembali harus angka minimal 0.');
    }
    if ($pay - $change !== $amount) {
        throw new InvalidArgumentException('Bayar dikurangi kembalian harus sama dengan ' . format_rupiah($amount) . '.');
    }
    if (!in_array($method, ['Tunai', 'Transfer'], true)) {
        throw new InvalidArgumentException('Pilih metode pembayaran.');
    }
    if ($method === 'Transfer' && $change !== 0) {
        throw new InvalidArgumentException('Transfer tidak memiliki kembalian.');
    }

    $proof = store_proof($_FILES['proof'] ?? [], $purchaseId);

    $pdo->beginTransaction();
    try {
        $attachment = fetch(executeQuery(
            'INSERT INTO attachments (product_id, report_id, attachment_type, file_path, file_name, file_type, file_size)
             VALUES (NULL, NULL, 3, ?, ?, ?, ?) RETURNING attachment_id',
            [$proof['url'], $proof['name'], $proof['mime'], $proof['size']]
        ));
        executeQuery('SELECT create_invoice(?, ?, ?, ?, ?, ?)', [
            $purchaseId,
            $userId,
            (int) $attachment['attachment_id'],
            $pay,
            $change,
            $method,
        ]);
        $pdo->commit();
    } catch (Throwable $error) {
        $pdo->rollBack();
        deleteFile('invoice/' . $proof['name']);
        throw $error;
    }
}

function cancel_transaction(): void
{
    global $userId;

    $validation = Validator::make([
        'purchase_id' => 'bail|required|numeric',
        'reason' => 'bail|required|string|max:255',
    ], $_POST);

    if ($validation->isFailed()) {
        throw new InvalidArgumentException('Alasan pembatalan wajib diisi.');
    }

    $result = (object) $validation->validated();
    $purchaseId = (int) $result->purchase_id;
    purchase_for_actor($purchaseId, $userId);

    executeQuery('SELECT cancel_purchase(?, ?, ?)', [
        $purchaseId,
        $userId,
        trim((string) $result->reason),
    ]);
}

function handle(): void
{
    $action = is_string($_POST['action'] ?? null) ? $_POST['action'] : '';
    $purchaseId = (int) ($_POST['purchase_id'] ?? 0);
    $fallback = $purchaseId > 0 ? '/riwayat/detail?id=' . $purchaseId : '/riwayat/pembelian';

    try {
        switch ($action) {
            case 'meeting':
                save_meeting();
                flash('success', 'Titik temu berhasil ditetapkan.');
                break;

            case 'invoice':
                save_invoice();
                flash('success', 'Invoice dibuat. Transaksi ditandai selesai.');
                break;

            case 'cancel':
                cancel_transaction();
                flash('success', 'Transaksi dibatalkan.');
                break;

            default:
                throw new InvalidArgumentException('Tindakan tidak valid.');
        }
    } catch (InvalidArgumentException $error) {
        flash('error', $error->getMessage());
    } catch (PDOException $error) {
        flash('error', db_message($error));
    } catch (Throwable $error) {
        error_log($error->getMessage());
        flash('error', 'Terjadi kesalahan server.');
    }

    $target = redirect_target($_POST['redirect'] ?? '');
    redirect($target !== '' ? $target : $fallback, 303);
}

handle();
