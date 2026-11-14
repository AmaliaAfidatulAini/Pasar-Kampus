<?php

declare(strict_types=1);
function admin_handle_post(string $redirect, callable $action): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return;
    }

    role_include('admin');

    try {
        flash('success', $action($_POST));
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

    redirect($redirect, 303);
}

function admin_post_string(array $input, string $key, string $label): string
{
    $value = $input[$key] ?? null;
    if (!is_string($value) || trim($value) === '') {
        throw new InvalidArgumentException($label . ' wajib diisi.');
    }
    return trim($value);
}

function admin_post_id(array $input, string $key = 'id'): int
{
    $value = $input[$key] ?? null;
    if (!(is_int($value) || (is_string($value) && ctype_digit($value))) || (int) $value < 1) {
        throw new InvalidArgumentException('ID tidak valid.');
    }
    return (int) $value;
}
