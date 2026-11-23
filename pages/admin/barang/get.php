<?php

declare(strict_types=1);

role_include('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    redirect('/admin/barang', 303);
}

function edit_product(): ?array
{
    $id = (int) ($_GET['id'] ?? 0);
    $product = fetch(executeQuery(
        "SELECT p.product_id AS id,
            p.item_name AS nama,
            COALESCE(seller.name, '') AS penjual,
            COALESCE(btrim(regexp_replace(regexp_replace(p.description, '<[^>]+>', ' ', 'g'), '\s+', ' ', 'g')), '') AS description,
            p.price,
            p.original_price,
            COALESCE(p.delivery_methods, '') AS delivery_method,
            p.status,
            COALESCE(p.moderation_note, '') AS note
         FROM products p
         LEFT JOIN LATERAL (
             SELECT name
             FROM user_profiles
             WHERE user_id = p.user_id
             ORDER BY profile_id DESC
             LIMIT 1
         ) seller ON TRUE
         WHERE p.product_id = ?",
        [$id]
    ));
    if (!$product) {
        return null;
    }
    // The modal writes this with textContent, so decode the entities that
    // Quill left in the stored markup.
    $product['description'] = html_entity_decode((string) $product['description'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
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
    return $product;
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

    $from = " FROM products p
        LEFT JOIN user_profiles up ON up.user_id = p.user_id
        LEFT JOIN categories c ON c.categories_id = p.category_id";
    $whereSql = '';
    $bindings = [];
    if ($search !== '') {
        $whereSql = " WHERE (
            p.item_name ILIKE :search_name
            OR COALESCE(up.name, '') ILIKE :search_seller
            OR COALESCE(c.category_name, '') ILIKE :search_category
        )";
        $term = '%' . $search . '%';
        $bindings = [':search_name' => $term, ':search_seller' => $term, ':search_category' => $term];
    }

    $total = (int) fetch(executeQuery('SELECT COUNT(*) AS total FROM products'))['total'];
    $filtered = (int) fetch(executeQuery('SELECT COUNT(*) AS total' . $from . $whereSql, $bindings))['total'];
    $orderColumns = [
        0 => 'p.product_id',
        1 => 'p.item_name',
        2 => "COALESCE(up.name, '')",
        3 => "COALESCE(c.category_name, '')",
        4 => 'p.status',
    ];
    $orderIndex = (int) ($_GET['order'][0]['column'] ?? 0);
    $order = $orderColumns[$orderIndex] ?? 'p.product_id';
    $direction = strtolower((string) ($_GET['order'][0]['dir'] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC';
    $rows = executeQuery(
        "SELECT p.product_id AS id,
            p.item_name AS nama,
            COALESCE(up.name, '') AS penjual,
            COALESCE(c.category_name, '') AS kategori,
            p.status,
            COALESCE(p.moderation_note, '') AS note
         {$from}{$whereSql}
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
        if ($query === 'edit-product') {
            $data = edit_product();
            if ($data === null) {
                response_json(['error' => 'Barang tidak ditemukan.'], 404);
            }
            response_json($data, 200);
        } elseif ($query === 'table') {
            response_json(table(), 200);
        } else {
            throw new InvalidArgumentException('Query barang tidak valid.');
        }
    } catch (InvalidArgumentException $error) {
        response_json(['error' => $error->getMessage()], 400);
    } catch (Throwable $error) {
        error_log($error->getMessage());
        response_json(['error' => 'Terjadi kesalahan server.'], 500);
    }
}

handle();
