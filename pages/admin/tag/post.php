<?php

declare(strict_types=1);

use Azolee\Validator\Validator;

role_include('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('/admin/tag', 303);
}

function create(): void
{
    $validation = Validator::make(['nama' => 'bail|required|string|max:255'], $_POST);

    if ($validation->isFailed())
        throw new InvalidArgumentException(implode(' ', parse_errors($validation)));

    $result = $validation->validated();
    
    $name = $result['nama'];

    executeQuery('INSERT INTO tags (name_tag) VALUES (?)', [$name]);
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

    $stmt = executeQuery('UPDATE tags SET name_tag = ?, updated_at = CURRENT_TIMESTAMP WHERE id_tag = ?', [$name, $id]);
    if ($stmt->rowCount() === 0) {
        throw new InvalidArgumentException('Tag tidak ditemukan.');
    }
}

function delete(): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = executeQuery('DELETE FROM tags WHERE id_tag = ?', [$id]);
    if ($stmt->rowCount() === 0) {
        throw new InvalidArgumentException('Tag tidak ditemukan.');
    }
}

function handle(): void
{
    $target = $_POST['target'] ?? '';

    try {
        if ($target === 'create') {
            create();
            flash('success', 'Tag berhasil ditambahkan.');
        } elseif ($target === 'update') {
            update();
            flash('success', 'Tag berhasil diperbarui.');
        } elseif ($target === 'delete') {
            delete();
            flash('success', 'Tag berhasil dihapus.');
        } else {
            flash('error', 'Tindakan tag tidak valid.');
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

    redirect('/admin/tag', 303);
}

handle();
