<?php

declare(strict_types=1);

require_model('admin/common');

function admin_handle_request_query(string $redirect): void
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
        if ($query !== 'table') {
            throw new InvalidArgumentException('Query pengajuan tidak valid.');
        }

        $draw = max(0, (int) ($input['draw'] ?? 0));
        $start = max(0, (int) ($input['start'] ?? 0));
        $length = (int) ($input['length'] ?? 10);
        $length = $length < 1 ? 10 : min($length, 100);
        $searchInput = $input['search'] ?? [];
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
                OR COALESCE(r.meeting_point, '') ILIKE :meeting_point
                OR COALESCE(r.status, '') ILIKE :status";
            $term = '%' . $search . '%';
            $bindings = [
                ':id' => $term,
                ':item' => $term,
                ':seller' => $term,
                ':buyer' => $term,
                ':campus' => $term,
                ':meeting_point' => $term,
                ':status' => $term,
            ];
        }

        $total = (int) fetch(executeQuery('SELECT COUNT(*) AS total FROM requests'))['total'];
        $filtered = (int) fetch(executeQuery(
            'SELECT COUNT(*) AS total' . $from . $where,
            $bindings
        ))['total'];
        $orderColumns = [
            0 => 'r.transaction_id',
            1 => "COALESCE(p.item_name, '')",
            2 => "COALESCE(seller.name, '')",
            3 => "COALESCE(buyer.name, '')",
            4 => "COALESCE(buyer.campus, '')",
            5 => 'r.created_at',
            6 => 'r.status',
        ];
        $orderIndex = (int) ($input['order'][0]['column'] ?? 0);
        $order = $orderColumns[$orderIndex] ?? 'r.transaction_id';
        $direction = strtolower((string) ($input['order'][0]['dir'] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC';
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
             ORDER BY {$order} {$direction} LIMIT {$length} OFFSET {$start}",
            $bindings,
            'fetchAll'
        );

        echo json_encode([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $rows,
        ], JSON_UNESCAPED_UNICODE);
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
