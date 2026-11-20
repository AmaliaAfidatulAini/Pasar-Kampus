<?php

declare(strict_types=1);

role_include('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    redirect('/admin/users', 303);
}

function single(): ?array
{
    $id = (int) ($_GET['id'] ?? 0);
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
        return null;
    }
    $user['preferences'] = json_decode((string) $user['category_ids'], true) ?: [];
    unset($user['profile_id'], $user['category_ids']);
    $user['role'] = $user['role'] === 'admin' ? 'Admin' : 'Mahasiswa';
    return $user;
}

function table(): array
{
    $draw = max(0, (int) ($_GET['draw'] ?? 0));
    $start = max(0, (int) ($_GET['start'] ?? 0));
    $length = (int) ($_GET['length'] ?? 10);
    $length = $length < 1 ? 10 : min($length, 100);
    $search = $_GET['search']['value'] ?? '';
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
    $orderIndex = (int) ($_GET['order'][0]['column'] ?? 0);
    $order = $orderColumns[$orderIndex] ?? 'u.user_id';
    $direction = strtolower((string) ($_GET['order'][0]['dir'] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC';
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
    return [
        'draw' => $draw,
        'recordsTotal' => $total,
        'recordsFiltered' => $filtered,
        'data' => $rows,
    ];
}

function handle(): void
{
    $query = $_GET['query'] ?? 'table';

    try {
        if ($query === 'single') {
            $data = single();
            if ($data === null)
                response_json(['error' => 'Pengguna tidak ditemukan.'], 404);
            response_json($data, 200);
        } elseif ($query === 'table') {
            $data = table();
            response_json($data, 200);
        } else {
            throw new InvalidArgumentException('Query pengguna tidak valid.');
        }
    } catch (InvalidArgumentException $error) {
        response_json(['error' => $error->getMessage()], 400);
    } catch (Throwable $error) {
        error_log($error->getMessage());
        response_json(['error' => 'Terjadi kesalahan server.'], 500);
    }
}

handle();
