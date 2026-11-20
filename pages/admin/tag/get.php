<?php

declare(strict_types=1);

role_include('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    redirect('/admin/tag', 303);
}

function single(): ?array
{
    $id = (int) ($_GET['id'] ?? 0);
    return fetch(executeQuery(
        'SELECT id_tag AS id, name_tag AS nama FROM tags WHERE id_tag = ?',
        [$id]
    )) ?: null;
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
        $where = ' WHERE t.name_tag ILIKE :name';
        $bindings = [':name' => '%' . $search . '%'];
    }

    $total = (int) fetch(executeQuery('SELECT COUNT(*) AS total FROM tags'))['total'];
    $filtered = (int) fetch(executeQuery('SELECT COUNT(*) AS total FROM tags t' . $where, $bindings))['total'];
    $orderColumns = [0 => 't.id_tag', 1 => 't.name_tag', 2 => 't.id_tag'];
    $orderIndex = (int) ($_GET['order'][0]['column'] ?? 0);
    $order = $orderColumns[$orderIndex] ?? 't.id_tag';
    $direction = strtolower((string) ($_GET['order'][0]['dir'] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC';
    $rows = executeQuery(
        'SELECT t.id_tag AS id, t.name_tag AS nama, COUNT(DISTINCT pt.productsproduct_id) AS "jmlProduk"
         FROM tags t
         LEFT JOIN products_tags pt ON pt.products_tagsid_tag = t.id_tag' . $where .
        " GROUP BY t.id_tag, t.name_tag ORDER BY {$order} {$direction} LIMIT :limit OFFSET :offset",
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
            if ($data === null) response_json(['error' => 'Tag tidak ditemukan.'], 404);
            response_json($data, 200);
        } elseif ($query === 'table') {
            $data = table();
            response_json($data, 200);
        } else {
            throw new InvalidArgumentException('Query tag tidak valid.');
        }
    } catch (InvalidArgumentException $error) {
        response_json(['error' => $error->getMessage()], 400);
    } catch (Throwable $error) {
        error_log($error->getMessage());
        response_json(['error' => 'Terjadi kesalahan server.'], 500);
    }
}

handle();
