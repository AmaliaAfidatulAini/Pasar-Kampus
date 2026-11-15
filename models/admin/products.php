<?php

declare(strict_types=1);

require_model('admin/common');

function admin_handle_product_query(string $redirect): void
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
            throw new InvalidArgumentException('Query barang tidak valid.');
        }

        if ($query === 'edit-product') {
            $id = admin_post_id($input);
            $product = fetch(executeQuery(
                "SELECT p.product_id AS id,
                    p.item_name AS nama,
                    COALESCE(up.name, '') AS penjual,
                    p.status,
                    COALESCE(p.moderation_note, '') AS note
                 FROM products p
                 LEFT JOIN user_profiles up ON up.user_id = p.user_id
                 WHERE p.product_id = ?",
                [$id]
            ));
            if (!$product) {
                http_response_code(404);
                echo json_encode(['error' => 'Barang tidak ditemukan.'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $product['requests'] = executeQuery(
                "SELECT COALESCE(buyer.name, '') AS pembeli,
                    COALESCE(buyer.campus, '') AS kampus,
                    r.created_at AS waktu,
                    r.status AS status
                 FROM requests r
                 LEFT JOIN user_profiles buyer ON buyer.user_id = r.user_id
                 WHERE r.product_id = ?
                 ORDER BY r.transaction_id",
                [$id],
                'fetchAll'
            );
            echo json_encode($product, JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($query !== 'table') {
            throw new InvalidArgumentException('Query barang tidak valid.');
        }

        $draw = max(0, (int) ($input['draw'] ?? 0));
        $start = max(0, (int) ($input['start'] ?? 0));
        $length = (int) ($input['length'] ?? 10);
        $length = $length < 1 ? 10 : min($length, 100);
        $searchInput = $input['search'] ?? [];
        $search = is_array($searchInput) && is_string($searchInput['value'] ?? null)
            ? trim($searchInput['value'])
            : '';

        $where = [];
        $bindings = [];
        if ($search !== '') {
            $where[] = "(
                p.item_name ILIKE :search_name
                OR COALESCE(up.name, '') ILIKE :search_seller
                OR COALESCE(c.category_name, '') ILIKE :search_category
            )";
            $term = '%' . $search . '%';
            $bindings[':search_name'] = $term;
            $bindings[':search_seller'] = $term;
            $bindings[':search_category'] = $term;
        }
        $whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';
        $from = " FROM products p
            LEFT JOIN user_profiles up ON up.user_id = p.user_id
            LEFT JOIN categories c ON c.categories_id = p.category_id";

        $total = (int) fetch(executeQuery('SELECT COUNT(*) AS total FROM products'))['total'];
        $filtered = (int) fetch(executeQuery(
            'SELECT COUNT(*) AS total' . $from . $whereSql,
            $bindings
        ))['total'];
        $orderColumns = [
            0 => 'p.product_id',
            1 => 'p.item_name',
            2 => "COALESCE(up.name, '')",
            3 => "COALESCE(c.category_name, '')",
            4 => 'p.status',
        ];
        $orderIndex = (int) ($input['order'][0]['column'] ?? 0);
        $order = $orderColumns[$orderIndex] ?? 'p.product_id';
        $direction = strtolower((string) ($input['order'][0]['dir'] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC';
        $rows = executeQuery(
            "SELECT p.product_id AS id,
                p.item_name AS nama,
                COALESCE(up.name, '') AS penjual,
                COALESCE(c.category_name, '') AS kategori,
                p.status,
                COALESCE(p.moderation_note, '') AS note
             {$from}{$whereSql}
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


function admin_handle_product_post(string $redirect): void
{
    admin_handle_post($redirect, static function (array $input): string {
        if (($input['action'] ?? '') !== 'update-product') {
            throw new InvalidArgumentException('Tindakan barang tidak valid.');
        }
        $id = admin_post_id($input);
        $status = admin_post_string($input, 'status', 'Status');
        if (!in_array($status, ['Tersedia', 'Dihapus'], true)) {
            throw new InvalidArgumentException('Status barang tidak valid.');
        }
        $note = is_string($input['moderation_note'] ?? null) ? trim($input['moderation_note']) : '';
        $product = fetch(executeQuery('SELECT product_id FROM products WHERE product_id = ?', [$id]));
        if (!$product) {
            throw new InvalidArgumentException('Barang tidak ditemukan.');
        }
        executeQuery(
            'UPDATE products SET status = ?, moderation_note = ?, updated_at = CURRENT_TIMESTAMP WHERE product_id = ?',
            [$status, $note, $id]
        );
        return 'Status barang berhasil diperbarui.';
    });
}
