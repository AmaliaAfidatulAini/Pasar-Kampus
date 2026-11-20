<?php

declare(strict_types=1);

role_include('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    redirect('/admin/pengajuan', 303);
}

function table(): array
{
    $draw = max(0, (int) ($_GET['draw'] ?? 0));
    $start = max(0, (int) ($_GET['start'] ?? 0));
    $length = (int) ($_GET['length'] ?? 10);
    $length = $length < 1 ? 10 : min($length, 100);
    $searchInput = $_GET['search'] ?? [];
    $search = is_array($searchInput) && is_string($searchInput['value'] ?? null)
        ? trim($searchInput['value'])
        : '';

    $from = " FROM requests r
        LEFT JOIN products p ON p.product_id = r.product_id
        LEFT JOIN user_profiles seller ON seller.user_id = p.user_id
        LEFT JOIN user_profiles buyer ON buyer.user_id = r.user_id";
    $where = '';
    $bindings = [];
    if ($search !== '') {
        $where = " WHERE CAST(r.transaction_id AS TEXT) ILIKE :id
            OR COALESCE(p.item_name, '') ILIKE :item
            OR COALESCE(seller.name, '') ILIKE :seller
            OR COALESCE(buyer.name, '') ILIKE :buyer
            OR COALESCE(buyer.campus, '') ILIKE :campus
            OR COALESCE(r.status, '') ILIKE :status";
        $term = '%' . $search . '%';
        $bindings = [
            ':id' => $term,
            ':item' => $term,
            ':seller' => $term,
            ':buyer' => $term,
            ':campus' => $term,
            ':status' => $term,
        ];
    }

    $total = (int) fetch(executeQuery('SELECT COUNT(*) AS total FROM requests'))['total'];
    $filtered = (int) fetch(executeQuery('SELECT COUNT(*) AS total' . $from . $where, $bindings))['total'];
    $orderColumns = [
        0 => 'r.transaction_id',
        1 => "COALESCE(p.item_name, '')",
        2 => "COALESCE(seller.name, '')",
        3 => "COALESCE(buyer.name, '')",
        4 => "COALESCE(buyer.campus, '')",
        5 => 'r.created_at',
        6 => 'r.status',
    ];
    $orderIndex = (int) ($_GET['order'][0]['column'] ?? 0);
    $order = $orderColumns[$orderIndex] ?? 'r.transaction_id';
    $direction = strtolower((string) ($_GET['order'][0]['dir'] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC';
    $rows = executeQuery(
        "SELECT r.transaction_id AS id,
            COALESCE(p.item_name, '') AS \"barangNama\",
            COALESCE(seller.name, '') AS penjual,
            COALESCE(buyer.name, '') AS pembeli,
            COALESCE(buyer.campus, '') AS kampus,
            r.created_at AS waktu,
            COALESCE(r.meeting_point, '') AS \"lokasiCod\",
            r.status AS status
         {$from}{$where}
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
        if ($query === 'table') {
            response_json(table(), 200);
        } else {
            throw new InvalidArgumentException('Query pengajuan tidak valid.');
        }
    } catch (InvalidArgumentException $error) {
        response_json(['error' => $error->getMessage()], 400);
    } catch (Throwable $error) {
        error_log($error->getMessage());
        response_json(['error' => 'Terjadi kesalahan server.'], 500);
    }
}

handle();
