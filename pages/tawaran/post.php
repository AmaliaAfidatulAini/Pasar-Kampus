<?php

declare(strict_types=1);

use Azolee\Validator\Validator;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('/tawaran/pembelian', 303);
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

function post_id(string $field, string $label): int
{
    $validation = Validator::make(
        [$field => 'bail|required|numeric'],
        [$field => $_POST[$field] ?? '']
    );

    if ($validation->isFailed()) {
        throw new InvalidArgumentException($label . ' tidak valid.');
    }

    $value = (int) ((object) $validation->validated())->{$field};
    if ($value < 1) {
        throw new InvalidArgumentException($label . ' tidak valid.');
    }

    return $value;
}

function handle(): void
{
    global $userId;

    $action = is_string($_POST['action'] ?? null) ? $_POST['action'] : '';
    $fallbacks = [
        'nego' => '/tawaran/pembelian',
        'terima' => '/tawaran/penjualan',
        'banding' => '/tawaran/penjualan',
        'beli' => '/tawaran/pembelian',
    ];
    $fallback = $fallbacks[$action] ?? '/tawaran/pembelian';

    try {
        switch ($action) {
            case 'nego':
                $productId = post_id('product_id', 'Barang');
                executeQuery('SELECT place_offer(?, ?, ?)', [$productId, $userId, post_id('price', 'Harga tawaran')]);
                flash('success', 'Tawaran berhasil dikirim.');
                break;

            case 'banding':
                executeQuery('SELECT counter_offer(?, ?, ?)', [post_id('offer_id', 'Tawaran'), $userId, post_id('price', 'Harga banding')]);
                flash('success', 'Banding berhasil dikirim.');
                break;

            case 'terima':
                executeQuery('SELECT accept_offer(?, ?)', [post_id('offer_id', 'Tawaran'), $userId]);
                flash('success', 'Tawaran diterima. Pembelian sudah dibuat.');
                break;

            case 'beli':
                $productId = post_id('product_id', 'Barang');
                executeQuery('SELECT create_purchase(?, ?)', [$productId, $userId]);
                flash('success', 'Pembelian berhasil diajukan.');
                break;

            default:
                throw new InvalidArgumentException('Tindakan tawaran tidak valid.');
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
