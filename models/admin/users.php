<?php

declare(strict_types=1);

use Azolee\Validator\Validator;
require_model('admin/common');

function admin_handle_user_query(string $redirect): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'QUERY') {
        return;
    }

    role_include('admin');
    header('Content-Type: application/json; charset=UTF-8');

    try {
        $input = $_GET;
        if (str_starts_with(strtolower((string) ($_SERVER['CONTENT_TYPE'] ?? '')), 'application/json')) {
            $body = json_decode(file_get_contents('php://input'), true);
            if (is_array($body)) {
                $input = array_merge($input, $body);
            }
        }
        $query = $input['query'] ?? 'table';
        if (!is_string($query)) {
            throw new InvalidArgumentException('Query tidak valid.');
        }

        if ($query === 'edit-user') {
            $id = admin_post_id($input);
            $user = fetch(executeQuery(
                "SELECT u.user_id AS id, p.profile_id AS profile_id,
                    COALESCE(p.name, '') AS name,
                    COALESCE(p.phone, '') AS phone,
                    COALESCE(p.student_number, '') AS nim,
                    COALESCE(p.campus, '') AS campus,
                    COALESCE(p.subdistrict, '') AS subdistrict,
                    COALESCE(p.district, '') AS district,
                    COALESCE((
                        SELECT json_agg(up.category_id ORDER BY up.category_id)
                        FROM user_preferences up
                        WHERE up.profile_id = p.profile_id
                    ), '[]'::json) AS category_ids,
                    u.email, u.role, u.account_status AS status, COALESCE(u.notes, '') AS reason
                 FROM users u
                 LEFT JOIN user_profiles p ON p.user_id = u.user_id
                 WHERE u.user_id = ? AND u.deleted_at IS FALSE
                 ORDER BY p.profile_id DESC NULLS LAST
                 LIMIT 1",
                [$id]
            ));
            if (!$user) {
                http_response_code(404);
                echo json_encode(['error' => 'Pengguna tidak ditemukan.'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $user['preferences'] = json_decode((string) $user['category_ids'], true) ?: [];
            unset($user['profile_id'], $user['category_ids']);
            $user['role'] = $user['role'] === 'admin' ? 'Admin' : 'Mahasiswa';
            echo json_encode($user, JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($query !== 'table') {
            throw new InvalidArgumentException('Query pengguna tidak valid.');
        }

        $draw = max(0, (int) ($input['draw'] ?? 0));
        $start = max(0, (int) ($input['start'] ?? 0));
        $length = (int) ($input['length'] ?? 10);
        $length = $length < 1 ? 10 : min($length, 100);
        $search = $input['search']['value'] ?? '';
        $search = is_string($search) ? trim($search) : '';
        $where = ' WHERE u.deleted_at IS FALSE';
        $bindings = [];
        if ($search !== '') {
            $where .= " AND (p.name ILIKE :name OR p.student_number ILIKE :nim OR p.campus ILIKE :campus OR u.email ILIKE :email OR u.account_status ILIKE :status)";
            $term = '%' . $search . '%';
            $bindings = [':name' => $term, ':nim' => $term, ':campus' => $term, ':email' => $term, ':status' => $term];
        }

        $total = (int) fetch(executeQuery('SELECT COUNT(*) AS total FROM users u WHERE u.deleted_at IS FALSE'))['total'];
        $filtered = (int) fetch(executeQuery(
            "SELECT COUNT(*) AS total FROM users u LEFT JOIN user_profiles p ON p.user_id = u.user_id" . $where,
            $bindings
        ))['total'];
        $orderColumns = [
            0 => 'u.user_id', 1 => "COALESCE(p.name, '')", 2 => "COALESCE(p.student_number, '')",
            3 => "COALESCE(p.campus, '')", 4 => 'u.account_status', 5 => 'u.user_id'
        ];
        $orderIndex = (int) ($input['order'][0]['column'] ?? 0);
        $order = $orderColumns[$orderIndex] ?? 'u.user_id';
        $direction = strtolower((string) ($input['order'][0]['dir'] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC';
        $rows = executeQuery(
            "SELECT u.user_id AS id, COALESCE(p.name, '') AS nama,
                COALESCE(p.student_number, '') AS nim, COALESCE(p.campus, '') AS kampus,
                u.email, CASE WHEN u.role = 'admin' THEN 'Admin' ELSE 'Mahasiswa' END AS role,
                u.account_status AS status, COALESCE(u.notes, '') AS notes
             FROM users u
             LEFT JOIN user_profiles p ON p.user_id = u.user_id" . $where .
            " ORDER BY {$order} {$direction} LIMIT :limit OFFSET :offset",
            [...$bindings, ':limit' => $length, ':offset' => $start],
            'fetchAll'
        );
        echo json_encode([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $rows,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    } catch (InvalidArgumentException $error) {
        http_response_code(400);
        echo json_encode(['error' => $error->getMessage()], JSON_UNESCAPED_UNICODE);
    } catch (Throwable $error) {
        error_log($error->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Terjadi kesalahan server.'], JSON_UNESCAPED_UNICODE);
    }
    exit;
}
function admin_user_preference_categories(): array
{
    return executeQuery(
        'SELECT categories_id AS id, category_name AS name FROM categories ORDER BY category_name ASC',
        [],
        'fetchAll'
    );
}

function admin_user_preference_ids(array $input): array
{
    $values = $input['preference_categories'] ?? [];
    if ($values === null || $values === '') {
        return [];
    }
    if (!is_array($values)) {
        throw new InvalidArgumentException('Preferensi pengguna tidak valid.');
    }

    $ids = [];
    foreach ($values as $value) {
        if (!(is_int($value) || (is_string($value) && ctype_digit($value))) || (int) $value < 1) {
            throw new InvalidArgumentException('Preferensi pengguna tidak valid.');
        }
        $ids[(int) $value] = true;
    }
    $ids = array_keys($ids);
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

function admin_sync_user_preferences(int $profileId, array $categoryIds): void
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

function admin_user_profile_values(array $input): array
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
        $value = $input[$field] ?? '';
        if (!is_string($value) || preg_match_all('/./us', $value, $matches) === false || count($matches[0]) > $maxLength) {
            throw new InvalidArgumentException('Data profil tidak valid.');
        }
        $value = trim($value);
        $profile[$column] = $value === '' ? ($field === 'name' ? '' : null) : $value;
    }
    return $profile;
}

function admin_handle_user_post(string $redirect): void
{
    admin_handle_post($redirect, static function (array $input): string {
        $action = $input['action'] ?? '';
        if ($action === 'create-user') {
            $validation = Validator::make([
                'email' => 'bail|required|string|email|max:50',
                'password' => 'bail|required|string|min:8',
                'role' => 'bail|required|string|in:Mahasiswa,Admin',
            ], $input);
            if ($validation->isFailed()) {
                throw new InvalidArgumentException(implode(' ', parse_errors($validation)));
            }
            $result = (object) $validation->validated();
            $profile = admin_user_profile_values($input);
            $preferenceIds = admin_user_preference_ids($input);
            global $pdo;
            $pdo->beginTransaction();
            try {
                $user = fetch(executeQuery("INSERT INTO users (email, password, role, account_status) VALUES (?, ?, ?, 'Aktif') RETURNING user_id", [$result->email, password_hash($result->password, PASSWORD_DEFAULT), $result->role === 'Admin' ? 'admin' : 'user']));
                $profileRow = fetch(executeQuery(
                    'INSERT INTO user_profiles (user_id, name, phone, campus, student_number, subdistrict, district) VALUES (?, ?, ?, ?, ?, ?, ?) RETURNING profile_id',
                    [(int) $user['user_id'], $profile['name'], $profile['phone'], $profile['campus'], $profile['student_number'], $profile['subdistrict'], $profile['district']]
                ));
                admin_sync_user_preferences((int) $profileRow['profile_id'], $preferenceIds);
                $pdo->commit();
            } catch (Throwable $error) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                throw $error;
            }
            return 'Pengguna berhasil ditambahkan.';
        }
        if ($action === 'delete-user') {
            $id = admin_post_id($input);
            $stmt = executeQuery('DELETE FROM users WHERE user_id = ?', [$id]);
            if ($stmt->rowCount() === 0) {
                throw new InvalidArgumentException('Pengguna tidak ditemukan.');
            }
            return 'Pengguna berhasil dihapus permanen.';
        }
        if ($action === 'update-user') {
            $id = admin_post_id($input);
            $user = fetch(executeQuery('SELECT role FROM users WHERE user_id = ? AND deleted_at IS FALSE', [$id]));
            if (!$user) {
                throw new InvalidArgumentException('Pengguna tidak ditemukan.');
            }

            $rules = [
                'email' => 'bail|required|string|email|max:50',
                'role' => 'bail|required|string|in:Mahasiswa,Admin',
            ];
            if (is_string($input['password'] ?? null) && $input['password'] !== '') {
                $rules['password'] = 'bail|required|string|min:8';
            }
            if (($input['role'] ?? null) === 'Mahasiswa') {
                $rules['status'] = 'bail|required|string|in:Aktif,Suspend,Banned';
                $rules['reason'] = 'bail|string|max:255';
            }
            $validation = Validator::make($rules, $input);
            if ($validation->isFailed()) {
                throw new InvalidArgumentException(implode(' ', parse_errors($validation)));
            }
            $result = (object) $validation->validated();
            $profile = admin_user_profile_values($input);
            $preferenceIds = admin_user_preference_ids($input);

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
                admin_sync_user_preferences($profileId, $preferenceIds);

                if ($result->role === 'Mahasiswa') {
                    $reason = is_string($input['reason'] ?? null) ? trim($input['reason']) : '';
                    executeQuery('UPDATE users SET account_status = ?, notes = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?', [$result->status, $result->status === 'Aktif' ? null : $reason, $id]);
                }
                $pdo->commit();
            } catch (Throwable $error) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                throw $error;
            }
            return 'Data pengguna berhasil diperbarui.';
        }
        throw new InvalidArgumentException('Tindakan pengguna tidak valid.');
    });
}
