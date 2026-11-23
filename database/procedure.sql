-- Pasar Kampus stored functions (PostgreSQL).
-- Every lifecycle write goes through these functions; direct SQL is for migration
-- and seed only. All mutations lock the relevant row with FOR UPDATE.

-- Offer: buyer places or replaces an offer on an available product.
DROP FUNCTION IF EXISTS place_offer(integer, integer, numeric);
CREATE OR REPLACE FUNCTION place_offer(p_product_id INT, p_user_id INT, p_price NUMERIC)
RETURNS INT
LANGUAGE plpgsql
AS $$
DECLARE
    v_price NUMERIC(19, 0);
    v_seller INT;
    v_offer_id INT;
BEGIN
    SELECT price, user_id INTO v_price, v_seller
    FROM products
    WHERE product_id = p_product_id
    FOR UPDATE;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'Produk % tidak ditemukan', p_product_id;
    END IF;

    IF v_seller = p_user_id THEN
        RAISE EXCEPTION 'Penjual tidak dapat menawar produk sendiri';
    END IF;

    IF EXISTS (
        SELECT 1 FROM purchase
        WHERE product_id = p_product_id AND cancelled_at IS NULL
    ) THEN
        RAISE EXCEPTION 'Produk sudah memiliki purchase aktif';
    END IF;

    IF p_price < v_price * 0.5 OR p_price >= v_price THEN
        RAISE EXCEPTION 'Harga tawar harus >= 50%% dan < harga produk (%)', v_price;
    END IF;

    IF EXISTS (
        SELECT 1 FROM offer
        WHERE product_id = p_product_id
          AND user_id = p_user_id
          AND status = 'OPEN'
    ) THEN
        RAISE EXCEPTION 'Offer masih OPEN; tidak dapat mengajukan offer baru';
    END IF;

    INSERT INTO offer (product_id, user_id, bargain_price, stage, status)
    VALUES (p_product_id, p_user_id, p_price, 1, 'OPEN')
    ON CONFLICT (product_id, user_id) DO UPDATE
        SET bargain_price = EXCLUDED.bargain_price,
            stage = 1,
            status = 'OPEN',
            updated_at = CURRENT_TIMESTAMP
    RETURNING offer_id INTO v_offer_id;

    RETURN v_offer_id;
END;
$$;

-- Offer: seller counters a stage-1 offer. Stage 2 locks the price for that buyer.
DROP FUNCTION IF EXISTS counter_offer(integer, integer, numeric);
CREATE OR REPLACE FUNCTION counter_offer(p_offer_id INT, p_actor_id INT, p_price NUMERIC)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    v_offer offer%ROWTYPE;
    v_seller INT;
    v_product_price NUMERIC(19, 0);
BEGIN
    SELECT * INTO v_offer
    FROM offer
    WHERE offer_id = p_offer_id
    FOR UPDATE;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'Offer % tidak ditemukan', p_offer_id;
    END IF;

    IF v_offer.status <> 'OPEN' THEN
        RAISE EXCEPTION 'Offer % sudah CLOSED', p_offer_id;
    END IF;

    IF v_offer.stage <> 1 THEN
        RAISE EXCEPTION 'Offer % sudah pada stage 2', p_offer_id;
    END IF;

    SELECT user_id INTO v_seller
    FROM products
    WHERE product_id = v_offer.product_id;

    IF p_actor_id <> v_seller THEN
        RAISE EXCEPTION 'Hanya penjual yang dapat mengajukan tawar banding';
    END IF;

    SELECT price INTO v_product_price
    FROM products
    WHERE product_id = v_offer.product_id
    FOR UPDATE;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'Produk % tidak ditemukan', v_offer.product_id;
    END IF;

    IF p_price <= v_offer.bargain_price OR p_price > v_product_price THEN
        RAISE EXCEPTION 'Harga banding harus > % dan <= %', v_offer.bargain_price, v_product_price;
    END IF;

    UPDATE offer
    SET bargain_price = p_price,
        stage = 2,
        updated_at = CURRENT_TIMESTAMP
    WHERE offer_id = p_offer_id;
END;
$$;

-- Offer: the other party accepts. Creates the purchase at the locked price and
-- closes every open offer for that product.
DROP FUNCTION IF EXISTS accept_offer(integer, integer);
CREATE OR REPLACE FUNCTION accept_offer(p_offer_id INT, p_actor_id INT)
RETURNS INT
LANGUAGE plpgsql
AS $$
DECLARE
    v_offer offer%ROWTYPE;
    v_seller INT;
    v_purchase_id INT;
BEGIN
    SELECT * INTO v_offer
    FROM offer
    WHERE offer_id = p_offer_id
    FOR UPDATE;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'Offer % tidak ditemukan', p_offer_id;
    END IF;

    IF v_offer.status <> 'OPEN' THEN
        RAISE EXCEPTION 'Offer % sudah CLOSED', p_offer_id;
    END IF;

    SELECT user_id INTO v_seller
    FROM products
    WHERE product_id = v_offer.product_id;

    IF v_offer.stage = 1 THEN
        IF p_actor_id <> v_seller THEN
            RAISE EXCEPTION 'Hanya penjual yang dapat menerima offer stage 1';
        END IF;
    ELSIF v_offer.stage = 2 THEN
        IF p_actor_id <> v_offer.user_id THEN
            RAISE EXCEPTION 'Hanya pembeli yang dapat menerima tawaran banding';
        END IF;
    END IF;

    PERFORM 1
    FROM products
    WHERE product_id = v_offer.product_id
    FOR UPDATE;

    IF EXISTS (
        SELECT 1 FROM purchase
        WHERE product_id = v_offer.product_id AND cancelled_at IS NULL
    ) THEN
        RAISE EXCEPTION 'Produk sudah memiliki purchase aktif';
    END IF;

    INSERT INTO purchase (user_id, product_id, amount)
    VALUES (v_offer.user_id, v_offer.product_id, v_offer.bargain_price)
    RETURNING purchase_id INTO v_purchase_id;

    UPDATE offer
    SET status = 'CLOSED',
        updated_at = CURRENT_TIMESTAMP
    WHERE product_id = v_offer.product_id
      AND status = 'OPEN';

    RETURN v_purchase_id;
END;
$$;

-- Purchase: direct buy at products.price. Closes every open offer.
DROP FUNCTION IF EXISTS create_purchase(integer, integer);
CREATE OR REPLACE FUNCTION create_purchase(p_product_id INT, p_user_id INT)
RETURNS INT
LANGUAGE plpgsql
AS $$
DECLARE
    v_product products%ROWTYPE;
    v_purchase_id INT;
BEGIN
    SELECT * INTO v_product
    FROM products
    WHERE product_id = p_product_id
    FOR UPDATE;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'Produk % tidak ditemukan', p_product_id;
    END IF;

    IF v_product.status <> 'Tersedia' THEN
        RAISE EXCEPTION 'Produk tidak tersedia (%)', v_product.status;
    END IF;

    IF v_product.user_id = p_user_id THEN
        RAISE EXCEPTION 'Penjual tidak dapat membeli produk sendiri';
    END IF;

    IF EXISTS (
        SELECT 1 FROM purchase
        WHERE product_id = p_product_id AND cancelled_at IS NULL
    ) THEN
        RAISE EXCEPTION 'Produk sudah memiliki purchase aktif';
    END IF;

    IF EXISTS (
        SELECT 1 FROM offer
        WHERE product_id = p_product_id
          AND user_id = p_user_id
          AND stage = 2
          AND status = 'OPEN'
    ) THEN
        RAISE EXCEPTION 'Gunakan terima tawaran penjual untuk offer stage 2';
    END IF;

    INSERT INTO purchase (user_id, product_id, amount)
    VALUES (p_user_id, p_product_id, v_product.price)
    RETURNING purchase_id INTO v_purchase_id;

    UPDATE offer
    SET status = 'CLOSED',
        updated_at = CURRENT_TIMESTAMP
    WHERE product_id = p_product_id
      AND status = 'OPEN';

    RETURN v_purchase_id;
END;
$$;

-- Purchase: seller fills the meeting point; product derives as Pickup.
DROP FUNCTION IF EXISTS set_meeting_point(integer, integer, numeric, numeric);
CREATE OR REPLACE FUNCTION set_meeting_point(p_purchase_id INT, p_actor_id INT, p_lat NUMERIC, p_lng NUMERIC)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    v_purchase purchase%ROWTYPE;
    v_seller INT;
BEGIN
    SELECT * INTO v_purchase
    FROM purchase
    WHERE purchase_id = p_purchase_id
    FOR UPDATE;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'Purchase % tidak ditemukan', p_purchase_id;
    END IF;

    IF v_purchase.cancelled_at IS NOT NULL THEN
        RAISE EXCEPTION 'Purchase % sudah dibatalkan', p_purchase_id;
    END IF;

    IF EXISTS (
        SELECT 1 FROM invoice WHERE purchase_id = p_purchase_id
    ) THEN
        RAISE EXCEPTION 'Purchase % sudah memiliki invoice', p_purchase_id;
    END IF;

    SELECT user_id INTO v_seller
    FROM products
    WHERE product_id = v_purchase.product_id;

    IF p_actor_id <> v_seller THEN
        RAISE EXCEPTION 'Hanya penjual yang dapat mengisi titik temu';
    END IF;

    IF p_lat IS NULL OR p_lng IS NULL THEN
        RAISE EXCEPTION 'Koordinat tidak boleh NULL';
    END IF;

    IF p_lat < -90 OR p_lat > 90 OR p_lng < -180 OR p_lng > 180 THEN
        RAISE EXCEPTION 'Koordinat di luar rentang';
    END IF;

    UPDATE purchase
    SET meeting_lat = p_lat,
        meeting_lng = p_lng,
        updated_at = CURRENT_TIMESTAMP
    WHERE purchase_id = p_purchase_id;
END;
$$;

-- Invoice: seller records payment with a proof attachment; product derives as Done.
DROP FUNCTION IF EXISTS create_invoice(integer, integer, integer, numeric, numeric, varchar);
CREATE OR REPLACE FUNCTION create_invoice(
    p_purchase_id INT,
    p_actor_id INT,
    p_attachment_id INT,
    p_pay NUMERIC,
    p_return NUMERIC,
    p_method VARCHAR
)
RETURNS INT
LANGUAGE plpgsql
AS $$
DECLARE
    v_purchase purchase%ROWTYPE;
    v_seller INT;
    v_invoice_id INT;
BEGIN
    SELECT * INTO v_purchase
    FROM purchase
    WHERE purchase_id = p_purchase_id
    FOR UPDATE;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'Purchase % tidak ditemukan', p_purchase_id;
    END IF;

    IF v_purchase.cancelled_at IS NOT NULL THEN
        RAISE EXCEPTION 'Purchase % sudah dibatalkan', p_purchase_id;
    END IF;

    IF EXISTS (
        SELECT 1 FROM invoice WHERE purchase_id = p_purchase_id
    ) THEN
        RAISE EXCEPTION 'Purchase % sudah memiliki invoice', p_purchase_id;
    END IF;

    IF v_purchase.meeting_lat IS NULL OR v_purchase.meeting_lng IS NULL THEN
        RAISE EXCEPTION 'Titik temu belum diisi';
    END IF;

    SELECT user_id INTO v_seller
    FROM products
    WHERE product_id = v_purchase.product_id;

    IF p_actor_id <> v_seller THEN
        RAISE EXCEPTION 'Hanya penjual yang dapat membuat invoice';
    END IF;

    IF p_method IS NULL OR p_method NOT IN ('Tunai', 'Transfer') THEN
        RAISE EXCEPTION 'Metode pembayaran tidak valid';
    END IF;

    IF p_pay IS NULL OR p_return IS NULL
       OR p_pay < 0 OR p_return < 0
       OR p_pay - p_return <> v_purchase.amount THEN
        RAISE EXCEPTION 'pay - return harus sama dengan amount (%)', v_purchase.amount;
    END IF;

    IF p_method = 'Transfer' AND p_return <> 0 THEN
        RAISE EXCEPTION 'Transfer tidak memiliki kembalian';
    END IF;

    INSERT INTO invoice (purchase_id, attachment_id, pay, return, method)
    VALUES (p_purchase_id, p_attachment_id, p_pay, p_return, p_method)
    RETURNING invoice_id INTO v_invoice_id;

    RETURN v_invoice_id;
END;
$$;

-- Purchase: soft cancel. The row stays as history; product returns to Belum Terjual.
DROP FUNCTION IF EXISTS cancel_purchase(integer, integer, varchar);
CREATE OR REPLACE FUNCTION cancel_purchase(p_purchase_id INT, p_actor_id INT, p_reason VARCHAR)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    v_purchase purchase%ROWTYPE;
    v_seller INT;
    v_role VARCHAR(10);
BEGIN
    SELECT * INTO v_purchase
    FROM purchase
    WHERE purchase_id = p_purchase_id
    FOR UPDATE;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'Purchase % tidak ditemukan', p_purchase_id;
    END IF;

    IF v_purchase.cancelled_at IS NOT NULL THEN
        RAISE EXCEPTION 'Purchase % sudah dibatalkan', p_purchase_id;
    END IF;

    IF EXISTS (
        SELECT 1 FROM invoice WHERE purchase_id = p_purchase_id
    ) THEN
        RAISE EXCEPTION 'Purchase % sudah memiliki invoice', p_purchase_id;
    END IF;

    SELECT user_id INTO v_seller
    FROM products
    WHERE product_id = v_purchase.product_id;

    IF p_actor_id = v_purchase.user_id THEN
        v_role := 'buyer';
    ELSIF p_actor_id = v_seller THEN
        v_role := 'seller';
    ELSE
        RAISE EXCEPTION 'Hanya pembeli atau penjual yang dapat membatalkan';
    END IF;

    IF v_role = 'buyer' AND v_purchase.meeting_lat IS NOT NULL THEN
        RAISE EXCEPTION 'Pembeli tidak dapat membatalkan setelah titik temu diisi';
    END IF;

    UPDATE purchase
    SET cancelled_at = CURRENT_TIMESTAMP,
        cancelled_by = v_role,
        cancel_reason = p_reason,
        updated_at = CURRENT_TIMESTAMP
    WHERE purchase_id = p_purchase_id;
END;
$$;

-- Read model: category ids from the user's newest profile, as a JSON array.
DROP FUNCTION IF EXISTS user_preferred_category_ids(integer);
CREATE OR REPLACE FUNCTION user_preferred_category_ids(p_user_id integer)
RETURNS jsonb
LANGUAGE sql STABLE
AS $$
    SELECT COALESCE(jsonb_agg(up.category_id ORDER BY up.category_id), '[]'::jsonb)
    FROM user_preferences up
    WHERE up.profile_id = (
        SELECT profile_id
        FROM user_profiles
        WHERE user_id = p_user_id
        ORDER BY profile_id DESC
        LIMIT 1
    );
$$;

-- Read model: the single source of catalog filtering. NULL or empty values
-- disable the matching filter.
DROP FUNCTION IF EXISTS catalog_matching_ids(text, jsonb, text, numeric, numeric, integer, integer, integer);
CREATE OR REPLACE FUNCTION catalog_matching_ids(
    p_search text DEFAULT NULL,
    p_categories jsonb DEFAULT NULL,
    p_campus text DEFAULT NULL,
    p_min_price numeric DEFAULT NULL,
    p_max_price numeric DEFAULT NULL,
    p_condition integer DEFAULT NULL,
    p_exclude_user_id integer DEFAULT NULL,
    p_exclude_id integer DEFAULT NULL
) RETURNS SETOF integer
LANGUAGE sql STABLE
AS $$
    SELECT d.id
    FROM v_product_display d
    WHERE d.listing_status = 'Tersedia'
      AND d.lifecycle <> 'Done'
      AND (p_search IS NULL OR TRIM(p_search) = ''
           OR d.title ILIKE '%' || TRIM(p_search) || '%'
           OR COALESCE(d.description, '') ILIKE '%' || TRIM(p_search) || '%'
           OR COALESCE(d.profile_campus, '') ILIKE '%' || TRIM(p_search) || '%')
      AND (p_categories IS NULL
           OR jsonb_typeof(p_categories) <> 'array'
           OR jsonb_array_length(p_categories) = 0
           OR d.category IN (SELECT jsonb_array_elements_text(p_categories)))
      AND (p_campus IS NULL OR TRIM(p_campus) = ''
           OR COALESCE(d.profile_campus, '') = TRIM(p_campus))
      AND (p_min_price IS NULL OR d.price >= p_min_price)
      AND (p_max_price IS NULL OR d.price <= p_max_price)
      AND (p_condition IS NULL OR p_condition < 1 OR p_condition > 4
           OR d.condition = p_condition)
      AND (p_exclude_user_id IS NULL OR p_exclude_user_id <= 0
           OR d.user_id <> p_exclude_user_id)
      AND (p_exclude_id IS NULL OR p_exclude_id <= 0 OR d.id <> p_exclude_id);
$$;

DROP FUNCTION IF EXISTS count_catalog_products(text, jsonb, text, numeric, numeric, integer, integer, integer);
CREATE OR REPLACE FUNCTION count_catalog_products(
    p_search text DEFAULT NULL,
    p_categories jsonb DEFAULT NULL,
    p_campus text DEFAULT NULL,
    p_min_price numeric DEFAULT NULL,
    p_max_price numeric DEFAULT NULL,
    p_condition integer DEFAULT NULL,
    p_exclude_user_id integer DEFAULT NULL,
    p_exclude_id integer DEFAULT NULL
) RETURNS bigint
LANGUAGE sql STABLE
AS $$
    SELECT COUNT(*)
    FROM catalog_matching_ids(
        p_search, p_categories, p_campus, p_min_price, p_max_price,
        p_condition, p_exclude_user_id, p_exclude_id);
$$;

-- Catalog page rows. Output columns are the components/common/card.php contract.
DROP FUNCTION IF EXISTS search_catalog(text, jsonb, text, numeric, numeric, integer, text, text, integer, integer, jsonb, integer, integer);
CREATE OR REPLACE FUNCTION search_catalog(
    p_search text DEFAULT NULL,
    p_categories jsonb DEFAULT NULL,
    p_campus text DEFAULT NULL,
    p_min_price numeric DEFAULT NULL,
    p_max_price numeric DEFAULT NULL,
    p_condition integer DEFAULT NULL,
    p_sort text DEFAULT 'recommended',
    p_seed text DEFAULT NULL,
    p_exclude_user_id integer DEFAULT NULL,
    p_exclude_id integer DEFAULT NULL,
    p_prefer_category_ids jsonb DEFAULT NULL,
    p_limit integer DEFAULT NULL,
    p_offset integer DEFAULT 0
) RETURNS TABLE (
    id integer,
    title text,
    short_desc text,
    category text,
    location text,
    "condition" text,
    original_price numeric,
    price numeric,
    image text,
    lifecycle text,
    lifecycle_label text
)
LANGUAGE sql STABLE
AS $$
    SELECT
        d.id,
        d.title,
        d.short_description,
        d.category,
        d.location,
        d.condition_label,
        d.original_price,
        d.price,
        d.image,
        d.lifecycle,
        d.lifecycle_label
    FROM v_product_display d
    JOIN catalog_matching_ids(
        p_search, p_categories, p_campus, p_min_price, p_max_price,
        p_condition, p_exclude_user_id, p_exclude_id
    ) AS ids(id) ON ids.id = d.id
    ORDER BY
        CASE WHEN p_sort = 'recommended'
             THEN (d.category_id IN (
                 SELECT (jsonb_array_elements_text(p_prefer_category_ids))::integer
             ))::integer
        END DESC NULLS LAST,
        CASE WHEN p_sort = 'price_asc' THEN d.price END ASC NULLS LAST,
        CASE WHEN p_sort = 'price_desc' THEN d.price END DESC NULLS LAST,
        CASE WHEN p_sort = 'recommended'
                  AND NULLIF(TRIM(COALESCE(p_seed, '')), '') IS NOT NULL
             THEN MD5(d.id::text || p_seed)
        END ASC NULLS LAST,
        d.id DESC
    LIMIT COALESCE(NULLIF(p_limit, 0), 2147483647)
    OFFSET GREATEST(COALESCE(p_offset, 0), 0);
$$;

-- Read model: the single source of seller listing filtering. p_status uses the
-- seller-facing labels.
DROP FUNCTION IF EXISTS owner_matching_ids(integer, text, jsonb, text);
CREATE OR REPLACE FUNCTION owner_matching_ids(
    p_user_id integer,
    p_search text DEFAULT NULL,
    p_categories jsonb DEFAULT NULL,
    p_status text DEFAULT NULL
) RETURNS SETOF integer
LANGUAGE sql STABLE
AS $$
    SELECT o.id
    FROM v_owner_products o
    WHERE o.user_id = p_user_id
      AND (p_search IS NULL OR TRIM(p_search) = ''
           OR o.title ILIKE '%' || TRIM(p_search) || '%'
           OR COALESCE(o.description, '') ILIKE '%' || TRIM(p_search) || '%')
      AND (p_categories IS NULL
           OR jsonb_typeof(p_categories) <> 'array'
           OR jsonb_array_length(p_categories) = 0
           OR o.category IN (SELECT jsonb_array_elements_text(p_categories)))
      AND (p_status IS NULL OR TRIM(p_status) = ''
           OR (p_status = 'Dijual' AND o.lifecycle = 'Belum Terjual')
           OR (p_status = 'Pemesanan' AND o.lifecycle = 'Appointment')
           OR (p_status = 'Pengambilan' AND o.lifecycle = 'Pickup')
           OR (p_status = 'Terjual' AND o.lifecycle = 'Done'));
$$;

DROP FUNCTION IF EXISTS count_owner_products(integer, text, jsonb, text);
CREATE OR REPLACE FUNCTION count_owner_products(
    p_user_id integer,
    p_search text DEFAULT NULL,
    p_categories jsonb DEFAULT NULL,
    p_status text DEFAULT NULL
) RETURNS bigint
LANGUAGE sql STABLE
AS $$
    SELECT COUNT(*)
    FROM owner_matching_ids(p_user_id, p_search, p_categories, p_status);
$$;

-- Seller page rows. Output columns are the components/common/card-long.php props.
DROP FUNCTION IF EXISTS owner_products(integer, text, jsonb, text, integer, integer);
CREATE OR REPLACE FUNCTION owner_products(
    p_user_id integer,
    p_search text DEFAULT NULL,
    p_categories jsonb DEFAULT NULL,
    p_status text DEFAULT NULL,
    p_limit integer DEFAULT NULL,
    p_offset integer DEFAULT 0
) RETURNS TABLE (
    id integer,
    thumbnail text,
    nama text,
    harga numeric,
    kategori text,
    status text,
    purchase_id integer
)
LANGUAGE sql STABLE
AS $$
    SELECT o.id, o.thumbnail, o.title, o.price, o.category, o.status, o.purchase_id
    FROM v_owner_products o
    JOIN owner_matching_ids(p_user_id, p_search, p_categories, p_status) AS ids(id)
      ON ids.id = o.id
    ORDER BY o.id DESC
    LIMIT COALESCE(NULLIF(p_limit, 0), 2147483647)
    OFFSET GREATEST(COALESCE(p_offset, 0), 0);
$$;

-- Offer inbox paging: role is 'seller' or 'buyer'; status is the role-relative
-- state key from v_offer_inbox. Argument order mirrors owner_matching_ids.
DROP FUNCTION IF EXISTS offer_matching_ids(integer, text, text, text, date, date);
CREATE OR REPLACE FUNCTION offer_matching_ids(
    p_user_id integer,
    p_role text,
    p_search text DEFAULT NULL,
    p_status text DEFAULT NULL,
    p_start date DEFAULT NULL,
    p_end date DEFAULT NULL
) RETURNS SETOF integer
LANGUAGE sql STABLE
AS $$
    SELECT i.offer_id
    FROM v_offer_inbox i
    WHERE CASE WHEN p_role = 'seller' THEN i.seller_id ELSE i.buyer_id END = p_user_id
      AND (p_status IS NULL OR TRIM(p_status) = ''
           OR (p_role = 'seller' AND i.seller_state = p_status)
           OR (p_role <> 'seller' AND i.buyer_state = p_status))
      AND (p_search IS NULL OR TRIM(p_search) = ''
           OR i.product_title ILIKE '%' || TRIM(p_search) || '%')
      AND (p_start IS NULL OR i.created_at::date >= p_start)
      AND (p_end IS NULL OR i.created_at::date <= p_end);
$$;

DROP FUNCTION IF EXISTS count_offers(integer, text, text, text, date, date);
CREATE OR REPLACE FUNCTION count_offers(
    p_user_id integer,
    p_role text,
    p_search text DEFAULT NULL,
    p_status text DEFAULT NULL,
    p_start date DEFAULT NULL,
    p_end date DEFAULT NULL
) RETURNS bigint
LANGUAGE sql STABLE
AS $$
    SELECT COUNT(*)
    FROM offer_matching_ids(p_user_id, p_role, p_search, p_status, p_start, p_end);
$$;

DROP FUNCTION IF EXISTS offer_rows(integer, text, text, text, date, date, integer, integer);
CREATE OR REPLACE FUNCTION offer_rows(
    p_user_id integer,
    p_role text,
    p_search text DEFAULT NULL,
    p_status text DEFAULT NULL,
    p_start date DEFAULT NULL,
    p_end date DEFAULT NULL,
    p_limit integer DEFAULT NULL,
    p_offset integer DEFAULT 0
) RETURNS TABLE (
    offer_id integer,
    product_id integer,
    product_title text,
    thumbnail text,
    asking_price numeric,
    offer_price numeric,
    stage smallint,
    status varchar,
    state text,
    buyer_name text,
    seller_name text,
    created_at timestamp
)
LANGUAGE sql STABLE
AS $$
    SELECT i.offer_id, i.product_id, i.product_title, i.thumbnail,
           i.asking_price, i.offer_price, i.stage, i.status,
           CASE WHEN p_role = 'seller' THEN i.seller_state ELSE i.buyer_state END,
           i.buyer_name, i.seller_name, i.created_at
    FROM v_offer_inbox i
    JOIN offer_matching_ids(p_user_id, p_role, p_search, p_status, p_start, p_end) AS ids(offer_id)
      ON ids.offer_id = i.offer_id
    ORDER BY (i.status = 'CLOSED'), i.created_at DESC, i.offer_id DESC
    LIMIT COALESCE(NULLIF(p_limit, 0), 2147483647)
    OFFSET GREATEST(COALESCE(p_offset, 0), 0);
$$;

-- Detail page state for one viewer: their offer and active purchase on a product.
-- Always returns exactly one row, all-NULL when the viewer has neither.
DROP FUNCTION IF EXISTS product_viewer_state(integer, integer);
CREATE OR REPLACE FUNCTION product_viewer_state(p_product_id integer, p_user_id integer)
RETURNS TABLE (
    offer_id integer,
    offer_stage smallint,
    offer_status varchar,
    offer_price numeric,
    purchase_id integer,
    purchase_amount numeric
)
LANGUAGE sql STABLE
AS $$
    SELECT off.offer_id, off.stage, off.status, off.bargain_price,
           act.purchase_id, act.amount
    FROM (SELECT 1) AS one
    LEFT JOIN LATERAL (
        SELECT offer_id, stage, status, bargain_price FROM offer
        WHERE product_id = p_product_id AND user_id = p_user_id
        LIMIT 1
    ) off ON TRUE
    LEFT JOIN LATERAL (
        SELECT purchase_id, amount FROM purchase
        WHERE product_id = p_product_id AND user_id = p_user_id AND cancelled_at IS NULL
        LIMIT 1
    ) act ON TRUE;
$$;
