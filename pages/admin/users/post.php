<?php

declare(strict_types=1);

use Azolee\Validator\Validator;

role_include('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('/admin/users', 303);
}

function user_profile_values(): array
{
    $fields = [
        'name' => ['name', 255],
        'phone' => ['phone', 50],
        'campus' => ['campus', 255],
        'nim' => ['student_number', 50],
        'subdistrict' => ['subdistrict', 255],
        'district' => ['district', 200],
    ];
    $profile = [];
    foreach ($fields as $field => [$column, $maxLength]) {
        $value = $_POST[$field] ?? '';
        if (!is_string($value) || mb_strlen($value) > $maxLength) {
            throw new InvalidArgumentException('Data profil tidak valid.');
        }
        $value = trim($value);
        $profile[$column] = $value === '' ? ($field === 'name' ? '' : null) : $value;
    }
    return $profile;
}

function user_preference_ids(): array
{
    $values = $_POST['preference_categories'] ?? [];
    if (!is_array($values)) {
        throw new InvalidArgumentException('Preferensi pengguna tidak valid.');
    }

    $ids = array_values(array_filter(array_map('intval', $values)));
    if ($ids === []) {
        return [];
    }

    $placeholders = implode(', ', array_fill(0, count($ids), '?'));
    $validIds = array_map(
        'intval',
        executeQuery(
            "SELECT categories_id FROM categories WHERE categories_id IN ({$placeholders})",
            $ids,
            'fetchColumn'
        )
    );
    sort($validIds);
    sort($ids);
    if ($validIds !== $ids) {
        throw new InvalidArgumentException('Preferensi pengguna tidak valid.');
    }
    return $ids;
}

function sync_user_preferences(int $profileId, array $categoryIds): void
{
    executeQuery('DELETE FROM user_preferences WHERE profile_id = ?', [$profileId]);
    if ($categoryIds === []) {
        return;
    }

    $placeholders = implode(', ', array_fill(0, count($categoryIds), '(?, ?)'));
    $bindings = [];
    foreach ($categoryIds as $categoryId) {
        $bindings[] = $profileId;
        $bindings[] = $categoryId;
    }
    executeQuery(
        "INSERT INTO user_preferences (profile_id, category_id) VALUES {$placeholders}",
        $bindings
    );
}

function create(): void
{
    $validation = Validator::make([
        'email' => 'bail|required|string|email|max:50',
        'password' => 'bail|required|string|min:8',
        'role' => 'bail|required|string|in:Mahasiswa,Admin',
    ], $_POST);
    if ($validation->isFailed()) {
        throw new InvalidArgumentException(implode(' ', parse_errors($validation)));
    }
    $result = (object) $validation->validated();
    $profile = user_profile_values();
    $preferenceIds = user_preference_ids();

    global $pdo;
    $pdo->beginTransaction();
    try {
        $user = fetch(executeQuery(
            "INSERT INTO users (email, password, role, account_status) VALUES (?, ?, ?, 'Aktif') RETURNING user_id",
            [$result->email, password_hash($result->password, PASSWORD_DEFAULT), $result->role === 'Admin' ? 'admin' : 'user']
        ));
        $profileRow = fetch(executeQuery(
            'INSERT INTO user_profiles (user_id, name, phone, campus, student_number, subdistrict, district) VALUES (?, ?, ?, ?, ?, ?, ?) RETURNING profile_id',
            [(int) $user['user_id'], $profile['name'], $profile['phone'], $profile['campus'], $profile['student_number'], $profile['subdistrict'], $profile['district']]
        ));
        sync_user_preferences((int) $profileRow['profile_id'], $preferenceIds);
        $pdo->commit();
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $error;
    }
}

function update(): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $user = fetch(executeQuery('SELECT role FROM users WHERE user_id = ? AND deleted_at IS FALSE', [$id]));
    if (!$user) {
        throw new InvalidArgumentException('Pengguna tidak ditemukan.');
    }
    if ($user['role'] === 'admin') {
        throw new InvalidArgumentException('Akun admin tidak dapat diubah.');
    }

    $rules = [
        'email' => 'bail|required|string|email|max:50',
        'role' => 'bail|required|string|in:Mahasiswa,Admin',
    ];
    if (is_string($_POST['password'] ?? null) && $_POST['password'] !== '') {
        $rules['password'] = 'bail|required|string|min:8';
    }
    if (($_POST['role'] ?? null) === 'Mahasiswa') {
        $rules['status'] = 'bail|required|string|in:Aktif,Suspend,Banned';
        $rules['reason'] = 'bail|string|max:255';
    }
    $validation = Validator::make($rules, $_POST);
    if ($validation->isFailed()) {
        throw new InvalidArgumentException(implode(' ', parse_errors($validation)));
    }
    $result = (object) $validation->validated();
    $profile = user_profile_values();
    $preferenceIds = user_preference_ids();

    global $pdo;
    $pdo->beginTransaction();
    try {
        if (isset($result->password)) {
            executeQuery('UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?', [password_hash($result->password, PASSWORD_DEFAULT), $id]);
        }
        executeQuery('UPDATE users SET email = ?, role = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?', [$result->email, $result->role === 'Admin' ? 'admin' : 'user', $id]);
        $existingProfile = fetch(executeQuery('SELECT profile_id FROM user_profiles WHERE user_id = ? ORDER BY profile_id DESC LIMIT 1', [$id]));
        if ($existingProfile) {
            $profileId = (int) $existingProfile['profile_id'];
            executeQuery(
                'UPDATE user_profiles SET name = ?, phone = ?, campus = ?, student_number = ?, subdistrict = ?, district = ?, updated_at = CURRENT_TIMESTAMP WHERE profile_id = ?',
                [$profile['name'], $profile['phone'], $profile['campus'], $profile['student_number'], $profile['subdistrict'], $profile['district'], $profileId]
            );
        } else {
            $profileRow = fetch(executeQuery(
                'INSERT INTO user_profiles (user_id, name, phone, campus, student_number, subdistrict, district) VALUES (?, ?, ?, ?, ?, ?, ?) RETURNING profile_id',
                [$id, $profile['name'], $profile['phone'], $profile['campus'], $profile['student_number'], $profile['subdistrict'], $profile['district']]
            ));
            $profileId = (int) $profileRow['profile_id'];
        }
        sync_user_preferences($profileId, $preferenceIds);

        if ($result->role === 'Mahasiswa') {
            $reason = is_string($_POST['reason'] ?? null) ? trim($_POST['reason']) : '';
            executeQuery('UPDATE users SET account_status = ?, notes = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?', [$result->status, $result->status === 'Aktif' ? null : $reason, $id]);
        }
        $pdo->commit();
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $error;
    }
}

function delete(): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $session = get_session();
    if ($session !== null && (int) ($session['user_id'] ?? 0) === $id) {
        throw new InvalidArgumentException('Anda tidak dapat menghapus akun Anda sendiri.');
    }
    $stmt = executeQuery('DELETE FROM users WHERE user_id = ?', [$id]);
    if ($stmt->rowCount() === 0) {
        throw new InvalidArgumentException('Pengguna tidak ditemukan.');
    }
}

function handle(): void
{
    $target = $_POST['target'] ?? '';

    try {
        if ($target === 'create') {
            create();
            flash('success', 'Pengguna berhasil ditambahkan.');
        } elseif ($target === 'update') {
            update();
            flash('success', 'Data pengguna berhasil diperbarui.');
        } elseif ($target === 'delete') {
            delete();
            flash('success', 'Pengguna berhasil dihapus permanen.');
        } else {
            flash('error', 'Tindakan pengguna tidak valid.');
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

    redirect('/admin/users', 303);
}

handle();
