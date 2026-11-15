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
INSERT INTO user_profiles (user_id, name, student_number, campus)
SELECT users.user_id, profiles.name, profiles.student_number, profiles.campus
FROM users
JOIN (VALUES
    ('fami@test.com', 'Fami Test', '2251501001', 'Universitas Brawijaya'),
    ('sando@test.com', 'Sando Test', '2251501002', 'Universitas Negeri Malang'),
    ('rani.dummy@pasarkampus.local', 'Rani Putri', '2251501003', 'Universitas Brawijaya'),
    ('bima.dummy@pasarkampus.local', 'Bima Pratama', '2251501004', 'Politeknik Negeri Malang'),
    ('nisa.dummy@pasarkampus.local', 'Nisa Rahma', '2251501005', 'Univ. Muhammadiyah Malang')
) AS profiles(email, name, student_number, campus) ON profiles.email = users.email;

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
    ('bima.dummy@pasarkampus.local', 'Elektronik', 'Mouse Wireless', 'Mouse wireless dengan dongle USB, kondisi normal.', 80000, 130000, 3, 'Dihapus', 'Foto produk tidak sesuai dengan barang yang ditawarkan.', 'COD'),
    ('nisa.dummy@pasarkampus.local', 'Buku', 'Komik Petualangan Volume 1-5', 'Paket lima volume komik, sampul masih utuh.', 100000, 160000, 2, 'Tersedia', NULL, 'COD'),
    ('fami@test.com', 'Elektronik', 'Keyboard Logiac - Silver', 'Dirancang khusus untuk menunjang produktivitas tinggi, keyboard wireless dengan balutan warna silver elegan dan desain slim yang hemat tempat.', 90000, 150000, 2, 'Tersedia', NULL, 'COD'),
    ('fami@test.com', 'Buku', 'Buku Informatika', 'Buku pengantar informatika untuk mahasiswa semester awal, kondisi masih sangat terawat dan bersih.', 35000, 60000, 2, 'Proses', NULL, 'COD'),
    ('fami@test.com', 'Pakaian', 'Jaket Adibas', 'Jaket olahraga kasual ukuran L, bahan fleece nyaman dan hangat.', 80000, 150000, 2, 'Ditolak', 'Dihapus Admin (Melanggar Aturan)', 'COD'),
    ('fami@test.com', 'Elektronik', 'Kipas Angin', 'Kipas angin meja kondisi normal putaran kencang, cocok untuk kebutuhan kamar kos.', 95000, 140000, 2, 'Terjual', NULL, 'COD')
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
    ('Hobi', 'Komik Petualangan Volume 1-5'),
    ('Keyboard', 'Keyboard Logiac - Silver'),
    ('Aksesoris', 'Keyboard Logiac - Silver'),
    ('Buku Kuliah', 'Buku Informatika'),
    ('Catatan', 'Buku Informatika'),
    ('Fashion', 'Jaket Adibas'),
    ('Aksesoris', 'Jaket Adibas'),
    ('Kamar Kos', 'Kipas Angin'),
    ('Elektronik', 'Kipas Angin')
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
    ('Komik Petualangan Volume 1-5', 1, 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?auto=format&fit=crop&q=80&w=800', 'komik-petualangan.jpg', 'image/jpeg', 179000),
    ('Keyboard Logiac - Silver', 1, 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=300', 'keyboard-logiac-silver.jpg', 'image/jpeg', 184520),
    ('Buku Informatika', 1, 'https://images.unsplash.com/photo-1532012197267-da84d127e765?w=300', 'buku-informatika.jpg', 'image/jpeg', 145230),
    ('Jaket Adibas', 1, 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=300', 'jaket-adibas.jpg', 'image/jpeg', 198400),
    ('Kipas Angin', 1, 'https://images.unsplash.com/photo-1618941721653-9652a23077e6?w=300', 'kipas-angin.jpg', 'image/jpeg', 162100)
) AS attachment_data(item_name, attachment_type, file_path, file_name, file_type, file_size)
JOIN products AS product_rows ON product_rows.item_name = attachment_data.item_name;

-- Request Dummy
INSERT INTO requests (user_id, product_id, completed_date, status, meeting_point, created_at)
SELECT buyers.user_id, product_rows.product_id, seed_requests.completed_date, seed_requests.status, seed_requests.meeting_point, seed_requests.created_at
FROM (VALUES
    ('sando@test.com', 'Buku Algoritma dan Pemrograman', NULL::date, 'PENDING', 'Perpustakaan pusat', TIMESTAMP '2026-04-01 09:10:00'),
    ('rani.dummy@pasarkampus.local', 'Buku Algoritma dan Pemrograman', NULL::date, 'PENDING', 'Gerbang utama kampus', TIMESTAMP '2026-04-02 10:20:00'),
    ('bima.dummy@pasarkampus.local', 'Novel Laut Bercerita', NULL::date, 'PENDING', 'Kantin fakultas', TIMESTAMP '2026-04-03 11:30:00'),
    ('nisa.dummy@pasarkampus.local', 'Novel Laut Bercerita', NULL::date, 'PENDING', 'Perpustakaan pusat', TIMESTAMP '2026-04-04 12:40:00'),
    ('fami@test.com', 'Kalkulator Scientific', NULL::date, 'PENDING', 'Gedung kuliah A', TIMESTAMP '2026-04-05 13:50:00'),
    ('sando@test.com', 'Kalkulator Scientific', NULL::date, 'PENDING', 'Gedung kuliah A', TIMESTAMP '2026-04-06 14:00:00'),
    ('bima.dummy@pasarkampus.local', 'Kalkulator Scientific', NULL::date, 'PENDING', 'Kantin fakultas', TIMESTAMP '2026-04-07 15:10:00'),
    ('fami@test.com', 'Keyboard Mechanical 87 Keys', NULL::date, 'PENDING', 'Gerbang utama kampus', TIMESTAMP '2026-04-08 16:20:00'),
    ('rani.dummy@pasarkampus.local', 'Keyboard Mechanical 87 Keys', NULL::date, 'PENDING', 'Perpustakaan pusat', TIMESTAMP '2026-04-09 08:30:00'),
    ('nisa.dummy@pasarkampus.local', 'Headset Kabel', NULL::date, 'PENDING', 'Kantin fakultas', TIMESTAMP '2026-04-10 09:40:00'),
    ('fami@test.com', 'Headset Kabel', NULL::date, 'PENDING', 'Gedung kuliah B', TIMESTAMP '2026-04-11 10:50:00'),
    ('sando@test.com', 'Headset Kabel', NULL::date, 'PENDING', 'Gerbang utama kampus', TIMESTAMP '2026-04-12 12:00:00'),
    ('bima.dummy@pasarkampus.local', 'Headset Kabel', NULL::date, 'PENDING', 'Perpustakaan pusat', TIMESTAMP '2026-04-13 13:10:00'),
    ('sando@test.com', 'Lampu Meja Belajar', NULL::date, 'PENDING', 'Kantin fakultas', TIMESTAMP '2026-04-14 14:20:00'),
    ('rani.dummy@pasarkampus.local', 'Jaket Kampus Navy', NULL::date, 'PENDING', 'Gedung kuliah A', TIMESTAMP '2026-04-15 15:30:00'),
    ('nisa.dummy@pasarkampus.local', 'Jaket Kampus Navy', NULL::date, 'PENDING', 'Gerbang utama kampus', TIMESTAMP '2026-04-16 16:40:00'),
    ('fami@test.com', 'Rak Buku Susun', NULL::date, 'PENDING', 'Area parkir', TIMESTAMP '2026-04-17 08:50:00'),
    ('sando@test.com', 'Rak Buku Susun', NULL::date, 'PENDING', 'Area parkir', TIMESTAMP '2026-04-18 09:00:00'),
    ('bima.dummy@pasarkampus.local', 'Rak Buku Susun', NULL::date, 'PENDING', 'Area parkir', TIMESTAMP '2026-04-19 10:10:00'),
    ('fami@test.com', 'Mouse Wireless', NULL::date, 'PENDING', 'Perpustakaan pusat', TIMESTAMP '2026-04-20 11:20:00'),
    ('rani.dummy@pasarkampus.local', 'Mouse Wireless', NULL::date, 'PENDING', 'Kantin fakultas', TIMESTAMP '2026-04-21 12:30:00'),
    ('nisa.dummy@pasarkampus.local', 'Mouse Wireless', NULL::date, 'PENDING', 'Gedung kuliah B', TIMESTAMP '2026-04-22 13:40:00'),
    ('sando@test.com', 'Komik Petualangan Volume 1-5', NULL::date, 'PENDING', 'Gerbang utama kampus', TIMESTAMP '2026-04-23 14:50:00')
) AS seed_requests(buyer_email, item_name, completed_date, status, meeting_point, created_at)
JOIN users buyers ON buyers.email = seed_requests.buyer_email
JOIN products AS product_rows ON product_rows.item_name = seed_requests.item_name;

-- Report Dummy
INSERT INTO reports (user_id, product_id, report_type, description, created_at, review_status, decision)
VALUES
 (2, (SELECT product_id FROM products WHERE item_name = 'Buku Algoritma dan Pemrograman'), 'Barang palsu', 'Deskripsi barang tidak sesuai dengan kondisi yang ditawarkan.', '2026-01-05 00:00:00', 'Menunggu', NULL),
 (3, (SELECT product_id FROM products WHERE item_name = 'Kalkulator Scientific'), 'Penipuan pembayaran', 'Penjual meminta pembayaran di luar prosedur transaksi.', '2026-01-08 00:00:00', 'Menunggu', NULL),
 (4, (SELECT product_id FROM products WHERE item_name = 'Headset Kabel'), 'Ghosting COD', 'Pengguna tidak hadir pada waktu dan lokasi COD yang disepakati.', '2026-01-12 00:00:00', 'Menunggu', NULL),
 (5, (SELECT product_id FROM products WHERE item_name = 'Keyboard Mechanical 87 Keys'), 'Barang tidak sesuai', 'Barang yang diterima berbeda dari foto dan deskripsi.', '2026-01-16 00:00:00', 'Menunggu', NULL),
 (6, NULL, 'Akun mencurigakan', 'Akun mengirimkan tautan pembayaran yang tidak dikenal.', '2026-01-20 00:00:00', 'Menunggu', NULL),
 (2, (SELECT product_id FROM products WHERE item_name = 'Jaket Kampus Navy'), 'Konten tidak pantas', 'Deskripsi barang mengandung konten yang tidak sesuai.', '2026-01-24 00:00:00', 'Menunggu', NULL),
 (3, (SELECT product_id FROM products WHERE item_name = 'Mouse Wireless'), 'Harga menyesatkan', 'Harga pada iklan berbeda dengan harga saat transaksi.', '2026-01-28 00:00:00', 'Menunggu', NULL),
 (4, NULL, 'Transaksi di luar platform', 'Pengguna mengarahkan transaksi ke kanal pembayaran pribadi.', '2026-02-02 00:00:00', 'Menunggu', NULL),
 (5, (SELECT product_id FROM products WHERE item_name = 'Lampu Meja Belajar'), 'Barang rusak', 'Barang diterima dalam kondisi rusak tanpa keterangan sebelumnya.', '2026-02-06 00:00:00', 'Menunggu', NULL),
 (6, NULL, 'Identitas tidak jelas', 'Informasi identitas penjual tidak dapat dikonfirmasi.', '2026-02-10 00:00:00', 'Menunggu', NULL),
 (2, (SELECT product_id FROM products WHERE item_name = 'Komik Petualangan Volume 1-5'), 'Spam katalog', 'Pengguna mengunggah iklan yang sama berulang kali.', '2026-02-14 00:00:00', 'Menunggu', NULL),
 (3, NULL, 'Pelanggaran komunikasi', 'Pesan transaksi berisi ancaman kepada pengguna lain.', '2026-02-18 00:00:00', 'Menunggu', NULL),
 (4, (SELECT product_id FROM products WHERE item_name = 'Kalkulator Scientific'), 'Penipuan pembayaran', 'Bukti pembayaran yang dikirimkan tidak dapat diverifikasi.', '2026-02-22 00:00:00', 'Selesai', 'Suspend'),
 (5, (SELECT product_id FROM products WHERE item_name = 'Headset Kabel'), 'Barang palsu', 'Produk bermerek diduga bukan barang asli.', '2026-02-26 00:00:00', 'Selesai', 'Banned'),
 (6, NULL, 'Ghosting COD', 'Pelapor dan terlapor menyelesaikan masalah setelah klarifikasi.', '2026-03-02 00:00:00', 'Diabaikan', 'Abaikan'),
 (2, NULL, 'Laporan tidak valid', 'Laporan tidak memiliki informasi yang cukup untuk diproses.', '2026-03-06 00:00:00', 'Diabaikan', 'Abaikan'),
 (3, (SELECT product_id FROM products WHERE item_name = 'Buku Algoritma dan Pemrograman'), 'Barang tidak sesuai', 'Pelanggaran terkonfirmasi setelah pemeriksaan admin.', '2026-03-10 00:00:00', 'Selesai', 'Suspend'),
 (4, NULL, 'Akun mencurigakan', 'Aktivitas akun menunjukkan pola penipuan berulang.', '2026-03-14 00:00:00', 'Selesai', 'Banned'),
 (5, (SELECT product_id FROM products WHERE item_name = 'Jaket Kampus Navy'), 'Konten tidak pantas', 'Konten dihapus setelah ditinjau oleh admin.', '2026-03-18 00:00:00', 'Diabaikan', 'Abaikan'),
 (6, (SELECT product_id FROM products WHERE item_name = 'Mouse Wireless'), 'Spam katalog', 'Laporan ditutup karena iklan sudah dihapus oleh pemiliknya.', '2026-03-22 00:00:00', 'Diabaikan', 'Abaikan');