DROP DATABASE IF EXISTS pasar_kampus;
CREATE DATABASE pasar_kampus;
USE pasar_kampus;

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
    preferences VARCHAR(255),
    subdistrict VARCHAR(255),
    district VARCHAR(200),
    coordinates VARCHAR(255),
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

CREATE TABLE tags (
    id_tag SERIAL PRIMARY KEY,
    name_tag VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    product_id SERIAL PRIMARY KEY,
    user_id INT,
    category_id INT,
    item_name VARCHAR(255) NOT NULL,
    description VARCHAR(255),
    price NUMERIC(19, 0) NOT NULL,
    original_price NUMERIC(19, 0),
    condition INT CHECK (condition BETWEEN 1 AND 4),
    status VARCHAR(255),
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

CREATE TABLE requests (
    transaction_id SERIAL PRIMARY KEY,
    user_id INT,
    product_id INT,
    completed_date DATE DEFAULT CURRENT_DATE,
    status VARCHAR(20) NOT NULL CHECK (status IN ('PENDING', 'PROCESS', 'CANCELLED', 'COMPLETED')),
    meeting_point VARCHAR(255),
    CONSTRAINT fk_transaction_requests_users FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_transaction_requests_products FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

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
    CONSTRAINT fk_user_profiles_attachments FOREIGN KEY (attachment_id) REFERENCES attachments(attachment_id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);