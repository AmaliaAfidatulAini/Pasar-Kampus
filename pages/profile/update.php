<?php
$session = session_include('/login');
$userId = (int) ($session['user_id'] ?? 0);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('/profile', 303);
}

try {
    $fields = [
        'name' => [trim((string) ($_POST['name'] ?? '')), 255],
        'phone' => [trim((string) ($_POST['phone'] ?? '')), 50],
        'campus' => [trim((string) ($_POST['campus'] ?? '')), 255],
        'student_number' => [trim((string) ($_POST['student_number'] ?? '')), 50],
        'subdistrict' => [trim((string) ($_POST['subdistrict'] ?? '')), 255],
        'district' => [trim((string) ($_POST['district'] ?? '')), 200],
    ];
    foreach ($fields as $label => [$value, $limit]) {
        if (mb_strlen($value) > $limit || ($label === 'name' && $value === '')) {
            throw new InvalidArgumentException($label === 'name' ? 'Nama lengkap wajib diisi.' : "{$label} melebihi batas karakter.");
        }
    }

    $preferences = $_POST['preferences'] ?? [];
    if (!is_array($preferences)) {
        throw new InvalidArgumentException('Preferensi kategori tidak valid.');
    }
    $preferences = array_values(array_unique(array_filter($preferences, static fn($id): bool => is_string($id) && ctype_digit($id) && (int) $id > 0)));
    if ($preferences !== []) {
        $placeholders = implode(',', array_fill(0, count($preferences), '?'));
        $validCount = (int) fetch(executeQuery("SELECT COUNT(*) AS total FROM categories WHERE categories_id IN ({$placeholders})", $preferences))['total'];
        if ($validCount !== count($preferences)) {
            throw new InvalidArgumentException('Preferensi kategori tidak valid.');
        }
    }

    $existing = fetch(executeQuery('SELECT profile_id, attachment_id FROM user_profiles WHERE user_id = ? ORDER BY profile_id DESC LIMIT 1', [$userId]));
    $avatarFile = $_FILES['avatar'] ?? null;
    $newAttachmentId = null;
    $newAvatarPath = null;
    if (is_array($avatarFile) && ($avatarFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        if (($avatarFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('Foto profil gagal diunggah.');
        }
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($avatarFile['tmp_name']);
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        if (!isset($extensions[$mime])) {
            throw new InvalidArgumentException('Gunakan foto JPG, PNG, WEBP, atau GIF.');
        }
        $fileName = 'profile_' . $userId . '_' . bin2hex(random_bytes(8)) . '.' . $extensions[$mime];
        if (!storeFile('profile', $avatarFile, $fileName)) {
            throw new RuntimeException('Foto profil gagal disimpan. Pastikan folder unggahan dapat ditulis.');
        }
        $newAvatarPath = 'profile/' . $fileName;
    }

    global $pdo;
    $pdo->beginTransaction();
    try {
        if ($newAvatarPath !== null) {
            $attachment = fetch(executeQuery(
                'INSERT INTO attachments (product_id, report_id, attachment_type, file_path, file_name, file_type, file_size) VALUES (NULL, NULL, 0, ?, ?, ?, ?) RETURNING attachment_id',
                [upload($newAvatarPath), $fileName, $mime, (int) $avatarFile['size']]
            ));
            $newAttachmentId = (int) $attachment['attachment_id'];
        }

        if ($existing) {
            $sql = 'UPDATE user_profiles SET name = ?, phone = ?, campus = ?, student_number = ?, subdistrict = ?, district = ?, updated_at = CURRENT_TIMESTAMP';
            $bindings = [$fields['name'][0], $fields['phone'][0] ?: null, $fields['campus'][0] ?: null, $fields['student_number'][0] ?: null, $fields['subdistrict'][0] ?: null, $fields['district'][0] ?: null];
            if ($newAttachmentId !== null) {
                $sql .= ', attachment_id = ?';
                $bindings[] = $newAttachmentId;
            }
            $sql .= ' WHERE profile_id = ?';
            $bindings[] = (int) $existing['profile_id'];
            executeQuery($sql, $bindings);
            $profileId = (int) $existing['profile_id'];
        } else {
            $row = fetch(executeQuery(
                'INSERT INTO user_profiles (user_id, attachment_id, name, phone, campus, student_number, subdistrict, district) VALUES (?, ?, ?, ?, ?, ?, ?, ?) RETURNING profile_id',
                [$userId, $newAttachmentId, $fields['name'][0], $fields['phone'][0] ?: null, $fields['campus'][0] ?: null, $fields['student_number'][0] ?: null, $fields['subdistrict'][0] ?: null, $fields['district'][0] ?: null]
            ));
            $profileId = (int) $row['profile_id'];
        }

        executeQuery('DELETE FROM user_preferences WHERE profile_id = ?', [$profileId]);
        foreach ($preferences as $categoryId) {
            executeQuery('INSERT INTO user_preferences (profile_id, category_id) VALUES (?, ?)', [$profileId, (int) $categoryId]);
        }
        $pdo->commit();
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        if ($newAvatarPath !== null) deleteFile($newAvatarPath);
        throw $error;
    }

    if ($newAvatarPath !== null && !empty($existing['attachment_id'])) {
        $oldAttachment = fetch(executeQuery('SELECT file_path FROM attachments WHERE attachment_id = ?', [(int) $existing['attachment_id']]));
        $uploadPrefix = rtrim(BASEURL, '/') . '/public/uploads/';
        if ($oldAttachment && str_starts_with((string) $oldAttachment['file_path'], $uploadPrefix)) {
            $oldPath = substr((string) $oldAttachment['file_path'], strlen($uploadPrefix));
            if (checkFile($oldPath)) deleteFile($oldPath);
        }
    }

    $_SESSION['name'] = $fields['name'][0];
    if ($newAvatarPath !== null) {
        $_SESSION['avatar_url'] = upload($newAvatarPath);
    }
    flash('success', 'Profil berhasil diperbarui.');
} catch (InvalidArgumentException $error) {
    flash('error', $error->getMessage());
} catch (Throwable $error) {
    error_log($error->getMessage());
    flash('error', 'Profil tidak dapat disimpan.');
}

redirect('/profile', 303);
