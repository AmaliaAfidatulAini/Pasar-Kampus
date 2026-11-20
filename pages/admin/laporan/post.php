<?php

declare(strict_types=1);

use Azolee\Validator\Validator;

role_include('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('/admin/laporan', 303);
}

function delete(): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = executeQuery('DELETE FROM reports WHERE report_id = ?', [$id]);
    if ($stmt->rowCount() === 0) {
        throw new InvalidArgumentException('Laporan tidak ditemukan.');
    }
}

function resolve(): void
{
    $validation = Validator::make([
        'id' => 'bail|required|numeric|min:1',
        'decision' => 'bail|required|string|in:Abaikan,Suspend,Banned',
    ], $_POST);
    if ($validation->isFailed()) {
        throw new InvalidArgumentException(implode(' ', parse_errors($validation)));
    }
    $result = (object) $validation->validated();
    $id = (int) $result->id;

    $report = fetch(executeQuery(
        'SELECT product.user_id AS target_user_id FROM reports r LEFT JOIN products product ON product.product_id = r.product_id WHERE r.report_id = ?',
        [$id]
    ));
    if (!$report) {
        throw new InvalidArgumentException('Laporan tidak ditemukan.');
    }

    executeQuery(
        'UPDATE reports SET review_status = ?, decision = ?, updated_at = CURRENT_TIMESTAMP WHERE report_id = ?',
        [$result->decision === 'Abaikan' ? 'Diabaikan' : 'Selesai', $result->decision, $id]
    );
    if ($result->decision !== 'Abaikan' && $report['target_user_id'] !== null) {
        executeQuery(
            'UPDATE users SET account_status = ?, notes = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ? AND deleted_at IS FALSE',
            [$result->decision, 'Sanksi dari Laporan Aduan #' . $id, (int) $report['target_user_id']]
        );
    }
}

function handle(): void
{
    $target = $_POST['target'] ?? '';

    try {
        if ($target === 'resolve') {
            resolve();
            flash('success', 'Keputusan laporan berhasil diproses.');
        } elseif ($target === 'delete') {
            delete();
            flash('success', 'Laporan berhasil dihapus.');
        } else {
            flash('error', 'Tindakan laporan tidak valid.');
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

    redirect('/admin/laporan', 303);
}

handle();
