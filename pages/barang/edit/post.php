<?php

declare(strict_types=1);

use Azolee\Validator\Validator;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('/barang', 303);
}

$session = session_include('/login');
$userId = (int) ($session['user_id'] ?? 0);

/** Returns [] when no file was sent; throws on any other upload problem. */
function store_image(array $file, int $userId, int $type, string $label): array
{
    $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($error === UPLOAD_ERR_NO_FILE) {
        return [];
    }
    if ($error !== UPLOAD_ERR_OK) {
        throw new InvalidArgumentException('Foto ' . $label . ' gagal diunggah.');
    }
    if ((int) ($file['size'] ?? 0) > 5 * 1024 * 1024) {
        throw new InvalidArgumentException('Ukuran foto ' . $label . ' maksimal 5MB.');
    }

    $mimes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file((string) ($file['tmp_name'] ?? ''));
    if (!is_string($mime) || !isset($mimes[$mime])) {
        throw new InvalidArgumentException('Format foto ' . $label . ' tidak didukung. Gunakan JPG, PNG, WEBP, atau GIF.');
    }

    $name = 'prod_' . $userId . '_' . bin2hex(random_bytes(8)) . '.' . $mimes[$mime];
    if (!storeFile('products', $file, $name)) {
        throw new RuntimeException('Foto ' . $label . ' gagal disimpan.');
    }

    return [
        'type' => $type,
        'url' => upload('products/' . $name),
        'relative' => 'products/' . $name,
        'name' => $name,
        'mime' => $mime,
        'size' => (int) ($file['size'] ?? 0),
    ];
}

function insert_attachment(int $productId, array $image): void
{
    executeQuery(
        'INSERT INTO attachments (product_id, report_id, attachment_type, file_path, file_name, file_type, file_size, created_at, updated_at)
         VALUES (?, NULL, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)',
        [$productId, $image['type'], $image['url'], $image['name'], $image['mime'], $image['size']]
    );
}

function sync_tags(int $productId, array $tags): void
{
    // ponytail: replace the link set wholesale so removed chips actually disappear.
    executeQuery('DELETE FROM products_tags WHERE productsproduct_id = ?', [$productId]);

    foreach ($tags as $name) {
        $existing = fetch(executeQuery('SELECT id_tag FROM tags WHERE name_tag ILIKE ? LIMIT 1', [$name]));
        if ($existing) {
            $tagId = (int) $existing['id_tag'];
        } else {
            $inserted = fetch(executeQuery('INSERT INTO tags (name_tag) VALUES (?) RETURNING id_tag', [$name]));
            $tagId = (int) $inserted['id_tag'];
        }
        executeQuery(
            'INSERT INTO products_tags (products_tagsid_tag, productsproduct_id) VALUES (?, ?) ON CONFLICT DO NOTHING',
            [$tagId, $productId]
        );
    }
}

function parse_tags(mixed $raw): array
{
    if (is_string($raw)) {
        $decoded = json_decode($raw, true);
        $raw = is_array($decoded) ? $decoded : explode(',', $raw);
    }
    if (!is_array($raw)) {
        return [];
    }

    $tags = [];
    foreach ($raw as $tag) {
        if (!is_string($tag)) {
            continue;
        }
        $tag = trim($tag);
        if ($tag !== '' && mb_strlen($tag) <= 255) {
            $tags[] = $tag;
        }
    }

    return array_values(array_unique($tags));
}

/** Turns a stored upload() URL back into a path relative to the upload root. */
function upload_path(string $filePath): ?string
{
    $prefix = rtrim(BASEURL, '/') . '/public/uploads/';

    return str_starts_with($filePath, $prefix) ? substr($filePath, strlen($prefix)) : null;
}

function update(): string
{
    global $userId, $pdo;

    $data = [
        'id' => trim((string) ($_POST['id'] ?? '')),
        'namaBarang' => trim((string) ($_POST['namaBarang'] ?? '')),
        'kategori' => trim((string) ($_POST['kategori'] ?? '')),
        'harga' => trim((string) ($_POST['harga'] ?? '')),
        'hargaAsli' => trim((string) ($_POST['hargaAsli'] ?? '')),
        'kondisi' => trim((string) ($_POST['kondisi'] ?? '')),
        'deskripsi' => trim((string) ($_POST['deskripsi'] ?? '')),
    ];

    // Stored as sanitized HTML; the length limits apply to its text, not markup.
    $description = sanitize_rich_text($data['deskripsi']);
    $descriptionText = trim(strip_tags($description));
    if (mb_strlen($descriptionText) < 5) {
        throw new InvalidArgumentException('Deskripsi barang minimal 5 karakter.');
    }
    if (mb_strlen($descriptionText) > 5000) {
        throw new InvalidArgumentException('Deskripsi barang maksimal 5000 karakter.');
    }
    $data['deskripsi'] = $description;

    $rules = [
        'id' => 'bail|required|numeric',
        'namaBarang' => 'bail|required|string|min:3|max:255',
        'kategori' => 'bail|required|string',
        'harga' => 'bail|required',
        'kondisi' => 'bail|required|string',
        'deskripsi' => 'bail|required|string',
    ];
    // Original price is optional: blank falls back to the selling price below.
    if ($data['hargaAsli'] !== '') {
        $rules['hargaAsli'] = 'bail|numeric|min:1';
    }

    $validation = Validator::make($rules, $data);

    if ($validation->isFailed()) {
        throw new InvalidArgumentException(implode(' ', parse_errors($validation)));
    }

    $productId = (int) $data['id'];
    if ($productId < 1 || !fetch(executeQuery('SELECT product_id FROM products WHERE product_id = ? AND user_id = ?', [$productId, $userId]))) {
        throw new InvalidArgumentException('Barang tidak ditemukan.');
    }

    $categoryId = 0;
    foreach (fetchAll(executeQuery('SELECT categories_id, category_name FROM categories')) as $category) {
        if (strcasecmp((string) $category['category_name'], $data['kategori']) === 0) {
            $categoryId = (int) $category['categories_id'];
            break;
        }
    }
    if ($categoryId === 0) {
        throw new InvalidArgumentException('Kategori barang tidak valid.');
    }

    $conditions = ['Sangat Bagus' => 4, 'Baik' => 3, 'Cukup' => 2, 'Bekas' => 1];
    if (!isset($conditions[$data['kondisi']])) {
        throw new InvalidArgumentException('Kondisi barang tidak valid.');
    }

    $price = (int) $data['harga'];
    if ($price < 1) {
        throw new InvalidArgumentException('Harga barang harus lebih dari Rp. 0.');
    }

    $originalPrice = $data['hargaAsli'] === '' ? $price : (int) $data['hargaAsli'];
    if ($originalPrice < $price) {
        throw new InvalidArgumentException('Harga asli tidak boleh lebih rendah dari harga jual.');
    }

    $oldCovers = fetchAll(executeQuery(
        'SELECT file_path FROM attachments WHERE product_id = ? AND attachment_type = 1',
        [$productId]
    ));

    $cover = [];
    $thumbs = [];
    $stored = [];

    try {
        $cover = store_image($_FILES['imgCover'] ?? [], $userId, 1, 'utama');
        if ($cover !== []) {
            $stored[] = $cover['relative'];
        }
        for ($i = 1; $i <= 5; $i++) {
            $image = store_image($_FILES['imgThumb' . $i] ?? [], $userId, 2, 'tambahan ' . $i);
            if ($image !== []) {
                $thumbs[] = $image;
                $stored[] = $image['relative'];
            }
        }

        $pdo->beginTransaction();
        executeQuery(
            'UPDATE products
             SET category_id = ?, item_name = ?, description = ?, price = ?, original_price = ?, condition = ?, updated_at = CURRENT_TIMESTAMP
             WHERE product_id = ? AND user_id = ?',
            [$categoryId, $data['namaBarang'], $data['deskripsi'], $price, $originalPrice, $conditions[$data['kondisi']], $productId, $userId]
        );

        if ($cover !== []) {
            executeQuery('DELETE FROM attachments WHERE product_id = ? AND attachment_type = 1', [$productId]);
            insert_attachment($productId, $cover);
        }
        foreach ($thumbs as $thumb) {
            insert_attachment($productId, $thumb);
        }
        sync_tags($productId, parse_tags($_POST['tags'] ?? []));

        $pdo->commit();
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        foreach ($stored as $relative) {
            if (checkFile($relative)) {
                deleteFile($relative);
            }
        }
        throw $error;
    }

    // Only after the commit, and only when a replacement was stored: drop the
    // cover files it superseded. Without a replacement the existing cover stays
    // untouched in the database and on disk.
    if ($cover !== []) {
        foreach ($oldCovers as $oldCover) {
            $relative = upload_path((string) $oldCover['file_path']);
            if ($relative !== null && checkFile($relative)) {
                deleteFile($relative);
            }
        }
    }

    return 'Barang berhasil diperbarui.';
}

function handle(): void
{
    $id = (int) ($_POST['id'] ?? 0);

    try {
        if (($_POST['target'] ?? '') !== 'edit') {
            throw new InvalidArgumentException('Tindakan barang tidak valid.');
        }

        flash('success', update());
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

    redirect($id > 0 ? '/barang/edit?id=' . $id : '/barang', 303);
}

handle();
