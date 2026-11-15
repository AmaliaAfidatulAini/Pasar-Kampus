<?php

declare(strict_types=1);

require_model('admin/common');

function admin_handle_category_query(string $redirect): void
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
            throw new InvalidArgumentException('Query kategori tidak valid.');
        }

        if ($query === 'edit-category') {
            $id = admin_post_id($input);
            $category = fetch(executeQuery(
                "SELECT c.categories_id AS id, c.category_name AS nama
                 FROM categories c
                 WHERE c.categories_id = ?",
                [$id]
            ));
            if (!$category) {
                http_response_code(404);
                echo json_encode(['error' => 'Kategori tidak ditemukan.'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            echo json_encode($category, JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($query !== 'table') {
            throw new InvalidArgumentException('Query kategori tidak valid.');
        }

        $draw = max(0, (int) ($input['draw'] ?? 0));
        $start = max(0, (int) ($input['start'] ?? 0));
        $length = (int) ($input['length'] ?? 10);
        $length = $length < 1 ? 10 : min($length, 100);
        $search = $input['search']['value'] ?? '';
        $search = is_string($search) ? trim($search) : '';
        $where = ' WHERE 1 = 1';
        $bindings = [];
        if ($search !== '') {
            $where .= ' AND c.category_name ILIKE :name';
            $bindings = [':name' => '%' . $search . '%'];
        }

        $total = (int) fetch(executeQuery('SELECT COUNT(*) AS total FROM categories'))['total'];
        $filtered = (int) fetch(executeQuery('SELECT COUNT(*) AS total FROM categories c' . $where, $bindings))['total'];
        $orderColumns = [0 => 'c.categories_id', 1 => 'c.category_name', 2 => 'c.categories_id'];
        $orderIndex = (int) ($input['order'][0]['column'] ?? 0);
        $order = $orderColumns[$orderIndex] ?? 'c.categories_id';
        $direction = strtolower((string) ($input['order'][0]['dir'] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC';
        $rows = executeQuery(
            "SELECT c.categories_id AS id, c.category_name AS nama,
                COUNT(p.product_id) AS \"jmlBarang\"
             FROM categories c
             LEFT JOIN products p ON p.category_id = c.categories_id" . $where .
            " GROUP BY c.categories_id, c.category_name ORDER BY {$order} {$direction} LIMIT {$length} OFFSET {$start}",
            $bindings,
            'fetchAll'
        );
        echo json_encode(['draw' => $draw, 'recordsTotal' => $total, 'recordsFiltered' => $filtered, 'data' => $rows], JSON_UNESCAPED_UNICODE);
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

function admin_handle_category_post(string $redirect): void
{
    admin_handle_post($redirect, static function (array $input): string {
        $action = $input['action'] ?? '';
        if ($action === 'save-category') {
            $name = admin_post_string($input, 'nama', 'Nama kategori');
            $rawId = $input['id'] ?? '';
            if ($rawId === '') {
                executeQuery('INSERT INTO categories (category_name) VALUES (?)', [$name]);
                return 'Kategori berhasil ditambahkan.';
            }
            $id = admin_post_id($input);
            executeQuery('UPDATE categories SET category_name = ?, updated_at = CURRENT_TIMESTAMP WHERE categories_id = ?', [$name, $id]);
            return 'Kategori berhasil diperbarui.';
        }
        if ($action === 'delete-category') {
            $id = admin_post_id($input);
            if ((int) fetch(executeQuery('SELECT COUNT(*) AS total FROM products WHERE category_id = ?', [$id]))['total'] > 0) throw new InvalidArgumentException('Kategori tidak dapat dihapus karena masih digunakan oleh barang.');
            if ((int) fetch(executeQuery('SELECT COUNT(*) AS total FROM user_preferences WHERE category_id = ?', [$id]))['total'] > 0) throw new InvalidArgumentException('Kategori tidak dapat dihapus karena masih dipilih sebagai preferensi pengguna.');
            $stmt = executeQuery('DELETE FROM categories WHERE categories_id = ?', [$id]);
            if ($stmt->rowCount() === 0) throw new InvalidArgumentException('Kategori tidak ditemukan.');
            return 'Kategori berhasil dihapus.';
        }
        throw new InvalidArgumentException('Tindakan kategori tidak valid.');
    });
}