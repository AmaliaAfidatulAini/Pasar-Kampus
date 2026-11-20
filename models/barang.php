<?php
declare(strict_types=1);

/**
 * Seller-owned listing helpers for the "Barang Saya" page.
 */

function barang_owner_filter_sql(int $userId, array $filters = []): array
{
    $where = ['p.user_id = :uid', "p.status <> 'Dihapus'"];
    $bindings = [':uid' => $userId];

    $search = trim((string) ($filters['q'] ?? ''));
    if ($search !== '') {
        $where[] = '(p.item_name ILIKE :kw OR p.description ILIKE :kw)';
        $bindings[':kw'] = '%' . $search . '%';
    }

    $categories = $filters['category'] ?? [];
    $categories = is_array($categories) ? $categories : [$categories];
    $categories = array_values(array_unique(array_filter(
        array_map(static fn ($value): string => trim((string) $value), $categories),
        static fn (string $value): bool => $value !== ''
    )));
    if ($categories !== []) {
        $placeholders = [];
        foreach ($categories as $index => $name) {
            $placeholder = ':cat' . $index;
            $placeholders[] = $placeholder;
            $bindings[$placeholder] = $name;
        }
        $where[] = 'c.category_name IN (' . implode(', ', $placeholders) . ')';
    }

    // TODO: filter by seller-facing status label once the card status prop is derived.

    return [implode(' AND ', $where), $bindings];
}

/** Rows shaped for `components/common/card-long.php` props. */
function get_owner_products(int $userId, array $filters = []): array
{
    [$whereSql, $bindings] = barang_owner_filter_sql($userId, $filters);

    $limitSql = !empty($filters['limit']) ? ' LIMIT ' . (int) $filters['limit'] : '';
    $offsetSql = '';
    if ($limitSql !== '' && !empty($filters['offset'])) {
        $offsetSql = ' OFFSET ' . max(0, (int) $filters['offset']);
    }

    $query = "
        SELECT
            p.product_id AS id,
            p.item_name AS title,
            p.price,
            COALESCE(c.category_name, 'Lainnya') AS category,
            COALESCE(a.file_path, '') AS image
        FROM products p
        LEFT JOIN categories c ON c.categories_id = p.category_id
        LEFT JOIN LATERAL (
            SELECT file_path
            FROM attachments
            WHERE product_id = p.product_id
            ORDER BY attachment_type ASC, attachment_id ASC
            LIMIT 1
        ) a ON TRUE
        WHERE {$whereSql}
        ORDER BY p.product_id DESC
        {$limitSql}
        {$offsetSql}
    ";

    $items = [];
    foreach (executeQuery($query, $bindings, 'fetchAll') as $row) {
        $items[] = [
            'id' => (int) $row['id'],
            'thumbnail' => (string) ($row['image'] ?? ''),
            'nama' => (string) $row['title'],
            'harga' => (int) $row['price'],
            'kategori' => (string) $row['category'],
            // TODO: derive from products.status + requests once the status mapping is implemented.
            'status' => 'Dijual',
        ];
    }

    return $items;
}

function count_owner_products(int $userId, array $filters = []): int
{
    [$whereSql, $bindings] = barang_owner_filter_sql($userId, $filters);

    $row = executeQuery(
        "SELECT COUNT(*) AS total
         FROM products p
         LEFT JOIN categories c ON c.categories_id = p.category_id
         WHERE {$whereSql}",
        $bindings,
        'fetch'
    );

    return (int) ($row['total'] ?? 0);
}

/** @return array{barang:int,dijual:int,pesanan:int,terjual:int} */
function get_owner_product_stats(int $userId): array
{
    $row = executeQuery(
        "SELECT COUNT(*) AS total,
                COUNT(*) FILTER (WHERE status = 'Tersedia') AS dijual,
                COUNT(*) FILTER (WHERE status = 'Proses') AS pesanan,
                COUNT(*) FILTER (WHERE status = 'Terjual') AS terjual
         FROM products
         WHERE user_id = ? AND status <> 'Dihapus'",
        [$userId],
        'fetch'
    );

    return [
        'barang' => (int) ($row['total'] ?? 0),
        'dijual' => (int) ($row['dijual'] ?? 0),
        'pesanan' => (int) ($row['pesanan'] ?? 0),
        'terjual' => (int) ($row['terjual'] ?? 0),
    ];
}
