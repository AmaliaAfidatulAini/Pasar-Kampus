-- Pasar Kampus read models (PostgreSQL).
-- Load order: schema.sql -> seed.sql -> views.sql -> procedure.sql.
-- procedure.sql functions depend on these views, so this file runs first.

DROP VIEW IF EXISTS v_product_display, v_owner_products, v_owner_product_stats,
    v_product_lifecycle, v_admin_stats, v_admin_campus_distribution,
    v_admin_activity, v_categories, v_campuses, v_user_header, v_offer_inbox,
    v_purchase_history CASCADE;

-- One row per product with the derived selling lifecycle.
CREATE OR REPLACE VIEW v_product_lifecycle AS
SELECT
    p.product_id,
    p.user_id,
    p.category_id,
    p.item_name,
    p.description,
    p.price,
    p.original_price,
    p.condition,
    p.status AS listing_status,
    p.moderation_note,
    p.delivery_methods,
    p.created_at,
    CASE
        WHEN p.status <> 'Tersedia' THEN p.status
        WHEN i.invoice_id IS NOT NULL THEN 'Done'
        WHEN pu.meeting_lat IS NOT NULL AND pu.meeting_lng IS NOT NULL THEN 'Pickup'
        WHEN pu.purchase_id IS NOT NULL THEN 'Appointment'
        ELSE 'Belum Terjual'
    END AS lifecycle
FROM products p
LEFT JOIN purchase pu
    ON pu.product_id = p.product_id AND pu.cancelled_at IS NULL
LEFT JOIN invoice i ON i.purchase_id = pu.purchase_id;

-- Display-ready product rows: seller profile, primary image, tags and gallery
-- are resolved here so PHP never joins or formats.
CREATE OR REPLACE VIEW v_product_display AS
SELECT
    d.*,
    CASE
        WHEN char_length(plain.text) > 95
            THEN LEFT(plain.text, 95) || '..'
        ELSE plain.text
    END AS short_description
FROM (
    SELECT
        l.product_id AS id,
        l.user_id,
        l.category_id,
        l.item_name AS title,
        l.description,
        COALESCE(
            NULLIF(TRIM(l.description), ''),
            'Kondisi barang ' || LOWER(cond.label)
                || ', siap transaksi COD area '
                || COALESCE(NULLIF(TRIM(prof.campus), ''), 'kampus') || '.'
        ) AS description_display,
        l.price,
        COALESCE(l.original_price, l.price) AS original_price,
        l.condition,
        cond.label AS condition_label,
        l.listing_status,
        l.lifecycle,
        CASE l.lifecycle
            WHEN 'Belum Terjual' THEN 'Dijual'
            WHEN 'Appointment' THEN 'Pemesanan'
            WHEN 'Pickup' THEN 'Pengambilan'
            WHEN 'Done' THEN 'Terjual'
            ELSE l.lifecycle
        END AS lifecycle_label,
        l.moderation_note,
        COALESCE(l.delivery_methods, 'COD') AS method,
        l.created_at,
        COALESCE(c.category_name, 'Lainnya') AS category,
        COALESCE(prof.name, 'Penjual Kampus') AS seller_name,
        COALESCE(prof.phone, '081234567890') AS seller_phone,
        prof.campus AS profile_campus,
        COALESCE(NULLIF(TRIM(prof.campus), ''), 'Area Kampus') AS campus,
        COALESCE(
            NULLIF(CONCAT_WS(', ',
                NULLIF(TRIM(prof.district), ''),
                NULLIF(TRIM(prof.subdistrict), '')), ''),
            CASE
                WHEN NULLIF(TRIM(prof.campus), '') IS NULL THEN 'Kecamatan Lowokwaru'
                WHEN TRIM(prof.campus) LIKE 'Area %' THEN TRIM(prof.campus)
                ELSE 'Area ' || TRIM(prof.campus)
            END
        ) AS location,
        COALESCE(
            NULLIF(TRIM(img.file_path), ''),
            'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=300'
        ) AS image,
        CASE
            WHEN tg.tags IS NULL OR jsonb_array_length(tg.tags) = 0
                THEN jsonb_build_array(
                    '#Preloved',
                    '#' || REGEXP_REPLACE(COALESCE(c.category_name, 'Lainnya'), '\s+', '', 'g')
                )
            ELSE tg.tags
        END AS tags,
        CASE
            WHEN gl.gallery IS NULL OR jsonb_array_length(gl.gallery) = 0
                THEN jsonb_build_array(
                    COALESCE(
                        NULLIF(TRIM(img.file_path), ''),
                        'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=300'
                    ))
            ELSE gl.gallery
        END AS gallery
    FROM v_product_lifecycle l
    LEFT JOIN categories c ON c.categories_id = l.category_id
    LEFT JOIN (VALUES
        (1, 'Bekas'), (2, 'Cukup'), (3, 'Baik'), (4, 'Sangat Bagus')
    ) AS cond(value, label) ON cond.value = COALESCE(l.condition, 3)
    LEFT JOIN LATERAL (
        SELECT name, phone, campus, district, subdistrict
        FROM user_profiles
        WHERE user_id = l.user_id
        ORDER BY profile_id DESC
        LIMIT 1
    ) prof ON TRUE
    LEFT JOIN LATERAL (
        SELECT file_path
        FROM attachments
        WHERE product_id = l.product_id
        ORDER BY attachment_type ASC, attachment_id ASC
        LIMIT 1
    ) img ON TRUE
    LEFT JOIN LATERAL (
        SELECT jsonb_agg(tag ORDER BY tag) AS tags
        FROM (
            SELECT DISTINCT
                CASE
                    WHEN LEFT(TRIM(t.name_tag), 1) = '#' THEN TRIM(t.name_tag)
                    ELSE '#' || TRIM(t.name_tag)
                END AS tag
            FROM products_tags pt
            JOIN tags t ON t.id_tag = pt.products_tagsid_tag
            WHERE pt.productsproduct_id = l.product_id
              AND TRIM(COALESCE(t.name_tag, '')) <> ''
        ) tag_rows
    ) tg ON TRUE
    LEFT JOIN LATERAL (
        SELECT jsonb_agg(a.file_path ORDER BY a.attachment_type, a.attachment_id) AS gallery
        FROM attachments a
        WHERE a.product_id = l.product_id
          AND NULLIF(TRIM(a.file_path), '') IS NOT NULL
    ) gl ON TRUE
) d
CROSS JOIN LATERAL (
    -- Tags become spaces so list items and paragraphs stay separable, then the
    -- whitespace runs collapse to single spaces.
    SELECT btrim(regexp_replace(regexp_replace(d.description_display, '<[^>]+>', ' ', 'g'), '\s+', ' ', 'g')) AS text
) plain;

-- Seller "Barang Saya" cards: one row per non-deleted listing of any seller.
CREATE OR REPLACE VIEW v_owner_products AS
SELECT
    d.id,
    d.user_id,
    d.title,
    d.description,
    d.price,
    d.category,
    d.image AS thumbnail,
    d.lifecycle,
    d.lifecycle_label AS status,
    active_purchase.purchase_id
FROM v_product_display d
LEFT JOIN LATERAL (
    SELECT pu.purchase_id
    FROM purchase pu
    WHERE pu.product_id = d.id AND pu.cancelled_at IS NULL
    LIMIT 1
) active_purchase ON TRUE
WHERE d.listing_status <> 'Dihapus';

-- Seller KPI counters. pesanan = Appointment + Pickup, terjual = Done.
CREATE OR REPLACE VIEW v_owner_product_stats AS
SELECT
    user_id,
    COUNT(*) AS barang,
    COUNT(*) FILTER (WHERE lifecycle = 'Belum Terjual') AS dijual,
    COUNT(*) FILTER (WHERE lifecycle IN ('Appointment', 'Pickup')) AS pesanan,
    COUNT(*) FILTER (WHERE lifecycle = 'Done') AS terjual
FROM v_product_lifecycle
WHERE listing_status <> 'Dihapus'
GROUP BY user_id;

CREATE OR REPLACE VIEW v_admin_stats AS
SELECT
    (SELECT COUNT(*) FROM products) AS products,
    (SELECT COUNT(*) FROM categories) AS categories,
    (SELECT COUNT(*) FROM reports WHERE review_status = 'Menunggu') AS reports,
    (SELECT COUNT(*) FROM users WHERE account_status = 'Aktif') AS users;

CREATE OR REPLACE VIEW v_admin_campus_distribution AS
SELECT
    COALESCE(prof.campus, 'Belum diisi') AS campus,
    COUNT(*) AS total
FROM users u
LEFT JOIN user_profiles prof ON prof.user_id = u.user_id
WHERE u.deleted_at IS FALSE AND u.role = 'user'
GROUP BY prof.campus
ORDER BY total DESC;

CREATE OR REPLACE VIEW v_admin_activity AS
SELECT 'Barang ditambahkan'::text AS label, p.item_name AS detail, p.created_at AS happened_at
FROM products p
UNION ALL
SELECT 'Pengajuan transaksi', p.item_name, pu.created_at
FROM purchase pu
JOIN products p ON p.product_id = pu.product_id
UNION ALL
SELECT 'Laporan masuk', rp.report_type, rp.created_at
FROM reports rp;

CREATE OR REPLACE VIEW v_categories AS
SELECT categories_id AS id, category_name AS name
FROM categories
ORDER BY category_name ASC;

CREATE OR REPLACE VIEW v_campuses AS
SELECT DISTINCT campus
FROM user_profiles
WHERE campus IS NOT NULL AND TRIM(campus) <> ''
ORDER BY campus ASC;

-- Header profile summary: newest profile name plus its avatar path, one row per user.
CREATE OR REPLACE VIEW v_user_header AS
SELECT
    u.user_id,
    u.email,
    NULLIF(TRIM(prof.name), '') AS name,
    NULLIF(TRIM(att.file_path), '') AS avatar_path
FROM users u
LEFT JOIN LATERAL (
    SELECT name, attachment_id
    FROM user_profiles
    WHERE user_id = u.user_id
    ORDER BY profile_id DESC
    LIMIT 1
) prof ON TRUE
LEFT JOIN attachments att ON att.attachment_id = prof.attachment_id;

-- Offer inbox: one row per offer with both parties, prices, and a state key
-- resolved per role. Seller and buyer inboxes read the same rows.
CREATE OR REPLACE VIEW v_offer_inbox AS
SELECT
    o.offer_id,
    o.product_id,
    o.user_id AS buyer_id,
    p.user_id AS seller_id,
    p.item_name AS product_title,
    p.price AS asking_price,
    o.bargain_price AS offer_price,
    o.stage,
    o.status,
    o.created_at,
    COALESCE(NULLIF(TRIM(img.file_path), ''),
        'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=300') AS thumbnail,
    COALESCE(buyer.name, 'Pembeli Kampus') AS buyer_name,
    buyer.phone AS buyer_phone,
    COALESCE(seller.name, 'Penjual Kampus') AS seller_name,
    CASE
        WHEN o.status = 'CLOSED' THEN 'ditutup'
        WHEN o.stage = 2 THEN 'balik'
        ELSE 'menunggu'
    END AS buyer_state,
    CASE
        WHEN o.status = 'CLOSED' THEN 'ditutup'
        WHEN o.stage = 1 THEN 'baru'
        ELSE 'menunggu'
    END AS seller_state
FROM offer o
JOIN products p ON p.product_id = o.product_id
LEFT JOIN LATERAL (
    SELECT name, phone FROM user_profiles
    WHERE user_id = o.user_id ORDER BY profile_id DESC LIMIT 1
) buyer ON TRUE
LEFT JOIN LATERAL (
    SELECT name FROM user_profiles
    WHERE user_id = p.user_id ORDER BY profile_id DESC LIMIT 1
) seller ON TRUE
LEFT JOIN LATERAL (
    SELECT file_path FROM attachments
    WHERE product_id = o.product_id
    ORDER BY attachment_type, attachment_id LIMIT 1
) img ON TRUE;

-- Purchase history: one row per purchase with its own derived status, both
-- parties, product card fields, meeting point and invoice (when one exists).
-- Serves /riwayat/pembelian, /riwayat/penjualan, /riwayat/detail and /invoice.
-- The status is per purchase, not per product: a cancelled purchase shows
-- 'cancelled' even though the product is listed again as Belum Terjual.
CREATE OR REPLACE VIEW v_purchase_history AS
SELECT
    pu.purchase_id,
    pu.product_id,
    pu.user_id AS buyer_id,
    p.user_id AS seller_id,
    p.item_name AS product_title,
    pu.amount,
    pu.meeting_lat,
    pu.meeting_lng,
    pu.cancelled_at,
    pu.cancelled_by,
    pu.cancel_reason,
    pu.created_at,
    CASE
        WHEN pu.cancelled_at IS NOT NULL THEN 'cancelled'
        WHEN i.invoice_id IS NOT NULL THEN 'done'
        WHEN pu.meeting_lat IS NOT NULL AND pu.meeting_lng IS NOT NULL THEN 'pickup'
        ELSE 'appointment'
    END AS status,
    COALESCE(NULLIF(TRIM(img.file_path), ''),
        'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=300') AS thumbnail,
    COALESCE(buyer.name, 'Pembeli Kampus') AS buyer_name,
    buyer.phone AS buyer_phone,
    COALESCE(buyer.location, 'Area Kampus') AS buyer_location,
    COALESCE(seller.name, 'Penjual Kampus') AS seller_name,
    seller.phone AS seller_phone,
    COALESCE(seller.location, 'Area Kampus') AS seller_location,
    i.invoice_id,
    i.pay,
    i."return" AS change_amount,
    i.method,
    i.created_at AS invoiced_at,
    proof.file_path AS proof_path
FROM purchase pu
JOIN products p ON p.product_id = pu.product_id
LEFT JOIN invoice i ON i.purchase_id = pu.purchase_id
LEFT JOIN attachments proof ON proof.attachment_id = i.attachment_id
LEFT JOIN LATERAL (
    SELECT prof.name, prof.phone,
        COALESCE(
            NULLIF(CONCAT_WS(', ',
                NULLIF(TRIM(prof.district), ''),
                NULLIF(TRIM(prof.subdistrict), '')), ''),
            NULLIF(TRIM(prof.campus), '')
        ) AS location
    FROM user_profiles prof
    WHERE prof.user_id = pu.user_id
    ORDER BY prof.profile_id DESC
    LIMIT 1
) buyer ON TRUE
LEFT JOIN LATERAL (
    SELECT prof.name, prof.phone,
        COALESCE(
            NULLIF(CONCAT_WS(', ',
                NULLIF(TRIM(prof.district), ''),
                NULLIF(TRIM(prof.subdistrict), '')), ''),
            NULLIF(TRIM(prof.campus), '')
        ) AS location
    FROM user_profiles prof
    WHERE prof.user_id = p.user_id
    ORDER BY prof.profile_id DESC
    LIMIT 1
) seller ON TRUE
LEFT JOIN LATERAL (
    SELECT a.file_path
    FROM attachments a
    WHERE a.product_id = p.product_id
    ORDER BY a.attachment_type, a.attachment_id
    LIMIT 1
) img ON TRUE;
