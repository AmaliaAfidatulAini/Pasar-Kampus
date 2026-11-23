-- Users Dummy
INSERT INTO users ("email", "password", "role")
VALUES
('admin@pasarkampus.local', '$2y$10$XxLXRFuSX88JpiXA7PgzyO17h5RCOP55tZVkISaGumKMJ0/yIqEu2', 'admin'),
('fami@test.com', '$2y$10$XmLljyuVXiOef5xPWFnOresf/Qb6fWvdXJL7L0icuNcoDyYvzKjde', 'user'),
('sando@test.com', '$2y$10$XmLljyuVXiOef5xPWFnOresf/Qb6fWvdXJL7L0icuNcoDyYvzKjde', 'user'),
('rani.dummy@pasarkampus.local', '$2y$10$XmLljyuVXiOef5xPWFnOresf/Qb6fWvdXJL7L0icuNcoDyYvzKjde', 'user'),
('bima.dummy@pasarkampus.local', '$2y$10$XmLljyuVXiOef5xPWFnOresf/Qb6fWvdXJL7L0icuNcoDyYvzKjde', 'user'),
('nisa.dummy@pasarkampus.local', '$2y$10$XmLljyuVXiOef5xPWFnOresf/Qb6fWvdXJL7L0icuNcoDyYvzKjde', 'user');

-- User Profiles Dummy
INSERT INTO user_profiles (user_id, name, student_number, campus, district, subdistrict)
SELECT users.user_id, profiles.name, profiles.student_number, profiles.campus, profiles.district, profiles.subdistrict
FROM users
JOIN (VALUES
    ('fami@test.com', 'Fami Test', '2251501001', 'Universitas Brawijaya', 'Ketawanggede', 'Lowokwaru'),
    ('sando@test.com', 'Sando Test', '2251501002', 'Universitas Negeri Malang', 'Sumbersari', 'Lowokwaru'),
    ('rani.dummy@pasarkampus.local', 'Rani Putri', '2251501003', 'Universitas Brawijaya', 'Dinoyo', 'Lowokwaru'),
    ('bima.dummy@pasarkampus.local', 'Bima Pratama', '2251501004', 'Politeknik Negeri Malang', 'Jatimulyo', 'Lowokwaru'),
    ('nisa.dummy@pasarkampus.local', 'Nisa Rahma', '2251501005', 'Univ. Muhammadiyah Malang', 'Landungsari', 'Dau')
) AS profiles(email, name, student_number, campus, district, subdistrict) ON profiles.email = users.email;

-- Category Dummy
INSERT INTO categories 
("category_name")
VALUES
('Buku'),
('ATK'),
('Elektronik'),
('Perlengkapan Kos'),
('Pakaian'),
('Lainnya');

-- User Preferences dummy
INSERT INTO user_preferences (profile_id, category_id)
SELECT profiles.profile_id, categories.categories_id
FROM user_profiles profiles
JOIN users ON users.user_id = profiles.user_id
JOIN (VALUES
    ('fami@test.com', 'Buku'),
    ('fami@test.com', 'ATK'),
    ('sando@test.com', 'Buku'),
    ('sando@test.com', 'Pakaian'),
    ('rani.dummy@pasarkampus.local', 'Elektronik'),
    ('rani.dummy@pasarkampus.local', 'ATK'),
    ('bima.dummy@pasarkampus.local', 'Elektronik'),
    ('bima.dummy@pasarkampus.local', 'Perlengkapan Kos'),
    ('nisa.dummy@pasarkampus.local', 'Buku'),
    ('nisa.dummy@pasarkampus.local', 'Elektronik')
) AS preference_data(email, category_name) ON preference_data.email = users.email
JOIN categories ON categories.category_name = preference_data.category_name;


-- Tag Dummy
INSERT INTO tags (name_tag)
VALUES
('Buku Kuliah'),
('Novel'),
('Komik'),
('Catatan'),
('Pulpen'),
('Pensil'),
('Kalkulator'),
('Laptop'),
('Smartphone'),
('Headset'),
('Keyboard'),
('Monitor'),
('Kamar Kos'),
('Dekorasi'),
('Fashion'),
('Sepatu'),
('Tas'),
('Aksesoris'),
('Hobi'),
('Olahraga');

-- Product Dummy
INSERT INTO products (user_id, category_id, item_name, description, price, original_price, condition, status, moderation_note, delivery_methods)
SELECT owners.user_id, categories.categories_id, product_data.item_name, product_data.description, product_data.price, product_data.original_price, product_data.condition, product_data.status, product_data.moderation_note, product_data.delivery_methods
FROM (VALUES
    ('fami@test.com', 'Buku', 'Buku Algoritma dan Pemrograman', 'Buku kuliah edisi terbaru, kondisi bersih dan lengkap.', 85000, 120000, 2, 'Tersedia', NULL, 'COD'),
    ('sando@test.com', 'Buku', 'Novel Laut Bercerita', 'Novel bekas terawat, beberapa halaman diberi penanda.', 55000, 90000, 2, 'Tersedia', NULL, 'COD'),
    ('rani.dummy@pasarkampus.local', 'ATK', 'Kalkulator Scientific', 'Kalkulator scientific berfungsi normal, baterai baru.', 95000, 150000, 2, 'Tersedia', NULL, 'COD'),
    ('bima.dummy@pasarkampus.local', 'Elektronik', 'Keyboard Mechanical 87 Keys', 'Keyboard mechanical dengan kabel USB-C dan keycap lengkap.', 320000, 450000, 2, 'Tersedia', NULL, 'COD'),
    ('nisa.dummy@pasarkampus.local', 'Elektronik', 'Headset Kabel', 'Headset kabel dengan mikrofon, suara kiri dan kanan normal.', 70000, 110000, 3, 'Tersedia', NULL, 'COD'),
    ('fami@test.com', 'Perlengkapan Kos', 'Lampu Meja Belajar', 'Lampu meja LED dengan tiga tingkat kecerahan.', 60000, 95000, 2, 'Tersedia', NULL, 'COD'),
    ('sando@test.com', 'Pakaian', 'Jaket Kampus Navy', 'Jaket kampus ukuran L, sudah dicuci dan siap pakai.', 125000, 220000, 2, 'Tersedia', NULL, 'COD'),
    ('rani.dummy@pasarkampus.local', 'Perlengkapan Kos', 'Rak Buku Susun', 'Rak buku tiga tingkat, rangka kokoh dan mudah dibongkar.', 140000, 200000, 2, 'Tersedia', NULL, 'COD'),
    ('bima.dummy@pasarkampus.local', 'Elektronik', 'Mouse Wireless', 'Mouse wireless dengan dongle USB, kondisi normal.', 80000, 130000, 3, 'Ditolak', 'Foto produk tidak sesuai dengan barang yang ditawarkan.', 'COD'),
    ('nisa.dummy@pasarkampus.local', 'Buku', 'Komik Petualangan Volume 1-5', 'Paket lima volume komik, sampul masih utuh.', 100000, 160000, 2, 'Tersedia', NULL, 'COD')
) AS product_data(owner_email, category_name, item_name, description, price, original_price, condition, status, moderation_note, delivery_methods)
JOIN users owners ON owners.email = product_data.owner_email
JOIN categories ON categories.category_name = product_data.category_name;

-- Product Tag Dummy
INSERT INTO products_tags (products_tagsid_tag, productsproduct_id)
SELECT tags.id_tag, product_rows.product_id
FROM (VALUES
    ('Buku Kuliah', 'Buku Algoritma dan Pemrograman'),
    ('Catatan', 'Buku Algoritma dan Pemrograman'),
    ('Novel', 'Novel Laut Bercerita'),
    ('Buku Kuliah', 'Novel Laut Bercerita'),
    ('Kalkulator', 'Kalkulator Scientific'),
    ('Pensil', 'Kalkulator Scientific'),
    ('Keyboard', 'Keyboard Mechanical 87 Keys'),
    ('Aksesoris', 'Keyboard Mechanical 87 Keys'),
    ('Headset', 'Headset Kabel'),
    ('Aksesoris', 'Headset Kabel'),
    ('Kamar Kos', 'Lampu Meja Belajar'),
    ('Dekorasi', 'Lampu Meja Belajar'),
    ('Fashion', 'Jaket Kampus Navy'),
    ('Aksesoris', 'Jaket Kampus Navy'),
    ('Kamar Kos', 'Rak Buku Susun'),
    ('Buku Kuliah', 'Rak Buku Susun'),
    ('Aksesoris', 'Mouse Wireless'),
    ('Komik', 'Komik Petualangan Volume 1-5'),
    ('Hobi', 'Komik Petualangan Volume 1-5')
) AS tag_data(tag_name, item_name)
JOIN tags ON tags.name_tag = tag_data.tag_name
JOIN products AS product_rows ON product_rows.item_name = tag_data.item_name;

-- Attachment Dummy
INSERT INTO attachments (product_id, attachment_type, file_path, file_name, file_type, file_size)
SELECT product_rows.product_id, attachment_data.attachment_type, attachment_data.file_path, attachment_data.file_name, attachment_data.file_type, attachment_data.file_size
FROM (VALUES
    ('Buku Algoritma dan Pemrograman', 1, 'https://images.unsplash.com/photo-1532012197267-da84d127e765?auto=format&fit=crop&q=80&w=800', 'buku-algoritma.jpg', 'image/jpeg', 204800),
    ('Novel Laut Bercerita', 1, 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&q=80&w=800', 'novel-laut-bercerita.jpg', 'image/jpeg', 195400),
    ('Kalkulator Scientific', 1, 'https://images.unsplash.com/photo-1611125832047-1d7ad1e8e48f?auto=format&fit=crop&q=80&w=800', 'kalkulator-scientific.jpg', 'image/jpeg', 152000),
    ('Keyboard Mechanical 87 Keys', 1, 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800', 'keyboard-mechanical-87.jpg', 'image/jpeg', 225000),
    ('Headset Kabel', 1, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&q=80&w=800', 'headset-kabel.jpg', 'image/jpeg', 181000),
    ('Lampu Meja Belajar', 1, 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&q=80&w=800', 'lampu-meja-belajar.jpg', 'image/jpeg', 145000),
    ('Jaket Kampus Navy', 1, 'https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&q=80&w=800', 'jaket-kampus-navy.jpg', 'image/jpeg', 210000),
    ('Rak Buku Susun', 1, 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&q=80&w=800', 'rak-buku-susun.jpg', 'image/jpeg', 198000),
    ('Mouse Wireless', 1, 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?auto=format&fit=crop&q=80&w=800', 'mouse-wireless.jpg', 'image/jpeg', 135000),
    ('Komik Petualangan Volume 1-5', 1, 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?auto=format&fit=crop&q=80&w=800', 'komik-petualangan.jpg', 'image/jpeg', 179000)
) AS attachment_data(item_name, attachment_type, file_path, file_name, file_type, file_size)
JOIN products AS product_rows ON product_rows.item_name = attachment_data.item_name;

-- Purchase Dummy (one active purchase per product, cancelled rows kept as history)
INSERT INTO purchase (user_id, product_id, amount, meeting_lat, meeting_lng,
                      cancelled_at, cancelled_by, cancel_reason, created_at)
SELECT buyers.user_id, p.product_id, seed.amount, seed.lat, seed.lng,
       seed.cancelled_at, seed.cancelled_by, seed.cancel_reason, seed.created_at
FROM (VALUES
    ('sando@test.com', 'Buku Algoritma dan Pemrograman', 85000,
     NULL::numeric, NULL::numeric, NULL::timestamp, NULL::varchar, NULL::varchar,
     TIMESTAMP '2026-04-01 09:10:00'),
    ('bima.dummy@pasarkampus.local', 'Novel Laut Bercerita', 55000,
     -7.952700::numeric, 112.614500::numeric, NULL::timestamp, NULL::varchar, NULL::varchar,
     TIMESTAMP '2026-04-02 10:20:00'),
    ('fami@test.com', 'Kalkulator Scientific', 95000,
     -7.953100::numeric, 112.615200::numeric, NULL::timestamp, NULL::varchar, NULL::varchar,
     TIMESTAMP '2026-04-03 11:30:00'),
    ('fami@test.com', 'Keyboard Mechanical 87 Keys', 320000,
     NULL::numeric, NULL::numeric, TIMESTAMP '2026-04-04 12:40:00', 'buyer', 'Berubah pikiran',
     TIMESTAMP '2026-04-04 12:40:00')
) AS seed(buyer_email, item_name, amount, lat, lng,
          cancelled_at, cancelled_by, cancel_reason, created_at)
JOIN users buyers ON buyers.email = seed.buyer_email
JOIN products p ON p.item_name = seed.item_name;

-- Offer Dummy (closed on products already purchased, open stage 1 / stage 2 on available products)
INSERT INTO offer (product_id, user_id, bargain_price, stage, status, created_at)
SELECT p.product_id, buyers.user_id, seed.bargain_price, seed.stage, seed.status, seed.created_at
FROM (VALUES
    ('Buku Algoritma dan Pemrograman', 'rani.dummy@pasarkampus.local', 70000, 1, 'CLOSED', TIMESTAMP '2026-04-05 09:00:00'),
    ('Buku Algoritma dan Pemrograman', 'bima.dummy@pasarkampus.local', 75000, 1, 'CLOSED', TIMESTAMP '2026-04-05 09:30:00'),
    ('Novel Laut Bercerita', 'nisa.dummy@pasarkampus.local', 45000, 1, 'CLOSED', TIMESTAMP '2026-04-05 10:00:00'),
    ('Kalkulator Scientific', 'sando@test.com', 80000, 1, 'CLOSED', TIMESTAMP '2026-04-05 10:30:00'),
    ('Keyboard Mechanical 87 Keys', 'rani.dummy@pasarkampus.local', 280000, 1, 'CLOSED', TIMESTAMP '2026-04-05 11:00:00'),
    ('Headset Kabel', 'fami@test.com', 66000, 2, 'OPEN', TIMESTAMP '2026-04-07 09:00:00'),
    ('Headset Kabel', 'sando@test.com', 65000, 2, 'OPEN', TIMESTAMP '2026-04-06 09:30:00'),
    ('Headset Kabel', 'rani.dummy@pasarkampus.local', 50000, 1, 'OPEN', TIMESTAMP '2026-04-06 10:00:00'),
    ('Lampu Meja Belajar', 'rani.dummy@pasarkampus.local', 40000, 1, 'OPEN', TIMESTAMP '2026-04-06 10:30:00'),
    ('Jaket Kampus Navy', 'nisa.dummy@pasarkampus.local', 70000, 1, 'OPEN', TIMESTAMP '2026-04-06 11:00:00'),
    ('Rak Buku Susun', 'bima.dummy@pasarkampus.local', 90000, 1, 'OPEN', TIMESTAMP '2026-04-06 11:30:00'),
    ('Komik Petualangan Volume 1-5', 'fami@test.com', 60000, 1, 'OPEN', TIMESTAMP '2026-04-06 12:00:00'),
    ('Lampu Meja Belajar', 'nisa.dummy@pasarkampus.local', 50000, 2, 'OPEN', TIMESTAMP '2026-04-07 10:00:00'),
    ('Lampu Meja Belajar', 'bima.dummy@pasarkampus.local', 45000, 1, 'OPEN', TIMESTAMP '2026-04-08 08:00:00'),
    ('Buku Algoritma dan Pemrograman', 'nisa.dummy@pasarkampus.local', 68000, 1, 'CLOSED', TIMESTAMP '2026-04-05 09:45:00'),
    ('Jaket Kampus Navy', 'rani.dummy@pasarkampus.local', 90000, 2, 'OPEN', TIMESTAMP '2026-04-07 10:00:00'),
    ('Jaket Kampus Navy', 'bima.dummy@pasarkampus.local', 80000, 1, 'OPEN', TIMESTAMP '2026-04-08 09:00:00'),
    ('Novel Laut Bercerita', 'rani.dummy@pasarkampus.local', 40000, 1, 'CLOSED', TIMESTAMP '2026-04-05 10:15:00'),
    ('Komik Petualangan Volume 1-5', 'sando@test.com', 75000, 1, 'OPEN', TIMESTAMP '2026-04-08 10:00:00')
) AS seed(item_name, buyer_email, bargain_price, stage, status, created_at)
JOIN products p ON p.item_name = seed.item_name
JOIN users buyers ON buyers.email = seed.buyer_email;

-- Invoice Dummy: payment proof attachment (attachment_type 3, no product/report owner)
WITH proof AS (
    INSERT INTO attachments (product_id, report_id, attachment_type,
                             file_path, file_name, file_type, file_size)
    VALUES (NULL, NULL, 3,
            'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&q=80&w=800',
            'bukti-pembayaran-kalkulator.jpg', 'image/jpeg', 128000)
    RETURNING attachment_id
)
INSERT INTO invoice (purchase_id, attachment_id, pay, return, method, created_at)
SELECT pu.purchase_id, proof.attachment_id, 100000, 5000, 'Tunai', TIMESTAMP '2026-04-03 13:00:00'
FROM proof
JOIN purchase pu ON pu.cancelled_at IS NULL
JOIN products p ON p.product_id = pu.product_id
WHERE p.item_name = 'Kalkulator Scientific';

-- Report Dummy
INSERT INTO reports (user_id, product_id, report_type, description, created_at, review_status, decision)
SELECT reporters.user_id, products.product_id, report_data.report_type, report_data.description, report_data.created_at, report_data.review_status, report_data.decision
FROM (VALUES
    ('fami@test.com', 'Buku Algoritma dan Pemrograman', 'Barang palsu', 'Deskripsi barang tidak sesuai dengan kondisi yang ditawarkan.', TIMESTAMP '2026-01-05 00:00:00', 'Menunggu', NULL),
    ('sando@test.com', 'Kalkulator Scientific', 'Penipuan pembayaran', 'Penjual meminta pembayaran di luar prosedur transaksi.', TIMESTAMP '2026-01-08 00:00:00', 'Menunggu', NULL),
    ('rani.dummy@pasarkampus.local', 'Headset Kabel', 'Ghosting COD', 'Pengguna tidak hadir pada waktu dan lokasi COD yang disepakati.', TIMESTAMP '2026-01-12 00:00:00', 'Menunggu', NULL),
    ('bima.dummy@pasarkampus.local', 'Keyboard Mechanical 87 Keys', 'Barang tidak sesuai', 'Barang yang diterima berbeda dari foto dan deskripsi.', TIMESTAMP '2026-01-16 00:00:00', 'Menunggu', NULL),
    ('nisa.dummy@pasarkampus.local', NULL, 'Akun mencurigakan', 'Akun mengirimkan tautan pembayaran yang tidak dikenal.', TIMESTAMP '2026-01-20 00:00:00', 'Menunggu', NULL),
    ('fami@test.com', 'Jaket Kampus Navy', 'Konten tidak pantas', 'Deskripsi barang mengandung konten yang tidak sesuai.', TIMESTAMP '2026-01-24 00:00:00', 'Menunggu', NULL),
    ('sando@test.com', 'Mouse Wireless', 'Harga menyesatkan', 'Harga pada iklan berbeda dengan harga saat transaksi.', TIMESTAMP '2026-01-28 00:00:00', 'Menunggu', NULL),
    ('rani.dummy@pasarkampus.local', NULL, 'Transaksi di luar platform', 'Pengguna mengarahkan transaksi ke kanal pembayaran pribadi.', TIMESTAMP '2026-02-02 00:00:00', 'Menunggu', NULL),
    ('bima.dummy@pasarkampus.local', 'Lampu Meja Belajar', 'Barang rusak', 'Barang diterima dalam kondisi rusak tanpa keterangan sebelumnya.', TIMESTAMP '2026-02-06 00:00:00', 'Menunggu', NULL),
    ('nisa.dummy@pasarkampus.local', NULL, 'Identitas tidak jelas', 'Informasi identitas penjual tidak dapat dikonfirmasi.', TIMESTAMP '2026-02-10 00:00:00', 'Menunggu', NULL),
    ('fami@test.com', 'Komik Petualangan Volume 1-5', 'Spam katalog', 'Pengguna mengunggah iklan yang sama berulang kali.', TIMESTAMP '2026-02-14 00:00:00', 'Menunggu', NULL),
    ('sando@test.com', NULL, 'Pelanggaran komunikasi', 'Pesan transaksi berisi ancaman kepada pengguna lain.', TIMESTAMP '2026-02-18 00:00:00', 'Menunggu', NULL),
    ('rani.dummy@pasarkampus.local', 'Kalkulator Scientific', 'Penipuan pembayaran', 'Bukti pembayaran yang dikirimkan tidak dapat diverifikasi.', TIMESTAMP '2026-02-22 00:00:00', 'Selesai', 'Suspend'),
    ('bima.dummy@pasarkampus.local', 'Headset Kabel', 'Barang palsu', 'Produk bermerek diduga bukan barang asli.', TIMESTAMP '2026-02-26 00:00:00', 'Selesai', 'Banned'),
    ('nisa.dummy@pasarkampus.local', NULL, 'Ghosting COD', 'Pelapor dan terlapor menyelesaikan masalah setelah klarifikasi.', TIMESTAMP '2026-03-02 00:00:00', 'Diabaikan', 'Abaikan'),
    ('fami@test.com', NULL, 'Laporan tidak valid', 'Laporan tidak memiliki informasi yang cukup untuk diproses.', TIMESTAMP '2026-03-06 00:00:00', 'Diabaikan', 'Abaikan'),
    ('sando@test.com', 'Buku Algoritma dan Pemrograman', 'Barang tidak sesuai', 'Pelanggaran terkonfirmasi setelah pemeriksaan admin.', TIMESTAMP '2026-03-10 00:00:00', 'Selesai', 'Suspend'),
    ('rani.dummy@pasarkampus.local', NULL, 'Akun mencurigakan', 'Aktivitas akun menunjukkan pola penipuan berulang.', TIMESTAMP '2026-03-14 00:00:00', 'Selesai', 'Banned'),
    ('bima.dummy@pasarkampus.local', 'Jaket Kampus Navy', 'Konten tidak pantas', 'Konten dihapus setelah ditinjau oleh admin.', TIMESTAMP '2026-03-18 00:00:00', 'Diabaikan', 'Abaikan'),
    ('nisa.dummy@pasarkampus.local', 'Mouse Wireless', 'Spam katalog', 'Laporan ditutup karena iklan sudah dihapus oleh pemiliknya.', TIMESTAMP '2026-03-22 00:00:00', 'Diabaikan', 'Abaikan')
) AS report_data(reporter_email, item_name, report_type, description, created_at, review_status, decision)
JOIN users reporters ON reporters.email = report_data.reporter_email
LEFT JOIN products ON products.item_name = report_data.item_name;