<?php

declare(strict_types=1);

role_include('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    redirect('/admin/kategori', 303);
}

function single(): ?array
{
    $id = (int) ($_GET['id'] ?? 0);
    return fetch(executeQuery(
        'SELECT c.categories_id AS id, c.category_name AS nama FROM categories c WHERE c.categories_id = ?',
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
        $where = ' WHERE c.category_name ILIKE :name';
        $bindings = [':name' => '%' . $search . '%'];
    }

    $total = (int) fetch(executeQuery('SELECT COUNT(*) AS total FROM categories'))['total'];
    $filtered = (int) fetch(executeQuery('SELECT COUNT(*) AS total FROM categories c' . $where, $bindings))['total'];
    $orderColumns = [0 => 'c.categories_id', 1 => 'c.category_name', 2 => 'c.categories_id'];
    $orderIndex = (int) ($_GET['order'][0]['column'] ?? 0);
    $order = $orderColumns[$orderIndex] ?? 'c.categories_id';
    $direction = strtolower((string) ($_GET['order'][0]['dir'] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC';
    $rows = executeQuery(
        "SELECT c.categories_id AS id, c.category_name AS nama,
            COUNT(p.product_id) AS \"jmlBarang\"
         FROM categories c
         LEFT JOIN products p ON p.category_id = c.categories_id" . $where .
        " GROUP BY c.categories_id, c.category_name ORDER BY {$order} {$direction} LIMIT :limit OFFSET :offset",
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
            if ($data === null) {
                response_json(['error' => 'Kategori tidak ditemukan.'], 404);
            }
            response_json($data, 200);
        } elseif ($query === 'table') {
            response_json(table(), 200);
        } else {
            throw new InvalidArgumentException('Query kategori tidak valid.');
        }
    } catch (InvalidArgumentException $error) {
        response_json(['error' => $error->getMessage()], 400);
    } catch (Throwable $error) {
        error_log($error->getMessage());
        response_json(['error' => 'Terjadi kesalahan server.'], 500);
    }
}

handle();
