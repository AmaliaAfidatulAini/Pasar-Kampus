<?php
declare(strict_types=1);

/**
 * Catalog helper functions for PasarKampus
 */

function format_rupiah(int|float $number): string
{
    return 'Rp. ' . number_format($number, 0, ',', '.');
}

function truncate_text(string $text, int $length = 95, string $suffix = '..'): string
{
    if (function_exists('mb_strimwidth')) {
        return mb_strimwidth($text, 0, $length, $suffix);
    }
    return strlen($text) > $length ? (substr($text, 0, $length) . $suffix) : $text;
}

/**
 * Get all available products for buyer catalog / marketplace
 * Fetches from database where status IN ('Tersedia', 'ONSELL')
 * Sorted newest first (p.product_id DESC) so newly uploaded items appear at the front!
 *
 * @param array $filters Optional filter parameters (category, search, limit, etc.)
 * @return array
 */
function get_catalog_products(array $filters = []): array
{
    global $pdo;

    $where = ["p.status IN ('Tersedia', 'ONSELL')"];
    $bindings = [];

    if (!empty($filters['category'])) {
        $where[] = "c.category_name ILIKE :cat";
        $bindings[':cat'] = '%' . trim((string)$filters['category']) . '%';
    }

    if (!empty($filters['search'])) {
        $where[] = "(p.item_name ILIKE :kw OR p.description ILIKE :kw OR COALESCE(up.campus, '') ILIKE :kw)";
        $bindings[':kw'] = '%' . trim((string)$filters['search']) . '%';
    }

    $whereSql = implode(' AND ', $where);
    $limitSql = !empty($filters['limit']) ? (' LIMIT ' . (int)$filters['limit']) : '';

    $query = "
        SELECT 
            p.product_id AS id,
            p.item_name AS title,
            p.description,
            p.price,
            p.original_price,
            p.condition,
            p.status,
            p.delivery_methods,
            p.user_id,
            p.created_at,
            COALESCE(c.category_name, 'Lainnya') AS category,
            COALESCE(up.name, 'Penjual Kampus') AS seller_name,
            COALESCE(up.phone, '081234567890') AS seller_phone,
            COALESCE(up.campus, 'Area Kampus') AS campus,
            CONCAT_WS(', ', NULLIF(TRIM(up.district), ''), NULLIF(TRIM(up.subdistrict), '')) AS location,
            COALESCE(a.file_path, 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=300') AS image
        FROM products p
        LEFT JOIN categories c ON c.categories_id = p.category_id
        LEFT JOIN LATERAL (
            SELECT name, phone, campus, district, subdistrict
            FROM user_profiles
            WHERE user_id = p.user_id
            ORDER BY profile_id DESC
            LIMIT 1
        ) up ON TRUE
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
    ";

    try {
        $rows = executeQuery($query, $bindings, 'fetchAll');
    } catch (Throwable $e) {
        error_log('get_catalog_products error: ' . $e->getMessage());
        return [];
    }

    if (empty($rows)) {
        return [];
    }

    $productIds = array_map(static fn($r) => (int)$r['id'], $rows);
    $tagsByProduct = [];
    $galleryByProduct = [];

    if (!empty($productIds)) {
        $inClause = implode(',', $productIds);
        try {
            $tagRows = executeQuery(
                "SELECT pt.productsproduct_id, t.name_tag 
                 FROM products_tags pt 
                 JOIN tags t ON t.id_tag = pt.products_tagsid_tag 
                 WHERE pt.productsproduct_id IN ({$inClause})",
                [],
                'fetchAll'
            );
            foreach ($tagRows as $tr) {
                $pid = (int) $tr['productsproduct_id'];
                $tagName = trim((string)$tr['name_tag']);
                if ($tagName !== '') {
                    $tagsByProduct[$pid][] = str_starts_with($tagName, '#') ? $tagName : ('#' . $tagName);
                }
            }
        } catch (Throwable $e) {}

        try {
            $attRows = executeQuery(
                "SELECT product_id, file_path, attachment_type 
                 FROM attachments 
                 WHERE product_id IN ({$inClause}) 
                 ORDER BY attachment_type ASC, attachment_id ASC",
                [],
                'fetchAll'
            );
            foreach ($attRows as $ar) {
                $pid = (int) $ar['product_id'];
                $path = trim((string)($ar['file_path'] ?? ''));
                if ($path !== '') {
                    $galleryByProduct[$pid][] = $path;
                }
            }
        } catch (Throwable $e) {}
    }

    $conditionMap = [
        1 => 'Bekas',
        2 => 'Cukup',
        3 => 'Baik',
        4 => 'Sangat Bagus'
    ];

    $formatted = [];
    foreach ($rows as $row) {
        $pid = (int) $row['id'];
        $rawCond = (int) ($row['condition'] ?? 3);
        $condText = $conditionMap[$rawCond] ?? 'Baik';

        $loc = trim((string)($row['location'] ?? ''));
        if ($loc === '') {
            $loc = !empty($row['campus']) ? ('Area ' . $row['campus']) : 'Kecamatan Lowokwaru';
        }

        $gallery = $galleryByProduct[$pid] ?? [];
        if (empty($gallery) && !empty($row['image'])) {
            $gallery = [$row['image']];
        }

        $tags = $tagsByProduct[$pid] ?? ['#Preloved', '#' . preg_replace('/\s+/', '', (string)$row['category'])];

        $desc = trim((string)($row['description'] ?? ''));
        if ($desc === '') {
            $desc = 'Kondisi barang ' . strtolower($condText) . ', siap transaksi COD area ' . ($row['campus'] ?: 'kampus') . '.';
        }

        $formatted[] = [
            'id' => $pid,
            'title' => $row['title'],
            'category' => $row['category'],
            'condition' => $condText,
            'condition_spec' => $condText,
            'method' => $row['delivery_methods'] ?: 'COD',
            'location' => $loc,
            'campus' => $row['campus'] ?: 'Area Kampus',
            'tags' => $tags,
            'original_price' => $row['original_price'] !== null ? (int)$row['original_price'] : (int)$row['price'],
            'price' => (int) $row['price'],
            'image' => $row['image'],
            'gallery' => $gallery,
            'description' => $desc,
            'short_desc' => truncate_text($desc, 95, '..'),
            'status' => $row['status'],
            'user_id' => (int) $row['user_id'],
            'seller_name' => $row['seller_name'],
            'seller_phone' => $row['seller_phone']
        ];
    }

    return $formatted;
}

/**
 * Get single catalog product by ID directly from database
 *
 * @param int|string $id
 * @return array|null Returns product data or null if not found
 */
function get_catalog_product_by_id(int|string $id): ?array
{
    $id = (int) $id;
    if ($id <= 0) {
        return null;
    }

    $query = "
        SELECT 
            p.product_id AS id,
            p.item_name AS title,
            p.description,
            p.price,
            p.original_price,
            p.condition,
            p.status,
            p.delivery_methods,
            p.user_id,
            p.moderation_note,
            p.created_at,
            COALESCE(c.category_name, 'Lainnya') AS category,
            COALESCE(up.name, 'Penjual Kampus') AS seller_name,
            COALESCE(up.phone, '081234567890') AS seller_phone,
            COALESCE(up.campus, 'Area Kampus') AS campus,
            CONCAT_WS(', ', NULLIF(TRIM(up.district), ''), NULLIF(TRIM(up.subdistrict), '')) AS location,
            COALESCE(a.file_path, 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=300') AS image
        FROM products p
        LEFT JOIN categories c ON c.categories_id = p.category_id
        LEFT JOIN LATERAL (
            SELECT name, phone, campus, district, subdistrict
            FROM user_profiles
            WHERE user_id = p.user_id
            ORDER BY profile_id DESC
            LIMIT 1
        ) up ON TRUE
        LEFT JOIN LATERAL (
            SELECT file_path
            FROM attachments
            WHERE product_id = p.product_id
            ORDER BY attachment_type ASC, attachment_id ASC
            LIMIT 1
        ) a ON TRUE
        WHERE p.product_id = :id
        LIMIT 1
    ";

    try {
        $row = executeQuery($query, ['id' => $id], 'fetch');
    } catch (Throwable $e) {
        error_log('get_catalog_product_by_id error: ' . $e->getMessage());
        return null;
    }

    if (!$row) {
        return null;
    }

    // Fetch tags
    $tags = [];
    try {
        $tagRows = executeQuery(
            "SELECT t.name_tag 
             FROM products_tags pt 
             JOIN tags t ON t.id_tag = pt.products_tagsid_tag 
             WHERE pt.productsproduct_id = :id",
            ['id' => $id],
            'fetchAll'
        );
        foreach ($tagRows as $tr) {
            $tagName = trim((string)$tr['name_tag']);
            if ($tagName !== '') {
                $tags[] = str_starts_with($tagName, '#') ? $tagName : ('#' . $tagName);
            }
        }
    } catch (Throwable $e) {}

    // Fetch gallery
    $gallery = [];
    try {
        $attRows = executeQuery(
            "SELECT file_path 
             FROM attachments 
             WHERE product_id = :id 
             ORDER BY attachment_type ASC, attachment_id ASC",
            ['id' => $id],
            'fetchAll'
        );
        foreach ($attRows as $ar) {
            $path = trim((string)($ar['file_path'] ?? ''));
            if ($path !== '') {
                $gallery[] = $path;
            }
        }
    } catch (Throwable $e) {}

    if (empty($gallery) && !empty($row['image'])) {
        $gallery = [$row['image']];
    }

    $conditionMap = [
        1 => 'Bekas',
        2 => 'Cukup',
        3 => 'Baik',
        4 => 'Sangat Bagus'
    ];
    $rawCond = (int) ($row['condition'] ?? 3);
    $condText = $conditionMap[$rawCond] ?? 'Baik';

    $loc = trim((string)($row['location'] ?? ''));
    if ($loc === '') {
        $loc = !empty($row['campus']) ? ('Area ' . $row['campus']) : 'Kecamatan Lowokwaru';
    }

    $desc = trim((string)($row['description'] ?? ''));
    if ($desc === '') {
        $desc = 'Kondisi barang ' . strtolower($condText) . ', siap transaksi COD area ' . ($row['campus'] ?: 'kampus') . '.';
    }

    return [
        'id' => $id,
        'title' => $row['title'],
        'category' => $row['category'],
        'condition' => $condText,
        'condition_spec' => $condText,
        'method' => $row['delivery_methods'] ?: 'COD',
        'location' => $loc,
        'campus' => $row['campus'] ?: 'Area Kampus',
        'tags' => !empty($tags) ? $tags : ['#Preloved', '#' . preg_replace('/\s+/', '', (string)$row['category'])],
        'original_price' => $row['original_price'] !== null ? (int)$row['original_price'] : (int)$row['price'],
        'price' => (int) $row['price'],
        'image' => $row['image'],
        'gallery' => $gallery,
        'description' => $desc,
        'short_desc' => truncate_text($desc, 95, '..'),
        'status' => $row['status'],
        'user_id' => (int) $row['user_id'],
        'seller_name' => $row['seller_name'],
        'seller_phone' => $row['seller_phone'],
        'moderation_note' => $row['moderation_note'] ?? null
    ];
}
