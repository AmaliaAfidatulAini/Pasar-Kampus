<?php

declare(strict_types=1);

role_include('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    redirect('/admin/laporan', 303);
}

const REPORT_FROM = " FROM reports r
    LEFT JOIN users reporter_user ON reporter_user.user_id = r.user_id
    LEFT JOIN user_profiles reporter ON reporter.user_id = reporter_user.user_id
    LEFT JOIN products product ON product.product_id = r.product_id
    LEFT JOIN users owner_user ON owner_user.user_id = product.user_id
    LEFT JOIN user_profiles owner ON owner.user_id = owner_user.user_id";

function single(): ?array
{
    $id = (int) ($_GET['id'] ?? 0);
    $report = fetch(executeQuery(
        "SELECT r.report_id AS id,
            COALESCE(reporter.name, '') || CASE WHEN reporter.name IS NOT NULL AND reporter_user.email IS NOT NULL THEN ' (' || reporter_user.email || ')' ELSE COALESCE(reporter_user.email, '') END AS pelapor,
            COALESCE(owner.name, '') || CASE WHEN owner.name IS NOT NULL AND owner_user.email IS NOT NULL THEN ' (' || owner_user.email || ')' ELSE COALESCE(owner_user.email, '') END AS terlapor,
            r.report_type AS alasan,
            COALESCE(r.description, '') AS deskripsi,
            r.review_status AS status,
            COALESCE(r.decision, '') AS keputusan,
            r.created_at,
            (SELECT COALESCE(json_agg(a.file_path), '[]'::json) FROM attachments a WHERE a.report_id = r.report_id) AS bukti
         FROM reports r
         LEFT JOIN users reporter_user ON reporter_user.user_id = r.user_id
         LEFT JOIN user_profiles reporter ON reporter.user_id = reporter_user.user_id
         LEFT JOIN products product ON product.product_id = r.product_id
         LEFT JOIN users owner_user ON owner_user.user_id = product.user_id
         LEFT JOIN user_profiles owner ON owner.user_id = owner_user.user_id
         WHERE r.report_id = ?",
        [$id]
    ));
    if (!$report) {
        return null;
    }
    $report['bukti'] = json_decode((string) $report['bukti'], true) ?: [];
    return $report;
}

function table(): array
{
    $draw = max(0, (int) ($_GET['draw'] ?? 0));
    $start = max(0, (int) ($_GET['start'] ?? 0));
    $length = (int) ($_GET['length'] ?? 10);
    $length = $length < 1 ? 10 : min($length, 100);
    $search = $_GET['search']['value'] ?? '';
    $search = is_string($search) ? trim($search) : '';
    $where = '';
    $bindings = [];
    if ($search !== '') {
        $where = " WHERE COALESCE(reporter.name, '') ILIKE :reporter
            OR COALESCE(reporter_user.email, '') ILIKE :reporter_email
            OR COALESCE(owner.name, '') ILIKE :owner
            OR COALESCE(owner_user.email, '') ILIKE :owner_email
            OR r.report_type ILIKE :type
            OR r.review_status ILIKE :status";
        $term = '%' . $search . '%';
        $bindings = [
            ':reporter' => $term,
            ':reporter_email' => $term,
            ':owner' => $term,
            ':owner_email' => $term,
            ':type' => $term,
            ':status' => $term,
        ];
    }

    $total = (int) fetch(executeQuery('SELECT COUNT(*) AS total FROM reports'))['total'];
    $filtered = (int) fetch(executeQuery('SELECT COUNT(DISTINCT r.report_id) AS total' . REPORT_FROM . $where, $bindings))['total'];
    $orderColumns = [
        0 => 'r.report_id',
        1 => 'reporter_user.email',
        2 => "COALESCE(owner.name, COALESCE(owner_user.email, ''))",
        3 => 'r.report_type',
        4 => 'r.report_id',
        5 => 'r.review_status',
        6 => 'r.created_at',
    ];
    $orderIndex = (int) ($_GET['order'][0]['column'] ?? 0);
    $order = $orderColumns[$orderIndex] ?? 'r.report_id';
    $direction = strtolower((string) ($_GET['order'][0]['dir'] ?? 'desc')) === 'asc' ? 'ASC' : 'DESC';
    $rows = executeQuery(
        "SELECT r.report_id AS id,
            COALESCE(reporter_user.email, '-') AS pelapor,
            COALESCE(owner.name, '') || CASE WHEN owner.name IS NOT NULL AND owner_user.email IS NOT NULL THEN ' (' || owner_user.email || ')' ELSE COALESCE(owner_user.email, '') END AS terlapor,
            r.report_type AS alasan,
            (SELECT COUNT(*) FROM attachments a WHERE a.report_id = r.report_id) AS \"jmlBukti\",
            r.review_status AS status,
            r.created_at
         " . REPORT_FROM . "{$where}
         ORDER BY {$order} {$direction} LIMIT :limit OFFSET :offset",
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
            if ($data === null) response_json(['error' => 'Laporan tidak ditemukan.'], 404);
            response_json($data, 200);
        } elseif ($query === 'table') {
            response_json(table(), 200);
        } else {
            throw new InvalidArgumentException('Query laporan tidak valid.');
        }
    } catch (InvalidArgumentException $error) {
        response_json(['error' => $error->getMessage()], 400);
    } catch (Throwable $error) {
        error_log($error->getMessage());
        response_json(['error' => 'Terjadi kesalahan server.'], 500);
    }
}

handle();
