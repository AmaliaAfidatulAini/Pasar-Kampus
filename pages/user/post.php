<?php

declare(strict_types=1);

use Azolee\Validator\Contracts\CustomRule;
use Azolee\Validator\Validator;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('/user', 303);
}

$session = session_include('/login');
$userId = (int) ($session['user_id'] ?? 0);

/**
 * The library's max rule compares numeric strings as numbers, which rejects any
 * digit-only value longer than the limit itself (phone numbers, NIMs).
 */
final class MaxLength implements CustomRule
{
    private string $label = '';

    public function __construct(private int $length)
    {
    }

    public function validate(mixed $value, string $key, mixed $dataToValidate): bool
    {
        $this->label = ucfirst($key);

        return is_null($value) || (is_string($value) && mb_strlen($value) <= $this->length);
    }

    public function message(): string
    {
        return "{$this->label} maksimal {$this->length} karakter.";
    }
}

function avatar_upload_path(string $filePath): ?string
{
    $uploadPrefix = rtrim(BASEURL, '/') . '/public/uploads/';
    return str_starts_with($filePath, $uploadPrefix) ? substr($filePath, strlen($uploadPrefix)) : null;
}

function update_profile(): string
{
    global $userId;

    $data = ['preferences' => $_POST['preferences'] ?? null];
    foreach (['name', 'phone', 'campus', 'student_number', 'subdistrict', 'district'] as $field) {
        $value = $_POST[$field] ?? null;
        $data[$field] = is_string($value) ? trim($value) : $value;
    }

    $validation = Validator::make([
        'name' => ['bail', 'required', 'string', new MaxLength(255)],
        'phone' => ['bail', 'string', new MaxLength(50)],
        'campus' => ['bail', 'string', new MaxLength(255)],
        'student_number' => ['bail', 'string', new MaxLength(50)],
        'subdistrict' => ['bail', 'string', new MaxLength(255)],
        'district' => ['bail', 'string', new MaxLength(200)],
        'preferences' => 'bail|array',
    ], $data);

    if ($validation->isFailed())
        throw new InvalidArgumentException(implode(' ', parse_errors($validation)));

    $result = $validation->validated();

    $name = (string) ($result['name'] ?? '');
    $phone = (string) ($result['phone'] ?? '');
    $campus = (string) ($result['campus'] ?? '');
    $studentNumber = (string) ($result['student_number'] ?? '');
    $subdistrict = (string) ($result['subdistrict'] ?? '');
    $district = (string) ($result['district'] ?? '');

    $preferences = array_values(array_unique(array_filter(
        (array) ($result['preferences'] ?? []),
        static fn ($id): bool => is_string($id) && ctype_digit($id) && (int) $id > 0
    )));
    if ($preferences !== []) {
        $placeholders = implode(',', array_fill(0, count($preferences), '?'));
        $validCount = (int) fetch(executeQuery("SELECT COUNT(*) AS total FROM categories WHERE categories_id IN ({$placeholders})", $preferences))['total'];
        if ($validCount !== count($preferences)) {
            throw new InvalidArgumentException('Preferensi kategori tidak valid.');
        }
    }

    $existing = fetch(executeQuery('SELECT profile_id, attachment_id FROM user_profiles WHERE user_id = ? ORDER BY profile_id DESC LIMIT 1', [$userId]));

    global $pdo;
    $pdo->beginTransaction();
    try {
        if ($existing) {
            executeQuery(
                'UPDATE user_profiles SET name = ?, phone = ?, campus = ?, student_number = ?, subdistrict = ?, district = ?, updated_at = CURRENT_TIMESTAMP WHERE profile_id = ?',
                [$name, $phone ?: null, $campus ?: null, $studentNumber ?: null, $subdistrict ?: null, $district ?: null, (int) $existing['profile_id']]
            );
            $profileId = (int) $existing['profile_id'];
        } else {
            $row = fetch(executeQuery(
                'INSERT INTO user_profiles (user_id, attachment_id, name, phone, campus, student_number, subdistrict, district) VALUES (?, ?, ?, ?, ?, ?, ?, ?) RETURNING profile_id',
                [$userId, null, $name, $phone ?: null, $campus ?: null, $studentNumber ?: null, $subdistrict ?: null, $district ?: null]
            ));
            $profileId = (int) $row['profile_id'];
        }

        executeQuery('DELETE FROM user_preferences WHERE profile_id = ?', [$profileId]);
        foreach ($preferences as $categoryId) {
            executeQuery('INSERT INTO user_preferences (profile_id, category_id) VALUES (?, ?)', [$profileId, (int) $categoryId]);
        }
        $pdo->commit();
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $error;
    }

    $_SESSION['name'] = $name;

    return 'Profil berhasil diperbarui.';
}

function update_avatar(): string
{
    global $userId;

    $remove = ($_POST['remove'] ?? '') === '1';
    $avatarFile = $_FILES['avatar'] ?? null;

    $validation = Validator::make([
        'avatar' => $remove ? 'bail|array' : 'bail|required|array',
    ], [
        'avatar' => is_array($avatarFile) ? $avatarFile : null,
    ]);

    if ($validation->isFailed())
        throw new InvalidArgumentException(implode(' ', parse_errors($validation)));

    $result = $validation->validated();

    $existing = fetch(executeQuery(
        'SELECT profile_id, attachment_id FROM user_profiles WHERE user_id = ? ORDER BY profile_id DESC LIMIT 1',
        [$userId]
    ));
    if (!$existing) {
        throw new InvalidArgumentException('Profil tidak ditemukan.');
    }
    $profileId = (int) $existing['profile_id'];
    $oldAttachmentId = (int) ($existing['attachment_id'] ?? 0);

    $oldFilePath = '';
    if ($oldAttachmentId > 0) {
        $oldFilePath = (string) (fetch(executeQuery('SELECT file_path FROM attachments WHERE attachment_id = ?', [$oldAttachmentId]))['file_path'] ?? '');
    }

    if ($remove) {
        executeQuery('UPDATE user_profiles SET attachment_id = NULL, updated_at = CURRENT_TIMESTAMP WHERE profile_id = ?', [$profileId]);
        if ($oldAttachmentId > 0) {
            executeQuery('DELETE FROM attachments WHERE attachment_id = ?', [$oldAttachmentId]);
            $oldPath = avatar_upload_path($oldFilePath);
            if ($oldPath !== null && checkFile($oldPath)) {
                deleteFile($oldPath);
            }
        }
        unset($_SESSION['avatar_url']);

        return 'Foto profil dihapus.';
    }

    $avatarFile = $result['avatar'];
    if (($avatarFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new InvalidArgumentException('Pilih foto terlebih dahulu.');
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($avatarFile['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($extensions[$mime])) {
        throw new InvalidArgumentException('Gunakan foto JPG, PNG, atau WEBP.');
    }
    $fileName = 'profile_' . $userId . '_' . bin2hex(random_bytes(8)) . '.' . $extensions[$mime];
    if (!storeFile('profile', $avatarFile, $fileName)) {
        throw new RuntimeException('Foto profil gagal disimpan. Pastikan folder unggahan dapat ditulis.');
    }
    $newPath = 'profile/' . $fileName;

    global $pdo;
    $pdo->beginTransaction();
    try {
        $attachment = fetch(executeQuery(
            'INSERT INTO attachments (product_id, report_id, attachment_type, file_path, file_name, file_type, file_size) VALUES (NULL, NULL, 0, ?, ?, ?, ?) RETURNING attachment_id',
            [upload($newPath), $fileName, $mime, (int) $avatarFile['size']]
        ));
        executeQuery(
            'UPDATE user_profiles SET attachment_id = ?, updated_at = CURRENT_TIMESTAMP WHERE profile_id = ?',
            [(int) $attachment['attachment_id'], $profileId]
        );
        $pdo->commit();
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        deleteFile($newPath);
        throw $error;
    }

    if ($oldAttachmentId > 0) {
        executeQuery('DELETE FROM attachments WHERE attachment_id = ?', [$oldAttachmentId]);
        $oldPath = avatar_upload_path($oldFilePath);
        if ($oldPath !== null && checkFile($oldPath)) {
            deleteFile($oldPath);
        }
    }

    $_SESSION['avatar_url'] = upload($newPath);

    return 'Foto profil berhasil diperbarui.';
}

function update_password(): string
{
    global $userId;

    $validation = Validator::make([
        'old_password' => 'bail|required|string',
        'new_password' => 'bail|required|string|min:8',
    ], $_POST);

    if ($validation->isFailed())
        throw new InvalidArgumentException(implode(' ', parse_errors($validation)));

    $result = $validation->validated();

    $user = fetch(executeQuery('SELECT password FROM users WHERE user_id = ?', [$userId]));
    if (!$user || !password_verify($result['old_password'], $user['password'])) {
        throw new InvalidArgumentException('Kata sandi lama tidak sesuai.');
    }

    executeQuery(
        'UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?',
        [password_hash($result['new_password'], PASSWORD_DEFAULT), $userId]
    );

    return 'Kata sandi berhasil diperbarui.';
}

function handle(): void
{
    $target = $_POST['target'] ?? '';

    try {
        if ($target === 'profile') {
            $message = update_profile();
        } elseif ($target === 'password') {
            $message = update_password();
        } elseif ($target === 'avatar') {
            $message = update_avatar();
        } else {
            throw new InvalidArgumentException('Tindakan profil tidak valid.');
        }

        flash('success', $message);
    } catch (InvalidArgumentException $error) {
        flash('error', $error->getMessage());
    } catch (PDOException $error) {
        error_log($error->getMessage());
        flash('error', 'Terjadi kesalahan database.');
    } catch (Throwable $error) {
        error_log($error->getMessage());
        flash('error', 'Terjadi kesalahan server.');
    }

    redirect('/user', 303);
}

handle();
