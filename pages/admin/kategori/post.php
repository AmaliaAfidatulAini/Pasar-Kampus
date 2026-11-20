<?php

declare(strict_types=1);

use Azolee\Validator\Validator;

role_include('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('/admin/kategori', 303);
}

function create(): void
{
    $validation = Validator::make(['nama' => 'bail|required|string|max:255'], $_POST);
    
    if ($validation->isFailed())
        throw new InvalidArgumentException(implode(' ', parse_errors($validation)));

    $result = $validation->validated();

    $name = $result['nama'];

    executeQuery('INSERT INTO categories (category_name) VALUES (?)', [$name]);
}

function update(): void
{
    $validation = Validator::make([
        'id' => 'bail|required|numeric|min:1',
        'nama' => 'bail|required|string|max:255'
    ], $_POST);
    
    if ($validation->isFailed())
        throw new InvalidArgumentException(implode(' ', parse_errors($validation)));

    $result = $validation->validated();

    $name = $result['nama'];
    $id = (int) $result['id'];

    $stmt = executeQuery('UPDATE categories SET category_name = ?, updated_at = CURRENT_TIMESTAMP WHERE categories_id = ?', [$name, $id]);

    if ($stmt->rowCount() === 0)
        throw new InvalidArgumentException('Kategori tidak ditemukan.');
}

function delete(): void
{
    $id = (int) ($_POST['id'] ?? 0);
    if ((int) fetch(executeQuery('SELECT COUNT(*) AS total FROM products WHERE category_id = ?', [$id]))['total'] > 0) {
        throw new InvalidArgumentException('Kategori tidak dapat dihapus karena masih digunakan oleh barang.');
    }
    if ((int) fetch(executeQuery('SELECT COUNT(*) AS total FROM user_preferences WHERE category_id = ?', [$id]))['total'] > 0) {
        throw new InvalidArgumentException('Kategori tidak dapat dihapus karena masih dipilih sebagai preferensi pengguna.');
    }
    $stmt = executeQuery('DELETE FROM categories WHERE categories_id = ?', [$id]);
    if ($stmt->rowCount() === 0) {
        throw new InvalidArgumentException('Kategori tidak ditemukan.');
    }
}

function handle(): void
{
    $target = $_POST['target'] ?? '';

    try {
        if ($target === 'create') {
            create();
            flash('success', 'Kategori berhasil ditambahkan.');
        } elseif ($target === 'update') {
            update();
            flash('success', 'Kategori berhasil diperbarui.');
        } elseif ($target === 'delete') {
            delete();
            flash('success', 'Kategori berhasil dihapus.');
        } else {
            flash('error', 'Tindakan kategori tidak valid.');
        }
    } catch (InvalidArgumentException $error) {
        flash('error', $error->getMessage());
    } catch (PDOException $error) {
        error_log($error->getMessage());
        flash('error', 'Terjadi kesalahan database.');
    } catch (Throwable $error) {
        error_log($error->getMessage());
        flash('error', 'Terjadi kesalahan server.');
    }

    redirect('/admin/kategori', 303);
}

handle();
