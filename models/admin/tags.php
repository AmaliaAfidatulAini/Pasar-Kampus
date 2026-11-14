<?php

declare(strict_types=1);

require_model('admin/common');

function admin_handle_tag_query(string $redirect): void
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
            throw new InvalidArgumentException('Query tag tidak valid.');
        }

        if ($query === 'edit-tag') {
            $id = admin_post_id($input);
            $tag = fetch(executeQuery(
                'SELECT id_tag AS id, name_tag AS nama FROM tags WHERE id_tag = ?',
                [$id]
            ));
            if (!$tag) {
                http_response_code(404);
                echo json_encode(['error' => 'Tag tidak ditemukan.'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            echo json_encode($tag, JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($query !== 'table') {
            throw new InvalidArgumentException('Query tag tidak valid.');
        }

        $draw = max(0, (int) ($input['draw'] ?? 0));
        $start = max(0, (int) ($input['start'] ?? 0));
        $length = (int) ($input['length'] ?? 10);
        $length = $length < 1 ? 10 : min($length, 100);
        $search = $input['search']['value'] ?? '';
        $search = is_string($search) ? trim($search) : '';
        $where = '';
        $bindings = [];
        if ($search !== '') {
            $where = ' WHERE t.name_tag ILIKE :name';
            $bindings = [':name' => '%' . $search . '%'];
        }

        $total = (int) fetch(executeQuery('SELECT COUNT(*) AS total FROM tags'))['total'];
        $filtered = (int) fetch(executeQuery('SELECT COUNT(*) AS total FROM tags t' . $where, $bindings))['total'];
        $orderColumns = [0 => 't.id_tag', 1 => 't.name_tag', 2 => 't.id_tag'];
        $orderIndex = (int) ($input['order'][0]['column'] ?? 0);
        $order = $orderColumns[$orderIndex] ?? 't.id_tag';
        $direction = strtolower((string) ($input['order'][0]['dir'] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC';
        $rows = executeQuery(
            'SELECT t.id_tag AS id, t.name_tag AS nama, COUNT(DISTINCT pt.productsproduct_id) AS "jmlProduk"
             FROM tags t
             LEFT JOIN products_tags pt ON pt.products_tagsid_tag = t.id_tag' . $where .
            " GROUP BY t.id_tag, t.name_tag ORDER BY {$order} {$direction} LIMIT :limit OFFSET :offset",
            [...$bindings, ':limit' => $length, ':offset' => $start],
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

function admin_handle_tag_post(string $redirect): void
{
    admin_handle_post($redirect, static function (array $input): string {
        $action = $input['action'] ?? '';
        if ($action === 'save-tag') {
            $name = admin_post_string($input, 'nama', 'Nama tag');
            $rawId = $input['id'] ?? '';
            if ($rawId === '') {
                executeQuery('INSERT INTO tags (name_tag) VALUES (?)', [$name]);
                return 'Tag berhasil ditambahkan.';
            }
            $id = admin_post_id($input);
            executeQuery('UPDATE tags SET name_tag = ?, updated_at = CURRENT_TIMESTAMP WHERE id_tag = ?', [$name, $id]);
            return 'Tag berhasil diperbarui.';
        }
        if ($action === 'delete-tag') {
            $id = admin_post_id($input);
            $stmt = executeQuery('DELETE FROM tags WHERE id_tag = ?', [$id]);
            if ($stmt->rowCount() === 0) {
                throw new InvalidArgumentException('Tag tidak ditemukan.');
            }
            return 'Tag berhasil dihapus.';
        }
        throw new InvalidArgumentException('Tindakan tag tidak valid.');
    });
}
