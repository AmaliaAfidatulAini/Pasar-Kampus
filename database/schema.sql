-- Pasar Kampus schema (PostgreSQL only, fresh database).
-- Target database must be created beforehand; this file never drops or creates databases.

DROP TABLE IF EXISTS invoice, attachments, reports, offer, purchase,
    products_tags, products, tags, user_preferences, categories,
    social_media, user_profiles, users CASCADE;

CREATE TABLE users (
    user_id SERIAL PRIMARY KEY,
    email VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user' CHECK (role IN ('admin', 'user')),
    account_status VARCHAR(20) NOT NULL DEFAULT 'Aktif' CHECK (account_status IN ('Aktif', 'Suspend', 'Banned')),
    notes VARCHAR(255),
    deleted_at BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_profiles (
    profile_id SERIAL PRIMARY KEY,
    user_id INT,
    attachment_id INT,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    campus VARCHAR(255),
    student_number VARCHAR(50),
    subdistrict VARCHAR(255),
    district VARCHAR(200),
    CONSTRAINT fk_user_profiles_users FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE social_media (
    social_media_id SERIAL PRIMARY KEY,
    profile_id INT,
    platform VARCHAR(255) NOT NULL,
    username_link VARCHAR(255) NOT NULL,
    CONSTRAINT fk_social_media_user_profiles FOREIGN KEY (profile_id) REFERENCES user_profiles(profile_id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    categories_id SERIAL PRIMARY KEY,
    category_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_preferences (
    profile_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (profile_id, category_id),
    CONSTRAINT fk_user_preferences_profiles FOREIGN KEY (profile_id) REFERENCES user_profiles(profile_id) ON DELETE CASCADE,
    CONSTRAINT fk_user_preferences_categories FOREIGN KEY (category_id) REFERENCES categories(categories_id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tags (
    id_tag SERIAL PRIMARY KEY,
    name_tag VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- status is listing/moderation only. Belum Terjual / Appointment / Pickup / Done
-- are derived from purchase + invoice, never stored here.
CREATE TABLE products (
    product_id SERIAL PRIMARY KEY,
    user_id INT,
    category_id INT,
    item_name VARCHAR(255) NOT NULL,
    description TEXT,
    price NUMERIC(19, 0) NOT NULL,
    original_price NUMERIC(19, 0),
    condition INT CHECK (condition BETWEEN 1 AND 4),
    status VARCHAR(255) DEFAULT 'Tersedia' CHECK (status IN ('Tersedia', 'Ditolak', 'Dihapus')),
    moderation_note VARCHAR(255),
    delivery_methods VARCHAR(255),
    CONSTRAINT fk_products_users FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_products_categories FOREIGN KEY (category_id) REFERENCES categories(categories_id) ON DELETE RESTRICT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products_tags (
    products_tagsid_tag INT,
    productsproduct_id INT,
    PRIMARY KEY (products_tagsid_tag, productsproduct_id),
    CONSTRAINT fk_ptp_tags FOREIGN KEY (products_tagsid_tag) REFERENCES tags(id_tag) ON DELETE CASCADE,
    CONSTRAINT fk_ptp_products FOREIGN KEY (productsproduct_id) REFERENCES products(product_id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- One row per purchase attempt. Cancelled rows stay as history, so the partial
-- unique index only guards the single active purchase per product.
CREATE TABLE purchase (
    purchase_id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    amount NUMERIC(19, 0) NOT NULL CHECK (amount >= 0),
    meeting_lat NUMERIC(9, 6),
    meeting_lng NUMERIC(9, 6),
    cancelled_at TIMESTAMP,
    cancelled_by VARCHAR(10) CHECK (cancelled_by IN ('buyer', 'seller')),
    cancel_reason VARCHAR(255),
    CONSTRAINT fk_purchase_users FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_purchase_products FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    CONSTRAINT chk_purchase_meeting_pair CHECK (
        (meeting_lat IS NULL AND meeting_lng IS NULL)
        OR (meeting_lat IS NOT NULL AND meeting_lng IS NOT NULL)
    ),
    CONSTRAINT chk_purchase_meeting_range CHECK (
        (meeting_lat IS NULL OR meeting_lat BETWEEN -90 AND 90)
        AND (meeting_lng IS NULL OR meeting_lng BETWEEN -180 AND 180)
    ),
    CONSTRAINT chk_purchase_cancel_pair CHECK (
        (cancelled_at IS NULL AND cancelled_by IS NULL)
        OR (cancelled_at IS NOT NULL AND cancelled_by IS NOT NULL)
    ),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE UNIQUE INDEX uq_purchase_active_product
    ON purchase (product_id)
    WHERE cancelled_at IS NULL;

CREATE INDEX idx_purchase_user ON purchase (user_id);

-- One row per (product, buyer). stage 1 = buyer offer, stage 2 = seller counter.
-- Price bounds depend on products.price, so they are enforced by place_offer /
-- counter_offer instead of a table CHECK.
CREATE TABLE offer (
    offer_id SERIAL PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    bargain_price NUMERIC(19, 0) NOT NULL CHECK (bargain_price > 0),
    stage SMALLINT NOT NULL DEFAULT 1 CHECK (stage IN (1, 2)),
    status VARCHAR(10) NOT NULL DEFAULT 'OPEN' CHECK (status IN ('OPEN', 'CLOSED')),
    CONSTRAINT fk_offer_products FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    CONSTRAINT fk_offer_users FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT uq_offer_product_user UNIQUE (product_id, user_id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_offer_product_status ON offer (product_id, status);

CREATE TABLE reports (
    report_id SERIAL PRIMARY KEY,
    user_id INT,
    product_id INT,
    report_type VARCHAR(255) NOT NULL,
    description VARCHAR(255),
    review_status VARCHAR(20) NOT NULL DEFAULT 'Menunggu' CHECK (review_status IN ('Menunggu', 'Selesai', 'Diabaikan')),
    decision VARCHAR(20) CHECK (decision IN ('Abaikan', 'Suspend', 'Banned')),
    CONSTRAINT fk_reports_users FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_reports_products FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- attachment_type: 0 avatar, 1 product primary, 2 product secondary, 3 invoice proof.
-- Invoice proof rows MUST have product_id = NULL and report_id = NULL.
CREATE TABLE attachments (
    attachment_id SERIAL PRIMARY KEY,
    report_id INT,
    product_id INT,
    attachment_type INT,
    file_path VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(255),
    file_size INT,
    CONSTRAINT fk_attachments_reports FOREIGN KEY (report_id) REFERENCES reports(report_id) ON DELETE SET NULL,
    CONSTRAINT fk_attachments_products FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Existence of this row is what makes a purchase "Done".
CREATE TABLE invoice (
    invoice_id SERIAL PRIMARY KEY,
    purchase_id INT NOT NULL UNIQUE,
    attachment_id INT NOT NULL UNIQUE,
    pay NUMERIC(19, 0) NOT NULL CHECK (pay >= 0),
    return NUMERIC(19, 0) NOT NULL DEFAULT 0 CHECK (return >= 0),
    method VARCHAR(20) NOT NULL CHECK (method IN ('Tunai', 'Transfer')),
    CONSTRAINT fk_invoice_purchase FOREIGN KEY (purchase_id) REFERENCES purchase(purchase_id) ON DELETE CASCADE,
    CONSTRAINT fk_invoice_attachments FOREIGN KEY (attachment_id) REFERENCES attachments(attachment_id) ON DELETE RESTRICT,
    CONSTRAINT chk_invoice_change CHECK (pay >= return),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
